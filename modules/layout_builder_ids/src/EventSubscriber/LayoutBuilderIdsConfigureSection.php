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
 * Class LayoutBuilderIdsConfigureSection.
 */
class LayoutBuilderIdsConfigureSection implements EventSubscriberInterface {

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
    if ($form['#form_id'] == 'layout_builder_configure_section') {

      // Get the config for the section.
      $config = $event->getFormState()->getFormObject()->getLayout()->getConfiguration();

      // Add the section id to the configure form.
      $form['layout_settings']['layout_builder_id'] = [
        '#type' => 'textfield',
        '#title' => 'Section ID',
        '#weight' => 0,
        '#default_value' => $config['layout_builder_id'] ?: NULL,
        '#description' => t('Enter an ID for the section.  IDs can contain letters, numbers, underscore, hyphen and period characters, and should start with a letter.'),
      ];

      // Add our custom submit handler.
      array_unshift($form['#submit'], [$this, 'LayoutBuilderIdsSubmitForm']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function LayoutBuilderIdsSubmitForm(array &$form, FormStateInterface $form_state) {

    // Get the layout builder id from the form.
    $layout_builder_id = $form_state->getValue(['layout_settings', 'layout_builder_id'], NULL);

    // If there is a layout builder id, store it.
    if ($layout_builder_id !== NULL) {

      // Get the layout.
      $layout = $this->getLayout($form_state);

      // Load in the config for this section.
      $configuration = $layout->getConfiguration();

      // Set the layout builder id in config variable.
      $configuration['layout_builder_id'] = Html::getId($layout_builder_id);

      // Set the config for this section.
      $layout->setConfiguration($configuration);
    }

  }

  /**
   * Get the layout object
   * @param FormStateInterface $form_state
   * @return mixed
   */
  private function getLayout(FormStateInterface $form_state) {

    // Get the form object.
    $formObject = $form_state->getFormObject();

    return $formObject->getLayout();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      HookEventDispatcherInterface::FORM_ALTER => 'alterForm',
    ];
  }
}
