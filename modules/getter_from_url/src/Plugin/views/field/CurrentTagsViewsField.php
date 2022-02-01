<?php

namespace Drupal\getter_from_url\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("current_tags_views_field")
 */
class CurrentTagsViewsField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $current_uri   = \Drupal::request()->getRequestUri();
    $path_parsed   = parse_url($current_uri);
    $parameters    = explode('&', $path_parsed['query']);
    $prm_to_return = null;

    foreach($parameters as $parameter) {
      $prms_exploded = explode('=', $parameter);

      if($prms_exploded[0] === 'tags') {
        $prm_to_return = '{'.$prms_exploded[1].'}';
      }
    }

    return $prm_to_return;
  }

}
