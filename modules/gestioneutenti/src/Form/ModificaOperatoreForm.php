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

class ModificaOperatoreForm extends FormBase {

  /**
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'ModificaOperatoreForm_form';  
  }


	/**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {
		$idutente = 132;
		$user = \Drupal::currentUser();
		if($user == null){
			drupal_set_message("utente null");
		} else {
			$idutente =	$user->id();
		}

		$conn = Database::getConnection();
		$query = \Drupal::database()->select('users_field_data', 'u');	
		$query->innerJoin("organizzazione","o","u.uid=o.idUtente");
		$query->fields('u',['uid','mail','name','status','pass']);
		$query->fields('o',['idOrganizzazione','nomeOrganizzazione','descrizioneOrganizzazione','idUtente','username','nomeCompleto',
												'email','telefono','url','regione','codiceIPAIVA']);
		$query->condition('u.uid', $idutente, '=');
		$result = $query->execute();
		$item=array();
		if($result != null){
			$riga = $result->fetchAssoc();
			$item["uid"]=$riga["uid"];
			$item["name"]=$riga["name"];
			$item["nomeCompleto"]=$riga["nomeCompleto"];
			$item["mail"]=$riga["mail"];
			$item["pass"]=$riga["pass"];
			$item["username"]=$riga["username"];
			$item["telefono"]=$riga["telefono"];
			$item["nomeOrganizzazione"]=$riga["nomeOrganizzazione"];
		}

    $form['#prefix'] = '<div class="container"><div class="row mt-5"><div class="col 12 d-flex justify-content-center"> <h1>Modifica password</h1></div></div><div id="myAlert"></div>';
    $form['#suffix'] = '</div>';

    $form['nomeCognome'] = [
      '#prefix' => '<div class="row mt-5">',
      '#suffix' => '</div>'
    ];

    $form['nomeCognome']['username'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('Username'),
      '#attributes' => ['readonly' => 'readonly'],
      '#default_value' => $item["name"],
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
      '#suffix' => '</div>',
    ];  

    $form['nomeCognome']['nomeCompleto'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('Nome e Cognome'),
      '#default_value' => $item["nomeCompleto"],
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
      '#suffix' => '</div>',
    ];

    $form['myEmail'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['myEmail']['email'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('E-mail'),
      '#default_value' => $item["mail"],
      '#attributes' => ['readonly' => 'readonly'],
      '#prefix' => '<div class="col-12">',
      '#suffix' => '</div>',
    ];

    $form['org'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['org']['org1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Amministrazione di riferimento'),
      '#default_value' => $item["nomeOrganizzazione"],
      '#attributes' => ['readonly' => 'readonly'],
      '#prefix' => '<div class="col-12">',
      '#suffix' => '</div>',
    ];

    $form['pass'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['pass']['corpass'] = [
      '#type' => 'password',
      '#title' => $this->t('Password corrente'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-12 col-md-12">',
      '#suffix' => '</div>',
      '#attributes' => [
        'onchange' => 'MyPass()'
      ],
    ];

    $form['myPassword'] = array(
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>'
    );

    $form['myPassword']['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Nuova password'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
      '#suffix' => '</div>',
    ];

    $form['myPassword']['confermaPassword'] = [
      '#type' => 'password',
      '#title' => $this->t('Conferma nuova password'),
      '#required' => TRUE,
      '#default_value' => "",
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
      '#suffix' => '</div>',
    ];


    /*Fine codice nuovo*/
    $form['actions']['salva'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Aggiorna utente'),
      "#weight" => 1,
      '#attributes' => array(
        'style'=>'margin-bottom:3%;width:100%',
        'onclick' => 'return check_form_modifca_anagrafica()'
      ),

    ];

    return $form;
  }

	public function validateForm(array &$form, FormStateInterface $form_state) {}

	/**  
   * {@inheritdoc}  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {
		$idUtente=\Drupal::currentUser()->id();
		$mail=$form_state->getValue('email');
		$telefono=$form_state->getValue('telefono');
		$nomeCompleto=$form_state->getValue('nomeCompleto');
		$pass=$form_state->getValue('password');
		$conn = \Drupal::database();
		$conn->update('organizzazione')
					->fields([
										'nomeCompleto' => $nomeCompleto,
									])
					->condition('idUtente', $idUtente, '=')
					->execute();

			$user_storage = \Drupal::entityManager()->getStorage('user');
			$user = $user_storage->load($idUtente);
			if ($pass !== NULL) {
        // Set the new password
        $user->setPassword($pass);
        // Save the user
        $user->save();
			}

  }        

}  
