<?php
/**
 * @file
 * Class Cleaner.
 */

namespace Drupal\cleaner;

/**
 * Class Cleaner.
 *
 * @package Drupal\cleaner
 */
class Cleaner {
  /**
   * Available services array.
   *
   * @var array
   */
  protected static $services = [
    'cleaner_tables',
    'cleaner_mysql',
    'cleaner_session',
    'cleaner_watchdog',
  ];

  /**
   * Run Cleaner service.
   */
  public static function run() {
    $services = (array) self::$services;
    if (!empty($services)) {
      foreach ($services as $service) {
        if (\Drupal::hasService($service)) {
          \Drupal::service($service)->execute();
        }
      }
    }
  }

}
