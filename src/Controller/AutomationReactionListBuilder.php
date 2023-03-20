<?php

namespace Drupal\social_automation\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Drupal\social_automation\Plugin\AutomationTriggerManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of WorkflowEventConfig entities.
 *
 * @see \Drupal\social_automation\Entity\WorkflowEventConfig
 * @see \Drupal\views_ui\ViewListBuilder
 */
class AutomationReactionListBuilder extends ConfigEntityListBuilder {

  /**
   * The Automation event plugin manager.
   *
   * @var \Drupal\social_automation\Plugin\AutomationTriggerManager
   */
  protected $triggerManager;

  /**
   * Constructs a new AutomationReactionListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\social_automation\Plugin\AutomationTriggerManager $trigger_manager
   *   The Automation event plugin manager.
   */
  public function __construct(EntityTypeInterface $entity_type,
                              EntityStorageInterface $storage,
                              AutomationTriggerManager $trigger_manager) {
    parent::__construct($entity_type, $storage);
    // Disable the pager because this list builder uses client-side filters,
    // which requires all entities to be listed.
    $this->limit = FALSE;
    $this->triggerManager = $trigger_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('plugin.manager.automation_trigger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entities = [
      'enabled' => [],
      'disabled' => [],
    ];
    foreach (parent::load() as $entity) {
      if ($entity->status()) {
        $entities['enabled'][] = $entity;
      }
      else {
        $entities['disabled'][] = $entity;
      }
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the reaction automation list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['label'] = $this->t('Workflow event');
    $header['event'] = $this->t('Event');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $event_names = $entity->getEventNames();
    $event_labels = [];
    // List all Events that trigger this Workflow event.
    foreach ($event_names as $event_name) {
      $event_definition = $this->triggerManager->getDefinition($event_name);
      $event_labels[] = $event_definition['label'];
    }

    /** @var \Drupal\social_automation\Entity\WorkflowEventConfig $entity */
    $details = $this->t('Machine name: @name', ['@name' => $entity->id()]);
    if ($entity->hasTags()) {
      $details .= '<br />' . $this->t('Tags: @tags', [
        '@tags' => implode(', ', $entity->getTags()),
      ]);
    }

    $row['label']['data-drupal-selector'] = 'automation-table-filter-text-source';
    $row['label']['data'] = [
      '#plain_text' => $entity->label(),
      '#suffix' => '<div class="description">' . $details . '</div>',
    ];
    $row['event']['data-drupal-selector'] = 'automation-table-filter-text-source';
    $row['event']['data'] = [
      '#plain_text' => implode(",<br />", $event_labels),
    ];
    $row['description']['data-drupal-selector'] = 'automation-table-filter-text-source';
    $row['description']['data'] = [
      '#type' => 'processed_text',
      '#text' => $entity->getDescription(),
      '#format' => 'restricted_html',
    ];
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildOperations(EntityInterface $entity) {
    $build = parent::buildOperations($entity);

    uasort($build['#links'], 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['description'] = [
      '#prefix' => '<p>',
      '#markup' => $this->t('Reaction automation, listed below, react on selected events on the site. Each workflow event may fire any number of <em>actions</em>, and may have any number of <em>conditions</em> that must be met for the actions to be executed. You can also set up <a href=":components">components</a> – stand-alone sets of Automation configuration that can be used in Automation and other parts of your site. See <a href=":documentation">the online documentation</a> for an introduction on how to use social_automation.', [
        ':components' => Url::fromRoute('entity.automation_component.collection')->toString(),
        ':documentation' => 'https://www.drupal.org/node/298480',
      ]),
      '#suffix' => '</p>',
    ];

    $entities = $this->load();
    $build['#type'] = 'container';
    $build['#attributes']['id'] = 'automation-entity-list';

    $build['#attached']['library'][] = 'core/drupal.ajax';
    $build['#attached']['library'][] = 'social_automation/social_automation_ui.listing';

    $build['filters'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['table-filter', 'js-show'],
      ],
    ];

    $build['filters']['text'] = [
      '#type' => 'search',
      '#title' => $this->t('Filter'),
      '#title_display' => 'invisible',
      '#size' => 60,
      '#placeholder' => $this->t('Filter by workflowevent name, machine name, event, description, or tag'),
      '#attributes' => [
        'class' => ['automation-filter-text'],
        'data-table' => '.automation-listing-table',
        'autocomplete' => 'off',
        'title' => $this->t('Enter a part of the workflowevent name, machine name, event, description, or tag to filter by.'),
      ],
    ];

    $build['enabled']['heading'] = [
      '#prefix' => '<h2>',
      '#markup' => $this->t('Enabled', [], ['context' => 'Plural']),
      '#suffix' => '</h2>',
    ];
    $build['disabled']['heading'] = [
      '#prefix' => '<h2>',
      '#markup' => $this->t('Disabled', [], ['context' => 'Plural']),
      '#suffix' => '</h2>',
    ];

    foreach (['enabled', 'disabled'] as $status) {
      $build[$status]['#type'] = 'container';
      $build[$status]['#attributes'] = [
        'class' => [
          'automation-list-section',
          $status,
        ],
      ];
      $build[$status]['table'] = [
        '#type' => 'table',
        '#header' => $this->buildHeader(),
        '#attributes' => ['class' => ['automation-listing-table', $status]],
        '#cache' => [
          'contexts' => $this->entityType->getListCacheContexts(),
          'tags' => $this->entityType->getListCacheTags(),
        ],
      ];
      foreach ($entities[$status] as $entity) {
        $build[$status]['table']['#rows'][$entity->id()] = $this->buildRow($entity);
      }
    }
    $build['enabled']['table']['#empty'] = $this->t('There are no enabled @label.', [
      '@label' => $this->entityType->getPluralLabel(),
    ]);
    $build['disabled']['table']['#empty'] = $this->t('There are no disabled @label.', [
      '@label' => $this->entityType->getPluralLabel(),
    ]);

    return $build;
  }

}
