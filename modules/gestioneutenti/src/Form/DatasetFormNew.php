<?php


namespace Drupal\gestioneutenti\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\taxonomy\Entity\Term;

class DatasetFormNew extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'dataset_form_new';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state,$idCkan=null,$idDataset=null) {


      $new = false;
      $label = 'Modifica Dataset (versione beta)';
      $arrayDataset = [];

      if ($idDataset === 'new' || $idDataset === null) {
          $new = TRUE;
          $label = 'Creazione Nuovo Dataset (versione beta)';
      }

      $form['#prefix'] = '<div class="container mb-5"> <div class="row mt-5 mb-5"><div class="col 12 d-flex justify-content-center"> <h1>'.$label.'</h1></div></div><div id="myAlert"></div>';
      $form['#suffix'] = '</div>';

      //Select
      $optionsVisibilita = [
        '' => 'Visibilità *',
        'True' => 'Privato',
        'False' => 'Pubblico',
      ];

      $optionsLingua = [
        'ITA' => 'Italiano',
        'DEU' => 'Tedesco',
        'ENG' => 'Inglese',
        'FRA' => 'Francese',
        'SPA' => 'Spagnolo',
        //'' => 'lingua',
      ];

      $optionsTemi = $this->getTemi();

      $optionsFrequenza = [
        'default' => 'Seleziona frequenza aggiornamento *',
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
        'OP_DATPRO' => 'dati provvisori',
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
      ];

      $optionsGeografico = [
        '' => 'Copertura geografica',
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
      ];
      $optionsLicenza = $this->selezionaLicenze();
      $optionsLicenza[''] = 'Seleziona licenza - nome *';
      ksort($optionsLicenza);
//      $optionsLicenza = [
//      '' => 'Seleziona licenza - nome *',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A1_PublicDomain' => 'Pubblico Dominio',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A11_CCO10' => 'Creative Commons CC0 1.0 Universale - Public Domain Dedication (CC0 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A12_PDDL' => 'Seleziona tipo licenza',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A2_Attribution' => 'Attribuzione',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A21_CCBY40' => 'Creative Commons Attribuzione 4.0 Internazionale (CC BY 4.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A210_ODCBY' => 'Open Data Commons Attribution License (ODC_BY)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A22_CCBY30' => 'Creative Commons Attribuzione 3.0 Unported (CC BY 3.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A23_CCBY30IT' => 'Creative Commons Attribuzione Italia 3.0 (CC BY 3.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A24_CCBY25' => 'Creative Commons Attribuzione 2.5 Generica (CC BY 2.5)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A25_CCBY25IT' => 'Seleziona tipo licenza',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A26_CCBY20' => 'Creative Commons Attribuzione 2.0 Generica (CC BY 2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A27_CCBY20IT' => 'Creative Commons Attribuzione 2.0 Italia (CC BY 2.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A28_CCBY10' => 'Creative Commons Attribuzione 1.0 Generica (CC BY 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A29_IODL20' => 'Italian Open Data License 2.0 (IODL 2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A3_ShareAlike' => 'Effetto Virale (aka Condivisione allo stesso modo)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A31_CCBYSA40' => 'Creative Commons Attribuzione-Condividi allo stesso modo 4.0 Internazionale (CC BY-SA 4.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A310_ODBL' => 'Open Data Commons Open Database License 1.0 (ODbL)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A311_GFDL13' => 'GNU Free Documentation License 1.3 (GFDL 1.3)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A32_CCBYSA30' => 'Creative Commons Attribuzione-Condividi allo stesso modo 3.0 Unported (CC BY-SA 3.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A33_CCBYSA30IT' => 'Creative Commons Attribuzione-Condividi allo stesso modo 3.0 Italia (CC BY-SA 3.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A34_CCBYSA25' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Generica (CC BY-SA 2.5)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A35_CCBYSA25IT' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia (CC BY-SA 2.5 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A36_CCBYSA20' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.0 Generica (CC BY-SA 2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A37_CCBYSA20IT' => 'Creative Commons Attribuzione-Condividi allo stesso modo 2.0 Italia (CC BY-SA 2.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A38_CCBYSA10' => 'Creative Commons Attribuzione-Condividi allo stesso modo 1.0 Generica (CC BY-SA 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A39_IODL10' => 'Italian Open Data License 1.0 (IODL 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A4_ShareAlikeCopyleftNonComp' => 'Condivisione allo stesso modo / copyleft non compatibile',
//      'https://w3id.org/italia/controlled-vocabulary/licences/A41_ADRM' => 'Against DRM (2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B1_NonCommercial' => 'Solo Uso non Commerciale',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B11_CCBYNC40' => 'Creative Commons Attribuzione-Non Commerciale 4.0 Internazionale (CC BY-NC 4.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B110_CCBYNCSA30' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 3.0 Unported (CC BY-NC-SA 3.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B111_CCBYNCSA30IT' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 3.0 Italia (CC BY-NC-SA 3.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B112_CCBYNCSA25' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.5 Generica (CC BY-NC-SA 2.5)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B113_CCBYNCSA25IT' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.5 Italia (CC BY-NC-SA 2.5 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B114_CCBYNCSA20' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.0 Generica (CC BY-NC-SA 2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B115_CCBYNCSA20IT' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 2.0 Italia (CC BY-NC-SA 2.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B116_CCBYNCSA10' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 1.0 Generica (CC BY-NC-SA 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B12_CCBYNC30' => 'Creative Commons Attribuzione-Non Commerciale 3.0 Unported (CC BY-NC 3.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B13_CCBYNC30IT' => 'Creative Commons Attribuzione-Non Commerciale 3.0 Italia (CC BY-NC 3.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B14_CCBYNC25' => 'Creative Commons Attribuzione-Non Commerciale 2.5 Generica (CC BY-NC 2.5)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B15_CCBYNC25IT' => 'Creative Commons Attribuzione-Non Commerciale 2.5 Italia (CC BY-NC 2.5 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B16_CCBYNC20' => 'Creative Commons Attribuzione-Non Commerciale 2.0 Generica (CC BY-NC 2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B17_CCBYNC20IT' => 'Creative Commons Attribuzione-Non Commerciale 2.0 Italia (CC BY-NC 2.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B18_CCBYNC10' => 'Creative Commons Attribuzione-Non Commerciale 1.0 Generica (CC BY-NC 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B19_CCBYNCSA40' => 'Creative Commons Attribuzione-Non Commerciale-Condividi allo stesso modo 4.0 Internazionale (CC BY-NC-SA 4.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B2_NoDerivs' => 'Non Opere Derivate',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B21_CCBYND40' => 'Creative Commons Attribuzione-Non Opere Derivate 4.0 Internazionale (CC BY-ND 4.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B22_CCBYND30' => 'Creative Commons Attribuzione-Non Opere Derivate 3.0 Unported (CC BY-ND 3.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B23_CCBYND30IT' => 'Creative Commons Attribuzione-Non Opere Derivate 3.0 Italia (CC BY-ND 3.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B24_CCBYND25' => 'Creative Commons Attribuzione-Non Opere Derivate 2.5 Generica (CC BY-ND 2.5)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B25_CCBYND25IT' => 'Creative Commons Attribuzione-Non Opere Derivate 2.5 Italia (CC BY-ND 2.5 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B26_CCBYND20' => 'Creative Commons Attribuzione-Non Opere Derivate 2.0 Generica (CC BY-ND 2.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B27_CCBYND20IT' => 'Creative Commons Attribuzione-Non Opere Derivate 2.0 Italia (CC BY-ND 2.0 IT)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B28_CCBYND10' => 'Creative Commons Attribuzione-Non Opere Derivate 1.0 Generica (CC BY-ND 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/B29_CCBYNDNC10' => 'Creative Commons Attribuzione-Non Opere Derivate-Non Commerciale 1.0 Generica (CC BY-ND-NC 1.0)',
//      'https://w3id.org/italia/controlled-vocabulary/licences/C1_Unknown' => 'Licenza Sconosciuta',
//      'https://w3id.org/italia/controlled-vocabulary/licences/C11_Unknown' => 'Licenza Sconosciuta'
//    ];

      /* End Select */

    /* usort($optionsFrequenza, function($a, $b) { return $a <=> $b; }); */

      if ($new) {
        $selectIdCkan = [];
        $defValue = 0;
        $query = \Drupal::database()->select('organizzazione', 'o');
        $query->fields('o');
        $query->condition('o.idUtente', \Drupal::currentUser()->id(), '=');
        $result = $query->execute();
        $riga = $result->fetchAssoc();
        if ($riga["flagDatigov"] === '1') {
          //  $selectIdCkan[1] = "Dati.gov.it";
        }
        if ($riga["flagBasigov"] === '1') {
            $selectIdCkan[2] = "Basi di dati";
        }
        if (count($selectIdCkan) === 1) {
          foreach ($selectIdCkan as $key => $value){
            $defValue = $key;
          }
        }
        $uuid = $riga['codiceIPAIVA'] . ':' . $this->v4();

        $selectIdCkan[0] = '* Seleziona il catalogo';

        $form['croupCkan'] = [
          '#prefix' => '<div class="row mt-5">',
          '#suffix' => '</div>'
        ];

$form['croupCkan']['idCkan'] = [
          '#type' => 'hidden',
          '#default_value' => $defValue,
        ];

/*
        $form['croupCkan']['idCkan'] = [
          '#type' => 'select',
          '#options' => $selectIdCkan,
          '#default_value' => $defValue,
          '#title' => $this->t('* Seleziona il catalogo'),
          '#prefix' => '<div class="col-12 pl-4 pr-4 mt-4">',
          '#suffix' => '</div>',
        ];*/
        $form['operation'] = [
          '#type' => 'hidden',
          '#default_value' => 1,
        ];
    } else {
        $form['idCkan'] = [
          '#type' => 'hidden',
          '#default_value' => $idCkan,
        ];
        $form['operation'] = [
          '#type' => 'hidden',
          '#default_value' => 2,
        ];
        $dataset = $this->dettaglioDataset($idCkan,$idDataset);

        $uuid = $dataset->identifier;
        //DT
        if ($dataset->modified !== '') {
          $arrayDataset['dataModifica'] = date('Y-m-d', strtotime($dataset->modified));
        }
        if ($dataset->issued !== '') {
          $arrayDataset['dataRilascio'] = date('Y-m-d', strtotime($dataset->issued));
        }
        //
        $form['name'] = [
          '#type' => 'hidden',
          '#default_value' => $dataset->name,
        ];
        //--------------------------------------------------
        $arrayDataset['versione'] = $dataset->version ?? '';
        $arrayDataset['frequenza'] =  $dataset->frequency ?? '';
        $arrayDataset['soggetti_nome'] =  $dataset->publisher_name ?? '';
        $arrayDataset['soggetti_codice'] = $dataset->publisher_identifier ?? '';
        $arrayDataset['coperturaGeografica'] = $dataset->geographical_name ?? '';
        $arrayDataset['urlGeografico'] = $dataset->geographical_geonames_url ?? '';
        $arrayDataset['visibilita'] = $dataset->private ? 'True' : 'False';
        $arrayDataset['licenzaRisorsa'] = $dataset->license_id ?? '';
//        $arrayDataset['uri'] = $dataset->uri ?? '';
        $arrayDataset['pagina'] = $dataset->url ?? '';

        $arrayDataset['nomeContatto'] = $dataset->private ?? '';
        $arrayDataset['emailContatto'] = $dataset->private ?? '';
        foreach ($dataset->extras as $value){
          if($value->key === "contact_name"){
            $arrayDataset['nomeContatto']=$value->value;
          }
          if($value->key === "contact_email"){
            $arrayDataset['emailContatto']=$value->value;
          }
          if($value->key === "uri"){
            $arrayDataset['uri']=$value->value;
          }
          if($value->key === "regione"){
            $arrayDataset['regione']=$value->value;
          }
          if($value->key === "provincia"){
            $arrayDataset['provincia']=$value->value;
          }
          if($value->key === "comune"){
            $arrayDataset['comune']=$value->value;
          }
        }
        $arrayDataset['titolo']=$dataset->title;
        $arrayDataset['note']=$dataset->notes;
//        $arrayDataset['autore_nome'] = $dataset->creator ?? '';
//        $arrayDataset['autore_codice'] = $dataset->;
        //Tags
        foreach ($dataset->tags as $value){
            if($value->state === 'active'){
                $arrayDataset['tags'][$value->display_name] = $value->display_name;
            }
        }
        //Temi
        $temitmp = json_decode($dataset->theme);
        foreach ($temitmp as $value){
          $arrayTemi['tema'] = $value->theme;
//          $this->getIdTema($value['sottotema'])
          foreach ($value->subthemes as $subthemes){
            $arrayTemi['sottotema'][] = $this->getIdTema($subthemes);
          }
          $arrayDataset['tema'][] = $arrayTemi;
        }

        $arrayDataset['lingua'] = $dataset->language;

        $extTemporare = json_decode($dataset->temporal_coverage);

        foreach ($extTemporare as $value){
            $array['start'] = $value->temporal_start ?? '';
            $array['end'] = $value->temporal_end ?? '';
            $arrayDataset['extTemporale'][] = $array;
        }
        unset($array);
        $conforms = json_decode($dataset->conforms_to);
        foreach ($conforms as $value){
          $array['url'] = $value->uri ?? '';
          $array['title'] = $value->title->it;
          $arrayDataset['conforms'][] = $array;
        }
        $creatorArray = json_decode($dataset->creator);
        $arrayDataset['autore_nome'] = '';
        $arrayDataset['autore_codice'] = '';
        foreach ($creatorArray as $value){
          $arrayDataset['autore_codice'] = $value->creator_identifier;
          $arrayDataset['autore_nome'] = $value->creator_name->it;
        }
      }
      $form['generale'] = [
        '#prefix' => '<div class="row mt-4"><div class="col 12 pl-4"><h4>Informazioni generali</h4></div>',
        '#suffix' => '</div>'
      ];

      //titolo
       $form['generale']['titolo'] = [
      //  '#attributes' => $new ? [] : ['readonly' => 'readonly'],
        '#type' => 'textfield',
        '#title' => $this->t('Titolo *'),
        '#default_value' => $arrayDataset['titolo'] ?? '',
        '#prefix' => '<div class="col-12 mt-3 pl-4 pr-4">',
        '#suffix' => '</div>',
      ];
      
      //identificativo del dataset

      $form['uuid'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Identificativo del dataset'),
        '#default_value' => $uuid,
        '#attributes' => ['readonly' => 'readonly'],
        '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4 mt-4">',
        '#suffix' => '</div></div>',
      ];

       /** 
       * SOGGETTI
      */
      $form['soggetti'] = [
        '#prefix' => '<div class="row mb-3 pr-4 pl-4"><div class="col-12"><h4>Soggetti</h4></div>',
        '#suffix' => '</div>',
      ];

      // Toggle mostra di più
      $form['soggetti']['toggle_me'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Altri campi opzionali'),
        '#prefix' => '<div class="col-12">',
        '#suffix' => '</div>',
      );

      // Input soggetti_nome
      $form['soggetti']['soggetti_nome'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Editore'),
        //'#placeholder' => $this->t('Editore'),
        '#default_value' => $arrayDataset['soggetti_nome'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 mt-3 col-md-6">',
        '#suffix' => '</div>',
        '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_me"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];

      // Input soggetti_codice
      $form['soggetti']['soggetti_codice'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Editore - Codice IPA/P. IVA'),
        '#default_value' =>  $arrayDataset['soggetti_codice'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 mt-3 col-md-6">',
        '#suffix' => '</div>',
        '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_me"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];

      $form['soggetti']['autore_nome'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Autore'),
        '#default_value' => $arrayDataset['autore_nome'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
        '#suffix' => '</div>',
        '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_me"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];

      $form['soggetti']['autore_codice'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Autore - Codice IPA/P. IVA'),
        '#default_value' =>  $arrayDataset['autore_codice'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
        '#suffix' => '</div>',
        '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_me"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];
      // Descrizione
      $form['note'] = [
        '#rows' => 5,
        '#cols' => 60,
        '#resizable' => TRUE,
        '#type' => 'textarea',
        '#title' => $this->t('Descrizione *'),
        '#default_value' => $arrayDataset['note'] ?? '',
        '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
      ];

      
      //inizio Temi
      $form['temiTitle'] = [
        '#prefix' => '<div id="temiTitle" class="row mb-3"><div class="col 12 pl-4"><h4>Temi *</h4></div>',
        '#suffix' => '</div>'
      ];

      //Temix
      $idTemi = 0;

      $form['temi'] = [
        '#prefix' => '<div id="temi">',
        '#suffix' => '</div> '
      ];

      foreach ($arrayDataset['tema'] as $value){
        $idTema = $this->getIdTema($value['tema']);
        $optionsSottotema = $this->getSottotemi($idTema);
        $form['temi']['tema-' . $idTemi] = [
          '#attributes' => [
            'name'=>'temi['.$idTemi.'][tema]',
            'onchange' => 'chengTemi('.$idTemi.');',
          ],
          '#type' => 'select',
          '#title' => $this->t('Tema'),
          '#options' => $optionsTemi,
          '#default_value' => (int)$idTema,
          '#prefix' => '<div id="div-row-temi-'.$idTemi.'" class="row mt-5 pl-4 pr-4"><div class="col-12 col-lg-5 col-md-5">',
          '#suffix' => '</div>'
        ];

        $form['temi']['sotto-' . $idTemi] = [
          '#attributes' => [
            'name'=>'temi['.$idTemi.'][sotto][]',
            'multiple' => 'true',
          ],
          '#type' => 'select',
          '#title' => $this->t('Sottotema'),
          '#options' => $optionsSottotema,
          '#default_value' => $value['sottotema'],
          '#prefix' => '<div id="div-sotto-'. $idTemi .'" class="col-12 col-lg-5 col-md-5">',
          '#suffix' => '</div>'
        ];
        $form['temi']['deleteTema-' . $idTemi] = [
          '#type'  => 'button',
          '#value' => $this->t('Rimuovi'),
          '#prefix' => '<div class="col-12 col-lg-2 col-md-2">',
          '#suffix' => '</div></div>',
          '#attributes' => [
            'onclick'=> 'deleteTemi('.$idTemi.')',
          ],
        ];
        $idTemi++;
      }

      $form['temi']['tema-' . $idTemi] = [
        '#attributes' => [
          'name'=>'temi['.$idTemi.'][tema]',
          'onchange' => 'chengTemi('.$idTemi.');',
        ],
        '#type' => 'select',
        '#title' => $this->t('Tema'),
        '#options' => $optionsTemi,
        '#default_value' => '',
        '#prefix' => '<div id="div-row-temi-'.$idTemi.'" class="row mt-5 pl-4 pr-4"><div class="col-12 col-lg-5 col-md-5">',
        '#suffix' => '</div>'
      ];

      $form['temi']['sotto-' . $idTemi] = [
        '#attributes' => [
          'name'=>'temi['.$idTemi.'][sotto][]',
          'disabled ' => 'disabled',
          'multiple' => 'true',
        ],
        '#type' => 'select',
        '#title' => $this->t('Sottotema'),
        '#options' => [],
        '#default_value' => '',
        '#prefix' => '<div id="div-sotto-'. $idTemi .'" class="col-12 col-lg-5 col-md-5">',
        '#suffix' => '</div>'
      ];

      $form['temi']['deleteTema-' . $idTemi] = [
        '#type'  => 'button',
        '#value' => $this->t('Rimuovi'),
        '#prefix' => '<div class="col-12 col-lg-2 col-md-2">',
        '#suffix' => '</div></div>',
        '#attributes' => [
          'onclick'=> 'deleteTemi('.$idTemi.')',
        ],
      ];

      $form['temiAdesso'] = [
        '#attributes' => [
          'id' => 'temiAdesso'
        ],
        '#type' => 'hidden',
        '#default_value' => $idTemi,
      ];

      $form['temiTotale'] = [
        '#attributes' => [
          'id' => 'temiTotale'
        ],
        '#type' => 'hidden',
        '#default_value' => $idTemi,
      ];

      $form['addTema'] = [
        '#type'  => 'button',
        '#value' => $this->t('Aggiungi Tema'),
        '#prefix' => '<div class="row mt-5 pl-4 pr-4"><div class="col-12 col-lg-2 col-md-2">',
        '#suffix' => '</div></div>',
        '#attributes' => [
          'id' => 'addTemaButton',
//          'onClick'=> 'return addTemi()',
        ],
      ];
      //End Temi

      //parole chiave
      $form['tags'] = [
        '#type' => 'select',
        '#title' => $this->t('Parole chiave'),
        '#options' => $arrayDataset['tags'] ?? [],
        '#multiple' => TRUE,
        '#attributes' => [
          'data-role' => "tagsinput",
        ],
        '#prefix' => '<div class="row mt-5"><div class="col-12 pl-4 pr-4 mb-4 ml-2">',
        '#suffix' => '</div></div>',
      ];

         /**
       * RIFERIMENTI TEMPORALI
       */
      $form['riferimenti'] = [
        '#prefix' => '<div class="row pr-4 pl-4"><div class="col-12 mb-4"><h4>Riferimenti temporali</h4></div>',
        '#suffix' => '</div>'
      ];

      $form['riferimenti']['dataRilascio'] = [
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#title' => $this->t('Data rilascio gg/mm/aaaa'),
        '#default_value' => $arrayDataset['dataRilascio'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
        '#suffix' => '</div>'
      ];

      $form['riferimenti']['dataModifica'] = [
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#title' => $this->t('Ultima modifica gg/mm/aaaa *'),
        '#default_value' => $arrayDataset['dataModifica'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
        '#suffix' => '</div>'
      ];

      $form['frequenza'] = [
        '#type' => 'select',
        '#options' => $optionsFrequenza,
        //'#options' => ['value_1' => 'Value 1'],
        '#title' => $this->t('Frequenza di aggiornamento *'),
              '#default_value' => $arrayDataset['frequenza'] ?? '',
              '#prefix' => '<div class="row pl-4 pr-4"><div class="col-12">',
              '#suffix' => '</div></div>',
        '#sort_options' => 'asc',
      ];

      //riferimenti temporali
      $idRif = 0;

      //Punto di contatto
      $form['contato'] = [
        '#prefix' => '<div class="row pr-4 pl-4 mt-3"><div class="col-12 mt-4"><h4>Punto di contatto</h4></div>',
        '#suffix' => '</div>'
      ];

      $form['contato']['toggle_contato'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Altri campi opzionali'),
        '#prefix' => '<div class="col-12 mb-3">',
        '#suffix' => '</div>',
      );

    $form['contato']['nomeContatto'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nome'),
      '#default_value' => $arrayDataset['nomeContatto'] ?? '',
      '#states' => array(
        // Only show this field when the 'toggle_rif' checkbox is enabled.
        'visible' => array(
          ':input[name="toggle_contato"]' => array(
            'checked' => TRUE,
          ),
        ),
      ),
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
      '#suffix' => '</div>',
    ];

    $form['contato']['emailContatto'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => $arrayDataset['emailContatto'] ?? '',
      '#states' => array(
        // Only show this field when the 'toggle_rif' checkbox is enabled.
        'visible' => array(
          ':input[name="toggle_contato"]' => array(
            'checked' => TRUE,
          ),
        ),
      ),
      '#required' => FALSE,
      '#prefix' => '<div class="col-12 col-lg-6 col-md-6">',
      '#suffix' => '</div>',
    ];

    //Estensione temporale
    
      /**
       * ESTENSIONE TEMPORALE
       */
      $form['rifTitle'] = [
        '#prefix' => '<div class="row pr-4 pl-4 mt-3"><div class="col-12"><h4>Estensione temporale</h4></div>',
        '#suffix' => '</div>'
      ];

      $form['rif'] = [
        '#prefix' => '<div id="temp">',
        '#suffix' => '</div>'
      ];
      
      $form['rif']['toggle_rif'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Altri campi opzionali'),
        '#prefix' => '<div class="col-12 px-4">',
        '#suffix' => '</div>',
      );
      
      
      foreach ($arrayDataset['extTemporale'] as $value){
        $form['rif']['fir-dt-ini-' . $idRif] = [
          '#title' => $this->t('Data iniziale gg/mm/aaaa'),
          '#attributes' => [
            'name'=>'ext['.$idRif.'][datainizio]'
          ],
          '#type' => 'date',
          '#date_date_format' => 'Y/m/d',
          '#prefix' => '<div id="div-row-temp-'.$idRif.'" class="row mt-5 pl-4 pr-4"><div class="col-12 col-lg-5 col-md-5">',
          '#suffix' => '</div>',
          '#default_value' => $value['start'],
          '#states' => array(
            // Only show this field when the 'toggle_rif' checkbox is enabled.
            'visible' => array(
              ':input[name="toggle_rif"]' => array(
                'checked' => TRUE,
              ),
            ),
          ),
        ];

        $form['rif']['fir-dt-fin-' . $idRif] = [
          '#title' => $this->t('Data finale gg/mm/aaaa'),
          '#attributes' => [
            'name'=>'ext['.$idRif.'][datafine]'
          ],
          '#type' => 'date',
          '#date_date_format' => 'Y/m/d',
          '#prefix' => '<div class="col-12 col-lg-5 col-md-5">',
          '#suffix' => '</div>',
          '#default_value' => $value['end'],
          '#states' => array(
            // Only show this field when the 'toggle_rif' checkbox is enabled.
            'visible' => array(
              ':input[name="toggle_rif"]' => array(
                'checked' => TRUE,
              ),
            ),
          ),
        ];

        $form['rif']['fir-del-' . $idRif] = [
          '#type'  => 'button',
          '#value' => $this->t('Rimuovi'),
          '#prefix' => '<div class="col-12 col-lg-2 col-md-2">',
          '#suffix' => '</div></div>',
          '#attributes' => array(
            'onclick'=> 'return delTemp('.$idRif.')',
          ),
          '#states' => array(
            // Only show this field when the 'toggle_rif' checkbox is enabled.
            'visible' => array(
              ':input[name="toggle_rif"]' => array(
                'checked' => TRUE,
              ),
            ),
          ),
        ];

        $idRif++;
      }

      $form['rif']['fir-dt-ini-' . $idRif] = [
        '#title' => $this->t('Data iniziale gg/mm/aaaa'),
        '#attributes' => [
          'name'=>'ext['.$idRif.'][datainizio]'
        ],
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#states' => array(
          // Only show this field when the 'toggle_rif' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_rif"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
        '#prefix' => '<div id="div-row-temp-'.$idRif.'" class="row mt-5 pl-4 pr-4"><div class="col-12 col-lg-5 col-md-5">',
        '#suffix' => '</div>',
      ];

      $form['rif']['fir-dt-fin-' . $idRif] = [
        '#title' => $this->t('Data finale gg/mm/aaaa'),
        '#attributes' => [
          'name'=>'ext['.$idRif.'][datafine]'
        ],
        '#type' => 'date',
        '#date_date_format' => 'Y/m/d',
        '#states' => array(
          // Only show this field when the 'toggle_rif' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_rif"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
        '#prefix' => '<div class="col-12 col-lg-5 col-md-5">',
        '#suffix' => '</div>',
      ];

      $form['rif']['fir-del-' . $idRif] = [
        '#type'  => 'button',
        '#value' => $this->t('Rimuovi'),
        '#prefix' => '<div class="col-12 col-lg-2 col-md-2">',
        '#suffix' => '</div></div>',
        '#attributes' => array(
          'onclick'=> 'return delTemp('.$idRif.')',
        ),
        '#states' => array(
          // Only show this field when the 'toggle_rif' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_rif"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];

      $form['addRif'] = [
        '#type'  => 'button',
        '#value' => $this->t('Aggiungi estensione temporale'),
        '#prefix' => '<div class="row pl-4 pr-4"><div class="col-12 col-lg-4 col-md-4">',
        '#suffix' => '</div></div>',
        '#attributes' => array(
          'onclick'=> ' return addTemp();',
        ),
        '#states' => array(
          // Only show this field when the 'toggle_rif' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_rif"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];

      $form['tempAdesso'] = [
        '#attributes' => [
          'id' => 'tempAdesso'
        ],
        '#type' => 'hidden',
        '#default_value' => $idRif,
      ];

      $form['tempTotale'] = [
        '#attributes' => [
          'id' => 'tempTotale'
        ],
        '#type' => 'hidden',
        '#default_value' => $idRif,
      ];

      // End

      //riferimenti geografici
         $form['geografici'] = [
        '#prefix' => '<div class="row pr-4 pl-4 mt-3"><div class="col-12 mt-4"><h4>Riferimenti geografici</h4></div>',
        '#suffix' => '</div>'
      ];

       $form['geografici']['toggle_me'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Altri campi opzionali'),
        '#prefix' => '<div class="col-12">',
        '#suffix' => '</div>',
      );

//      $form['geografici']['coperturaGeografica'] = [
//        '#type' => 'select',
//        '#options' => $optionsGeografico,
//        '#default_value' => $arrayDataset['coperturaGeografica'] ?? '',
//        '#prefix' => '<div class="col-12">',
//        '#suffix' => '</div>',
//      ];

      $form['geografici']['urlGeografico'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Url geografico'),
        '#default_value' => $arrayDataset['urlGeografico'] ?? '',
        '#prefix' => '<div class="col-12 mt-3">',
        '#suffix' => '</div>',
         '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_me"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];

      $form['generale1row'] = [
        '#prefix' => '<div class="row">',
        '#suffix' => '</div>'
      ];

       //Lingua
        $form['generale1row']['lingua'] = [
        '#type' => 'select',
        '#title' => $this->t('Lingua'),
        '#options' => $optionsLingua,
        '#default_value' => $arrayDataset['lingua'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mt-4 pl-4 pr-4">',
        '#suffix' => '</div>',
      ];
        //Versione
        $form['generale1row']['versione'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Versione'),
          '#default_value' => $arrayDataset['versione'] ?? '',
          '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mt-4 pl-4 pr-4">',
          '#suffix' => '</div>',
        ];
        
        //blocco conformità
        $idConfor = 0;

        $form['con'] = [
          '#prefix' => '<div id="con"><div class="row pr-4 pl-4"><div class="col-12"><h4>Conformità</h4></div></div>',
          '#suffix' => '</div>'
        ];
  
        $form['con']['toggle_con'] = array(
          '#type' => 'checkbox',
          '#title' => $this->t('Altri campi opzionali'),
          '#prefix' => '<div class="col-12 px-4 mb-3">',
          '#suffix' => '</div>',
        );
  
        foreach ($arrayDataset['conforms'] as $value){
  
            $form['con']['con-tit-' . $idConfor] = [
              '#attributes' => [
                'name'=>'con['.$idConfor.'][titolostandard]'
              ],
              '#type' => 'textfield',
              '#title' => t('Titolo'),
              '#default_value' => $value['title'],
              '#states' => array(
                // Only show this field when the 'toggle_rif' checkbox is enabled.
                'visible' => array(
                  ':input[name="toggle_con"]' => array(
                    'checked' => TRUE,
                  ),
                ),
              ),
              '#prefix' => '<div id="div-row-con-'.$idConfor.'" class="row pr-4 pl-4"><div class="col-12 col-lg-5 col-md-5">',
              '#suffix' => '</div>',
            ];
  
          $form['con']['con-url-' . $idConfor] = [
            '#attributes' => [
              'name'=>'con['.$idConfor.'][urlstandard]'
            ],
            '#type' => 'textfield',
            '#default_value' => $value['url'],
            '#title' => t('URI standard'),
            '#states' => array(
              // Only show this field when the 'toggle_rif' checkbox is enabled.
              'visible' => array(
                ':input[name="toggle_con"]' => array(
                  'checked' => TRUE,
                ),
              ),
            ),
            '#prefix' => '<div class="col-12 col-lg-5 col-md-5">',
            '#suffix' => '</div>',
          ];
  
          $form['con']['con-del-' . $idConfor] = [
            '#type'  => 'button',
            '#value' => $this->t('Rimuovi'),
            '#states' => array(
              // Only show this field when the 'toggle_rif' checkbox is enabled.
              'visible' => array(
                ':input[name="toggle_con"]' => array(
                  'checked' => TRUE,
                ),
              ),
            ),
            '#prefix' => '<div class="col-12 col-lg-2 col-md-2">',
            '#suffix' => '</div></div>',
            '#attributes' => array(
              'onclick'=> 'return delCon('.$idConfor.')',
            ),
          ];
  
          $idConfor++;
        }
  
        $form['con']['con-tit-' . $idConfor] = [
          '#attributes' => [
            'name'=>'con['.$idConfor.'][titolostandard]'
          ],
          '#type' => 'textfield',
          '#title' => t('Titolo'),
          '#states' => array(
            // Only show this field when the 'toggle_rif' checkbox is enabled.
            'visible' => array(
              ':input[name="toggle_con"]' => array(
                'checked' => TRUE,
              ),
            ),
          ),
          '#prefix' => '<div id="div-row-con-'.$idConfor.'" class="row pr-4 pl-4"><div class="col-12 col-lg-5 col-md-5">',
          '#suffix' => '</div>',
        ];
  
        $form['con']['con-url-' . $idConfor] = [
          '#attributes' => [
            'name'=>'con['.$idConfor.'][urlstandard]'
          ],
          '#type' => 'textfield',
          '#title' => t('URI standard'),
          '#states' => array(
            // Only show this field when the 'toggle_rif' checkbox is enabled.
            'visible' => array(
              ':input[name="toggle_con"]' => array(
                'checked' => TRUE,
              ),
            ),
          ),
          '#prefix' => '<div class="col-12 col-lg-5 col-md-5">',
          '#suffix' => '</div>',
        ];
  
        $form['con']['con-del-' . $idConfor] = [
          '#type'  => 'button',
          '#value' => $this->t('Rimuovi'),
          '#states' => array(
            // Only show this field when the 'toggle_rif' checkbox is enabled.
            'visible' => array(
              ':input[name="toggle_con"]' => array(
                'checked' => TRUE,
              ),
            ),
          ),
          '#prefix' => '<div class="col-12 col-lg-2 col-md-2">',
          '#suffix' => '</div></div>',
          '#attributes' => array(
            'onclick'=> 'return delCon('.$idConfor.')',
          ),
        ];
  
        $form['addCon'] = [
        '#type'  => 'button',
        '#value' => $this->t('Aggiungi conformità'),
        '#states' => array(
          // Only show this field when the 'toggle_rif' checkbox is enabled.
          'visible' => array(
            ':input[name="toggle_con"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
        '#prefix' => '<div class="row pl-4 pr-4"><div class="col-12 col-lg-4 col-md-4">',
        '#suffix' => '</div></div>',
        '#attributes' => array(
          'onclick'=> 'return addCon();'
        ),
      ];
  
        $form['conAdesso'] = [
          '#attributes' => [
            'id' => 'conAdesso'
          ],
          '#type' => 'hidden',
          '#default_value' => $idConfor,
        ];
  
        $form['conTotale'] = [
          '#attributes' => [
            'id' => 'conTotale'
          ],
          '#type' => 'hidden',
          '#default_value' => $idConfor,
        ];
        // fine conformità

        $form['pag'] = [
          '#prefix' => '<div id="con"><div class="row pr-4 pl-4"><div class="col-12 mt-4"></div></div>',
          '#suffix' => '</div>'
        ];
  
        //pagina di accesso
        $form['pag']['pagina'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Pagina di accesso al dataset'),
          '#default_value' => $arrayDataset['pagina'] ?? '',
          '#prefix' => '<div class="row"><div class="col-12 pr-4 pl-4">',
          '#suffix' => '</div></div>',
        ];
        
        if($idCkan != 2){
          $form['pag']['licenzaRisorsa'] = [
            '#type' => 'select',
            '#title' => $this->t('* Licenza'),
            '#options' => $optionsLicenza,
            '#default_value' => $arrayDataset['licenzaRisorsa'],
            '#prefix' => '<div class="row" id="licenza" ><div class="col-12 pl-4 pr-4">',
            '#suffix' => '</div></div>',
          ];
        }


//      $form['uri'] = [
//        '#type' => 'textfield',
//        '#title' => $this->t('URI *'),
//        '#default_value' => $arrayDataset['uri'] ?? '',
//        '#prefix' => '<div class="row"><div class="col-12 pr-4 pl-4">',
//        '#suffix' => '</div></div>',
//      ];

      //Visibilità
      $form['generale1row']['visibilita'] = [
        '#type' => 'select',
        '#title' => $this->t('Visibilità *'),
        '#options' => $optionsVisibilita,
        '#default_value' => 'False',
        '#prefix' => '<div class="col-12 col-lg-4 col-md-4 mt-4 pl-4 pr-4 d-none">',
        '#suffix' => '</div>',
      ];
    
    if (!$new && $idCkan == 2) {
      $form['regione'] = [
        '#prefix' => '<div class="row pr-4 pl-4"><div class="col-12 mt-4 mb-4"><h4>Localizzazione amministrazioni   <small class="text-muted">(Ulteriori informazioni per consentire la ricerca per territorio)</small></h4></div>',
        '#suffix' => '</div>'
      ];
      $form['regione']['regione'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Regione *'),
        '#default_value' => $arrayDataset['regione'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-4 col-md-4">',
        '#suffix' => '</div>',
      ];
      $form['regione']['provincia'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Provincia *'),
        '#default_value' => $arrayDataset['provincia'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-4 col-md-4">',
        '#suffix' => '</div>',
      ];
      $form['regione']['comune'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Comune *'),
        '#default_value' => $arrayDataset['comune'] ?? '',
        '#prefix' => '<div class="col-12 col-lg-4 col-md-4">',
        '#suffix' => '</div>',
      ];
    } else {
      $form['regione'] = [
        '#prefix' => '<div id="divRegione">',
        '#suffix' => '</div>'
      ];
    }

      $form['myButton'] = [
        '#prefix' => '<div class="row mt-5 d-flex justify-content-end pl-4 pr-4">',
        '#suffix' => '</div>',
      ];

      $buttonSave = 'Salva Dataset';
      if (!$new) {
  $form['myButton']['dataset_go_back'] = [
          '#type' => 'button',
          '#value' => $this->t('Indietro'),
          "#weight" => 1,
          '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mb-5">',
          '#suffix' => '</div>',
          '#submit' => array([$this, 'submitFormRisorseDataset']),
        ];  

        $buttonSave = 'Aggiorna Dataset';
        $form['myButton']['delete'] = [
          '#type'  => 'submit',
          '#value' => $this->t('Elimina Dataset'),
          "#weight" => 1,
          '#prefix' => '<div class="col-12 col-lg-3 col-md-3 mb-5">',
          '#suffix' => '</div>',
          '#attributes' => array(
            'style'=>'margin-bottom:3%;width:100%;background-color:#ff0000;',
            'onclick' => 'this.form.action="/admin/config/gestioneutenti/dataset/delete";'
          ),
        ];
      } else {
       $form['myButton']['dataset_go_back'] = [
       '#type' => 'button',
       '#value' => $this->t('Indietro'),
       "#weight" => 1,
       '#prefix' => '<div class="col-12 col-lg-9 col-md-9 mb-5">',
       '#suffix' => '</div>',
       '#submit' => array([$this, 'submitFormRisorseDataset']),
  ];
      }

      $form['myButton']['inviaDataset'] = [
        '#type'  => 'submit',
        '#value' => $this->t($buttonSave),
        "#weight" => 1,
        '#prefix' => '<div class="col-12 col-lg-3 col-md-3">',
        '#suffix' => '</div>',
        '#attributes' => array(
          'style'=>'margin-bottom:3%;width:100%',
          'onclick' => 'return form_dataset_submit()'
        ),
      ];

       $form['#method'] = 'post';
       $form['#action'] = '/admin/config/gestioneutenti/dataset/save';


      return $form;
  }
   /*
   * @param $idCkan
   *
   * @return string[]
   */
  private function getDatiCkan($idCkan): array {
    $arrayReturn = [
      'url' => '',
      'api_key' => ''
    ];
    //Recupero dati ckan
    $query = \Drupal::database()->select('t_configuration', 't');
    $query->fields('t');
    $query->condition('t.id', $idCkan, '=');
    $resultUrl =  $query->execute();
    if ($resultUrl) {
        $rowUrl = $resultUrl->fetchAssoc();
        $arrayReturn['url'] =  $rowUrl['value'];
    }
    $query = \Drupal::database()->select('organizzazione', 'o');
    $query->fields('o');
    $query->condition('o.idUtente', \Drupal::currentUser()->id(), '=');
    $resultApi = $query->execute()->fetchAssoc();
    if ($resultApi) {
        $rigaUrl = $query->execute()->fetchAssoc();
        if ($idCkan === '1') {
          $arrayReturn['api_key'] = $rigaUrl['ckankeyDatigov'];
        }
        if ($idCkan === '2') {
          $arrayReturn['api_key'] = $rigaUrl['ckankeyBasigov'];
        }
    }
    return $arrayReturn;
  }

  private function getTemi(){
    $arrayReturn = [ '' => 'Seleziona Temi'];
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
            $arrayReturn[$value['tid']] = $value['name'];
      }
    }
    return $arrayReturn;
  }

  private function dettaglioDataset($idCkan, $idDataset){
      $ckan = $this->getDatiCkan($idCkan);
      $url = $ckan['url'] . '/api/3/action/package_show';
      $post = [ 'id' => $idDataset ];
      $header = [
        'Authorization: ' . $ckan['api_key'],
        'X-CKAN-API-Key: ' . $ckan['api_key']
      ];

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
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $risultato = curl_exec ($ch);
      curl_close ($ch);

      return json_decode($risultato, FALSE, 512, JSON_THROW_ON_ERROR)->result;
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

  private function getIdTema($ckanValue){
      $query = \Drupal::database()->select('taxonomy_term_revision__field_ckan', 'ckan');
      $query->fields('ckan',['entity_id']);
      $query->condition('ckan.field_ckan_value', $ckanValue, '=');
      $result = $query->execute();
      if ($result !== NULL) {
          $riga = $result->fetchAssoc();
          return $riga['entity_id'];
      }
      return '';
  }

  private function getSottotemi($idTema){
    $arrayReturn = [ '' => 'Seleziona Sottotema'];
    $query = \Drupal::database()->select('taxonomy_term_field_data', 'dt');
    //    $query->join('taxonomy_term__field_groups', 'gr','dt.tid = gr.entity_id AND dt.vid = gr.bundle');
    $query->join('taxonomy_term__parent', 'pr ','dt.vid = pr.bundle and dt.tid = pr.entity_id');
    $query->fields('dt',['name', 'tid']);
    //    $query->fields('gr',['field_groups_value']);
    $query->condition('dt.status', 1, '=');
    $query->condition('dt.vid', 'dati', '=');
    $query->condition('pr.parent_target_id', $idTema, '=');

    $result = $query->execute();
    if($result !== null) {
      foreach ($result->fetchAll(2) as $value) { // 2 = PDO::FETCH_ASSOC
        $arrayReturn[$value['tid']] = $value['name'];
      }
    }
    return $arrayReturn;
}

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }


  private function selezionaLicenze() {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    curl_setopt($ch, CURLOPT_HEADER, FALSE); //impostando a true mi visualizza header della risposta, a false no
    curl_setopt($ch, CURLOPT_USERAGENT, "Ckan_client-PHP/");
    curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
    curl_setopt($ch, CURLOPT_FILETIME, TRUE);

    $url = getenv('CKAN_HOST').':'. getenv('CKAN_PORT') . '/'  . "api/3/action/license_list";
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER,[]);
    $risultato = curl_exec($ch);
    //echo $risultato;
    curl_close($ch);
    $jo = json_decode($risultato, TRUE);
    $arrayReturn = [];

    foreach ($jo['result'] as $value){
      $arrayReturn[$value['id']] = $value['title'];
    }

    return $arrayReturn;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) { }

  }
