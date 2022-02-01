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
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Link; 

class GestioneRisorsaForm extends FormBase {

	/**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'GestioneRisorsaForm',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'GestioneRisorsa_form';  
  }


	/**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state,$ckan=null,$dataset=null,$id=null) {

		$user = \Drupal::currentUser();
		$idutente =	$user->id();
		$ckanapi="";
		$conn = Database::getConnection();
		$query = \Drupal::database()->select('organizzazione', 'o');	
		$query->fields('o');
		$query->condition('o.idUtente', $idutente, '=');
		if($ckan=="dati-gov")
				$query->condition('o.flagDatigov', 1, '=');
		else if($ckan=="basi-gov")
				$query->condition('o.flagBasigov', 1, '=');
		$query->range(0, 1);
		$result = $query->execute();
		if($result != null){
				$riga = $result->fetchAssoc();
				if($ckan=="dati-gov")
					$ckanapi=	$riga["ckankeyDatigov"];	
				else if($ckan=="basi-gov")
					$ckanapi=	$riga["ckankeyBasigov"];	
		}

		$ckanurl="";
		$query = \Drupal::database()->select('t_configuration', 't');
		$query->fields('t');
		if($ckan=="dati-gov")
				$query->condition('t.key', "dati-gov", '=');
		else if($ckan=="basi-gov")
				$query->condition('t.key', "basi-gov", '=');
		$query->range(0, 1);
		$result = $query->execute();
		if($result != null){
				$riga = $result->fetchAssoc();
				$ckanurl=	$riga["value"];	
		}

		$joRisorsa=null;
		$formato="";

		$label = 'Modifica Risorsa';

    $form['#prefix'] = '<div class="container mb-5"> <div class="row mt-5 mb-5"><div class="col 12 d-flex justify-content-center"> <h1>'.$label.'</h1></div></div><div id="myAlert"></div>';
    $form['#suffix'] = '</div>';

		$form['ckanconf'] = [  
      '#type' => 'hidden',  
      '#default_value' => $ckan,
    ];
    $form['idDatasetNascosto'] = [  
      '#type' => 'hidden',  
      '#default_value' => $dataset,
    ];

		$form['ckanapiNascosto'] = [  
      '#type' => 'hidden',  
      '#default_value' => $ckanapi,
    ];  

		$form['ckanurlNascosto'] = [  
      '#type' => 'hidden',  
      '#default_value' => $ckanurl,
    ];  

		if($id == "support-request"){
		  $form['operazione'] = [
		    '#type' => 'hidden',  
		    '#default_value' => 1,
		  ];
		} else{
			$json=$this->resourceShow($ckanurl,$id,$ckanapi);
			$joRisorsa=json_decode($json);
      //package_id
		  $form['operazione'] = [  
		    '#type' => 'hidden',  
		    '#default_value' => 2,
		  ];
      $form['idRisorsa'] = [
        '#type' => 'hidden',
        '#default_value' => $joRisorsa->result->id,
      ];

    }

      $form['ckankey'] = [
        '#type' => 'hidden',
        '#default_value' => $ckanapi,
      ];

      $form['urlA'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL di accesso*'),
        '#default_value' => $joRisorsa->result->access_url ?? '',
	'#maxlength' => 1025,
        '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
      ];

      $form['urlD'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL di download'),
        '#default_value' => $joRisorsa->result->url ?? '',
	'#maxlength' => 1025,
        '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
      ];

			$form['titolo'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Titolo della risorsa *'),
        '#default_value' => $joRisorsa->result->name ?? '',
        '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
		  ];

			$form['descrizione'] = [
        '#rows' => 5,
        '#cols' => 60,
        '#resizable' => TRUE,
        '#type' => 'textarea',
        '#title' => $this->t('Descrizione della risorsa'),
        '#prefix' => '<div class="row"><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
			  '#default_value' => $joRisorsa->result->description ?? '',
      ];

		  $optionsFormato = [
		  '' => 'Formato *',
		  'ARC' => 'ARC',
		  'ARC_GZ' => 'ARC_GZ',
		  'ATOM' => 'ATOM',
		  'AZW' => 'AZW',
		  'BIN' => 'BIN',
		  'BITS' => 'BITS',
		  'BMP' => 'BMP',
		  'BWF' => 'BWF',
		  'CSS' => 'CSS',
		  'CSV' => 'CSV',
		  'DBF' => 'DBF',
		  'DCR' => 'DCR',
		  'DMP' => 'DMP',
		  'DOC' => 'DOC',
		  'DOCX' => 'DOCX',
		  'DTD_SGML' => 'DTD_SGML',
		  'DTD_XML' => 'DTD_XML',
		  'E00' => 'E00',
		  'ECW' => 'ECW',
		  'EPS' => 'EPS',
		  'EPUB' => 'EPUB',
		  'EXE' => 'EXE',
		  'FMX2' => 'FMX2',
		  'FMX3' => 'FMX3',
		  'FMX4' => 'FMX4',
		  'GDB' => 'GDB',
		  'GEOJSON' => 'GEOJSON',
		  'GIF' => 'GIF',
		  'GML' => 'GML',
		  'GMZ' => 'GMZ',
		  'GRID' => 'GRID',
		  'GRID_ASCII' => 'GRID_ASCII',
		  'GZIP' => 'GZIP',
		  'HDF' => 'HDF',
		  'HDT' => 'HDT',
		  'HTML' => 'HTML',
		  'HTML_SIMPL' => 'HTML_SIMPL',
		  'ICS' => 'ICS',
		  'IMMC_XML' => 'IMMC_XML',
		  'INDD' => 'INDD',
		  'ISO' => 'ISO',
		  'ISO_ZIP' => 'ISO_ZIP',
		  'JATS' => 'JATS',
		  'JPEG' => 'JPEG',
		  'JS' => 'JS',
		  'JSON' => 'JSON',
		  'JSON_LD' => 'JSON_LD',
		  'KML' => 'KML',
		  'KMZ' => 'KMZ',
		  'LAS' => 'LAS',
		  'LAZ' => 'LAZ',
		  'LEG' => 'LEG',
		  'MAP_PRVW' => 'MAP_PRVW',
		  'MAP_SRVC' => 'MAP_SRVC',
		  'MBOX' => 'MBOX',
		  'MDB' => 'MDB',
		  'METS' => 'METS',
		  'METS_ZIP' => 'METS_ZIP',
		  'MHTML' => 'MHTML',
		  'MOBI' => 'MOBI',
		  'MOP' => 'MOP',
		  'MPEG2' => 'MPEG2',
		  'MPEG4' => 'MPEG4',
		  'MPEG4_AVC' => 'MPEG4_AVC',
		  'MRSID' => 'MRSID',
		  'MSG_HTTP' => 'MSG_HTTP',
		  'MXD' => 'MXD',
		  'N3' => 'N3',
		  'NETCDF' => 'NETCDF',
		  'OCTET' => 'OCTET',
		  'ODB' => 'ODB',
		  'ODC' => 'ODC',
		  'ODF' => 'ODF',
		  'ODG' => 'ODG',
		  'ODS' => 'ODS',
		  'ODT' => 'ODT',
		  'OP_DATPRO' => 'OP_DATPRO',
		  'OVF' => 'OVF',
		  'OWL' => 'OWL',
		  'PDF' => 'PDF',
		  'PDF1X' => 'PDF1X',
		  'PDFA1A' => 'PDFA1A',
		  'PDFA1B' => 'PDFA1B',
		  'PDFA2A' => 'PDFA2A',
		  'PDFA2B' => 'PDFA2B',
		  'PDFA3' => 'PDFA3',
		  'PDFX' => 'PDFX',
		  'PDFX1A' => 'PDFX1A',
		  'PDFX2A' => 'PDFX2A',
		  'PDFX4' => 'PDFX4',
		  'PL' => 'PL',
		  'PNG' => 'PNG',
		  'PPS' => 'PPS',
		  'PPSX' => 'PPSX',
		  'PPT' => 'PPT',
		  'PS' => 'PS',
		  'PSD' => 'PSD',
		  'PWP' => 'PWP',
		  'QGS' => 'QGS',
		  'RAR' => 'RAR',
		  'RDF' => 'RDF',
		  'RDFA' => 'RDFA',
		  'RDF_N_QUADS' => 'RDF_N_QUADS',
		  'RDF_N_TRIPLES' => 'RDF_N_TRIPLES',
		  'RDF_TRIG' => 'RDF_TRIG',
		  'RDF_TURTLE' => 'RDF_TURTLE',
		  'RDF_XML' => 'RDF_XML',
		  'REST' => 'REST',
		  'RSS' => 'RSS',
		  'RTF' => 'RTF',
		  'SCHEMA_XML' => 'SCHEMA_XML',
		  'SDMX' => 'SDMX',
		  'SGML' => 'SGML',
		  'SHP' => 'SHP',
		  'SKOS_XML' => 'SKOS_XML',
		  'SPARQLQ' => 'SPARQLQ',
		  'SPARQLQRES' => 'SPARQLQRES',
			'SQL' => 'SQL',
		  'SVG' => 'SVG',
		  'TAB' => 'TAB',
		  'TAB_RSTR' => 'TAB_RSTR',
		  'TAR' => 'TAR',
		  'TAR_GZ' => 'TAR_GZ',
		  'TAR_XZ' => 'TAR_XZ',
			'TIFF' => 'TIFF',
		  'TIFF_FX' => 'TIFF_FX',
		  'TMX' => 'TMX',
		  'TSV' => 'TSV',
		  'TXT' => 'TXT',
		  'WARC' => 'WARC',
		  'WARC_GZ' => 'WARC_GZ',
		  'WFS_SRVC' => 'WFS_SRVC',
		  'WMS_SRVC' => 'WMS_SRVC',
		  'WORLD' => 'WORLD',
		  'XHTML' => 'XHTML',
		  'XHTML_SIMPL' => 'XHTML_SIMPL',
		  'XLIFF' => 'XLIFF',
		  'XLS' => 'XLS',
		  'XLSB' => 'XLSB',
		  'XLSM' => 'XLSM',
		  'XLSX' => 'XLSX',
		  'XML' => 'XML',
		  'XSLFO' => 'XSLFO',
		  'XSLT' => 'XSLT',
		  'XYZ' => 'XYZ',
		  'ZIP' => 'ZIP'
    ];

      $form['formatoDistribuzione'] = array(
        '#type' => 'select',
        '#prefix' => '<div class="row "><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
        '#title' => $this->t('Formato *'),
        '#options' => $optionsFormato,
        '#default_value' => $joRisorsa->result->format ?? '',
      );

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

      $form['licenzaRisorsa'] = array(
        '#type' => 'select',
        '#title' => $this->t('Licenza della risorsa'),
        '#options' => $optionsLicenza,
        '#default_value' => $joRisorsa->result->license_type ?? '',
        '#prefix' => '<div class="row mt-5"><div class="col-12 pl-4 pr-4">',
        '#suffix' => '</div></div>',
      );

      $form['generale1row'] = [
        '#prefix' => '<div class="row mt-5">',
        '#suffix' => '</div>'
      ];
      $form['generale1row']['datetime'] = [
        '#type' => 'date',
        '#title' => $this->t('Ultima modifica gg/mm/aaaa'), //
        '#default_value' => $joRisorsa->result->last_modified ? date('Y-m-d', strtotime($joRisorsa->result->last_modified)) : '',
        '#date_date_format' => 'Y/m/d',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mt-4 pl-4 pr-4">',
        '#suffix' => '</div>',
      ];

      $form['generale1row']['dimensioneFile'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Dimensione in byte '),
        '#default_value' => $joRisorsa->result->size ?? '',
        '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mt-4 pl-4 pr-4">',
        '#suffix' => '</div>',
      ];

      $form['myButton'] = [
        '#prefix' => '<div class="row mb-5 d-flex justify-content-end pl-4 pr-4">',
        '#suffix' => '</div>',
      ];

      if($id != "support-request"){ //delete
	$form['myButton']['visualizzarisorsedataset'] = [
          '#type' => 'submit',
          '#value' => $this->t('Indietro'),
          "#weight" => 1,
          '#prefix' => '<div class="col-12 col-lg-6 col-md-6 mb-5">',
          '#suffix' => '</div>',
          '#submit' => array([$this, 'submitFormRisorseDataset']),
        ];

        $form['myButton']['cancellarisorsa'] = [
          '#type' => 'submit',
          '#value' => $this->t('Elimina Risorsa'),
          "#weight" => 1,
          '#submit' => array([$this, 'submitFormTwo']),
          '#prefix' => '<div class="col-12 col-lg-3 col-md-3 mb-5">',
          '#suffix' => '</div>',
          '#attributes' => array(
            'style'=>'width:100%;background-color:#ff0000;'
          ),
        ];
      } else {
        $form['myButton']['visualizzarisorsedataset'] = [
          '#type' => 'submit',
          '#value' => $this->t('Indietro'),
          "#weight" => 1,
          '#prefix' => '<div class="col-12 col-lg-9 col-md-9 mb-5">',
          '#suffix' => '</div>',
          '#submit' => array([$this, 'submitFormRisorseDataset']),
        ];	
      }



      $form['myButton']['save'] = [
        '#type' => 'submit',
        '#value' => $this->t('Salva Risorsa'),
        "#weight" => 1,
        '#prefix' => '<div class="col-12 col-lg-3 col-md-3 mb-5">',
        '#suffix' => '</div>',
        '#attributes' => array(
          'style'=>'width:100%;'
        ),
      ];


      return $form;
  }

	  public function submitFormRisorseDataset(array &$form, FormStateInterface $form_state){
      $id=$form_state->getValue('idDatasetNascosto');
      $ckan=$form_state->getValue('ckanconf');
      $url = \Drupal\Core\Url::fromRoute('gestioneutenti.risorse_dataset')->setRouteParameters(array('ckan'=>$ckan,'id'=>$id));
      $form_state->setRedirectUrl($url);
		}


	public function submitFormVisualizzaRisorsa(array &$form, FormStateInterface $form_state){
			$url=	$form_state->getValue('urlrisorsatrovata');
 			$response = new TrustedRedirectResponse(Url::fromUri($url)->toString());
       $link = Link::fromTextAndUrl('some text', Url::fromUri($url, array('attributes' => array('target' => '_blank'))));
			 $form_state->setResponse($response);
	}

	public function submitFormTwo(array &$form, FormStateInterface $form_state){
			$id=$form_state->getValue('idRisorsa');//ckankey
		  $urlckan=$form_state->getValue("ckanurlNascosto");
			$ckankey=$form_state->getValue("ckanapiNascosto"); 		
			$this->resourceDelete($urlckan,$id,$ckankey);
      $id=$form_state->getValue('idDatasetNascosto');
      $ckan=$form_state->getValue('ckanconf');
      $url = \Drupal\Core\Url::fromRoute('gestioneutenti.risorse_dataset')->setRouteParameters(array('ckan'=>$ckan,'id'=>$id));
      $form_state->setRedirectUrl($url);
		}

	public function validateForm(array &$form, FormStateInterface $form_state) {}

	/**  
   * {@inheritdoc}  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  

		$operazione = (int)$form_state->getValue('operazione'); // 1 - New ; 2 - Edit

    $post = [
      'package_id' => $form_state->getValue('idDatasetNascosto'),
      'id' => $_POST['idRisorsa'] ?? '',
      'name' => $form_state->getValue('titolo'),
      'description' => $form_state->getValue('descrizione'),
      'format' => $form_state->getValue('formatoDistribuzione'),
      'access_url' => $form_state->getValue('urlA'),
      'url' => $form_state->getValue('urlD'),
      'license_type' => $form_state->getValue('licenzaRisorsa'),
      'last_modified' => $form_state->getValue('datetime'),
      'size' => $form_state->getValue('dimensioneFile'),
    ];

    $urlCkan = $form_state->getValue("ckanurlNascosto");
    $apiKey = $form_state->getValue("ckanapiNascosto");
    $url = '';
    if($operazione === 1){
        $url = $urlCkan . '/api/3/action/resource_create';
		} else if ($operazione === 2) {
        $url = $urlCkan . '/api/3/action/resource_update';
    }

    $this->aggiornaRisorsa($post, $url, $apiKey);
    $id=$form_state->getValue('idDatasetNascosto');
    $ckan=$form_state->getValue('ckanconf');
    $url = \Drupal\Core\Url::fromRoute('gestioneutenti.risorse_dataset')->setRouteParameters(array('ckan'=>$ckan,'id'=>$id));
    $form_state->setRedirectUrl($url);
  }

	private function aggiornaRisorsa($post,$url,$apiKey){
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_USERAGENT => 'Ckan_client-PHP/',
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => [
        'Authorization: ' . $apiKey,
        'X-CKAN-API-Key: '. $apiKey,
        'Content-Type: multipart/form-data'
      ],
    ));
    $response = curl_exec($curl);
    curl_close($curl);
  }


	private function resourceDelete($url, $id, $apiKey){
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url . '/api/3/action/resource_delete',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_USERAGENT => 'Ckan_client-PHP/',
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => ['id' => $id],
      CURLOPT_HTTPHEADER => [
        'Authorization: ' . $apiKey,
        'X-CKAN-API-Key: '. $apiKey,
        'Content-Type: multipart/form-data'
      ],
    ));
    $response = curl_exec($curl);
    curl_close($curl);
 }

	private function resourceShow($urlckan,$id,$ckanapi){
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
				"id" => $id
		];		
		$url=$urlckan."/api/3/action/resource_show";
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
		curl_close ($ch);
	  return $risultato;
	}

}  

