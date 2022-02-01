<?php


namespace Drupal\gestioneutenti\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class GestioneutentiConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'gestioneutenti.settings';


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'gestioneutenti_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['group1'] = array(
      '#title' => t('DATI.GOV.IT'),
      '#type' => 'details',
      '#open' => FALSE,
    );

    $form['group1']['api_key_dati'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('ADMIN API KEY DATI.GOV.IT'),
      '#default_value' => $config->get('api_key_dati'),
    ];

    $form['group1']['catalogo_titolo'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Titolo Catalogo'),
      '#default_value' => $config->get('catalogo_titolo'),
    ];

    $form['group1']['catalogo_descrizione'] = [
      '#rows' => 5,
      '#cols' => 60,
      '#resizable' => TRUE,
      '#required' => TRUE,
      '#type' => 'textarea',
      '#title' => $this->t('Descrizione Catalogo'),
      '#default_value' => $config->get('catalogo_descrizione'),
    ];

    $form['group1']['catalogo_language'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Linguaggio Catalogo'),
      '#default_value' => $config->get('catalogo_language'),
    ];

    $form['group1']['catalogo_homepage'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Homepage Catalogo'),
      '#default_value' => $config->get('catalogo_homepage'),
    ];

    $form['group1']['catalogo_modified'] = [
      '#type' => 'date',
      '#date_date_format' => 'Y/m/d',
      '#title' => $this->t('Ultima modifica catalogo gg/mm/aaaa'),
      '#default_value' => $config->get('catalogo_modified'),
    ];

    $form['group1']['catalogo_publisher_url'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('URL Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_url'),
    ];

    $form['group1']['catalogo_publisher_email'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => $this->t('Email Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_email'),
    ];

    $form['group1']['catalogo_publisher_type'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Genere Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_type'),
    ];

    $form['group1']['catalogo_publisher_uri'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('URI Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_uri'),
    ];

    $form['group1']['catalogo_publisher_name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Nome Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_name'),
    ];

    $form['group2'] = array(
      '#title' => t('BASI.GOV.IT'),
      '#type' => 'details',
      '#open' => FALSE,
    );

    $form['group2']['catalogo_titolo_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Titolo Catalogo'),
      '#default_value' => $config->get('catalogo_titolo_basi'),
    ];

    $form['group2']['api_key_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('ADMIN API KEY BASI.GOV.IT'),
      '#default_value' => $config->get('api_key_basi'),
    ];

    $form['group2']['catalogo_descrizione_basi'] = [
      '#rows' => 5,
      '#cols' => 60,
      '#resizable' => TRUE,
      '#required' => TRUE,
      '#type' => 'textarea',
      '#title' => $this->t('Descrizione Catalogo'),
      '#default_value' => $config->get('catalogo_descrizione_basi'),
    ];

    $form['group2']['catalogo_language_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Linguaggio Catalogo'),
      '#default_value' => $config->get('catalogo_language_basi'),
    ];

    $form['group2']['catalogo_homepage_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Homepage Catalogo'),
      '#default_value' => $config->get('catalogo_homepage_basi'),
    ];

    $form['group2']['catalogo_modified_basi'] = [
      '#type' => 'date',
      '#date_date_format' => 'Y/m/d',
      '#title' => $this->t('Ultima modifica catalogo gg/mm/aaaa'),
      '#default_value' => $config->get('catalogo_modified_basi'),
    ];

    $form['group2']['catalogo_publisher_url_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('URL Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_url_basi'),
    ];

    $form['group2']['catalogo_publisher_email_basi'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => $this->t('Email Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_email_basi'),
    ];

    $form['group2']['catalogo_publisher_type_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Genere Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_type_basi'),
    ];

    $form['group2']['catalogo_publisher_uri_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('URI Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_uri_basi'),
    ];

    $form['group2']['catalogo_publisher_name_basi'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Nome Catalogo Editore'),
      '#default_value' => $config->get('catalogo_publisher_name_basi'),
    ];

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('api_key_dati', $form_state->getValue('api_key_dati'))
      ->set('api_key_basi', $form_state->getValue('api_key_basi'))
      ->set('catalogo_titolo', $form_state->getValue('catalogo_titolo'))
      ->set('catalogo_descrizione', $form_state->getValue('catalogo_descrizione'))
      ->set('catalogo_language', $form_state->getValue('catalogo_language'))
      ->set('catalogo_homepage', $form_state->getValue('catalogo_homepage'))
      ->set('catalogo_modified', $form_state->getValue('catalogo_modified'))
      ->set('catalogo_publisher_url', $form_state->getValue('catalogo_publisher_url'))
      ->set('catalogo_publisher_email', $form_state->getValue('catalogo_publisher_email'))
      ->set('catalogo_publisher_type', $form_state->getValue('catalogo_publisher_type'))
      ->set('catalogo_publisher_uri', $form_state->getValue('catalogo_publisher_uri'))
      ->set('catalogo_publisher_name', $form_state->getValue('catalogo_publisher_name'))
      ->set('catalogo_titolo_basi', $form_state->getValue('catalogo_titolo_basi'))
      ->set('catalogo_descrizione_basi', $form_state->getValue('catalogo_descrizione_basi'))
      ->set('catalogo_language_basi', $form_state->getValue('catalogo_language_basi'))
      ->set('catalogo_homepage_basi', $form_state->getValue('catalogo_homepage_basi'))
      ->set('catalogo_modified_basi', $form_state->getValue('catalogo_modified_basi'))
      ->set('catalogo_publisher_url_basi', $form_state->getValue('catalogo_publisher_url_basi'))
      ->set('catalogo_publisher_email_basi', $form_state->getValue('catalogo_publisher_email_basi'))
      ->set('catalogo_publisher_type_basi', $form_state->getValue('catalogo_publisher_type_basi'))
      ->set('catalogo_publisher_uri_basi', $form_state->getValue('catalogo_publisher_uri_basi'))
      ->set('catalogo_publisher_name_basi', $form_state->getValue('catalogo_publisher_name_basi'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}