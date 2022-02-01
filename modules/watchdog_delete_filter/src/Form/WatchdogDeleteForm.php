<?php

namespace Drupal\watchdog_delete_filter\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Logger\RfcLogLevel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form before clearing out the logs.
 *
 * @internal
 */
class WatchdogDeleteForm extends ConfirmFormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new DblogClearLogConfirmForm.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'watchdog_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the selected logs?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('dblog.overview');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $types = $this->connection->query('SELECT DISTINCT(type) FROM {watchdog}')->fetchCol('type');

    foreach ($types as $type) {
      $options[$type] = t($type);
    }

    $form['filters'] = [
      '#type' => 'details',
      '#title' => $this->t('Filter log messages to delete'),
      '#description' => $this->t('If filters are not used, none messages will be deleted.'),
      '#open' => TRUE,
      '#attributes' => [ 'class' => [ 'form--inline', 'clearfix' ] ], 
    ];

    $form['filters']['type'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Type'),
      '#description' => t('Select one or more types to delete.'),
      '#options' => $options,
      '#size' => 10,
      '#weight' => '0',
      '#attributes' => [ 'id' => 'select-type' ],
      ];

    $form['filters']['severity'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Severity'),
      '#description' => t('Select one or more severities to delete.'),
      '#options' => RfcLogLevel::getLevels(),
      '#size' => 8,
      '#weight' => '1',
      '#attributes' => [ 'id' => 'select-severity'],
      ];

    $form['filters']['select_all'] = [
      '#type' => 'html_tag',
      '#tag' => 'input',
      '#weight' => '2',
      '#attributes' => [
        'id' => 'select-all-button',
        'class' => 'button',
        'type' => 'button',
        'value' => $this->t('Select all'),
        ],
      '#attached' => [
        'library' => [
          'watchdog_delete_filter/watchdog_delete_filter',
          ],
        ],
      ];

    $form['filters']['reset'] = [
      '#type' => 'html_tag',
      '#tag' => 'input',
      '#weight' => '3',
      '#attributes' => [
        'class' => 'button',
        'type' => 'reset',
        'value' => $this->t('Reset'),
        ],
      ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $types = $form_state->getValue('type');
    $severities = $form_state->getValue('severity');

    if ($types) {
      $cnt_types = (int) $this->connection->select('watchdog', 'w')
        ->condition('w.type', array_values($types), 'IN')
        ->countQuery()
        ->execute()
        ->fetchField();
    }
    else {
      $cnt_types = 0;
    }

    if ($severities) {
      $cnt_severities = (int) $this->connection->select('watchdog', 'w')
        ->condition('w.severity', array_values($severities), 'IN')
        ->countQuery()
        ->execute()
        ->fetchField();
    }
    else {
      $cnt_severities = 0;
    }

    $query = $this->connection->delete('watchdog');
    if ($cnt_types > 0) {
      $query->condition('type', array_values($types), 'IN');
    }
    if ($cnt_severities > 0) {
      $query->condition('severity', array_values($severities), 'IN');
    }      
    
    $cnt_total = $cnt_types + $cnt_severities;        
    if ($cnt_total > 0) {        
      $query->execute();
      $this->messenger()->addStatus($this->t('Database log cleared (@cnt entries).', [ '@cnt' => $cnt_total]));
    }
    else {
      $this->messenger()->addStatus($this->t('No entries deleted. Please review your filters.'));
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}
