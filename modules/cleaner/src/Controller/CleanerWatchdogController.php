<?php
/**
 * @file
 * Class CleanerWatchdogController file definition.
 */

namespace Drupal\cleaner\Controller;

use Psr\Log\LogLevel;

/**
 * Class CleanerWatchdogController.
 *
 * @package Drupal\cleaner\Controller
 */
class CleanerWatchdogController implements CleanerControllersInterface {
  /**
   * Configuration name.
   *
   * @var string
   */
  public static $configName = 'cleaner_empty_watchdog';
  /**
   * Watchdog log level.
   *
   * @type string
   */
  protected static $logLevel;
  /**
   * Watchdog log message.
   *
   * @type string
   */
  protected static $logMessage;

  /**
   * {@inheritdoc}
   */
  public function execute() {
    if (\Drupal::config(CLEANER_SETTINGS)->get(self::$configName)) {
      if (self::cleanWatchdog()) {
        static::$logLevel   = LogLevel::INFO;
        static::$logMessage = 'Watchdog logs has been successfully cleared.';
      }
      else {
        static::$logLevel   = LogLevel::ERROR;
        static::$logMessage = 'Something going wrong - watchdog logs can\'t be cleared.';
      }
      \Drupal::service('cleaner_logger')
        ->log(static::$logLevel, static::$logMessage);
    }
  }

  /**
   * Perform clearing database table - "watchdog".
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  private static function cleanWatchdog() {
    return (bool) \Drupal::database()->query('TRUNCATE {watchdog}')->execute();
  }

}
