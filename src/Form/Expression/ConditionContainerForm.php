<?php

namespace Drupal\social_automation\Form\Expression;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Engine\ConditionExpressionContainerInterface;

/**
 * Form view structure for Automation condition containers.
 */
class ConditionContainerForm extends ExpressionContainerFormBase {

  /**
   * The workflowevent expression object this form is for.
   *
   * @var \Drupal\social_automation\Engine\ConditionExpressionContainerInterface
   */
  protected $conditionContainer;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ConditionExpressionContainerInterface $condition_container) {
    $this->conditionContainer = $condition_container;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form['conditions-table'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['edit-conditions-table']],
    ];

    $form['conditions-table']['conditions'] = [
      '#type' => 'table',
      '#header' => [
        'element' => $this->t('Conditions'),
        'operations' => $this->t('Operations'),
        'weight' => [
          'data' => $this->t('List position'),
          'class' => ['tabledrag-hide'],
        ],
      ],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-sort-weight',
        ],
      ],
      '#empty' => $this->t('None'),
    ];

    /** @var \Drupal\social_automation\Engine\ExpressionInterface $condition */
    foreach ($this->conditionContainer as $condition) {
      $uuid = $condition->getUuid();
      $configuration = $condition->getConfiguration();
      $description = $this->getParameterDescription($configuration);
      $form['conditions-table']['conditions'][$uuid] = [
        'element' => [
          'data' => [
            '#type' => 'item',
            '#plain_text' => $condition->getLabel(),
            '#suffix' => '<div class="description">' . $description . '</div>',
          ],
          // So that the full parameter description will show on hover.
          '#wrapper_attributes' => ['title' => [$description]],
        ],
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => $this->t('Edit'),
                'url' => $this->getAutomationUiHandler()->getUrlFromRoute('expression.edit', [
                  'uuid' => $uuid,
                ]),
              ],
              'delete' => [
                'title' => $this->t('Delete'),
                'url' => $this->getAutomationUiHandler()->getUrlFromRoute('expression.delete', [
                  'uuid' => $uuid,
                ]),
              ],
            ],
          ],
        ],
        'weight' => [
          '#type' => 'weight',
          '#delta' => 50,
          '#attributes' => ['class' => ['table-sort-weight']],
          '#default_value' => $condition->getWeight(),
        ],
        '#attributes' => ['class' => ['draggable']],
        '#weight' => $condition->getWeight(),
      ];
    }

    // Put action buttons in the table footer.
    $links['add-condition'] = [
      '#theme' => 'menu_local_action',
      '#link' => [
        'title' => $this->t('Add condition'),
        'url' => $this->getAutomationUiHandler()->getUrlFromRoute('expression.add', [
          'expression_id' => 'social_automation_condition',
        ]),
      ],
    ];

    $form['conditions-table']['conditions']['#footer'][] = [
      [
        'data' => [
          '#prefix' => '<ul class="action-links">',
          'local-action-links' => $links,
          '#suffix' => '</ul>',
        ],
        'colspan' => 3,
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('conditions', []);
    if (empty($values)) {
      // Core FormState::getValue() doesn't return the default parameter []
      // when there are no values?
      return;
    }
    $component = $this->getAutomationUiHandler()->getComponent();
    /** @var \Drupal\social_automation\Plugin\AutomationExpression\AutomationExpression $automation_expression */
    $automation_expression = $component->getExpression();

    foreach ($values as $uuid => $expression) {
      $condition = $automation_expression->getExpression($uuid);
      $condition->setWeight($expression['weight']);
      $condition->setConfiguration($condition->getConfiguration());
    }

    $this->getAutomationUiHandler()->updateComponent($component);
  }

}
