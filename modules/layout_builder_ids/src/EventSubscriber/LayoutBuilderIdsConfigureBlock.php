<?php

namespace Drupal\layout_builder_ids\EventSubscriber;

use Drupal\Core\Form\FormStateInterface;
use Drupal\core_event_dispatcher\Event\Form\FormAlterEvent;
use Drupal\core_event_dispatcher\Event\Form\FormBaseAlterEvent;
use Drupal\core_event_dispatcher\Event\Form\FormIdAlterEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Component\Utility\Html;

/**
 * Class LayoutBuilderIdsConfigureBlock.
 */
class LayoutBuilderIdsConfigureBlock implements EventSubscriberInterface {

  /**
   * Alter form.
   *
   * @param \Drupal\core_event_dispatcher\Event\Form\FormAlterEvent $event
   *   The event.
   */
  public function alterForm(FormAlterEvent $event): void {

    // Get the form from the event.
    $form = &$event->getForm();

    // If we are on a configure section form, alter it.
    if (in_array($form['#form_id'], ['layout_builder_add_block', 'layout_builder_update_block'], TRUE)) {

      // Pull out the layout_builder_id from config.
      $layout_builder_id = &$event->getFormState()->getFormObject()->getCurrentComponent()->get('layout_builder_id');

      // Add the section id to the configure form.
      $form['settings']['layout_builder_id'] = [
        '#type' => 'textfield',
        '#title' => 'Block ID',
        '#weight' => 0,
        '#default_value' => $layout_builder_id ?: NULL,
        '#description' => t('Enter an ID for the block. IDs can contain letters, numbers, underscore, hyphen and period characters, and should start with a letter.'),
      ];

      // Add our custom submit function.
      array_unshift($form['#submit'], [$this, 'LayoutBuilderIdsSubmitForm']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function LayoutBuilderIdsSubmitForm(array &$form, FormStateInterface $form_state) {

    // Load in the layout_builder_id.
    $layout_builder_id = $form_state->getValue(['settings', 'layout_builder_id']);

    // If there is in id, save it in config.
    if ($layout_builder_id !== NULL) {

      // Load in the component/block.
      $component = $form_state->getFormObject()->getCurrentComponent();

      // Set the layout_builder_id.
      $component->set('layout_builder_id', Html::getId($layout_builder_id));
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {

    return [
      HookEventDispatcherInterface::FORM_ALTER => 'alterForm',
    ];
  }
}
