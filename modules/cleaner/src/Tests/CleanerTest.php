<?php
/**
 * @file
 * Class CleanerTest file definition.
 */

namespace Drupal\cleaner\Tests;

use Drupal\simpletest\WebTestBase;
use Psr\Log\LogLevel;

/**
 * Class CleanerTest.
 *
 * @package Drupal\cleaner\Tests
 *
 * @group cleaner
 */
class CleanerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['cleaner', 'dblog', 'syslog', 'system'];
  /**
   * Cleaner configs array with the default values.
   *
   * @type array
   */
  private static $cleanerConfigs = [
    'cleaner_cron'              => 0,
    'cleaner_clear_cache'       => 0,
    'cleaner_additional_tables' => '',
    'cleaner_empty_watchdog'    => 0,
    'cleaner_clean_sessions'    => 0,
    'cleaner_optimize_db'       => 0,
  ];

  /**
   * Test Cleaner config page.
   */
  public function testCleanerConfigPage() {
    $this->drupalLogin($this->drupalCreateUser([], NULL, TRUE));
    $this->drupalGet('admin/config/system/cleaner');
    $this->assertResponse(200);
    // Ensure all fields exists and has a default values.
    foreach (self::$cleanerConfigs as $name => $value) {
      $message = "$name setting exist and has a correct initial value";
      $this->assertFieldByName($name, $value, $message);
    }
  }

  /**
   * Test Cleaner clearing caches.
   */
  public function testCleanerClearCache() {
    $this->setConfig('cleaner_clear_cache', 1);
    $cid = 'cleaner_test_cache';
    \Drupal::cache()->set($cid, $this->randomString());
    self::cleanerExecute();
    $this->assertFalse(\Drupal::cache()->get($cid), 'Dummy cache has been cleared');
  }

  /**
   * Test Cleaner clearing watchdog logs.
   */
  public function testCleanerClearWatchdog() {
    $this->setConfig('cleaner_empty_watchdog', 1);
    for ($i = 0; $i <= 10; $i++) {
      \Drupal::service('cleaner_logger')->log(LogLevel::INFO, $this->randomString());
    }
    $this->assertTrue(self::getWatchdogLogsCount() >= 10, 'Logger sucessfully generated logs');
    self::cleanerExecute();
    $this->assertTrue(self::getWatchdogLogsCount() <= 1, 'Logs has been sucessfully wiped');
  }

  /**
   * Execute the Cleaner.
   */
  private static function cleanerExecute() {
    \Drupal::moduleHandler()->invokeAll('cleaner_run');
  }

  /**
   * Get Watchdog logs count.
   *
   * @return int
   *   Logs count.
   */
  private static function getWatchdogLogsCount() {
    $logs = \Drupal::database()
      ->select('watchdog', 'w')
      ->fields('w', ['wid'])
      ->execute()->fetchCol();
    return count($logs);
  }

  /**
   * Set cleaner's config.
   *
   * @param string $name
   *   Name of the config.
   * @param string|int $value
   *   Config value.
   */
  private static function setConfig($name, $value) {
    \Drupal::configFactory()
      ->getEditable(CLEANER_SETTINGS)
      ->set($name, $value)
      ->save();
  }

}
