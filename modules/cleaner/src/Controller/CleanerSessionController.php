<?php
/**
 * @file
 * Class CleanerSessionController file definition.
 */

namespace Drupal\cleaner\Controller;

use Psr\Log\LogLevel;

/**
 * Class CleanerSessionController.
 *
 * @package Drupal\cleaner\Controller
 */
class CleanerSessionController implements CleanerControllersInterface {
  /**
   * Configuration name.
   *
   * @var string
   */
  public static $configName = 'cleaner_clean_sessions';

  /**
   * {@inheritdoc}
   */
  public function execute() {
    if (\Drupal::config(CLEANER_SETTINGS)->get(self::$configName)) {
      $count = \Drupal::database()
        ->delete('sessions')
        ->condition('timestamp', self::getExpirationTime(), '<')
        ->execute();
      if ($count) {
        \Drupal::service('cleaner_logger')
          ->log(LogLevel::INFO, 'Old sessions cleared.');
      }
    }
  }

  /**
   * Get the sessions expiration time.
   *
   * @return int
   *   Expiration timestamp.
   */
  private static function getExpirationTime() {
    return (int) (REQUEST_TIME - session_get_cookie_params()['lifetime']);
  }

}
