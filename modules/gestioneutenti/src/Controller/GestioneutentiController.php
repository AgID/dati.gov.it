<?php

namespace Drupal\gestioneutenti\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\user\Entity\User;

/**
 * An example controller.
 */
class GestioneutentiController extends ControllerBase {



	/**
   * Esegue logout
   */
  
  public function dataSet() {}

		/**
   * Esegue logout
   */
  
  public function esci() {
    $session_manager = \Drupal::service('session_manager');
    $session_manager->delete(\Drupal::currentUser()->id());
		return $this->redirect('user.login');
	}

  public function login() {
		return $this->redirect('user.login');
  }

  public function checkUsername($username = NULL){
    $ids = \Drupal::entityQuery('user')
      ->condition('name', $username)
      ->range(0, 1)
      ->execute();

    if(!empty($ids)){
      $return = true;
    } else {
      $return = false;
    }
    return new JsonResponse([
      'status' => $return,
    ]);

  }


  /**
   * Returns a render-able array for a test page.
   */
  public function content() {
  //====load filter controller
	$form['form'] = $this->formBuilder()->getForm('Drupal\gestioneutenti\Form\UtentifilterForm');
 
	$nome = \Drupal::request()->query->get('nome');
	$cognome = \Drupal::request()->query->get('cognome');
	$stato = \Drupal::request()->query->get('stato');



		$header = [
      'Id' => $this->t('Id'),
      'Username' => $this->t('Username'),
		  'Nome completo' => $this->t('Nome completo'),
		  'Stato' => $this->t('Stato'),
		  'opt' =>$this->t('Operazioni')
    ];
		$opt="Tutti";
		$righe=$this->selezionaUtenti($opt,$nome,$cognome,$stato);
		if($righe !=	null){

			 $form['table'] = [
		    '#type' => 'table',
		    '#header' => $header,
		    '#rows' => $righe,
		    '#empty' => $this->t('Nessun utente trovato'.count($righe)),
		  ];

		}
		else{
		 $form['table'] = [
		    '#type' => 'table',
		    '#header' => $header,
		    '#rows' => array(),
		    '#empty' => $this->t('Nessun utente trovato null '.count($righe)),
		  ];
		
		}

		return $form;
  }

	
 function selezionaUtenti($opt,$nome,$cognome,$stato) {
		$conn = \Drupal::database();
		$risultati=null;
		$result=null;
		$risultati=array();
		$orGruppo=null;
		//$stato=0;
		$query = null;
		if($opt=="Tutti"){

			$conn = \Drupal::database();
			$query = \Drupal::database()->select('users_field_data', 'u');	
			$query->fields('u',['uid']);
			$query->groupBy('uid');
			$numeroUtenti = $query->countQuery()->execute()->fetchField();
			if((strlen($nome)==0) && (strlen($cognome) == 0)){
						$query = \Drupal::database()->select('users_field_data', 'u');	
						$query->innerJoin("organizzazione","o","u.uid=o.idUtente");
						$query->fields('u',['uid','mail','name','status']);
						$query->fields('o',['idOrganizzazione','username','nomeCompleto','idUtente']);
				if($stato!=0 && $stato!=3){
							if($stato==2)
								$stato=0;
							$query->condition('u.status', $stato, '=');
					}
					
					else if($stato==0 || $stato==3){
							if($stato==2)
								$stato=0;
							$orGroup = $query->orConditionGroup()
										  ->condition('u.status', 1, '=')
										  ->condition('u.status', 0, '=');
							$query->condition($orGroup);
						

					}
						$result = $query->execute();

					if ($result != null) {
								$vetutenti=array();
								while ($riga = $result->fetchAssoc()) {
									if($riga["uid"]==0 || $riga["uid"]==1)
											continue;
									if(in_array($riga["uid"],$vetutenti))
											continue;
									array_push($vetutenti, $riga["uid"]);
									$item=array();
									$item["uid"]=$riga["uid"];
									$item["name"]=$riga["name"];
									$item["nomeCompleto"]=$riga["nomeCompleto"];
									if($riga["status"]==0)
										$item["status"]="Non attivo";
									else
										$item["status"]="Attivo";
									$edit = Url::fromUserInput('/admin/config/gestioneutenti/modificautente/' . $riga["uid"]);
									$edit_link = \Drupal::l('Modifica', $edit);
									$mainLink = t('@linkApprove  @linkReject', array('@linkApprove' => $edit_link, '@linkReject' => $delete_link));
									$item["operazioni"]=$mainLink;
									array_push($risultati,$item);
								}
							}
		
			}	
			else if((strlen($nome) >0 )|| (strlen($cognome) > 0)){
						$vetutenti=array();
						$valore=$nome.' '.$cognome;						
						$query = \Drupal::database()->select('users_field_data', 'u');
						$query->innerJoin("organizzazione","o","u.uid=o.idUtente");
						$query->fields('u',['uid','mail','name','status']);
						$query->fields('o',['idOrganizzazione','username','nomeCompleto']);
					if($stato!=0 && $stato!=3){
							if($stato==2)
								$stato=0;
							$query->condition('o.nomeCompleto',"%". \Drupal\Core\Database\Connection::escapeLike($nome)."%",'LIKE');												
							$query->condition('u.status', $stato, '=');
					}
					else if($stato==0 || $stato==3){
							if($stato==2)
								$stato=0;
							$query->condition('o.nomeCompleto',"%". \Drupal\Core\Database\Connection::escapeLike($nome)."%",'LIKE');												
							$orGroup = $query->orConditionGroup()
										  ->condition('u.status', 1, '=')
										  ->condition('u.status', 0, '=');
							$query->condition($orGroup);

					}
						$result = $query->execute();
						if ($result != null) {
									while ($riga = $result->fetchAssoc()) {
										if($riga["uid"]==0 || $riga["uid"]==1)
												continue;
										if(in_array($riga["uid"],$vetutenti))
											continue;
										array_push($vetutenti, $riga["uid"]);
										$item=array();
										$item["uid"]=$riga["uid"]; 
										$item["name"]=$riga["name"];
										$item["nomeCompleto"]=$riga["nomeCompleto"];
										if($riga["status"]==0)
											$item["status"]="Non attivo";
										else
											$item["status"]="Attivo";
										$edit = Url::fromUserInput('/admin/config/gestioneutenti/modificautente/' . $riga["uid"]);
										$edit_link = \Drupal::l('Modifica', $edit);
										$mainLink = t('@linkApprove  @linkReject', array('@linkApprove' => $edit_link, '@linkReject' => $delete_link));
										$item["operazioni"]=$mainLink;
										array_push($risultati,$item);
									}
								}
			}
		}
	
    return $risultati;

	}

	public function gestioneDataset(){
			$form['form'] = $this->formBuilder()->getForm('Drupal\gestioneutenti\Form\DatasetForm');
			return $form;
	}

    	public function checkPass($password){
          $user = User::load(\Drupal::currentUser()->id());
          return new JsonResponse([
              'status' => \Drupal::service('password')->check($password, $user->getPassword()),
          ]);
  }
}
