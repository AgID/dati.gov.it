<?php
/**
 * @file
 * Class CleanerCacheController file definition.
 */

namespace Drupal\cleaner\Controller;

use Drupal\Component\Utility\Xss;
use Psr\Log\LogLevel;

/**
 * Class CleanerCacheController.
 *
 * @package Drupal\cleaner\Controller
 */
class CleanerCacheController implements CleanerControllersInterface {
  /**
   * Configuration name.
   *
   * @var string
   */
  public static $configName = 'cleaner_clear_cache';
  /**
   * Additional config name.
   *
   * @var string
   */
  public static $additionalConfigName = 'cleaner_additional_tables';
  /**
   * Log level.
   *
   * @type string
   */
  protected static $logLevel;
  /**
   * Log message.
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
   * {@inheritdoc}
   */
  public function execute() {
    $this->clearTables();
  }

  /**
   * Clear cache tables.
   */
  protected function clearTables() {
    if (\Drupal::config(CLEANER_SETTINGS)->get(self::$configName)) {
      $cleared = 0;
      $this->connection = \Drupal::database();
      // Prepare cache tables list.
      $tables = (array) $this->cleanerGetCacheTables();
      // Ensure tables exists.
      if (!empty($tables)) {
        // Clear the tables and increments the cleared entries count.
        $cleared += (int) $this->performClearing($tables);
      }
      // Log the operation.
      self::logResults($cleared);
    }
  }

  /**
   * Write log about a clearing results.
   *
   * @param int|null $cleared
   *   Count of cleared tables.
   */
  protected static function logResults($cleared = NULL) {
    if ($cleared <= 0 || empty($cleared)) {
      static::$logLevel   = LogLevel::ERROR;
      static::$logMessage = 'There are no selected tables in the database.';
    }
    else {
      static::$logLevel   = LogLevel::INFO;
      static::$logMessage = 'Cleared tables by Cleaner.';
    }
    \Drupal::service('cleaner_logger')
      ->log(static::$logLevel, static::$logMessage);
  }

  /**
   * Perform caches clearing work.
   *
   * @param array $tables
   *   Table names array.
   *
   * @return int
   *   Count of cleared tables.
   */
  protected function performClearing(array $tables) {
    $cleared = 0;
    // Additionally clearing caches for the static caches.
    static::cleanerClearStaticCaches();
    foreach ($tables as $table) {
      if (!$this->connection->schema()->tableExists($table)) {
        continue;
      }
      if ($this->connection->query("TRUNCATE $table")->execute()) {
        $cleared++;
      }
    }
    return $cleared;
  }

  /**
   * Clear Drupal statically cached data.
   */
  protected static function cleanerClearStaticCaches() {
    \Drupal::cache()->deleteAll();
  }

  /**
   * Helper function for gathering all names of cache tables in DB.
   *
   * @return array
   *   List of all cache tables names.
   */
  protected function cleanerGetCacheTables() {
    $tables = [];
    $database_name = $this->getDatabaseName();
    if (!empty($database_name)) {
      $query = $this->connection
        ->select('INFORMATION_SCHEMA.TABLES', 'tables')
        ->fields('tables', ['table_name', 'table_schema'])
        ->condition('table_schema', $database_name)
        ->condition('table_name', 'cache_%', 'LIKE')
        // Exclude cachetags table.
        ->condition('table_name', 'cachetags', '<>');
      $tables = $query->execute()->fetchCol();
    }
    return array_merge((array) $tables, (array) $this->getAdditionalTables());
  }

  /**
   * Get an additional tables for clearing.
   *
   * @return array
   *   Additional tables array.
   */
  protected function getAdditionalTables() {
    $tables = [];
    $additional = \Drupal::config(CLEANER_SETTINGS)->get(self::$additionalConfigName);
    foreach (self::explode($additional) as $table) {
      if ($this->connection->schema()->tableExists($table)) {
        $tables[] = $table;
      }
    }
    return $tables;
  }

  /**
   * Explode the string into the array.
   *
   * @param string $string
   *   String to be exploded.
   *
   * @return array
   *   Exploded string in array format.
   */
  private static function explode($string = '') {
    return (is_string($string) && !empty($string))
      ? explode(',', self::sanitize($string))
      : [];
  }

  /**
   * Sanitize the string.
   *
   * @param string $input
   *   Input to be sanitized.
   *
   * @return string|null
   *   Sanitized string.
   */
  private static function sanitize($input = '') {
    return !empty($input) && is_string($input)
      ? Xss::filter(trim($input))
      : NULL;
  }

  /**
   * Get the current database name.
   *
   * @return null|string
   *   Current database name.
   */
  private function getDatabaseName() {
    $options = $this->connection->getConnectionOptions();
    return isset($options['database']) ? $options['database'] : NULL;
  }

}
