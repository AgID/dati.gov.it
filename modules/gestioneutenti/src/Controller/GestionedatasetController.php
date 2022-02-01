<?php

namespace Drupal\gestioneutenti\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database; 
use Drupal\Core\Link;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * An example controller.
 */
class GestionedatasetController extends ControllerBase {

    public function dataSetNuovoVista($ckan,$iddataset) {
        $dataset=array();
        return ['#dataset' => $dataset ,'#theme' => 'dettaglio_dataset_page'];
    }

    public function trovaDatasetAvanzata(){

        /*Recupero il menu*/
        $menu=array();
        $listaDataset=array();
        /*Recupero elenco dei temi*/
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "dati", "IN");
        $query->orderBy('t.name', 'DESC');
        $result = $query->execute();
        $urlckan="";
        $vettemi=[];
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $temitemp=[];
                $temitemp[$riga["id"]]=$riga["tid"];
                $temitemp["value"]=$riga["name"];
                $vettemi[]=$temitemp;
            }
        }
        $menu=[];
        $menu["temi"]=$vettemi;

        /*Recupero i cataloghi*/
        $catalogo=[];
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "macrocategorie_geodati", '=');
        $result = $query->execute();
        $ckanurl="";
        $vet=array();
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $catalogotemp=[];
                $catalogotemp[$riga["id"]]=$riga["ti"];
                $catalogotemp["value"]=$riga["name"];
                $catalogo[]=$catalogotemp;
            }
            $menu["cataloghi"]=$catalogo;
        }

        $testo = \Drupal::request()->query->get('testoCerca');
        return [ '#menu' => $menu,'#elencoDataset' => $listaDataset,'#theme' => 'elenco_dataset_page'];

    }

    public function trovaDatasetFormatoDist($formato){
        $menu=array();
        $listaDataset=array();
        /*Recupero elenco dei temi*/
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "dati", "IN");
        $query->orderBy('t.name', 'DESC');
        $result = $query->execute();
        $urlckan="";
        $vettemi=[];
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $temitemp=[];
                $temitemp[$riga["id"]]=$riga["tid"];
                $temitemp["value"]=$riga["name"];
                $vettemi[]=$temitemp;
            }
        }

        $menu=[];
        $menu["temi"]=$vettemi;

        /*Recupero i cataloghi*/
        $catalogo=[];
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "macrocategorie_geodati", '=');
        $result = $query->execute();
        $ckanurl="";
        $vet=array();
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $catalogotemp=[];
                $catalogotemp[$riga["id"]]=$riga["ti"];
                $catalogotemp["value"]=$riga["name"];
                $catalogo[]=$catalogotemp;
            }
            $menu["cataloghi"]=$catalogo;
        }
        /*codice per recuperare i dataset*/
        $urlckandati=$this->selezionaUrlCkan("1")."/api/3/action/package_search";
        $urlckanbasi=$this->selezionaUrlCkan("2")."/api/3/action/package_search";
        $idutente=\Drupal::currentUser()->id();
        $dati=array();
        $dati[0]=$idutente;
        $dati[1]="1";
        $apidati=$this->selezionaApiCkan($dati);
        $dati=array();
        $dati[0]=$idutente;
        $dati[1]="2";
        $apibasi=$this->selezionaApiCkan($dati);
        $datasetDatiGov=array();
        $datasetBasiGov=array();
        $datasets=array();
        $listaDataset=array();
        $query="res_format:".$formato;
        $datasetDatiGov=$this->packageSearch($urlckandati,$apidati,$query);
        $datasetBasiGov=$this->packageSearch($urlckanbasi,$apibasi,$query);
        $listaDataset=array();
        if($datasetDatiGov != null && count($datasetDatiGov) > 0){
            $listaDataset=array_merge($listaDataset, $datasetDatiGov);
        }
        return [ '#menu' => $menu,'#elencoDataset' => $listaDataset,'#theme' => 'elenco_dataset_page'];
    }

    public function trovaDatasetTag($tags){
        $menu=array();
        $listaDataset=array();
        /*Recupero elenco dei temi*/
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "dati", "IN");
        $query->orderBy('t.name', 'DESC');
        $result = $query->execute();
        $urlckan="";
        $vettemi=[];
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $temitemp=[];
                $temitemp[$riga["id"]]=$riga["tid"];
                $temitemp["value"]=$riga["name"];
                $vettemi[]=$temitemp;
            }
        }

        $menu=[];
        $menu["temi"]=$vettemi;

        /*Recupero i cataloghi*/
        $catalogo=[];
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "macrocategorie_geodati", '=');
        $result = $query->execute();
        $ckanurl="";
        $vet=array();
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $catalogotemp=[];
                $catalogotemp[$riga["id"]]=$riga["ti"];
                $catalogotemp["value"]=$riga["name"];
                $catalogo[]=$catalogotemp;
            }
            $menu["cataloghi"]=$catalogo;
        }
        /*codice per recuperare i dataset*/
        $urlckandati=$this->selezionaUrlCkan("1")."/api/3/action/package_search";
        $urlckanbasi=$this->selezionaUrlCkan("2")."/api/3/action/package_search";
        $idutente=\Drupal::currentUser()->id();
        $dati=array();
        $dati[0]=$idutente;
        $dati[1]="1";
        $apidati=$this->selezionaApiCkan($dati);
        $dati=array();
        $dati[0]=$idutente;
        $dati[1]="2";
        $apibasi=$this->selezionaApiCkan($dati);
        $datasetDatiGov=array();
        $datasetBasiGov=array();
        $datasets=array();
        $listaDataset=array();
        $query="tags:".$tags;
        $datasetDatiGov=$this->packageSearch($urlckandati,$apidati,$query);
        $datasetBasiGov=$this->packageSearch($urlckanbasi,$apibasi,$query);
        $listaDataset=array();
        if($datasetDatiGov != null && count($datasetDatiGov) > 0){
            $listaDataset=array_merge($listaDataset, $datasetDatiGov);
        }
        return [ '#menu' => $menu,'#elencoDataset' => $listaDataset,'#theme' => 'elenco_dataset_page'];
    }

    public function trovaDatasetTemi($temi){
        $menu=array();
        $listaDataset=array();
        /*Recupero elenco dei temi*/
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "dati", "IN");
        $query->orderBy('t.name', 'DESC');
        $result = $query->execute();
        $urlckan="";
        $vettemi=[];
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $temitemp=[];
                $temitemp[$riga["id"]]=$riga["tid"];
                $temitemp["value"]=$riga["name"];
                $vettemi[]=$temitemp;
            }
        }
        $menu=[];
        $menu["temi"]=$vettemi;

        /*Recupero i cataloghi*/
        $catalogo=[];
        $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
        $query->fields('t');
        $query->condition('t.vid', "macrocategorie_geodati", '=');
        $result = $query->execute();
        $ckanurl="";
        $vet=array();
        if($result != null){
            while ($riga = $result->fetchAssoc()) {
                $catalogotemp=[];
                $catalogotemp[$riga["id"]]=$riga["ti"];
                $catalogotemp["value"]=$riga["name"];
                $catalogo[]=$catalogotemp;
            }
            $menu["cataloghi"]=$catalogo;
        }
        /*codice per recuperare i dataset*/
        $urlckandati=$this->selezionaUrlCkan("1")."/api/3/action/package_search";
        $urlckanbasi=$this->selezionaUrlCkan("2")."/api/3/action/package_search";
        $idutente=\Drupal::currentUser()->id();
        $dati=array();
        $dati[0]=$idutente;
        $dati[1]="1";
        $apidati=$this->selezionaApiCkan($dati);
        $dati=array();
        $dati[0]=$idutente;
        $dati[1]="2";
        $apibasi=$this->selezionaApiCkan($dati);
        $datasetDatiGov=array();
        $datasetBasiGov=array();
        $datasets=array();
        $listaDataset=array();
        $query="tags:".$temi;
        $datasetDatiGov=$this->packageSearch($urlckandati,$apidati,$query);
        $datasetBasiGov=$this->packageSearch($urlckanbasi,$apibasi,$query);
        $listaDataset=array();
        if($datasetDatiGov != null && count($datasetDatiGov) > 0){
            $listaDataset=array_merge($listaDataset, $datasetDatiGov);
        }
        return [ '#menu' => $menu,'#elencoDataset' => $listaDataset,'#theme' => 'elenco_dataset_page'];
    }


    private function packageSearch($urlckan,$ckanapi,$query){
        $ch = curl_init();
        // Follow any Location: headers that the server sends.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        // However, don't follow more than five Location: headers.
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        // Automatically set the Referer: field in requests
        // following a Location: redirect.
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        // Return the transfer as a string instead of dumping to screen.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // If it takes more than 45 seconds, fail
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        // We don't want the header (use curl_getinfo())
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // Set user agent to Ckan_client
        curl_setopt($ch, CURLOPT_USERAGENT, "Ckan_client-PHP/");
        // Track the handle's request string
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        // Attempt to retrieve the modification date of the remote document.
        curl_setopt($ch, CURLOPT_FILETIME, TRUE);
        $queryfinale = $query;
        $post = [
            "fq" => $queryfinale
        ];
        $tempurl=$urlckan.$queryfinale;
        curl_setopt($ch, CURLOPT_URL,$urlckan);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $param1='Authorization: '.$ckanapi;
        $param2='X-CKAN-API-Key: '.$ckanapi;
        $headers1=array($param1, $param2);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);

        $headers1=array($param1, $param2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);


        $risultato = curl_exec ($ch);
        $jo=json_decode($risultato);
        $datasets=$jo->result->results;
        curl_close ($ch);
        return $datasets;
    }

  public function dataSetNuovo($ckan,$iddataset) {
        $urlckan="";
        $idckan="";
        if($ckan == "dati-gov")
            $idckan="1";
        else if($ckan == "basi-gov")
            $idckan="2";
        $urlckan=$this->selezionaUrlCkan($idckan);
        $idUtente=\Drupal::currentUser()->id();
        $dati=array();
        $dati[0]=$idUtente;
        if($ckan == "dati-gov")
            $dati[1]="1";
        else if($ckan == "basi-gov")
            $dati[1]=2;
        $api=$this->selezionaApiCkan($dati);
        $risultato=$this->packageShow($urlckan,$iddataset,$api);
        $dataset=json_decode($risultato,true);
        $dataset=$dataset["result"];
        $dataset["theme"]=json_decode($dataset["theme"]);
        return ['#dataset' => $dataset ,'#theme' => 'dettaglio_dataset_page'];
    }


	public function provametodo() {
			$form['form'] = $this->formBuilder()->getForm('Drupal\gestioneutenti\Form\UtentifilterForm');
			$query = \Drupal::database()->select('taxonomy_term_field_data', 't');	
			$query->fields('t');
			$query->condition('t.vid', "macrocategorie_geodati", '=');
			$result = $query->execute();
			$ckanurl="";
			$vet=array();
			if($result != null){
			while ($riga = $result->fetchAssoc()) {
           $vet[$riga["name"]]=$riga["name"];	
				}
			}		
			$urlckan=$this->selezionaUrlCkan("1");
			$dati=array();
			$idutente=\Drupal::currentUser()->id();
			$dati[0]=$idutente;
			$dati[1]=2;
			$api=$this->selezionaApiCkan($dati);
			return ['#id' => "10" ,'#form' => $form, '#vettore' => $vet,'#urlckan' =>$urlckan,'#apickan'=>$api,'#theme' => 'contacts_show_page'];
	}

	public function temi(){
    $arrayReturn[] = [ '' => 'Seleziona Temi'];
    $query = \Drupal::database()->select('taxonomy_term_field_data', 'dt');
    //    $query->join('taxonomy_term__field_groups', 'gr','dt.tid = gr.entity_id AND dt.vid = gr.bundle');
    $query->join('taxonomy_term__parent', 'pr ','dt.vid = pr.bundle and dt.tid = pr.entity_id');
    $query->fields('dt',['name', 'tid']);
    //    $query->fields('gr',['field_groups_value']);
    $query->condition('dt.status', 1, '=');
    $query->condition('dt.vid', 'dati', '=');
    $query->condition('pr.parent_target_id', 0, '=');

    $result = $query->execute();
    if($result !== null){
      foreach ($result->fetchAll(2) as $value) { // 2 = PDO::FETCH_ASSOC
        $arrayReturn[][$value['tid']] = $value['name'];
      }
    }
    return new JsonResponse([
      'data' => $arrayReturn,
      'method' => 'GET',
    ]);
  }

  public function sottotemi($idTema){
    $arrayReturn[] = [ '' => 'Seleziona Sottotema'];
    $query = \Drupal::database()->select('taxonomy_term_field_data', 'dt');
    //    $query->join('taxonomy_term__field_groups', 'gr','dt.tid = gr.entity_id AND dt.vid = gr.bundle');
    $query->join('taxonomy_term__parent', 'pr ','dt.vid = pr.bundle and dt.tid = pr.entity_id');
    $query->fields('dt',['name', 'tid']);
    //    $query->fields('gr',['field_groups_value']);
    $query->condition('dt.status', 1, '=');
    $query->condition('dt.vid', 'dati', '=');
    $query->condition('pr.parent_target_id', $idTema, '=');

    $result = $query->execute();
    if($result !== null){
      foreach ($result->fetchAll(2) as $value) { // 2 = PDO::FETCH_ASSOC
        $arrayReturn[][$value['tid']] = $value['name'];
      }
    }
    return new JsonResponse([
      'data' => $arrayReturn,
      'method' => 'GET',
    ]);
  }

	private function selezionaUrlCkan($ckanvalue){

			$query = \Drupal::database()->select('t_configuration', 't');	
			$query->fields('t');
			$query->condition('t.id', $ckanvalue, '=');
			$result = $query->execute();
			$ckanurl="";
			if($result != null){
				$riga = $result->fetchAssoc();
				$ckanurl=$riga["value"];
			}			
			return $ckanurl;
  }

  private function selezionaApiCkan($dati){
			$query = \Drupal::database()->select('organizzazione', 'o');	
			$query->fields('o');
			$idutente=$dati[0];
			$query->condition('o.idUtente',$idutente , '=');
      if($dati[1]==1){
					$query->condition('o.flagDatigov', 1, '=');
			}
			else if($dati[1]==2){
					$query->condition('o.flagBasigov', 1, '=');
			}
			$result = $query->execute();
			$ckanapi="";
			if($result != null){
				$riga = $result->fetchAssoc();
				if($dati[1]==1){
					$ckanapi=$riga["ckankeyDatigov"];
				}
				else if($dati[1]==2){
					$ckanapi=$riga["ckankeyBasigov"];
				}
			}			
			return $ckanapi;
  }

  public function elencoRisorse() {
			$ckanvalue = \Drupal::routeMatch()->getParameter('ckan');
			$dataset = \Drupal::routeMatch()->getParameter('id');
			$idUtente=\Drupal::currentUser()->id();
			$query = \Drupal::database()->select('t_configuration', 't');	
			$query->fields('t');
			$query->condition('t.key', $ckanvalue, '=');
			$result = $query->execute();
			$ckanurl="";
			if($result != null){
				$riga = $result->fetchAssoc();
				$ckanurl=$riga["value"];
			}			
			$query = \Drupal::database()->select('organizzazione', 'o');
			$query->fields('o',['idOrganizzazione','username','nomeOrganizzazione','ckankeyDatigov','ckankeyBasigov','idUtente']);
			$query->condition('o.idUtente', $idUtente, '=');
			$result = $query->execute();
			$username="";
			$ckankey="";
			if($result != null){
				$riga = $result->fetchAssoc();
				$ckankey=$riga["ckankeyDatigov"];
			}
			$risorse = $this->packageShow($ckanurl,$dataset,$ckankey);
      $risultati=array();
			if($risorse != null){
        $vetRisorse = $risorse['result']['resources'] ?? [];
				foreach ($vetRisorse as $risorsa){
						$item=array();
						//if($risorsa['state']=="active"){
              // $item["stato"]="Attivo";
            //}
                                                $item["nome"]=$risorsa['name'];
                                                
						$item["licenza"]=$this->getLicenseFormat($risorsa['license_type']);
						$item["formato"]=$risorsa['format'];
						
						$edit = Url::fromUserInput('/admin/config/gestioneutenti/risorsa/'.$ckanvalue."/".$dataset."/". $risorsa['id']);
						$edit_link = \Drupal::l('Modifica', $edit);
					  $mainLink = t('@linkApprove', array('@linkApprove' => $edit_link));
						$item["operazioni"]=$mainLink;
            $risultati[] = $item;
				}
			}


      $header = [
        // 'Stato' => $this->t('Stato'),
        'Nome risorsa' => $this->t('Nome risorsa'),
	'Licenza' => $this->t('Licenza'),
	'Formato' => $this->t('Formato'),
        'opt' =>$this->t('Operazioni')
      ];
      $url = '/admin/config/gestioneutenti/risorsa/'.$ckanvalue.'/'.$dataset;
      $html = '<div class="container mb-5"> <div class="row mt-5"><div class="col 12 d-flex justify-content-center"><h1>Elenco Risorse (versione beta)</h1></div></div>';
      $html .= '<div class="row mb-3 d-flex justify-content-end pl-4 pr-4"><div class="col-md-12 col-sm-3 col-lg-3"><button class="btn btn-primary btn-lg btn-block" onclick="window.location.href=\''.$url.'\'" type="button">Aggiungi Risorsa</button></div></div>';
      $form['#prefix'] = \Drupal\Core\Render\Markup::create($html);
      $form['#suffix'] = '</div>';

			$form['table'] = [
		    '#type' => 'table',
		    '#header' => $header,
		    '#rows' => $risultati,
		    '#empty' => $this->t('Nessuna risorsa trovata: '.count($risultati)),
		  ];
			return $form;
	}
  
  public function dataSet() {
    $idUtente=\Drupal::currentUser()->id();
    $idCkan = $_POST['idCkan'] ?? "0";
    $listaDataset = [];
    $datasetDatiGov = [];
    $datasetBasiGov = [];
    

    //$form['form'] = $this->formBuilder()->getForm('Drupal\gestioneutenti\Form\AggiungidatasetForm');
    $filtro = FALSE;
    $query = \Drupal::database()->select('organizzazione', 'o');
    $query->fields('o',['idOrganizzazione','username','nomeOrganizzazione','ckankeyDatigov','flagDatigov','flagBasigov','ckankeyBasigov','idUtente','telefono', 'email']);
    $query->condition('o.idUtente', $idUtente, '=');
    if($idCkan == 1)
      $query->condition('o.flagDatigov', 1, '=');
    else if($idCkan == 2)
      $query->condition('o.flagBasigov', 1, '=');
    $query->range(0, 1);
    $result = $query->execute();
    $username="";
    $ckankey="";
    if($result != null) {
      $riga = $result->fetchAssoc();
      
      if ($riga['flagDatigov'] === "1" && $riga['flagBasigov'] === "1" ) {
        $filtro = TRUE;
      }
    }
    
        $idCkan = 0;
    if ($filtro) {
      $idCkan = $idCkan = $_POST['idCkan'] ?? "2";
    }

    if ($idCkan == 0 || $idCkan == 1) {
      $datasetDatiGov=$this->selezionaDatasetCkan($idUtente,1);
    }
    if ($idCkan == 0 || $idCkan == 2) {
      $datasetBasiGov=$this->selezionaDatasetCkan($idUtente,2);
    }
    if($datasetDatiGov != null && count($datasetDatiGov) > 0){
      $listaDataset=array_merge($listaDataset, $datasetDatiGov);

    }
    if($datasetBasiGov != null && count($datasetBasiGov) > 0){
      $listaDataset=array_merge($listaDataset, $datasetBasiGov);
    }

    // recupero il nomeAmministrazione
    $query = \Drupal::database()->select('organizzazione', 'o');
    $query->fields('o',['nomeOrganizzazione']);
    $query->condition('o.idUtente', $idUtente, '=');
    $result = $query->execute();
    if($result != null) {
      $org = $result->fetchAssoc();
    }    

    $countDataset = count($listaDataset);
    // $html = '<div class="container mb-5"> <div class="row mt-5"><div class="col-12 d-flex justify-content-center"><h1>Elenco Dataset (versione beta)</h1></div></div>';
    // $html .= '<div class="row mb-3 d-flex justify-content-end pl-4 pr-4"><div class="col-md-6 col-sm-12 col-lg-6"><h3>Dataset Trovati: '.$countDataset.'</h3></div><div class="col-md-6 col-sm-12 col-lg-6 d-flex justify-content-end"><button class="btn btn-primary btn-lg" onclick="window.location.href=\'/admin/config/gestioneutenti/datasetFormNew\'" type="button">Aggiungi Dataset</button></div></div>';

    $html = '<div class="container mb-5"><div class="row"><div class="col-12 d-flex justify-content-center"><h3><span class="badge badge-secondary">'.$org['nomeOrganizzazione'].'</span></h3></div></div>';
    $html .= '<div class="row mt-2"><div class="col-12 d-flex justify-content-center"><h1>Elenco Dataset (versione beta)</h1></div></div>';
    $html .= '<div class="row mb-3 d-flex justify-content-end pl-4 pr-4"><div class="col-md-6 col-sm-12 col-lg-6"><h3>Dataset Trovati: '.$countDataset.'</h3></div><div class="col-md-6 col-sm-12 col-lg-6 d-flex justify-content-end"><button class="btn btn-primary btn-lg" onclick="window.location.href=\'/admin/config/gestioneutenti/datasetFormNew\'" type="button">Aggiungi Dataset</button></div></div>';    
   /* if ($filtro) {
      $html .= '<div class="row">
            <div class="col-12"> 
<form method="post" action="/admin/config/gestioneutenti/dataSet">
                <div class="bootstrap-select-wrapper">
                <label>Scegli un catalogo</label>
                <select name="idCkan"  title="Scegli un catalogo" onchange="this.form.submit()" >
     //             <option value="0"';
      //if($idCkan == 0){ $html .= 'selected'; }
      //$html .= ' >Tutti cataloghi</option>';
      //$html .= '<option value="1"';
      //if($idCkan == 1){ $html .= 'selected'; }
      //$html .= '>Dati.gov.it</option>';
      $html .= ' <option value="2"';
      if($idCkan == 2){ $html .= 'selected'; }
      $html .= '>Basi di dati</option>';
      $html .= '</select>
            </div>
</form>
            </div>
            </div>';
    }*/


    $form['#prefix'] = \Drupal\Core\Render\Markup::create($html);
    $form['#suffix'] = '</div>';

    $header = [
      'Ckan' => $this->t('Catalogo'),
      'Titolo' => $this->t('Titolo'),
      'Descrizione' => $this->t('Descrizione'),
      //'Organizzazione' => $this->t('Amministrazione'),
      //'Tipo licenza' => $this->t('Tipo licenza'),
      //'Nome' => $this->t('Nome'),
      //'Titolo' => $this->t('Titolo'),
      //'Data' => $this->t('Ultima modifica'),
      'opt' =>$this->t('Operazione')
    ];
    if($listaDataset !=	null){
	$rowChunked = $this->returnChuckedRowsForPager($listaDataset, 10);
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rowChunked,
        '#empty' => $this->t('Nessun dataset trovato: '. count($listaDataset)),
      ];

      // Finally add the pager.
      $form['pager'] = [
      	'#type' => 'pager',
      ];
    }  else {
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => array(),
        '#empty' => $this->t('Nessun dataset trovato: '. count($listaDataset)),
      ];

    }
    return $form;
	}

public function returnChuckedRowsForPager($items, $pages) {

    $allItems         = count($items);

    $currentPage      = pager_default_initialize($allItems, $pages);

    $chunks           = array_chunk($items, $pages);

    $currentPageItems = $chunks[$currentPage];



    return $currentPageItems;

  }


/*
private function keepXLines($str, $num=10) {
    $lines = explode("\n", $str);
    $firsts = array_slice($lines, 0, $num);
    return implode("\n", $firsts);
}
*/
	private function selezionaDatasetCkan($idUtente,$idCkan){
			$query = \Drupal::database()->select('t_configuration', 't');
			$query->fields('t');
			if($idCkan == 1)
				$query->condition('t.key', "dati-gov", '=');
			else if($idCkan == 2)
				$query->condition('t.key', "basi-gov", '=');
			$result = $query->execute();
			$urlckan="";			
			if($result !== null){
				$riga = $result->fetchAssoc();
				$urlckan = $riga["value"];
			} else {
			   return [];
      }
		  $query = \Drupal::database()->select('organizzazione', 'o');	
			$query->fields('o',['idOrganizzazione','username','nomeOrganizzazione','ckankeyDatigov','flagDatigov','flagBasigov','ckankeyBasigov','idUtente','telefono', 'email']);
			$query->condition('o.idUtente', $idUtente, '=');
			if($idCkan == 1)
				$query->condition('o.flagDatigov', 1, '=');
			else if($idCkan == 2)
				$query->condition('o.flagBasigov', 1, '=');	
			$query->range(0, 1);
			$result = $query->execute();
			$username="";
			$ckankey="";
        if($result != null){
          $riga = $result->fetchAssoc();
          if($idCkan == 1){
            if ($riga['ckankeyDatigov'] === NULL) return [];
            $ckankey = $riga['ckankeyDatigov'];
            $username = $riga['email'];
          } else if($idCkan == 2){
            if ($riga['ckankeyBasigov'] === NULL) return [];
            $ckankey= $riga['ckankeyBasigov'];
            $username = $riga['telefono'];
          }
        }

        $datasets = $this->userShow($username,$ckankey,$urlckan);
        $listaDataset = [];
        foreach($datasets as $dataset){
            $item=array();
            if($idCkan == 1)
              $item["ckan"]="Dati.gov.it";
            else if($idCkan == 2)
              $item["ckan"]="Basi di dati";
            $item["Titolo"]=$dataset->title;
           // $desc=$dataset->notes;
           
           // $item["Descrizione"]=$this->keepXLines($desc,2);
            $item["Descrizione"]=$dataset->notes;
            //$item["organizzazione"]=$dataset->organization->name;
            //$item["licenseId"]=$dataset->license_id;
            //$item["nome"]=$dataset->name;
            //$item["Titolo"]=$dataset->title;
            //$item["Ultima modifica"]=explode("T",$dataset->modified)[0];
            $edit = Url::fromUserInput('/admin/config/gestioneutenti/datasetFormNew/'.$idCkan.'/'.$dataset->name);
            $arrayLink['@linkApprove'] = \Drupal::l('Modifica', $edit);
            $link = '@linkApprove';
            if($idCkan == 1){
              $risorse = Url::fromUserInput('/admin/config/gestioneutenti/visualizzarisorsa/dati-gov/'.$dataset->name);
              $arrayLink['@linkRisorse'] = \Drupal::l('Risorse', $risorse);
              $link .= ' |  @linkRisorse';
            }

            $mainLink = t($link, $arrayLink);
            $item["operazione"]=$mainLink;
            $listaDataset[] = $item;
        }

       return $listaDataset;
	}





	private function userShow($idUtente,$ckanapi,$urlckan){

    $ch = curl_init();
		// Follow any Location: headers that the server sends.
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		// However, don't follow more than five Location: headers.
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		// Automatically set the Referer: field in requests 
		// following a Location: redirect.
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		// Return the transfer as a string instead of dumping to screen. 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// If it takes more than 45 seconds, fail
		curl_setopt($ch, CURLOPT_TIMEOUT, 45);
		// We don't want the header (use curl_getinfo())
		curl_setopt($ch, CURLOPT_HEADER, FALSE); //impostando a true mi visualizza header della risposta, a false no
		// Set user agent to Ckan_client
		curl_setopt($ch, CURLOPT_USERAGENT, "Ckan_client-PHP/");
		// Track the handle's request string
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		// Attempt to retrieve the modification date of the remote document.
		curl_setopt($ch, CURLOPT_FILETIME, TRUE);

		$url=$urlckan."/api/3/action/package_search?fq=organization:" . $idUtente . "&rows=1000";

		curl_setopt($ch, CURLOPT_URL,$url);
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;
		$headers1=array($param1, $param2);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec($ch);
    $jo=json_decode($risultato);

    return $jo->result->results;
 }


	private function packageShow($urlckan,$package,$ckanapi){
		  $ch = curl_init();
			// Follow any Location: headers that the server sends.
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			// However, don't follow more than five Location: headers.
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			// Automatically set the Referer: field in requests 
			// following a Location: redirect.
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			// Return the transfer as a string instead of dumping to screen. 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			// If it takes more than 45 seconds, fail
			curl_setopt($ch, CURLOPT_TIMEOUT, 45);
			// We don't want the header (use curl_getinfo())
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			// Set user agent to Ckan_client
			curl_setopt($ch, CURLOPT_USERAGENT, "Ckan_client-PHP/");
			// Track the handle's request string
			curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
			// Attempt to retrieve the modification date of the remote document.
			curl_setopt($ch, CURLOPT_FILETIME, TRUE);
			$post = [
					"id" => $package
			];		
			$url=$urlckan."/api/3/action/package_show";
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$param1='Authorization: '.$ckanapi;
			$param2='X-CKAN-API-Key: '.$ckanapi;
			$headers1=array($param1, $param2);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
			$headers1=array($param1, $param2);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
			$risultato = curl_exec ($ch);
			$jo=json_decode($risultato)->result;
			curl_close ($ch);
			return json_decode($risultato,true);
	}


private function compattaStringa($carattere,$stringa,$carattereConfronto){
	$pos=strpos($stringa,$carattere,0);
	while($pos!=FALSE){
      $trovato=0;
			$j=$pos;
      while($trovato==0){
				  if($stringa[$j]==$carattereConfronto){
						$sottostringa=$sottostringa.$stringa[$j];
						$j++;		
					}
					else{
						 $trovato=1;
						 $pos=$j;
						 //$fatto=1;
					}
	    }
      $stringa=str_replace($sottostringa,"-",$stringa);
			$pos=strpos($stringa,$carattere,0);
			$sottostringa="";
  }
 return $stringa;
}


private function sostituisci($stringa){
	$stringa=str_replace(" ", "-", $stringa);
	$stringa=str_replace("#", "-", $stringa);
	$stringa=str_replace("%", "-", $stringa);
	$stringa=str_replace("!", "-", $stringa);
  $stringa=str_replace("(", "-", $stringa);
  $stringa=str_replace(")", "-", $stringa);
  $stringa=str_replace("*", "-", $stringa);
  $stringa=str_replace("+", "-", $stringa);
  $stringa=str_replace("^", "-", $stringa);
  $stringa=str_replace(">", "-", $stringa);
  $stringa=str_replace("<", "-", $stringa);
	$stringa=str_replace("@", "-", $stringa);
	$stringa=str_replace("/", "-", $stringa);
  $stringa=str_replace("?", "-", $stringa);
  $stringa=str_replace("|", "-", $stringa);
  $stringa=str_replace("&", "-", $stringa);
  $stringa=str_replace("à", "a", $stringa);
  $stringa=str_replace("è", "e", $stringa);
  $stringa=str_replace("è", "e", $stringa);
  $stringa=str_replace("ò", "o", $stringa);
  $stringa=str_replace("ù", "u", $stringa);
  $stringa=str_replace("&", "-", $stringa);
  $stringa=str_replace("§", "s", $stringa);
  $stringa=str_replace("\\", "-", $stringa);
  $stringa=str_replace("\'", "-", $stringa);
  $stringa=str_replace("\"", "-", $stringa);
  return $stringa;

}

private function getLicenseFormat($stringa) {
	$optionsLicenza = [
        '' => 'Licenza della risorsa',
        'https://w3id.org/italia/controlled-vocabulary/licences/A1_PublicDomain' => 'Pubblico Dominio',
        'https://w3id.org/italia/controlled-vocabulary/licences/A11_CCO10' => 'Creative Commons CC0 1.0 Universale - Public Domain Dedication (CC0 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A12_PDDL' => 'Seleziona tipo licenza',
        'https://w3id.org/italia/controlled-vocabulary/licences/A2_Attribution' => 'Attribuzione',
        'https://w3id.org/italia/controlled-vocabulary/licences/A21_CCBY40' => 'Creative Commons Attribuzione 4.0 Internazionale (CC BY 4.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A210_ODCBY' => 'Open Data Commons Attribution License (ODC_BY)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A22_CCBY30' => 'Creative Commons Attribuzione 3.0 Unported (CC BY 3.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A23_CCBY30IT' => 'Creative Commons Attribuzione Italia 3.0 (CC BY 3.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A24_CCBY25' => 'Creative Commons Attribuzione 2.5 Generica (CC BY 2.5)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A25_CCBY25IT' => 'Seleziona tipo licenza',
        'https://w3id.org/italia/controlled-vocabulary/licences/A26_CCBY20' => 'Creative Commons Attribuzione 2.0 Generica (CC BY 2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A27_CCBY20IT' => 'Creative Commons Attribuzione 2.0 Italia (CC BY 2.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A28_CCBY10' => 'Creative Commons Attribuzione 1.0 Generica (CC BY 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A29_IODL20' => 'Italian Open Data License 2.0 (IODL 2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A3_ShareAlike' => 'Effetto Virale (aka Condivisione allo stesso modo)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A31_CCBYSA40' => 'Creative Commons Attribuzione-Condividi allo stesso modo 4.0 Internazionale (CC BY-SA 4.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A310_ODBL' => 'Open Data Commons Open Database License 1.0 (ODbL)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A311_GFDL13' => 'GNU Free Documentation License 1.3 (GFDL 1.3)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A32_CCBYSA30' => 'Creative Commons Attribuzione-Condividi allo stesso modo 3.0 Unported (CC BY-SA 3.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A33_CCBYSA30IT' => 'Creative Commons Attribuzione-Condividi allo stesso modo 3.0 Italia (CC BY-SA 3.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A34_CCBYSA25' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Generica (CC BY-SA 2.5)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A35_CCBYSA25IT' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia (CC BY-SA 2.5 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A36_CCBYSA20' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.0 Generica (CC BY-SA 2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A37_CCBYSA20IT' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.0 Italia (CC BY-SA 2.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A38_CCBYSA10' => 'Creative Commons Attribuzione-Condividi allo stesso modo 1.0 Generica (CC BY-SA 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A39_IODL10' => 'Italian Open Data License 1.0 (IODL 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/A4_ShareAlikeCopyleftNonComp' => 'Condivisione allo stesso modo / copyleft non compatibile',
        'https://w3id.org/italia/controlled-vocabulary/licences/A41_ADRM' => 'Against DRM (2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B1_NonCommercial' => 'Solo Uso non Commerciale',
        'https://w3id.org/italia/controlled-vocabulary/licences/B11_CCBYNC40' => 'Creative Commons Attribuzione-Non Commerciale 4.0 Internazionale (CC BY-NC 4.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B110_CCBYNCSA30' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 3.0 Unported (CC BY-NC-SA 3.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B111_CCBYNCSA30IT' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 3.0 Italia (CC BY-NC-SA 3.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B112_CCBYNCSA25' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.5 Generica (CC BY-NC-SA 2.5)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B113_CCBYNCSA25IT' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.5 Italia (CC BY-NC-SA 2.5 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B114_CCBYNCSA20' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.0 Generica (CC BY-NC-SA 2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B115_CCBYNCSA20IT' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.0 Italia (CC BY-NC-SA 2.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B116_CCBYNCSA10' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 1.0 Generica (CC BY-NC-SA 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B12_CCBYNC30' => 'Creative Commons Attribuzione-Non Commerciale 3.0 Unported (CC BY-NC 3.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B13_CCBYNC30IT' => 'Creative Commons Attribuzione-Non Commerciale 3.0 Italia (CC BY-NC 3.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B14_CCBYNC25' => 'Creative Commons Attribuzione-Non Commerciale 2.5 Generica (CC BY-NC 2.5)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B15_CCBYNC25IT' => 'Creative Commons Attribuzione-Non Commerciale 2.5 Italia (CC BY-NC 2.5 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B16_CCBYNC20' => 'Creative Commons Attribuzione-Non Commerciale 2.0 Generica (CC BY-NC 2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B17_CCBYNC20IT' => 'Creative Commons Attribuzione-Non Commerciale 2.0 Italia (CC BY-NC 2.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B18_CCBYNC10' => 'Creative Commons Attribuzione-Non Commerciale 1.0 Generica (CC BY-NC 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B19_CCBYNCSA40' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 4.0 Internazionale (CC BY-NC-SA 4.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B2_NoDerivs' => 'Non Opere Derivate',
        'https://w3id.org/italia/controlled-vocabulary/licences/B21_CCBYND40' => 'Creative Commons Attribuzione-Non Opere Derivate 4.0 Internazionale (CC BY-ND 4.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B22_CCBYND30' => 'Creative Commons Attribuzione-Non Opere Derivate 3.0 Unported (CC BY-ND 3.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B23_CCBYND30IT' => 'Creative Commons Attribuzione-Non Opere Derivate 3.0 Italia (CC BY-ND 3.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B24_CCBYND25' => 'Creative Commons Attribuzione-Non Opere Derivate 2.5 Generica (CC BY-ND 2.5)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B25_CCBYND25IT' => 'Creative Commons Attribuzione-Non Opere Derivate 2.5 Italia (CC BY-ND 2.5 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B26_CCBYND20' => 'Creative Commons Attribuzione-Non Opere Derivate 2.0 Generica (CC BY-ND 2.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B27_CCBYND20IT' => 'Creative Commons Attribuzione-Non Opere Derivate 2.0 Italia (CC BY-ND 2.0 IT)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B28_CCBYND10' => 'Creative Commons Attribuzione-Non Opere Derivate 1.0 Generica (CC BY-ND 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/B29_CCBYNDNC10' => 'Creative Commons Attribuzione-Non Opere Derivate-Non Commerciale 1.0 Generica (CC BY-ND-NC 1.0)',
        'https://w3id.org/italia/controlled-vocabulary/licences/C1_Unknown' => 'Licenza Sconosciuta',
        'https://w3id.org/italia/controlled-vocabulary/licences/C11_Unknown' => 'Licenza Sconosciuta'
      ];

	return $optionsLicenza[$stringa];
}

}
