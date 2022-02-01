<?php

namespace Drupal\menu_ckan\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class JsonForm extends ConfigFormBase {

  private $jsonConfig; 

  public function getFormId() {
    return 'json_controller_id';
  }

  protected function getEditableConfigNames() {
    return [
      'json_controller.json_files',
    ];
  }

  public function __construct() {
    $this->jsonConfig = $this->config('json_controller.json_files');
  }

  public function checkJsonConfiguration($jsonType = null) {
    $configJsonType = $this->jsonConfig->get('json_controller.json_type_' . $jsonType); 
    return (isset($configJsonType) ? true : false);
  }

  public function setJsonConfiguration($jsonType = null, $jsonContent = null) {
    $this->jsonConfig->set('json_controller.json_type_' . $jsonType, $jsonContent);
    $this->jsonConfig->save();
  }

  public function getJsonConfiguration($jsonType = null) {
    return $this->jsonConfig->get('json_controller.json_type_' . $jsonType);
  }

  public function deleteJsonConfiguration() {
    \Drupal::configFactory()
      ->getEditable('json_controller.json_files')
      ->delete();
  }

}
