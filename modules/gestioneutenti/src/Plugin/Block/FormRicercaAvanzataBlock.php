<?php

namespace Drupal\gestioneutenti\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\gestioneutenti\Form\RicercaAvanzataForm;


/**
 * Provides a 'article' block.
 *
 * @Block(
 *   id = "form_ricerca_avanzata_block",
 *   admin_label = @Translation("Form ricerca avanzata per modal"),
 * )
 */

class FormRicercaAvanzataBlock extends BlockBase {
  /**
   * @inheritDoc
   */
  public function build() {
    return \Drupal::formBuilder()->getForm(RicercaAvanzataForm::class);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}