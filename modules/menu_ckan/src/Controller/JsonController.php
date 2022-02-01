<?php

namespace Drupal\menu_ckan\Controller; 

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File;

class JsonController extends ControllerBase {

  private $filesPath; 

  public function __construct() {
    $this->filesPath = 'public://menu_ckan_json_files/';
  }

  public function checkJsonFile($jsonFilename = null) {
    $fileCompletePath = $this->filesPath . $jsonFilename; 
    $fileExistence    = false; 

    if(file_exists($fileCompletePath)) {
      $fileExistence = true;
    }

    return $fileExistence; 
  }

  public function writeJsonFile($jsonFilename = null, $jsonContent = null) {
    $fileCompletePath = $this->filesPath . $jsonFilename;
    \Drupal::service('file_system')->saveData($jsonContent, $fileCompletePath, FILE_EXISTS_REPLACE);
  }

  public function getJsonFileContent($jsonFilename = null) {
    $fileCompletePath = $this->filesPath . $jsonFilename;
    $realPath         = \Drupal::service('file_system')->realpath($fileCompletePath);
    
    return file_get_contents($realPath);
  }

  public function getJsonPath($jsonFilename = null) {
    return \Drupal::service('file_system')->realpath($this->filesPath . $jsonFilename);
  }

  public function deleteJsonFiles() {
    \Drupal::service('file_system')->deleteRecursive($this->filesPath);
    mkdir($this->filesPath);
  }

}
