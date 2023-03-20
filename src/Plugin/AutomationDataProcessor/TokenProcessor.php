<?php

namespace Drupal\social_automation\Plugin\AutomationDataProcessor;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\social_automation\Context\DataProcessorInterface;
use Drupal\social_automation\Context\ExecutionStateInterface;
use Drupal\typed_data\PlaceholderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A data processor for placeholder token replacements.
 *
 * @AutomationDataProcessor(
 *   id = "social_automation_tokens",
 *   label = @Translation("Placeholder token replacements")
 * )
 */
class TokenProcessor extends PluginBase implements DataProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * The placeholder resolver.
   *
   * @var \Drupal\typed_data\PlaceholderResolverInterface
   */
  protected $placeholderResolver;

  /**
   * Constructs a TokenProcessor object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\typed_data\PlaceholderResolverInterface $placeholder_resolver
   *   The placeholder resolver.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PlaceholderResolverInterface $placeholder_resolver) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->placeholderResolver = $placeholder_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('typed_data.placeholder_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($value, ExecutionStateInterface $social_automation_state) {
    $data = [];
    $placeholders_by_data = $this->placeholderResolver->scan($value);
    foreach ($placeholders_by_data as $variable_name => $placeholders) {
      // Note that accessing an unavailable variable will throw an evaluation
      // exception. That's exactly what needs to happen. Invalid tokens must
      // be detected when checking integrity. The Workflow event must
      // not be executed if the integrity check fails.
      // Runtime is too late to handle invalid tokens gracefully.
      $data[$variable_name] = $social_automation_state->getVariable($variable_name);
    }
    return $this->placeholderResolver->replacePlaceHolders($value, $data);
  }

}
