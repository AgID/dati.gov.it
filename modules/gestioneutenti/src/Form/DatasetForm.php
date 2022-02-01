<?php
namespace Drupal\gestioneutenti\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\taxonomy\Entity\Term;
 
/**
 * Provides the form for filter Students.
 */
class DatasetForm extends FormBase {
 
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dataset_form';
  }
 
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$ckan=null,$id=null) {

		$idorgform="";
		$uuid=$this->v4();
		$item = array();
		$user = \Drupal::currentUser();
		$idUtente=\Drupal::currentUser()->id();
		$conn = Database::getConnection();
		$query = \Drupal::database()->select('organizzazione', 'o');	
		$query->fields('o');
   	$query->condition('o.idUtente', $idUtente, '=');
		$result = $query->execute();
		$vetorg=array();
		
    $vetorg[0]="Seleziona istanza ckan";
 
    if($result != null ){
      $riga = $result->fetchAssoc();
      $item= array('0' => 'Seleziona istanza ckan');
      if($riga["flagDatigov"]==1 && $riga["flagBasigov"]==0 ){
          $vetorg["1"] = "Dati.gov.it";
      }
      else if($riga["flagBasigov"]==1 && $riga["flagDatigov"]==0 ){
          $vetorg["1"] = "Basi.gov.it";
      }
      else if($riga["flagBasigov"]==1 && $riga["flagDatigov"]==1 ){
          $vetorg["1"]="Dati.gov.it";
          $vetorg["2"]="Basi.gov.it";
      }
    }

    $form['#prefix'] = '<div class="container"><div class="row mt-5"><div class="col 12 d-flex justify-content-center"> <h1>Registrazione Utente</h1></div></div>';
    $form['#suffix'] = '</div>';

    if($id === "support-request"){

      $form['croupCkan'] = [
        '#prefix' => '<div class="row mt-5">',
        '#suffix' => '</div>'
      ];

      $form['croupCkan']['organizzazionedataset'] = [
        '#type' => 'select',
        '#options' => $vetorg,
        '#default_value' => 0,
        '#prefix' => '<div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div>',
      ];
    }

	
		if($user !== null){
			$idutente =	$user->id();
 			$conn = Database::getConnection();
			$query = \Drupal::database()->select('organizzazione', 'o');	
			$query->fields('o',['idOrganizzazione','nomeOrganizzazione','descrizioneOrganizzazione','idUtente','username','nomeCompleto','ckankeyDatigov','ckankeyBasigov','fid','linkImmagine',
												'email','telefono','url','regione','codiceIPAIVA']);
			$query->condition('o.idUtente', $idutente, '=');
			$result = $query->execute();
			if($result != null){
				$riga = $result->fetchAssoc();
				$item["nomeOrganizzazione"]=$riga["nomeOrganizzazione"];
				$item["idOrganizzazione"]=$riga["idOrganizzazione"];
				if($ckan=="dati-gov")
						$item["ckankey"]=$riga["ckankeyDatigov"];		
				else if ($ckan=="basi-gov")	
						$item["ckankey"]=$riga["ckankeyBasigov"];		
				$item["username"]=$riga["username"];
				$item["codiceIPAIVA"]=$riga["codiceIPAIVA"];
			}		
		}

		$query = \Drupal::database()->select('t_configuration', 't');
		$query->fields('t');
		$query->condition('t.key', $ckan, '=');
		$result = $query->execute();
		$url="";
		if($result != null){
			$riga = $result->fetchAssoc();	
			$url=$riga["value"];
		}
		$jsonOrg=$this->organizzationListForUser($item["username"],$item["ckankey"]);
		$joOrg=json_decode($jsonOrg);
		$datiForm=array();	
		$jo=null;	
		$tema="";
		$nomedataset="";
		$frequenzaAgggiornamento="";
		$dataUltimaModifica="";
		$editoreSoggetto="";
		$editoreSoggettoIpaIVa="";
		$coperturaGeografica="";
		$visibilita="";
		$contattoNome="";
		$contattoEmail="";
		$dataRilascio="";
		$versione="";
 		$estensioniTemporali="";
		$istanzackan="";
		if($ckan != null){
			if($ckan=="dati-gov")
				$istanzackan="1";
			else
				$istanzackan="2";
		}
		$vetstandard=null;
		if($id != null){
      $form['idckan'] = [
        '#type' => 'hidden',
        '#default_value' => $istanzackan,
      ];
			$dataset=$this->dettaglioDataset($url,$id,$item["username"],$item["ckankey"]);
			$jo = json_decode($dataset)->result;
			$vetstandard=json_decode($jo->conforms_to,true);
			$frequenzaAgggiornamento=$jo->frequency;
			$estensioniTemporali=$jo->temporal_coverage;
			$dataUltimaModifica=$jo->modified;

			if(strlen($jo->issued) > 0 ){
				 $vetdata=explode("-",$jo->issued);
				$dataRilascio=$vetdata[2]."-".$vetdata[1]."-".$vetdata[0];
			}

			if($jo->version != null) {
        $versione=$jo->version;
      }

      $editoreSoggetto=$jo->publisher_name;
      $editoreSoggettoIpaIVa=$jo->publisher_identifier;
			$idorgform=$jo->organization->id;
			$coperturaGeografica=$jo->geographical_name;
				if($jo->private == FALSE){
          $visibilita=1;
        } else {
          $visibilita=2;
        }
				$extras=$jo->extras;
				$nomeContatto="";
				$emailContatto="";
				foreach ($extras as $extra){
					if($extra->key=="contact_name")
							$contattoNome=$extra->value;
					else if($extra->key=="contact_mail")
						$contattoEmail=$extra->value;

				}
			$datiForm["titolo"]=$jo->title;
			$datiForm["note"]=$jo->notes;
			$datiForm["identificativoDataset"]=$jo->identifier;
			$datiForm["lingua"]=$jo->language;
			$datiForm["autore"]=$jo->author;
   		$tema=json_decode($jo->theme)[0]->theme;
			$nomedataset=$jo->name;
		} else{
			$datiForm["titolo"]="";
			$datiForm["note"]="";
			$datiForm["lingua"]="seleziona";						
			$datiForm["autore"]="";
		}

    $form['informazioniGenerali']['temp'] = [
       '#attributes' => array(
            'style'=>'margin-top:20%'
          ),
        '#type' => 'textfield',
        '#title' => $this->t('Titolo'),
        '#required' => TRUE,
        '#default_value' => $dataUltimaModifica,
    ];

		if($id == "support-request"){
		  $form['operazione'] = [  
		    '#type' => 'hidden',  
		    '#default_value' => 1,
		  ];
		} else {
		  $form['idformorganizzazione'] = [  
		    '#type' => 'hidden',  
		    '#default_value' => $idorgform,
		  ];
		  $form['operazione'] = [  
		    '#type' => 'hidden',  
		    '#default_value' => 2,
		  ];
      $form['idDatasetNascosto'] = [
        '#type' => 'hidden',
        '#default_value' => $id,
      ];
      $form['urlckan'] = [
        '#type' => 'hidden',
        '#default_value' => $url,
      ];
		}
		$form['username'] = [
      '#type' => 'hidden',  
      '#default_value' => $item["username"],
    ];
    $form['ckankey'] = [  
      '#type' => 'hidden',  
      '#default_value' => $item["ckankey"],
    ];
    $form['idOrg'] = [  
      '#type' => 'hidden',  
      '#default_value' => "",
    ];
    $form['informazioniGenerali'] = [
        '#title' => t('Informazioni generali'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];

		if($id == "support-request"){
			$form['informazioniGenerali']['titoloDataset'] = [  
          '#attributes' => array(
            'style'=>'margin-top:2%'
          ),
        '#type' => 'textfield',
        '#title' => $this->t('Titolo'),
        '#required' => TRUE,
        '#default_value' => $datiForm["titolo"],
      ];
		} else{
        $vet1=$estensioniTemporali;

				$form['informazioniGenerali']['titoloDataset'] = [  
					'#attributes' => array(
						'style'=>'margin-top:2%',
            'readonly' => 'readonly',
					), 
		      '#type' => 'textfield',
		      '#title' => $this->t('Titolo'),
				  '#default_value' => $datiForm["titolo"],
          '#required' => TRUE,
		    ];

        $form['informazioniGenerali']['nomeDatasetok'] = [
            '#attributes' => array(
              'style'=>'margin-top:2%',
              'readonly' => 'readonly',
            ),
          '#type' => 'hidden',
          '#default_value' => $nomedataset,
          '#required' => TRUE,
        ];

        $form['informazioniGenerali']['idDatasetokNascosto'] = [
            '#attributes' => array(
              'style'=>'margin-top:2%',
              'readonly' => 'readonly',
            ),
          '#type' => 'textfield',
          '#default_value' => $id,
          '#required' => TRUE,
        ];
		}		   

		$form['informazioniGenerali']['textarea'] = [  
				'#attributes' => array(
					'style'=>'margin-top:2%'
				), 
				'#rows' => 5,
				'#cols' => 60,
				'#resizable' => TRUE,
      '#type' => 'textarea',  
      '#title' => $this->t('Descrizione'), 
			'#required' => TRUE, 
			'#default_value' => $datiForm["note"],
    ];  

	

    //ok cosi mi costruisco la select chiave valore

    $valoriLingue=null;

    $options = array('seleziona' => 'Seleziona lingua',
        'DEU' => 'Tedesco',
        'ENG' => 'Inglese',
        'FRA' => 'Francese',
        'SPA' => 'Spagnolo',
        'ITA' => 'Italiano',
    );


  if(isset($datiForm["lingua"])) {
    $valoreLingua=$datiForm["lingua"];
  } else {
    $valoreLingua="ITA";
  }

	$form['informazioniGenerali']['linguaDataset'] = array(
		'#type' => 'select',
		'#options' => $options,
		'#required' => TRUE,
		'#default_value' => $valoreLingua,
	);

  $options = array('seleziona' => 'Visibilita *',
				'True' => 'Privato',
				'False' => 'Pubblico',				
	);

	if($id != "support-request") {
		if($visibilita==1){
			$form['informazioniGenerali']['visibilita'] = array(
					'#type' => 'select',
					'#options' => $options,
					'#default_value' => "False",
					'#attributes' => array(
							'style'=>'margin-bottom:1%'
						),   
				);
			
		} else if ($visibilita==2) {
			$form['informazioniGenerali']['visibilita'] = array(
					'#type' => 'select',
					'#options' => $options,
					'#default_value' => "True",
					'#attributes' => array(
							'style'=>'margin-bottom:1%'
						),   
				);


		}
  } else {
		$form['informazioniGenerali']['visibilita'] = array(
					'#type' => 'select',
					'#options' => $options,
					'#default_value' => $visibilita,
					'#attributes' => array(
							'style'=>'margin-bottom:1%'
						),   
				);
  }

    $form['informazioniGenerali']['versione'] = [
         '#attributes' => array(
              'style'=>'margin-top:0%'
         ),
         '#type' => 'textfield',
         '#title' => $this->t('Versione'),
         '#default_value' => $versione,
    ];

		if($id == "support-request"){
			$form['informazioniGenerali']['identificativoDatasetGenerale'] = [  
					'#attributes' => array(
						'style'=>'margin-top:2%'
					), 
		    '#type' => 'textfield',  
		    '#title' => $this->t('Identificativo del dataset'),  
				//'#required' => TRUE,
				'#default_value' => $uuid,  
		    '#attributes' => array('readonly' => 'readonly'),    
		  ];
		}

    $form['Temi'] = [
        '#title' => t('Temi'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];

		$options = array('seleziona' => 'Temi *',
				'AGRI' => 'Agricoltura, pesca, silvicoltura e prodotti alimentari',
				'ECON' => 'Economia e finanze',
				'EDUC' => 'Istruzione, cultura e sport',
				'ENER' => 'Energia',
				'ENVI' => 'Ambiente',
				'GOVE' => 'Governo e settore pubblico',
				'HEAL' => 'Salute',
				'INTR' => 'Tematiche internazionali',
				'JUST' => 'Giustizia, sistema giuridico e sicurezza pubblica<',
				'OP_DATPRO' => 'Dati provvisori',
				'REGI' => 'Regioni e città',
				'SOCI' => 'Popolazione e società',
				'TECH' => 'Scienza e tecnologia',
				'TRAN' => 'Trasporti',
				'ITA' => 'Italiano',
		);

	  if($id == "support-request"){
      $form['Temi']['tag'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => 0,
      );
		} else {
			$form['Temi']['tag'] = array(
				'#type' => 'select',
				'#options' => $options,
				'#default_value' => $tema,
			);
		}		   

    $form['soggetti'] = [
        '#title' => t('Soggetti'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];

		$form['soggetti']['nome'] = [  
				'#attributes' => array(
					'style'=>'margin-top:0%'
				), 
      '#type' => 'textfield',  
      '#title' => $this->t('Editore'),  
			//'#required' => TRUE,
			'#default_value' => $editoreSoggetto,  
    ];  

		$form['soggetti']['codiceIPAIVAUTORE'] = [  
				'#attributes' => array(
					'style'=>'margin-top:0%'
				), 
      '#type' => 'textfield',  
      '#title' => $this->t('Editore - Codice IPA/P. IVA'),  
			//'#required' => TRUE,
			'#default_value' =>  $editoreSoggettoIpaIVa,  
    ];  

    $form['riferimentiTemporali'] = [
        '#title' => t('Riferimenti temporali'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];


    $form['riferimentiTemporali']['dataRilascio'] = [
				'#attributes' => array(
					'style'=>'margin-top:2%'
				), 
      '#type' => 'date',  
			'#date_date_format' => 'Y/m/d',
      '#title' => $this->t('Data rilascio gg/mm/aaaa'),  
			//'#required' => TRUE,
			'#default_value' => $dataRilascio,  
    ];  

    $vetdata=explode("-",$dataUltimaModifica);
	  $vetdatastr=$vetdata[2]."-".$vetdata[1]."-".$vetdata[0];

	  $form['riferimentiTemporali']['dataModifica'] = [
          '#attributes' => array(
            'style'=>'margin-top:1%'
          ),
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#title' => $this->t('Ultima modifica gg/mm/aaaa'),
        '#required' => TRUE,
        '#default_value' => [
           $vetdatastr,
          ],
      ];

  $options = array(
    'frequenza' => 'Seleziona frequenza aggiornamento *',
    'ANNUAL' => 'annuale',
    'ANNUAL_2' => 'semestrale',
    'ANNUAL_3' => 'tre volte anno',
    'BIDECENNIAL' => 'bidecennale',
    'BIENNIAL' => 'biennale',
    'BIHOURLY' => 'ogni due ore',
    'BIMONTHLY' => 'bimestrale',
    'BIWEEKLY' => 'quindicinale',
    'CONT' => 'continuo',
    'DAILY' => 'quotidiano',
    'DAILY_2' => 'due volte al giorno<',
    'DECENNIAL' => 'decennale',
    'HOURLY' => 'ogni ora',
    'IRREG' => 'irregolare',
    'MONTHLY' => 'mensile',
    'MONTHLY_2' => 'bimensile',
    'MONTHLY_3' => 'tre volte al mese',
    'NEVER' => 'mai',
    'OP_DATPRO' => 'Dati provvisori',
    'OTHER' => 'altro',
    'QUADRENNIAL' => 'ogni quattro anni<',
    'QUARTERLY' => 'trimestrale',
    'QUINQUENNIAL' => 'ogni cinque anni',
    'TRIDECENNIAL' => 'tridecennale',
    'TRIENNIAL' => 'triennale',
    'TRIHOURLY' => 'ogni tre ore',
    'UNKNOWN' => 'sconosciuto',
    'UPDATE_CONT' => 'in continuo aggiornamento',
    'WEEKLY' => 'settimanale',
    'WEEKLY_2' => 'bisettimanale',
    'WEEKLY_3' => 'tre volte a settimana',



);

  if($id == "support-request"){
		$form['riferimentiTemporali']['frequenzaAggiornamento'] = array(
			'#type' => 'select',
			'#options' => $options,
			'#attributes' => array(
					'style'=>'margin-bottom:1%'
				),   
		);
	} else {
			$form['riferimentiTemporali']['frequenzaAggiornamento'] = array(
			'#type' => 'select',
			'#options' => $options,
			'#default_value' => $frequenzaAgggiornamento,
			'#attributes' => array(
					'style'=>'margin-bottom:1%'
				),   
		);
	}

   /*nuovo codice riferimenti temporali*/
  $form['riferimentiTemporali']['rigaprincipaleinizioRifTemporali'] = [
				'#type' => 'markup',  
				'#prefix' => '<div class="row" id="iniziorecordRifTemp"><div class="col-12" id="rigaprinok"><div class="row" id="rigaint"><div class="col-4"><h5>Data iniziale</h5></div><div class="col-4"><h5>Data finale</h5></div><div class="col-4"></div></div>',
		];

 	$vetestjo=json_decode($estensioniTemporali);
	$i=0;
	for($i=0;$i<count($vetestjo);$i++){
      $vetdata=explode("-",$vetestjo[$i]->temporal_start);
	    $vetdatastr=$vetdata[2]."-".$vetdata[1]."-".$vetdata[0];
      $id="estempinizio".$i;
      $form['riferimentiTemporali'][$id] = [
				'#attributes' => array(
					'style'=>'margin-top:1%',
          'name'=>'datainizio[]'
				), 
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#default_value' => [
           $vetestjo[$i]->temporal_start
          ],
				'#prefix' => '<div class="row"><div class="col-4">',
				'#suffix' => '</div>',
      ];
      $id="esttempfine".$i;
			$form['riferimentiTemporali'][$id] = [  
				'#attributes' => array(
					'style'=>'margin-top:1%',
          'name'=>'datafine[]',
				), 
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#default_value' => [
            $vetestjo[$i]->temporal_end
         ],
        '#prefix' => '<div class="col-4">',
				'#suffix' => '</div>',
      ];

 		  $id="idtempbtn".$i;
 			$form['riferimentiTemporali'][$id] = [
        '#type'  => 'button',
        '#value' => $this->t('Rimuovi'),
				'#prefix' => '<div class="col-4">',
				'#suffix' => '</div></div>',
				'#attributes' => array(
					'onclick'=>'return cancellaRiferimentoTemporale("edit-'.$id.'")'
				),
      ];
	}
  

    $form['riferimentiTemporali']['rigaprincipalefine'] = [
				'#type' => 'markup',  
				'#prefix' => '</div></div>',
		];
		
		$form['riferimentiTemporali']['aggiungiestensionetemporale'] = [
			'#type' => 'button',
			'#value' => t('Aggiungi estensione temporale'),
			'#weight' => 19,
		];

	  /*fine nuovo codice riferimenti temporali */

    $form['riferimentiGeografici'] = [
        '#title' => t('Riferimenti geografici'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];

		$options = array(
      'frequenza' => 'Copertura geografica',
      'ITA_AGR' => 'Agrigento',
      'ITA_AOI' => 'Ancona',
      'ITA_AOT' => 'Aosta',
      'ITA_BEK' => 'Brunico',
      'ITA_BEN' => 'Benevento',
      'ITA_BGO' => 'Bergamo',
      'ITA_BLQ' => 'Bologna',
      'ITA_BRI' => 'Bari',
      'ITA_BZO' => 'Bolzano',
      'ITA_CPU' => 'Capua',
      'ITA_CRA' => 'Carrara',
      'ITA_CST' => 'Caserta',
      'ITA_CTA' => 'Catania',
      'ITA_CUN' => 'Cuneo',
      'ITA_FLR' => 'Firenze',
      'ITA_FRR' => 'Ferrara',
      'ITA_GOA' => 'Genova',
      'ITA_GZA' => 'Gorizia',
      'ITA_ISR' => 'Ispra',
      'ITA_LIV' => 'Livorno',
      'ITA_LVE' => 'Laives',
      'ITA_MAN' => 'Mantova',
      'ITA_MEN' => 'Merano',
      'ITA_MGF' => 'Monguelfo-Tesido',
      'ITA_MIL' => 'Milano',
      'ITA_MOD' => 'Modena',
      'ITA_MSN' => 'Messina',
      'ITA_MTR' => 'Matera',
      'ITA_NAP' => 'Napoli',
      'ITA_NVR' => 'Novara',
      'ITA_OMO' => 'Como',
      'ITA_ORA' => 'Ora',
      'ITA_OTA' => 'Prato',
      'ITA_PAV' => 'Pavia',
      'ITA_PDA' => 'Padova',
      'ITA_PMF' => 'Parma',
      'ITA_PMO' => 'Palermo',
      'ITA_PMP' => 'Pompei',
      'ITA_PSR' => 'Pescara',
      'ITA_RAN' => 'Ravenna',
      'ITA_REG' => 'Reggio Calabria',
      'ITA_RGA' => 'Ragusa',
      'ITA_RMI' => 'Rimini',
      'ITA_RNE' => 'Reggio Emilia',
      'ITA_ROM' => 'Roma',
      'ITA_RVL' => 'Rivoli',
      'ITA_SAL' => 'Salerno',
      'ITA_SIR' => 'Siracusa',
      'ITA_SIT' => 'Ostia',
      'ITA_SNA' => 'Siena',
      'ITA_SRE' => 'Sanremo',
      'ITA_SVN' => 'Savona',
      'ITA_TAR' => 'Taranto',
      'ITA_TIV' => 'Tivoli',
      'ITA_TRN' => 'Torino',
      'ITA_TRS' => 'Trieste',
      'ITA_TRT' => 'Trento',
      'ITA_TRV' => 'Treviso',
      'ITA_VAR' => 'Varese',
      'ITA_VCE' => 'Venezia',
      'ITA_VEN' => 'Ventimiglia',
      'ITA_VRN' => 'Verona',
    );


    $form['riferimentiGeografici']['coperturaGeografica'] = array(
			'#type' => 'select',
			'#options' => $options,
			'#default_value' => $coperturaGeografica,
			'#attributes' => array(
					'style'=>'margin-bottom:1%'
				),   
		);

    $form['standards'] = [
        '#title' => t('conformità'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];

    $form['standards']['rigaprincipaleinizio'] = [
				'#type' => 'markup',  
				'#prefix' => '<div class="row" id="iniziorecordRifTemp"><div class="col-12" id="rigaPrincipaleConformita"><div class="row" id="rigaint"><div class="col-4"><h5>Titolo</h5></div><div class="col-4"><h5>Url</h5></div><div class="col-4"></div></div>',
		];
  
		
	for($i=0;$i<count($vetstandard);$i++){
       $id="idstandard1".$i;
       $id="idstandard2".$i;
			 $valore=$vetstandard[$i]["title"]["it"];
       $form['standards'][$id] = [
         '#attributes' => array(
           'style'=>'margin-top:2%',
           'name'=>'titolostandard[]'
         ),
         '#type' => 'textfield',
         '#default_value' => $valore,
         '#prefix' => '<div class="row"><div class="col-4">',
         '#suffix' => '</div>',
       ];

	     $id="idstandarduri".$i;
        $valore=$vetstandard[$i]["uri"];
        $form['standards'][$id] = [
					'#attributes' => array(
						'style'=>'margin-top:2%',
            'name'=>'urlstandard[]',
					), 
		    '#type' => 'textfield',  
				'#default_value' => $valore,
				'#prefix' => '<div class="col-4">',
				'#suffix' => '</div>',
		  ]; 

 		   $id="idstandard3".$i;
        $form['standards'][$id] = [
          '#type'  => 'button',
          '#value' => $this->t('Rimuovi'),
          '#prefix' => '<div class="col-4">',
          '#suffix' => '</div></div>',
          '#attributes' => array(
            'onclick'=>'return cancellaElementoStandard("edit-'.$id.'")'
          ),
        ];
	}

    $form['standards']['rigaprincipalefine'] = [
				'#type' => 'markup',  
				'#prefix' => '</div></div>',
		];
		
		$form['standards']['preview'] = [
			'#type' => 'button',
			'#value' => t('Aggiungi'),
			'#weight' => 19,
		];

    $form['informazioniSupplementari'] = [
        '#title' => t('Punti di contatto'),
			  '#type' => 'details',
			  '#open' => FALSE,
				'#attributes' => array(
					'style'=>'margin-bottom:3%'
				),   
    ];
   
    $form['informazioniSupplementari']['nomeContatto'] = [  
				'#attributes' => array(
					'style'=>'margin-top:0%'
				), 
      '#type' => 'textfield',  
      '#title' => $this->t('Nome'),  
			'#default_value' => $contattoNome,  
    ];

     $form['informazioniSupplementari']['emailContatto'] = [  
				'#attributes' => array(
					'style'=>'margin-top:0%'
				), 
      '#type' => 'textfield',  
      '#title' => $this->t('email'),  
			'#default_value' => $contattoEmail,  
    ];  
    

    $form['actions'] = [
        '#type'       => 'actions'
    ];

    $form['actions']['inviaDataset'] = [
        '#type'  => 'submit',
        '#value' => $this->t('Invia'),
				"#weight" => 1,
				'#attributes' => array(
					'style'=>'margin-bottom:3%;width:100%'
				),
    ];

		if($id != "support-request"){
			$form['ckanbase'] = [
		    '#type' => 'hidden',  
				'#default_value' => $ckan,
		    '#attributes' => array('readonly' => 'readonly'),    
		  ];  

		}

		if($id != "support-request"){
			$form['actions']['submitcancellad'] = [
		    '#type' => 'submit',
		    '#value' => $this->t('Cancella'),
		    "#weight" => 2,
		    '#submit' => array([$this, 'submitFormTwo']),
				'#attributes' => array(
						'style'=>'margin-bottom:3%;width:100%;background-color:#ff0000;'
					),
		  ];
		}

    return $form;
  }

	private function v4() {
		  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		    // 32 bits for "time_low"
		    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		    // 16 bits for "time_mid"
		    mt_rand(0, 0xffff),
		    // 16 bits for "time_hi_and_version",
		    // four most significant bits holds version number 4
		    mt_rand(0, 0x0fff) | 0x4000,
		    // 16 bits, 8 bits for "clk_seq_hi_res",
		    // 8 bits for "clk_seq_low",
		    // two most significant bits holds zero and one for variant DCE1.1
		    mt_rand(0, 0x3fff) | 0x8000,
		    // 48 bits for "node"
		    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		  );
	}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitFormTwo(array &$form, FormStateInterface $form_state){
        $nomeDataset=$form_state->getValue('idDatasetokNascosto');
        $ckankey=$form_state->getValue('ckankey');//ckankey
        $urlckan=$form_state->getValue('urlckan');
        $this->deletePackage($urlckan,$nomeDataset,$ckankey);
        $form_state->setRedirect('gestioneutenti.dataset');
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

		  $idutente=\Drupal::currentUser()->id();
			$istanzackan="";
			$operazione=$form_state->getValue('operazione');
			if($operazione==1){
					$istanzackan=$form_state->getValue('organizzazionedataset');
			}
			else if($operazione==2){
					$istanzackan=$form_state->getValue('idckan');
			}
      $dati=array();
      $dati[0]=$idutente;
			$dati[1]=$istanzackan;
      $ckanapi=$this->selezionaApiCkan($dati);
			$urlvar_ckan=$this->selezionaUrlCkan($istanzackan);
			$codiceipaiva="";
			$idOrg="";//recupero id organizzazione per dataset
			if($istanzackan==1){
			 			$jsonorg=$this->organizationShow($urlvar_ckan,$ckanapi,"dati-gov-it");
						$idOrg=json_decode($jsonorg)->result->id;
						$codiceipaiva=json_decode($jsonorg)->result->identifier;
			}
		  else if($istanzackan==2){
						$jsonorg=$this->organizationShow($urlvar_ckan,$ckanapi,"basi-gov-it");
						$idOrg=json_decode($jsonorg)->result->id;
						$codiceipaiva=json_decode($jsonorg)->result->identifier;
			}
			/*popolo array con i campi*/

      $nome=$form_state->getValue('titoloDataset');
			$nome=strtolower($nome);
      $nome=$this->sostituisci($nome);
      $nome=$this->compattaStringa("--",$nome,"-");
			$dati["name"]=$nome;
			$dati["title"]=$form_state->getValue('titoloDataset');
			$dati["coperturaGeografica"]=$form_state->getValue("coperturaGeografica"); 
			$dati["language"]=$form_state->getValue("linguaDataset");;
			$dati["frequency"]=$form_state->getValue('frequenzaAggiornamento');
			$dati["ckankey"]=$ckanapi;
			$dati["private"]=$form_state->getValue('visibilita');
			$dati["author"]=$form_state->getValue('nomeContatto'); 
			$dati["owner_org"]=$idOrg; 
			$dati["theme"]=$form_state->getValue('tag'); 
			$dati["notes"]=$form_state->getValue('textarea');
			$dati["identifier"]=$codiceipaiva.":".$this->v4();
			$dati['modified']=$form_state->getValue('dataModifica');
			$dati["nomeContatto"]=$form_state->getValue('nomeContatto');
			$dati["emailContatto"]=$form_state->getValue('emailContatto');
			$dati["nomeAutore"]=$form_state->getValue('emailContatto');
			$dati["codiceIPAIVAUTORE"]=$form_state->getValue('codiceIPAIVAUTORE');
			$dati["issued"]=$form_state->getValue('dataRilascio');
			$dati["version"]=$form_state->getValue('versione');
			$jsonstandard='';
			$jsontemp = [];
			$url="http://www.prova.it";
			$titolovet=array(); //popolo il campo conforms_to
			$titolovet=["titolo1","titolo2","titolo3"];
			$urlvet=["url1","url2","url3"];
			$vetstandard=$form_state->getValue('standard');
			$vettemp = \Drupal::request()->request->get('standard');
			$titoloStandard = \Drupal::request()->request->get('titolostandard');
			$titoloStandard = $_POST['titolostandard'];
			$uriStandard = $_POST['urlstandard'];
			$dataInizio = $_POST['datainizio'];
			$dataFine = $_POST['datafine'];
      /*ciclo sui record delle conformita*/
			if(count($titoloStandard) > 0){
							$i=0;
							for($i=0;$i<count($titoloStandard);$i++){
									//$jsonstandard.=conformea($titolovet[$i],$urlvet[$i]);
									$vet=array();
									$titolovalore=$titoloStandard[$i];
									$urlvalore=$uriStandard[$i];
									$vet["referenceDocumentation"]=[];
									$description=["fr" =>"","it" =>$titolovalore,"de" =>"","en" =>""];
									$vet["description"]=$description;
									$vet["identifier"]=$titolovalore;
									$vet["uri"]=$urlvalore;
									$titolo=["fr" =>"","it" =>$titolovalore,"de" =>"","en" =>""];
									$vet["title"]=$titolo;
									$jsontemp[] = $vet;
								}
							 $jsonstandard.= str_replace('\"', '"', json_encode($jsontemp) );

			} else {
				$jsonstandard.="[]";
			}
		  
			$dati["conforms_to"] = $jsonstandard;
			/*aggiungo i campi extras*/
			$extrasok=array();
			$item=array("key" => "source_catalog_description",  );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$item=array( );
			array_push($extrasok,$item);
			$dati["extras"]=$extrasok;
			$jsontemporal="";
			$jsontemporaltemp=[];
			if(count($dataInizio) > 0){
				$i=0;
				for($i=0;$i<count($dataInizio);$i++){
						//$jsonstandard.=conformea($titolovet[$i],$urlvet[$i]);
						$vet=array();
						$vet["temporal_start"]=$dataInizio[$i];
						$vet["temporal_end"]=$dataFine[$i];
						$jsontemporaltemp[] = $vet;
					}
				 $jsontemporal.=str_replace('\"', '"', json_encode($jsontemporaltemp) );

			} else {
				$jsontemporal.="[]";
			}
			$dati["temporal_coverage"]=$jsontemporal;
			$operazione=$form_state->getValue('operazione');
			if($operazione==1){
					$dati["urlckan"]=$urlvar_ckan."/api/3/action/package_create";
					$this->nuovoCreaDataset($dati);
			} else {
				$dati["id"]=$form_state->getValue('idDatasetokNascosto');
				$dati["urlckan"]=$urlvar_ckan."/api/3/action/package_update";
        $risultato=$this->aggiornaDataset($dati);
			}

    $form_state->setRedirect('gestioneutenti.dataset');
		
  }

  private function aggiornaDataset($dati){
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
		$dataOggi=date("d/m/Y");

		curl_setopt($ch, CURLOPT_URL,$dati["urlckan"]); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$json= json_encode($dati);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
		$param1='Authorization: '.$dati["ckankey"];
		$param2='X-CKAN-API-Key: '.$dati["ckankey"];
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec ($ch);
		curl_close ($ch);
}

  private function nuovoCreaDataset($dataset){
	
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
		curl_setopt($ch, CURLOPT_URL,$dataset["urlckan"]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$json= json_encode($dataset);

		curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
    $param1='Authorization: '.$dataset["ckankey"];
		$param2='X-CKAN-API-Key: '.$dataset["ckankey"];

		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		curl_setopt($ch, CURLOPT_URL,$dataset["urlckan"]); 

		$risultato = curl_exec ($ch);
    curl_close ($ch);
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

  private function compattaStringa($carattere,$stringa,$carattereConfronto){
	    $pos=strpos($stringa,$carattere,0);
      while(strlen($pos) > 0){
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
            }
        }
        $stringa=str_replace($sottostringa,"-",$stringa);
        $pos=strpos($stringa,$carattere,0);
        $sottostringa="";
    }
    return $stringa;
  }

  private function sostituisciTitolo($stringa){

  $stringa=str_replace('"', '\"', $stringa);
  $stringa=str_replace("'", '\'', $stringa);
  return $stringa;

}


  private function sostituisci($stringa){;
  $stringa=str_replace("£", "-", $stringa);
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

  public function creaDataset($dati){
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
		$dataOggi=date("d/m/Y");
                $titolo=$dati["nomeDataset"];
                $nome=strtolower($dati["nomeDataset"]);
		$url=$dati["urlckan"]."/api/3/action/package_create";

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$titolo=addslashes($dati["nomeDataset"]);
		$nome=$this->sostituisci($titolo);
		$pos=strpos($nome,"--",0);
		if($pos > 0)
		 $nome=$this->compattaStringa("--",$nome,"-");
		 $nome=strtolower($nome);
     $titolo=$dati["nomeDataset"];
     $titolo=$this->sostituisciTitolo($titolo);

     $titolovet=$dati["standard"]["titolo"];
     $urlvet=$dati["standard"]["url"];
     $jsonstandard='"conforms_to":"[';
    if(count($titolovet) > 0){
      $i=0;
      for($i=0;$i<count($titolovet);$i++){
        $jsonstandard.='{\"referenceDocumentation\": \"[]\"},';
        $jsonstandard.='\"identifier\": \"'.$titolovet[$i].'\",';
        $jsonstandard.='\"description\": {\"fr\":\"'.$urlvet[$i].'\",\"de\":\"'.$urlvet[$i].'\",\"en\":\"'.$urlvet[$i].'\",\"it\":\"'.$urlvet[$i].'\"},';
        $jsonstandard.='\"title\": {\"fr\":\"'.$titolovet[$i].'\",\"de\":\"'.$titolovet[$i].'\",\"en\":\"'.$titolovet[$i].'\",\"it\":\"'.$titolovet[$i].'\"}},';
      }
      $jsonstandard=substr($jsonstandard,0,strlen($jsonstandard)-1).']"';
    } else {
        $jsonstandard='"conforms_to":[]';
    }
    $json='{"name":"'.$nome.'","title":"'.$nome.'","geographical_name":"'.$dati["coperturaGeografica"].'","private":false,"language":"'.$dati["lingua"].'","frequency":"'.$dati["frequenzaAggiornamento"].'","private":"'.$dati["visibilitaDataset"].'","author":"'.$dati["autore"].'","owner_org":"'.$dati["idOrgDataset"].'","theme":"'.$dati["theme"].'","notes":"'.$dati["note"].'","identifier":"'.$dati["identificativoDataset"].'","modified":"'.$dati['dataModifica'].'","extras":[{"key": "source_catalog_description","value": "Il Portale dati.gov.it, gestito dall’Agenzia per Italia digitale, è il catalogo nazionale dei metadati relativi ai dati rilasciati in formato aperto dalle pubbliche amministrazioni italiane."},	{
            "key": "source_catalog_homepage",
            "value": "https://dati.gov.it"
            },
            {
            "key": "source_catalog_language",
            "value": "http://publications.europa.eu/resource/authority/language/ITA"
            },
            {
            "key": "contact_name",
            "value": "'.$dati["nomeContatto"].'"
            },
            {
            "key": "contact_mail",
            "value": "'.$dati["emailContatto"].'"
            },
            {
            "key": "source_catalog_modified",
            "value": "2020-04-23"
            },
            {
            "key": "source_catalog_title",
            "value": "dati.gov.it - Catalogo dei dati aperti della Pubblica Amministrazione"
            },
            {
            "key": "source_catalog_publisher",
            "value": "{\"url\": \"\", \"email\": \"\", \"type\": \"\", \"uri\": \"https://indicepa.gov.it/ricerca/n-dettaglioamministrazione.php?cod_amm=agid\", \"name\": \"Agenzia per l Italia Digitale\"}"
            }],"publisher_name":"'.$dati["nomeAutore"].'","publisher_identifier":"'.$dati["codiceIPAIVAUTORE"].'",';
    $json.='"issued":"'.$dati["dataRilascio"].'","version":"'.$dati["versione"].'",'.$jsonstandard.'}';


    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

		$param1='Authorization: '.$dati["ckankey"];
		$param2='X-CKAN-API-Key: '.$dati["ckankey"];

		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	
		$risultato = curl_exec ($ch);

		curl_close ($ch);
}

  private function ritornaIddataset($ckanurl,$package,$ckanapi){
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
		$url=$ckanurl."/api/3/action/package_show";
		curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec ($ch);
		$jo=json_decode($risultato);
		$organizzazione=$jo->result->organization->name;
		$id=$jo->result->identifier;
		return $id;

 }

  private function ritornaIdOrg($ckanurl,$package,$ckanapi){
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
		$url=$ckanurl."/api/3/action/package_show";
		curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec ($ch);
		$jo=json_decode($risultato);
		$organizzazione=$jo->result->organization->name;
		$id=$jo->result->organization->id;
		return $id;

 }

  private function organizationShow($urlckan,$ckanapi,$id){
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
				"id" =>	$id,
		];		
		$url=$urlckan."/api/3/action/organization_show";
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec ($ch);
		curl_close ($ch);
		return $risultato;
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
			return $risultato;
	}

	private function dettaglioDataset($urlckan,$idDataset,$username,$ckanapi){

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
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;

		$post = [
				"id" => $idDataset
		];
		$url=$urlckan."/api/3/action/package_show";

		curl_setopt($ch, CURLOPT_URL,$url);
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec ($ch);
		curl_close ($ch);

		return $risultato;
	}

  private function organizzationListForUser($username,$ckanapi){
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
				"id" =>	$username,
				"permission" => "create_dataset",			
		];		
		
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_URL,"http://italia-ckan-it:5000/api/3/action/organization_list_for_user"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	
		$risultato = curl_exec($ch);
		curl_close($ch);
		return $risultato;
}

  private function deletePackage($urlckan,$idPackage,$ckanapi){

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
				"id" =>	$idPackage,
		];
		$url=$urlckan."/api/3/action/package_delete";
		$param1='Authorization: '.$ckanapi;
		$param2='X-CKAN-API-Key: '.$ckanapi;
		$headers1=array($param1, $param2);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		$risultato = curl_exec ($ch);
		curl_close ($ch);
}


}
