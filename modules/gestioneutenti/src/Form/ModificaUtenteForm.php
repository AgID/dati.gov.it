<?php  
/**  
 * @file  
 * Contains Drupal\welcome\Form\MessagesForm.  
 */  
namespace Drupal\gestioneutenti\Form;  
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\user\Entity\User;

class ModificaUtenteForm extends FormBase {

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'ModificaUtenteForm_form';  
  }

	/**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state,$idutente = NULL) {

    $users = User::load($idutente);

		$query = \Drupal::database()->select('users_field_data', 'u');	
		$query->innerJoin("organizzazione","o","u.uid=o.idUtente");
		$query->fields('u',['uid','mail','name','status','pass']);
		$query->fields('o',['idOrganizzazione','nomeOrganizzazione','descrizioneOrganizzazione','idUtente','username','nomeCompleto',
												'ckankeyDatigov','ckankeyBasigov','flagDatigov','flagBasigov',
												'fid','linkImmagine','email','telefono','url','regione','codiceIPAIVA']);
		$query->condition('u.uid', $idutente, '=');
		$result = $query->execute();

		$riga = [];

		if($result != null){
			$riga = $result->fetchAssoc();
		}

    $form['#prefix'] = '<div class="container mb-5"> <div class="row mt-5 mb-5"><div class="col 12 d-flex justify-content-center"> <h1>Modifica Utente</h1></div></div><div id="myAlert"></div>';
    $form['#suffix'] = '</div>';

    $form['generale1row'] = [
      '#prefix' => '<div class="row mt-5">',
      '#suffix' => '</div>'
    ];
    $form['idutente'] = [
      '#type' => 'hidden',  
      '#default_value' => $riga['uid'] ?? '',
    ];  

    $form['generale1row']['username'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('Nome utente'),  
      '#default_value' => $riga['name'] ?? '',
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mt-4 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];
    
    

    $form['generale1row']['nomeCompleto'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('Nome e Cognome'),  
      '#default_value' => $riga['nomeCompleto'] ?? '',
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mt-4 pl-4 pr-4">',
      '#suffix' => '</div>',
    ];  

    $form['mail'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('E-mail istituzionale'),  
      '#default_value' => $users->getEmail(),
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];

		if($riga['flagDatigov'] === "1"){
				$form['ckankeyDatigov'] = [
				  '#type' => 'textfield',  
				  '#title' => $this->t('Chiave ckan dati gov'),  
					'#default_value' => $riga["ckankeyDatigov"],
          '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
          '#suffix' => '</div></div>',
				];
        $form['idOrgDatigov'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Nome organizzazione su Istanza ckan:Dati gov'),
          '#default_value' => $riga["email"],
          '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
          '#suffix' => '</div></div>',
        ];
		}

		if($riga['flagBasigov'] === "1"){
				$form['ckankeyBasigov'] = [
				  '#type' => 'textfield',  
				  '#title' => $this->t('Chiave ckan basi gov'),  
				  //'#description' => $this->t('Chiave ckan'),  
					'#default_value' => $riga["ckankeyBasigov"],
          '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
          '#suffix' => '</div></div>',
				];
        $form['idOrgBasigov'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Nome organizzazione su Istanza ckan:Basi gov'),
          '#default_value' => $riga["telefono"],
          '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
          '#suffix' => '</div></div>',
        ];
		}

    //Select
    $optionsStato = [
      '' => 'Seleziona stato *',
      '1' => 'Abilitato',
      '0' => 'Disabilita',
    ];

		$form['statoUtente'] = array(
			'#type' => 'select',
      '#default_value' => $riga["status"],
			'#options' => $optionsStato,
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
		);

    $form['org'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nome amministrazione'),
      '#default_value' => $riga["nomeOrganizzazione"],
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="row mt-5"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];


    $form['orgIpa'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Codice IPA/P.IVA'),
      '#default_value' => $riga["codiceIPAIVA"],
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];



    $form['orgEmail'] = [
      '#type' => 'textfield',
      '#title' => $this->t('E-mail amministrazione'),
      '#default_value' => $riga["linkImmagine"],
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];

    $form['descrizione'] = [
      '#rows' => 3,
      '#cols' => 60,
      '#resizable' => TRUE,
      '#type' => 'textarea',
      '#attributes' => array('readonly' => 'readonly'),
      '#title' => $this->t('Descrizione amministrazione'),
      '#default_value' => $riga["descrizioneOrganizzazione"],
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];

    $form['regione'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Referente del servizio'),
      '#default_value' => $riga["regione"],
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('E-mail istituzionale del Referente'),
      '#default_value' => $riga["url"] ?? '',
      '#attributes' => array('readonly' => 'readonly'),
      '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
      '#suffix' => '</div></div>',
    ];

		/*Fine codice nuovo*/
    $form['actions']['salva'] = [
          '#type'  => 'submit',
          '#value' => $this->t('Aggiorna utente'),
          "#weight" => 1,
          '#attributes' => array(
            'style'=>'margin-bottom:3%;width:100%'
          ),

      ];

		return $form;
  }

	public function submitFormTwo(array &$form, FormStateInterface $form_state){}

	public function validateForm(array &$form, FormStateInterface $form_state) {}


	/**  
   * {@inheritdoc}  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
		$idUtente=$form_state->getValue('idutente');
		$stato=$form_state->getValue('statoUtente');
		$mail=$form_state->getValue('mail');
		$conn = \Drupal::database();
		$ckankeydatigov="";
		$ckankeybasigov="";
		$idOrgDati = '';
		$idOrgBasi = '';
		$conn = Database::getConnection();
		$query = \Drupal::database()->select('organizzazione', 'o');	
		$query->fields('o',['idOrganizzazione','nomeOrganizzazione','descrizioneOrganizzazione','idUtente','username','nomeCompleto',
												'ckankeyDatigov','ckankeyBasigov','flagDatigov','flagBasigov',
												'fid','linkImmagine','email','telefono','url','regione','codiceIPAIVA']);
		$query->condition('o.idUtente', $idUtente, '=');
		$result = $query->execute();
		$emailutente="";		
		if($result != null){
			$riga = $result->fetchAssoc();
			$emailutente=$riga["email"];
		}
    if(($form_state->getValue('ckankeyDatigov')!=null) && (strlen($form_state->getValue('ckankeyDatigov'))>0)){
			 $ckankeydatigov=$form_state->getValue('ckankeyDatigov');
       $idOrgDati=$form_state->getValue('idOrgDatigov');
		}
		if(($form_state->getValue('ckankeyBasigov')!=null) && (strlen($form_state->getValue('ckankeyBasigov'))>0)){
			 $ckankeybasigov=$form_state->getValue('ckankeyBasigov');
       $idOrgBasi=$form_state->getValue('idOrgBasigov');
		}
		if($stato==1 || $stato==0){
			 	$conn->update('organizzazione')
					->fields([
										'ckankeyDatigov' => $ckankeydatigov,
										'ckankeyBasigov' => $ckankeybasigov,
										'email' => $idOrgDati,
										'telefono' => $idOrgBasi,
									])
					->condition('idUtente', $idUtente, '=')
					->execute();
			 	$conn->update('users_field_data')
					->fields([
										'status' => $stato,
									])
					->condition('uid', $idUtente, '=')
					->execute();
        $conn = Database::getConnection();
        $query = \Drupal::database()->select('users_field_data', 'u');
        $query->fields('u');
        $query->condition('u.uid', $idUtente, '=');
        $result = $query->execute();
        if($result != null){
          $riga = $result->fetchAssoc();
          $emailutente = $riga["mail"];
        }
        if($stato == 1){
        $mailManager = \Drupal::service('plugin.manager.mail');
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $params['context']['subject'] = "Profilo attivato";
        $params['context']['message'] = "Gentile Utente,

il tuo profilo è stato abilitato. Per eseguire l’accesso è necessario inserire l’USER NAME e PASSWORD indicati in fase di registrazione. 
Ogni comunicazione verrà inviata alla e-mail di registrazione ".$mail.".
Ti ricordiamo che i dati verranno trattati secondo quanto previsto dall’art. 5 del Regolamento (UE) 2016/679.

Cordiali saluti,
Staff Dati.gov.it
";
        $mailManager->mail('system', 'mail', $mail, $langcode, $params);
        \Drupal::service('cache.data')->invalidateAll();
        \Drupal::service('cache.entity')->invalidateAll();
       }
			}
  }        





}  
