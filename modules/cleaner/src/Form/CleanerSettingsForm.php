<?php

/**
 * @file
 * Class CleanerSettingsForm file definition.
 */

namespace Drupal\cleaner\Form;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CleanerSettingsForm.
 *
 * @package Drupal\cleaner\Form
 */
class CleanerSettingsForm extends ConfigFormBase {
  /**
   * Static array with the time intervals.
   *
   * @var array
   */
  private static $intervals = [
    900    => '15 min:',
    1800   => '30 min:',
    3600   => '1 hour:',
    7200   => '2 hour:',
    14400  => '4 hours:',
    21600  => '6 hours:',
    43200  => '12 hours:',
    86400  => '1 day:',
    172800 => '2 days:',
    259200 => '3 days:',
    604800 => '1 week:',
  ];

  /**
   * FirstSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactory $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return parent::create($container);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cleaner_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['cleaner.settings'];
  }

  /**
   * Get cache tables table.
   */
  protected function getCacheTablesTable() {
    // Get all CACHE tables form database.
    $list = self::getAllCacheTables();
    if (!empty($list)) {
      // Prepare table's rows.
      $rows = self::prepareRows($list);
      // Create theme table rendered HTML.
      $table = self::themeTable($rows);
      return $this->t('The current cache tables are:') . $table;
    }
    return $this->t('There is no cache tables in the database.');
  }

  /**
   * Get list of all cache tables.
   *
   * @return mixed
   *   List of all cache tables.
   */
  private static function getAllCacheTables() {
    $query = \Drupal::database()
      ->select('INFORMATION_SCHEMA.TABLES', 'tables')
      ->fields('tables', array('table_name', 'table_schema'))
      ->condition('table_schema', \Drupal::database()->getConnectionOptions()['database'])
      ->condition('table_name', 'cache_%', 'LIKE')
      ->condition('table_name', 'cachetags', '<>');
    return $query->execute()->fetchCol();
  }

  /**
   * Prepare table rows array.
   *
   * @param array $list
   *   All cache tables form database.
   *
   * @return array
   *   Table rows array.
   */
  private static function prepareRows(array $list) {
    $table_rows = []; $cols = 4;
    $count  = count($list);
    $rows   = ceil($count / $cols);
    $list   = array_pad($list, $rows * $cols, ' ');
    for ($i = 0; $i < $count; $i += $cols) {
      $table_rows[] = array_slice($list, $i, $cols);
    }
    return $table_rows;
  }

  /**
   * Render the table.
   *
   * @param array $rows
   *   Table rows.
   *
   * @return string
   *   Rendered HTML.
   */
  private static function themeTable($rows = []) {
    return \Drupal::theme()->render('table',
      [
        'rows'       => $rows,
        'attributes' => [
          'class' => ['cleaner-cache-tables'],
        ],
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get config handler.
    $conf = $this->config(CLEANER_SETTINGS);
    // Prepare Yes/No options array.
    $yes_no = [$this->t('No:'), $this->t('Yes:')];
    // Prepare cron's intervals options array.
    $interval = array_merge([0 => $this->t('Every time:')], self::$intervals);
    // Attach the "cleaner-admin" library for some admin page styling.
    $form['cleaner']['#attached']['library'][] = 'cleaner/cleaner-admin';
    // Cron interval settings.
    $form['cleaner']['cleaner_cron'] = [
      '#type'           => 'radios',
      '#title'          => $this->t('Run interval'),
      '#options'        => $interval,
      '#default_value'  => (int) $conf->get('cleaner_cron'),
      '#description'    => $this->t('This is how often the options below will occur. The actions will occur on the next Cron run after this interval expires. "Every time" means on every Cron run.'),
    ];
    // Cache clearing settings.
    $form['cleaner']['cleaner_clear_cache'] = [
      '#type'           => 'radios',
      '#options'        => $yes_no,
      '#title'          => $this->t('Clean up cache'),
      '#default_value'  => (int) $conf->get('cleaner_clear_cache'),
      '#description'    => $this->getCacheTablesTable(),
    ];
    // Additional tables clearing settings.
    $form['cleaner']['cleaner_additional_tables'] = [
      '#type'           => 'textfield',
      '#title'          => $this->t('Additional tables to clear'),
      '#default_value'  => (string) $conf->get('cleaner_additional_tables'),
      '#description'    => $this->t('A comma separated list of table names which also needs to be cleared.'),
    ];
    // Watchdog clearing settings.
    $form['cleaner']['cleaner_empty_watchdog'] = [
      '#type'           => 'radios',
      '#options'        => $yes_no,
      '#title'          => $this->t('Clean up Watchdog'),
      '#default_value'  => (int) $conf->get('cleaner_empty_watchdog'),
      '#description'    => $this->t('There is a standard setting for controlling Watchdog contents. This is more useful for test sites.'),
    ];
    // Get cookies params array.
    $cookie = session_get_cookie_params();
    // Get current database connection.
    $connection = \Drupal::database();
    // Select old sessions from the sessions db table.
    $count = $connection
      ->query("SELECT COUNT(*) FROM sessions WHERE timestamp < @lifetime", ['@lifetime' => REQUEST_TIME - $cookie['lifetime']])
      ->fetchAll();
    // Sessions clearing settings.
    $form['cleaner']['cleaner_clean_sessions'] = [
      '#type'           => 'radios',
      '#options'        => $yes_no,
      '#title'          => $this->t('Clean up Sessions table'),
      '#default_value'  => (int) $conf->get('cleaner_clean_sessions'),
      '#description'    => $this->t('The sessions table can quickly become full with old, abandoned sessions. This will delete all sessions older than @interval (as set by your site administrator). There are currently @count such sessions.',
        ['@interval' => $cookie['lifetime'], '@count' => count($count)]),
    ];
    // We can only offer OPTIMIZE to MySQL users.
    if ($connection->driver() == 'mysql') {
      // Database(MySQL) optimizing settings.
      $form['cleaner']['cleaner_optimize_db'] = [
        '#type'           => 'radios',
        '#options'        => $yes_no + ['2' => 'Local only'],
        '#title'          => $this->t('Optimize tables with "overhead" space'),
        '#default_value'  => (int) $conf->get('cleaner_optimize_db'),
        '#description'    => $this->t('The module will compress (optimize) all database tables with unused space.<br><strong>NOTE</strong>: During an optimization, the table will locked against any other activity; on a high vloume site, this may be undesirable. "Local only" means do not replicate the optimization (if it is being done).'),
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save variables.
    \Drupal::configFactory()
      ->getEditable(CLEANER_SETTINGS)
      ->set('cleaner_cron', $form_state->getValue('cleaner_cron'))
      ->set('cleaner_clear_cache', $form_state->getValue('cleaner_clear_cache'))
      ->set('cleaner_additional_tables', $form_state->getValue('cleaner_additional_tables'))
      ->set('cleaner_empty_watchdog', $form_state->getValue('cleaner_empty_watchdog'))
      ->set('cleaner_clean_sessions', $form_state->getValue('cleaner_clean_sessions'))
      ->set('cleaner_optimize_db', $form_state->getValue('cleaner_optimize_db'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
