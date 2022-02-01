<?php  
/**  
 * @file  
 * Contains Drupal\welcome\Form\MessagesForm.  
 */  
namespace Drupal\gestioneutenti\Form;  
/*
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface; 
use Drupal\Core\Database\Database; 
use Drupal\Core\Form\FormBase;

*/
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\Routing;

class GestioneUtenteForm extends FormBase {

  /**  
   * {@inheritdoc}  
   */  

  public function getFormId() {  
    return 'gestioneutenteForm_form';  
  }

	/**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  

    $vetorg=[
      '' => 'Seleziona il Catalogo',
     // '1' => 'Catalogo degli Open Data test',
      '2' => 'Catalogo delle Basi di Dati della PA',
     // '4' => 'Entrambi i cataloghi',
    ];
    $form['#prefix'] = '<div class="container"><div class="row mt-5"><div class="col 12 d-flex justify-content-center"> <h1>Registrazione Utente</h1></div></div><div id="myAlert"></div>';
    $form['#suffix'] = '</div>';

    $form['nomeCognome'] = [
      '#prefix' => '<div class="row mt-5">',
      '#suffix' => '</div>'
    ];

    $form['nomeCognome']['username'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('Username'),
			'#required' => TRUE,
			'#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];

    $form['nomeCognome']['nomeCompleto'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('Nome e Cognome'),
			'#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];

    $form['myEmail'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['myEmail']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail istituzionale'),
			'#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];

    $form['myPassword'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['myPassword']['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];

    $form['myPassword']['confermaPassword'] = [
      '#type' => 'password',
      '#title' => $this->t('Conferma password'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];


    $form['responsabile'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['responsabile']['nomeCompletoResponsabile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Referente del servizio'),
      '#required' => TRUE,

      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];

    $form['responsabile']['emailResponsabile'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail istituzionale del Referente'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];
 	
    $form['org'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['org']['organizazione'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nome amministrazione'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div id="org" class="col-12 col-lg-6 col-md-6 pl-4 pr-4 mb-0">',
      '#suffix' => '<div class="row pl-2 mb-5"><small id="formGroupExampleInputWithHelpDescriptionAmministrazione" class="form-text text-muted">* Per le amministrazioni riportare la denominazione presente su IndicePA</small></div></div>',
    ];

    $form['org']['codice'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Codice IPA/P.IVA'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div id="orgIpa" class="col-12 col-lg-6 col-md-6 pl-4 pr-4 mb-0">',
      '#suffix' => '<div class="row pl-2 mb-5"><small id="formGroupExampleInputWithHelpDescriptionCodiceIVA" class="form-text text-muted"><a href="https://indicepa.gov.it/ipa-portale/" title="indice-pa">https://indicepa.gov.it/ipa-portale/</a></small></div></div>',
    ];





//    $form['ipa'] = array(
//      '#prefix' => '<div class="row">',
//      '#suffix' => '</div>'
//    );



//    $form['ipa']['emailOrg'] = [
//      '#type' => 'email',
//      '#title' => $this->t('Email organizzazione'),
//      '#required' => TRUE,
//      '#default_value' => "",
//      '#prefix' => '<div class="col-12 pl-4 pr-4">',
//      '#suffix' => '</div>',
//    ];

//    $form['descrizione'] = [
//      '#rows' => 3,
//      '#cols' => 60,
//      '#resizable' => TRUE,
//      '#type' => 'textarea',
//      '#title' => $this->t('Descrizione organizzazione'),
//      '#default_value' => '',
//      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
//      '#suffix' => '</div></div>',
//    ];


    $form['istanza'] = [
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    ];


    $form['istanza']['istanzaCkan'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Seleziona il Catalogo'),
      '#default_value' => '2',
                        '#required' => TRUE,
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];


    /*$form['istanza']['istanzaCkan'] = array(
			'#type' => 'select',
      '#title' => $this->t('Seleziona il Catalogo'),
			'#options' => $vetorg,
			'#default_value' => 0,
      '#prefix' => '<div class="col-12 col-lg-12 col-md-12 pl-4 pr-4 mb-0">',
      '#suffix' => '<div class="row pl-2 pr-4 mt-3"><small id="formGroupExampleInputWithHelpDescription" class="form-text text-muted">* open data e/o basi di dati della PA</small></div></div>',
		);*/



    $form['myButton'] = [
      '#prefix' => '<div class="row mb-5 mt-5 d-flex justify-content-end pl-4 pr-4">',
      '#suffix' => '</div>'
    ];


		$form['myButton']['submit1'] = [
        '#type'  => 'submit',
        '#value' => $this->t('Registrati'),
				"#weight" => 1,
        '#prefix' => '<div class="col-12 col-lg-3 col-md-3">',
        '#suffix' => '</div>',
				'#attributes' => array(
					'style'=>'margin-bottom:3%;width:100%'
				),
    ];

    return $form;
  }

	public function validateForm(array &$form, FormStateInterface $form_state) {}

	/**  
   * {@inheritdoc}  
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

      $username = $form_state->getValue('username');
			$ckan = $form_state->getValue('istanzaCkan');
			$nomeCompleto = $form_state->getValue('nomeCompleto');
			$email = $form_state->getValue('email');
			$password = $form_state->getValue('password');
			$responsabile = $form_state->getValue('nomeCompletoResponsabile');
			$emailResponsabile = $form_state->getValue('emailResponsabile');
			$codice = $form_state->getValue('codice');
			$org = $form_state->getValue('organizazione');
			$emailORG = $form_state->getValue('emailOrg');
			$descrizione = $form_state->getValue('descrizione');


			$user = \Drupal\user\Entity\User::create();
        $user->setPassword($password);
        $user->enforceIsNew();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->addRole('operatore');
        $user->save();
			$idUtente = $user->id();
			$flagDati = "0";
			$flagBase = "0";

			switch ($ckan){
        case "1":
          $flagDati = "1";
          break;
        case "2":
          $flagBase = "1";
          break;
        case "4":
          $flagBase = "1";
          $flagDati = "1";
          break;
      }
      Database::getConnection()->insert('organizzazione')
        ->fields([
          'nomeOrganizzazione' => $org,
          'descrizioneOrganizzazione' => $descrizione,
          'idUtente' => $idUtente,
          'username' => $username,
          'nomeCompleto' => $nomeCompleto,
          'fid' => 0,
          'linkImmagine' => $emailORG,
          'ckankeyDatigov' => "",
          'ckankeyBasigov' => "",
          'flagDatigov' => $flagDati,
          'flagBasigov' => $flagBase,
          'email' => "", //Id organizzazione Dati
          'telefono' => "", //Id organizzazione Basi
          'url' => $emailResponsabile,
          'regione' => $responsabile,
          'codiceIPAIVA' => $codice,
          'dataCreazione' => REQUEST_TIME,
        ])->execute();

      $emailAgid="";

      $query = \Drupal::database()->select('user__roles', 'ur');
      $query->innerJoin("users_field_data","u","ur.entity_id=u.uid");
		  $query->fields('u',['uid','mail','name','status']);
			$query->fields('ur',['entity_id','roles_target_id']);
			$query->condition('ur.roles_target_id', "agid", '=');
			$result = $query->execute();
			if($result != null){
				$riga = $result->fetchAssoc();
        $emailAgid=$riga["mail"];
      }

		//invio email
    /**************/
		
		$mailManager = \Drupal::service('plugin.manager.mail');
		$langcode = \Drupal::currentUser()->getPreferredLangcode();
		$params['subject'] = "Invio mail registrazione";
		$params['message'] = "Gentile utente, 
la registrazione è avvenuta correttamente. A breve riceverei una mail di avvenuta abilitazione per accedere alla tua area mediante NOME UTENTE e PASSWORD che hai inserito in fase di registrazione.

Staff Dati.gov.it
";

		if ($email !== $emailResponsabile) {
      $email = $email . ',' . $emailResponsabile;
		}
    $params['Cc'] = $emailAgid;
		$myReturnA = $mailManager->mail('gestioneutenti', 'mail', $email, $langcode, $params, NULL, TRUE);

		//    //Email to Agid
//		$params['subject'] = "Invio mail registrazione";
//		$params['message'] = "Gentile Responsabile,
//l’utente ". $nomeCompleto ." - ". $email .", ha inoltrato la richiesta di registrazione al portale dati.gov.it in data ".date("d/m/Y").".
//
//Saluti,
//Staff Dati.gov.it”
//";
//    $myReturnR = $mailManager->mail('gestioneutenti', 'mail', $emailResponsabile, $langcode, $params, NULL, TRUE);
//		//Email to Agid
//		$mailManager = \Drupal::service('plugin.manager.mail');
//		$langcode = \Drupal::currentUser()->getPreferredLangcode();
//		$params['context']['subject'] = "Invio mail registrazione";
//		$params['context']['message'] = 'Nuovo utente registrato a sistema';
//		$mailManager->mail('system', 'mail', $emailAgid, $langcode, $params, NULL, TRUE);


    $url = Url::fromUserInput('/registrazione-succes', ['query' => []]);
    $form_state->setRedirectUrl($url);
  }        
}  
