<?php

namespace Drupal\social_automation\Form\Expression;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Engine\ActionExpressionContainerInterface;

/**
 * Form handler for action containers.
 */
class ActionContainerForm extends ExpressionContainerFormBase {

  /**
   * The workflowevent expression object this form is for.
   *
   * @var \Drupal\social_automation\Engine\ActionExpressionContainerInterface
   */
  protected $actionSet;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ActionExpressionContainerInterface $action_set) {
    $this->actionSet = $action_set;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form['actions-table'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['edit-actions-table']],
    ];

    $form['actions-table']['actions'] = [
      '#type' => 'table',
      '#header' => [
        'element' => $this->t('Actions'),
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

    /** @var \Drupal\social_automation\Engine\ExpressionInterface $action */
    foreach ($this->actionSet as $action) {
      $uuid = $action->getUuid();
      $configuration = $action->getConfiguration();
      $description = $this->getParameterDescription($configuration);
      $form['actions-table']['actions'][$uuid] = [
        'element' => [
          'data' => [
            '#type' => 'item',
            '#plain_text' => $action->getLabel(),
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
          '#default_value' => $action->getWeight(),
        ],
        '#attributes' => ['class' => ['draggable']],
        '#weight' => $action->getWeight(),
      ];
    }

    // Put action buttons in the table footer.
    $links['add-action'] = [
      '#theme' => 'menu_local_action',
      '#link' => [
        'title' => $this->t('Add action'),
        'url' => $this->getAutomationUiHandler()->getUrlFromRoute('expression.add', [
          'expression_id' => 'automation_action',
        ]),
      ],
    ];

    $form['actions-table']['actions']['#footer'][] = [
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
    $values = $form_state->getValue('actions', []);
    if (empty($values)) {
      // Core FormState::getValue() doesn't return the default parameter []
      // when there are no values?
      return;
    }
    $component = $this->getAutomationUiHandler()->getComponent();
    /** @var \Drupal\social_automation\Plugin\AutomationExpression\AutomationExpression $automation_expression */
    $automation_expression = $component->getExpression();

    foreach ($values as $uuid => $expression) {
      $action = $automation_expression->getExpression($uuid);
      $action->setWeight($expression['weight']);
      $action->setConfiguration($action->getConfiguration());
    }

    $this->getAutomationUiHandler()->updateComponent($component);
  }

}
