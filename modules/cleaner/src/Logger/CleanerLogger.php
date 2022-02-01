<?php
/**
 * @file
 * Contains Drupal\cleaner\Logger\CleanerLogger.
 */

namespace Drupal\cleaner\Logger;

use Psr\Log\LogLevel;

/**
 * Class CleanerLogger.
 *
 * @package Drupal\cleaner\Logger
 */
class CleanerLogger {
  /**
   * Log levels array.
   *
   * @var array
   */
  private static $levels = [
    LogLevel::EMERGENCY,
    LogLevel::ALERT,
    LogLevel::CRITICAL,
    LogLevel::ERROR,
    LogLevel::WARNING,
    LogLevel::NOTICE,
    LogLevel::INFO,
    LogLevel::DEBUG,
  ];

  /**
   * Write a log into the database.
   *
   * @param string $level
   *   Log level.
   * @param string $message
   *   Log message text.
   */
  public static function log($level, $message) {
    if (!empty($message) && static::isAvailable($level)) {
      \Drupal::logger('cleaner')->log($level, $message);
    }
  }

  /**
   * Check if the specified log level is available.
   *
   * @param string|null $level
   *   Log level.
   *
   * @return bool
   *   Checking result.
   */
  private static function isAvailable($level = NULL) {
    return (bool) (!empty($level) && in_array($level, static::$levels));
  }

}
