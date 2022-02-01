<?php
/**
 * @file
 * Class CleanerMySqlOptimizationController file definition.
 */

namespace Drupal\cleaner\Controller;

use Drupal\Component\Utility\Timer;
use Psr\Log\LogLevel;

/**
 * Class CleanerMySqlOptimizationController.
 *
 * @package Drupal\cleaner\Controller
 */
class CleanerMySqlOptimizationController implements CleanerControllersInterface {
  /**
   * Configuration name.
   *
   * @var string
   */
  public static $configName = 'cleaner_optimize_db';
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
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Perform the operation.
   */
  public function execute() {
    $this->mysqlOptimize();
  }

  /**
   * MySQL optimizer.
   */
  protected function mysqlOptimize() {
    $opt = \Drupal::config(CLEANER_SETTINGS)->get(self::$configName);
    if ($opt) {
      // Set's the default log level - info.
      static::$logLevel = LogLevel::INFO;
      // Get database connection.
      $this->connection = \Drupal::database();
      // Get's the database driver name.
      $db_type = $this->connection->driver();
      // Make sure the db type hasn't changed.
      if ($db_type == 'mysql') {
        // Gathering tables list.
        $list = $this->buildTablesList();
        if (!empty($list)) {
          // Run optimization timer.
          Timer::start('cleaner_db_optimization');
          // Perform optimization.
          $this->optimizeIt(static::getOptimizationQuery($opt, $list));
          // Write a log about successful optimization into the watchdog.
          // Convert tables list into a comma-separated list.
          $list = implode(', ', $list);
          // Get the timers's results.
          $time = static::getTimerResult();
          // Create a log message.
          static::$logMessage = "Optimized tables: $list. This required $time seconds.";
        }
        else {
          // Write a log about thing that optimization process is
          // no tables which can to be optimized.
          static::$logMessage = 'There is no tables which can to be optimized.';
        }
      }
      else {
        // Write a log(error) about thing that optimization process
        // isn't allowed for non-MySQL databases into the watchdog.
        // Change log level to an error.
        static::$logLevel   = LogLevel::ERROR;
        static::$logMessage = "Database type ($db_type) not allowed to be optimized.";
      }
      // Write some logs.
      \Drupal::service('cleaner_logger')
        ->log(static::$logLevel, static::$logMessage);
    }
  }

  /**
   * Extract and format the timer results.
   *
   * @return string
   *   Formatted timer results.
   */
  private static function getTimerResult() {
    // Get raw timer's data in milliseconds.
    $raw = Timer::read('cleaner_db_optimization');
    // Convert it to seconds.
    $raw /= 1000;
    // Convert it to the correct number format.
    return number_format($raw, 3);
  }

  /**
   * Perform the optimization query.
   *
   * @param string $query
   *   Query string.
   */
  protected function optimizeIt($query) {
    $this->connection->query((string) $query)->execute();
  }

  /**
   * Build the optimization query string.
   *
   * @param int $opt
   *   Operation flag.
   * @param array $list
   *   Tables list array.
   *
   * @return string
   *   Optimization query string.
   */
  protected static function getOptimizationQuery($opt, $list) {
    $query = 'OPTIMIZE ' . ($opt == 2 ? 'LOCAL ' : '');
    $query .= 'TABLE {' . (implode('}, {', $list)) . '}';
    return $query;
  }

  /**
   * Build the tables list.
   *
   * @return array Tables list array.
   *   Tables list array.
   */
  protected function buildTablesList() {
    $list = [];
    $tables = (array) $this->connection->query("SHOW TABLE STATUS");
    if (!empty($tables)) {
      foreach ($tables as $table) {
        if (isset($table->Data_free) && !empty($table->Data_free)) {
          $list[] = (string) $table->Name;
        }
      }
    }
    return $list;
  }

}
