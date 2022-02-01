<?php
namespace Drupal\gestioneutenti\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
 
/**
 * Provides the form for filter Students.
 */
class UtentifilterForm extends FormBase {
 
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'utenti_filter_form';
  }
 
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['filters'] = [
        '#title' => t('Parametri ricerca'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];
 
    $form['filters']['nome'] = [
        '#title'         => 'Nome',
        '#type'          => 'search',
				'#attributes' => array(
					'style'=>'margin-top:3%'
				),   
		
    ];
		$form['filters']['cognome'] = [
        '#title'         => 'Cognome',
        '#type'          => 'search'
    ];
    $form['filters']['statoUtente'] = array(
        //'#title' => t('Seleziona stato'),
        '#type' => 'select',
        '#default_value' => $item["status"],
        '#options' => array(t('Seleziona stato'), t('Solo attivi'),t('Solo non attivi'), t('Entrambe')),
      );

    $form['filters']['actions'] = [
        '#type'       => 'actions'
    ];
 
    $form['filters']['actions']['submit'] = [
        '#type'  => 'submit',
        '#value' => $this->t('Filter')
		
    ];
   
    return $form;
 
  }
 
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
		$field = $form_state->getValues();
	  $nome = $field["nome"];
	  $cognome = $field["cognome"];
		$attivo=$form_state->getValue('stato');
		$stato=$form_state->getValue('statoUtente');
		$value = array_filter($attivo);
		$url = \Drupal\Core\Url::fromRoute('gestioneutenti.mostra_utenti')
          ->setRouteParameters(array('nome'=>$nome,'cognome'=>$cognome,'stato'=>$stato));
    $form_state->setRedirectUrl($url);
  }
 
}
