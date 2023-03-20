<?php

namespace Drupal\social_automation\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\State\StateInterface;
use Drupal\social_automation\Context\ExecutionState;
use Drupal\social_automation\Engine\AutomationComponentRepositoryInterface;
use Drupal\social_automation\Event\AutomationDrushEvent;
use Drupal\social_automation\Event\EntityEvent;
use Drupal\social_automation\Plugin\AutomationTriggerManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Event as SymfonyComponentEvent;
use Symfony\Contracts\EventDispatcher\Event as SymfonyContractsEvent;

/**
 * Subscribes to Symfony events and maps them to Automation events.
 */
class GenericEventSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager used for loading workflow event config entities.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Automation event manager.
   */
  protected AutomationTriggerManager $triggerManager;

  /**
   * The component repository.
   */
  protected AutomationComponentRepositoryInterface $componentRepository;

  /**
   * The automation debug logger channel.
   */
  protected LoggerChannelInterface $automationDebugLogger;

  /**
   * The state.
   */
  protected StateInterface $state;

  /**
   * The eventDispatcher service.
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * A ModuleHandler.
   */
  protected ModuleHandlerInterface $moduleHandler;


  /**
   * Events to subscribe if container is not available. See #2816033.
   *
   * @var array
   */
  private static array $staticEvents = [
    KernelEvents::CONTROLLER => ['registerDynamicEvents', 100],
    KernelEvents::REQUEST => ['registerDynamicEvents', 100],
    KernelEvents::TERMINATE => ['registerDynamicEvents', 100],
    KernelEvents::VIEW => ['registerDynamicEvents', 100],
    AutomationDrushEvent::DRUSHINIT => ['registerDynamicEvents', 100],
  ];

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\social_automation\Plugin\AutomationTriggerManager $trigger_manager
   *   The Automation event manager.
   * @param \Drupal\social_automation\Engine\AutomationComponentRepositoryInterface $component_repository
   *   The component repository.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The Automation debug logger channel.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AutomationTriggerManager $trigger_manager, AutomationComponentRepositoryInterface $component_repository, LoggerChannelInterface $logger, StateInterface $state, EventDispatcherInterface $event_dispatcher, ModuleHandlerInterface $module_handler) {
    $this->entityTypeManager = $entity_type_manager;
    $this->triggerManager = $trigger_manager;
    $this->componentRepository = $component_repository;
    $this->automationDebugLogger = $logger;
    $this->state = $state;
    $this->eventDispatcher = $event_dispatcher;
    $this->moduleHandler = $module_handler;
  }

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * The array keys are event names and the value can be:
   *
   *  * The method name to call (priority defaults to 0)
   *  * An array composed of the method name to call and the priority
   *  * An array of arrays composed of the method names to call and respective
   *    priorities, or 0 if unset
   *
   * For instance:
   *
   *  * ['eventName' => 'methodName']
   *  * ['eventName' => ['methodName', $priority]]
   *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
   *
   * The code must not depend on runtime state as it will only be called at
   * compile time. All logic depending on runtime state must be put into the
   * individual methods handling the events.
   *
   * @see EventSubscriberInterface::getSubscribedEvents()
   *
   * @return array
   *   The event names with priorities.
   */
  public static function getSubscribedEvents(): array {

    // Register this listener for every event that is used by a workflow event.
    $events = [];

    // If there is no state service there is nothing we can do here. This static
    // method could be called early when the container is built, so the state
    // service might not always be available.
    if (!\Drupal::hasService('state')) {
      return self::$staticEvents;
    }

    // Since we cannot access the workflow event config storage here we have to
    // use the state system to provide registered events. The Reaction
    // Workflow event storage is responsible for keeping the registered events
    // up to date in the state system.
    // @see \Drupal\social_automation\Entity\WorkflowEventStorage
    $state = \Drupal::state();
    $registered_event_names = $state->get('social_automation.registered_events');

    if (!empty($registered_event_names)) {
      foreach ($registered_event_names as $key => $event_name) {
        $events[$key] = ['doAutomation', 1500];
      }
    }

    return $events;
  }

  /**
   * Rebuilds container when dynamic rule eventsubscribers are not registered.
   *
   * @param object $event
   *   The event object containing context for the event.
   *   In Drupal 9 this will be a \Symfony\Component\EventDispatcher\Event,
   *   In Drupal 10 this will be a \Symfony\Contracts\EventDispatcher\Event.
   * @param string $event_name
   *   The event name.
   */
  public function registerDynamicEvents(object $event, $event_name): void {
    // @todo The 'object' type hint should be replaced with the appropriate
    // class once Symfony 4 is no longer supported, and the assert() should be
    // removed.
    assert(
      $event instanceof SymfonyComponentEvent ||
      $event instanceof SymfonyContractsEvent
    );

    foreach (self::$staticEvents as $old_event_name => $method) {
      $this->eventDispatcher->removeListener($old_event_name, [
        $this,
        $method[0],
      ]);
    }
    $this->eventDispatcher->addSubscriber($this);
    $this->moduleHandler->reload();
  }

  /**
   * Reacts on the given event and invokes configured reaction automation.
   *
   * @param object $event
   *   The event object containing context for the event.
   *   In Drupal 9 this will be a \Symfony\Component\EventDispatcher\Event,
   *   In Drupal 10 this will be a \Symfony\Contracts\EventDispatcher\Event.
   * @param string $event_name
   *   The event name.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\social_automation\Exception\EvaluationException
   * @throws \Drupal\social_automation\Exception\InvalidArgumentException
   */
  public function doAutomation(object $event, string $event_name): void {
    // @todo The 'object' type hint should be replaced with the appropriate
    // class once Symfony 4 is no longer supported, and the assert() should be
    // removed.
    assert(
      $event instanceof SymfonyComponentEvent ||
      $event instanceof SymfonyContractsEvent
    );

    // Get triggers based on the event_name from the state system.
    // events.
    $registered_events = $this->state->get('social_automation.registered_events');
    $triggers = $registered_events[$event_name];

    // Determine the triggered events.
    foreach ($triggers as $trigger) {
      $trigger_plugin = $this->triggerManager->getDefinition($trigger);
      $fq_event_name = $trigger_plugin['event'];
      // Check if any bundle is specified.
      $bundle = explode('--', $fq_event_name);

      // This means a bundle is specified... something like 'node--page'.
      if (isset($bundle[1]) && $event instanceof EntityEvent &&
        $bundle[1] !== $event->getSubject()->bundle()) {
        continue;
      }

      $triggered_events[] = $trigger_plugin['id'];

      // @todo This is the point in time where we decide if a trigger is
      // @todo executed immediately or send to a queue.
      // Setup the execution state.
      $state = ExecutionState::create();
      foreach ($trigger_plugin['context_definitions'] as $context_name => $context_definition) {
        // If this is a GenericEvent, get the context for the workflowevent
        // from the event arguments.
        if ($event instanceof GenericEvent) {
          $value = $event->getArgument($context_name);
        }
        // Else there must be a getter method or public property.
        else {
          $value = $event->$context_name;
        }
        $state->setVariable(
          $context_name,
          $context_definition,
          $value
        );
      }

      $components = $this->componentRepository->getMultiple($triggered_events, 'automation_event');

      /** @var \Drupal\social_automation\Engine\AutomationComponent $component */
      foreach ($components as $component) {
        // Debug logging.
        $this->automationDebugLogger->info('Reacting on event %label.', [
          '%label' => $trigger_plugin['label'],
          'element' => NULL,
          'scope' => TRUE,
        ]);

        // Execute the action.
        $component->getExpression()->executeWithState($state);

        // Debug logging.
        $this->automationDebugLogger->info('Finished reacting on event %label.', [
          '%label' => $trigger_plugin['label'],
          'element' => NULL,
          'scope' => FALSE,
        ]);
      }
      $state->autoSave();
    }

  }

}
