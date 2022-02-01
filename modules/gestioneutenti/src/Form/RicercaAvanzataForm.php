<?php
namespace Drupal\gestioneutenti\Form;
 
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Render\Markup;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use PDO;

/**
 *
 */
class RicercaAvanzataForm extends FormBase {
 
  /**
   * {@inheritdoc}
   */


  private $numeroElementiVisibili = 7;

  public function getFormId() {
    return 'ricercaavanzata_form';
  }
 
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


		$form['#title'] = "Ricerca avanzata";
    /* JavaScript per Form  */
    $form['#attached'] = [ 'library' => ['gestioneutenti/advanced-search'], 'drupalSettings' => [] ];


//  	$query = \Drupal::database()->select('taxonomy_term_field_data', 'taxonomy_term_field_data');
//		$query->join('taxonomy_term__field_groups', 'taxonomy_term__field_groups','taxonomy_term_field_data.tid = taxonomy_term__field_groups.entity_id AND taxonomy_term_field_data.vid = taxonomy_term__field_groups.bundle');
//    $query->join('taxonomy_term__parent', 'taxonomy_term__parent','')
//		$query->fields('taxonomy_term_field_data',['name']);
//    $query->fields('taxonomy_term__field_groups',['field_groups_value']);
//		$query->condition('taxonomy_term_field_data.status', 1, '=');
//		$query->condition('taxonomy_term_field_data.vid', 'dati', '=');

    $query = \Drupal::database()->select('taxonomy_term_field_data', 'dt');
    $query->join('taxonomy_term__field_groups', 'gr','dt.tid = gr.entity_id AND dt.vid = gr.bundle');
    $query->join('taxonomy_term__parent', 'pr ','dt.vid = pr.bundle and dt.tid = pr.entity_id');
    $query->fields('dt',['name']);
    $query->fields('gr',['field_groups_value']);
    $query->condition('dt.status', 1, '=');
    $query->condition('dt.vid', 'dati', '=');
    $query->condition('pr.parent_target_id', 0, '=');

		$result = $query->execute();

    $html1 = '';
    if($result != null){
      foreach ($result->fetchAll(2) as $key => $value){ // 2 = PDO::FETCH_ASSOC
        $this->getHtmlLi($html1, 'checkbox','temi' . $key, $value['field_groups_value'], $value['name'], 'groups[]');
      }
      $this->getHtmlUl($html1,'Temi', 'listaTemi');
    }


    $htmlTmp = '<span class="autocomplete-icon" aria-hidden="true"><button type="button" id="advancedSearchButton" data-toggle="tooltip" title="Cerca" class="p-0" style="background-color:#fff;border:none" type="submit"><svg class="icon icon-sm"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-search"></use></svg></button></span>';
    $htmlTmp .= '<span class="autocomplete-icon-right" aria-hidden="true"><button type="button" data-toggle="Pulisci" title="Cerca" id="advancedSearchFormReset" class="p-0" style="background-color:#fff;border:none" type="submit"><svg class="icon icon-sm"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-delete"></use></svg></button></span>';
		/**
		*   Campo Ricerca
    */
	  $form['testoCerca'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => 'Ricerca avanzata all\'interno dei Dataset',
        'class' => ['autocomplete'],
        'id' => 'testoCerco',
        'title' => 'ricerca avanzata',

      ],
      '#children' => \Drupal\Core\Render\Markup::create($htmlTmp),
    ];

	  /* End Campo Ricerca */

    /* Radio buttons Cataloghi */
    $cataloghi  =  $this->recuperaCataloghi("", "", "");
	  $urlckan    =  $this->selezionaUrlCkan("1");
	  $vetlicenze =  $this->selezionaLicenze("","",$urlckan);
	  $vetformati =  $this->formati("","",$urlckan);


    $html2 = '';

        usort($cataloghi, function($a, $b) { return $a['display_name'] <=> $b['display_name']; });
        foreach ( $cataloghi as $key => $value ){
          $this->getHtmlLi($html2, 'radio','cataloghi' . $key, $value['name'], $value['display_name'], 'cataloghi');
        }
        $this->getHtmlUl($html2,'Cataloghi', 'listaCataloghi');


    /* Radio buttons Licenze */
    $html3 = '';

        usort($vetlicenze, function($a, $b) { return $b['count'] <=> $a['count']; });

        foreach ( $vetlicenze as $key => $value ){
            $this->getHtmlLi($html3, 'radio','licenze' . $key, $value['name'], $value['name'], 'licenze');
        }
        $this->getHtmlUl($html3,'Licenze', 'listaLicenze');

    /* End Radio buttons Licenze */

    /* Radio buttons Formati */
    $html4 = '';

        usort($vetformati, function($a, $b) { return $b['count'] <=> $a['count']; });
        foreach ( $vetformati as $key => $value ){
          $this->getHtmlLi($html4, 'radio','formati' . $key, $value['name'], $value['display_name'], 'formati');
        }
        $this->getHtmlUl($html4,'Formati', 'listaFormati');
    /* End Radio buttons Formati */



    $form['elencoCategorie'] = [
      '#type' => 'inline_template',
      '#template' => $html1,
      '#prefix' => '<div class="row"><div class="col-md-3 col-sm-4 col-lg-3">',
      '#suffix' => '</div>',
    ];

    $form['elencoCataloghi'] = [
      '#type' => 'inline_template',
      '#template' => $html2,
      '#prefix' => '<div class="col-md-3 col-sm-4 col-lg-3">',
      '#suffix' => '</div>',
    ];

    $form['elencoLicenze'] = [
      '#type' => 'inline_template',
      '#template' => $html3,
      '#prefix' => '<div class="col-md-3 col-sm-4 col-lg-3">',
      '#suffix' => '</div>',
    ];

    $form['elencoFormati'] = [
      '#type' => 'inline_template',
      '#template' => $html4,
      '#prefix' => '<div class="col-md-3 col-sm-4 col-lg-3">',
      '#suffix' => '</div></div>',
    ];

    $htmlButtons1 = '<button class="btn btn-primary btn-lg btn-block" id="advancedSearchButtonG" type="button">Cerca</button>';
    $htmlButtons2 = '<button class="btn btn-outline-secondary btn-lg btn-block" id="advancedSearchFormResetG"  type="button" >Pulisci</button>';

    $form['buttons1'] = [
      '#type' => 'inline_template',
      '#template' => $htmlButtons1,
      '#prefix' => '<div class="row mb-5"><div class="col-md-12 col-sm-2 col-lg-2 ">',
      '#suffix' => '</div>',
    ];
    $form['buttons2'] = [
      '#type' => 'inline_template',
      '#template' => $htmlButtons2,
      '#prefix' => '<div class="col-md-12 col-sm-2 col-lg-2">',
      '#suffix' => '</div></div>',
    ];

    return $form;
 
  }

  /**
   * @param string $html
   * @param string $typeInput - 'radio' , 'checkbox'
   * @param string $html_id
   * @param string $value
   * @param string $title
   * @param string $name - il nome di campo
   */
  private function getHtmlLi(string &$html, string $typeInput, string $html_id, string $value, string $title, string $name): void {
    $role = '';
    if ($typeInput === 'radio') { $role = 'role="radiogroup"'; }
    if ($typeInput === 'checkbox') { $role = 'role="group"'; }
    $html .= '<li>';
      $html .= '<div class="form-check form-check-inline">';
        $html .= '<input aria-label="'.$value.'"name="'.$name.'" value="'.$value.'" type="'.$typeInput.'"' . $role .'class="with-gap" id="'. $html_id .'">';
        $html .= '<label for="'. $html_id .'">'.$title.'</label>';
      $html .= '</div>';
    $html .= '</li>';
  }

  private function getHtmlUl(string &$html, string $title,string $id){
    $htmlTmp1 = '<div class="link-list-wrapper">';
    $htmlTmp1 .= '<h3 id="allineamento-del-testo-'.$id.'">';
    $htmlTmp1 .= '<span class="bd-content-title">'.$title.'</span>';
    $htmlTmp1 .= '</h3>';
    $htmlTmp1 .= '<ul class="link-list mb-0" id="'.$id.'">';

    /*
     * Bottoni Mostra e Nascondi
     */
    $htmlTmp2 = '</ul><ul class="link-list">';
      $htmlTmp2 .= '<li id="'.$id . 'LiM'.'">';
        $htmlTmp2 .= '<button id="'.$id . 'ButtonM'.'" class="btn btn-outline-primary d-flex align-items-center pl-1" type="button" style="box-shadow: none;" ><span class="text" style="font-size: 18px;" >Mostra Altri	&nbsp;</span><svg class="icon icon-primary icon-xs align-middle"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-plus-circle"></use></svg></button>';
      $htmlTmp2 .= '</li>';
      $htmlTmp2 .= '<li id="'.$id . 'LiN'.'">';
        $htmlTmp2 .= '<button id="'.$id . 'ButtonN'.'" class="btn btn-outline-primary d-flex align-items-center pl-1" type="button" style="box-shadow: none;" ><span class="text" style="font-size: 18px;">Nascondi	&nbsp;</span><svg class="icon icon-primary icon-xs align-middle"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-minus-circle"></use></svg></button>';
      $htmlTmp2 .= '</li>';
    $htmlTmp2 .= '</ul>';
    $htmlTmp2 .= '</div>';

    $html = $htmlTmp1 . $html . $htmlTmp2;
  }

  private function selezionaUrlCkan($ckanvalue){
//		  $query = \Drupal::database()->select('t_configuration', 't');
//		  $query->fields('t');
//		  $query->condition('t.id', $ckanvalue, '=');
//		  $result = $query->execute();
//		  $ckanurl = "";
//		  if($result != null){
//			  $riga = $result->fetchAssoc();
//			  $ckanurl=$riga["value"];
//		  }
		  $ckanurl = getenv('CKAN_HOST').':'. getenv('CKAN_PORT') . '/';
		  return $ckanurl;
}

private function formati($idUtente,$ckanapi,$urlckan){
    
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
  
  /*
	  $post = [
			  "id" => $idUtente,
			  "include_datasets" => TRUE
	  ];
	*/
	  $url=$urlckan."api/3/action/package_search?facet.field=[%22res_format%22]&facet.limit=-1";
  //$url='https://dati.gov.it/api/3/action/package_search?facet.field=[%22res_format%22]&facet.limit=-1';
	  curl_setopt($ch, CURLOPT_URL,$url);		
	  $param1='Authorization: '.$ckanapi;
	  $param2='X-CKAN-API-Key: '.$ckanapi;
	  $headers1=array($param1, $param2);
  
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	  //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	  $risultato = curl_exec($ch);
	  //echo $risultato;		
	  curl_close ($ch);
  $jo=json_decode($risultato,TRUE);
  $formati=$jo['result']['search_facets']['res_format']['items'];
  return $formati;

}


private function selezionaLicenze($idUtente,$ckanapi,$urlckan){
    
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
  
  	  $url=$urlckan."api/3/action/package_search?facet.field=[%22license_title%22]&facet.limit=-1";
  	  curl_setopt($ch, CURLOPT_URL,$url);		
	  $param1='Authorization: '.$ckanapi;
	  $param2='X-CKAN-API-Key: '.$ckanapi;
	  $headers1=array($param1, $param2);


	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	  //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	  $risultato = curl_exec($ch);
	  //echo $risultato;		
	  curl_close ($ch);

	  $jo=json_decode($risultato, TRUE);

    $array = $jo['result']['search_facets']['license_title']['items'];

//    $arrayLicenze = $this->selezionaLicenzeOld('',$ckanapi,$urlckan);
//    $arrayReturn = [];
//
//    foreach ($array as $value){
//        $id = FALSE;
//        foreach ($arrayLicenze as $valueLicenze){
//            if ($value['name'] === $valueLicenze['title']) {
//                $id = $valueLicenze['id'];
//            }
//        }
//       $arrayReturn[] = [
//         'count' => $value['count'],
//         'display_name' => $value['display_name'],
//         'name' => $value['name'],
//         'id' => $id ?? $value['name'],
//       ];
//    }
    return $array;

  }

  private function selezionaLicenzeOld($idUtente,$ckanapi,$urlckan) {

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

    $url = $urlckan . "api/3/action/license_list";
    curl_setopt($ch, CURLOPT_URL, $url);
    $param1 = 'Authorization: ' . $ckanapi;
    $param2 = 'X-CKAN-API-Key: ' . $ckanapi;
    $headers1 = [$param1, $param2];


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
    $risultato = curl_exec($ch);
    //echo $risultato;
    curl_close($ch);
    $jo = json_decode($risultato, TRUE);

    return $jo['result'];
  }

  private function recuperaCataloghi($idUtente,$ckanapi,$urlckan){

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
	$urlckan = $this->selezionaUrlCkan("1");
	$url=$urlckan."api/3/action/package_search?facet.field=[%22organization%22]&facet.limit=-1&sort=name%20asc";
	curl_setopt($ch, CURLOPT_URL,$url);
	$param1='Authorization: '.$ckanapi;
	$param2='X-CKAN-API-Key: '.$ckanapi;
	$headers1=array($param1, $param2);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	$risultato = curl_exec($ch);
	curl_close ($ch);
	$jo = json_decode($risultato,'true');

	return $jo['result']['search_facets']['organization']['items'];
  }
 
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  
	 
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {	  
	  
		 $testo=$form_state->getValue('testoCerca');
		 $tipoRicerca=$form_state->getValue('tipoRicerca');
		 $filtroTemi=$form_state->getValue('filtroTemi');
		 $filtroCataloghi=$form_state->getValue('catalogoSelezionato');
		 $filtroFormati=$form_state->getValue('formatoSelezionato');
     $filtroLicenze=$form_state->getValue('filtroLicenze');

     $cerca = $form_state->getValue('testoCerca');
     $licenza = $form_state->getValue('licenza');
     $temi = $form_state->getValue('groups');
     $formati = $form_state->getValue('formati');
     $cataloghi = $form_state->getValue('cataloghi');

     $path = '/view-dataset';

     $parametroGruppo="groups=";
		 $path_param = [];
		 //drupal_set_message("valore di testo ".$testo." ".strlen($testo));
		 if(strlen($testo)>0){
			$path_param["Cerca"]=$testo;
		 }
		 else{
			$path_param["Cerca"]="";
		 }
		 if(strlen($filtroTemi)>0){
			$path_param["groups"]= $filtroTemi;
		 }
		 if(strlen($filtroCataloghi)>0){
			$path_param["organization"]= $filtroCataloghi;
		 }
		 if(strlen($filtroLicenze)>0){
			$path_param["licenze"]= $filtroLicenze;
		 }
		 if(strlen($filtroFormati)>0){
			$path_param["format"]= $filtroFormati;
		 }
	
		 $url = Url::fromUserInput($path, ['query' => $path_param]);
		 $form_state->setRedirectUrl($url);
		 
  }

  function arrSortObjsByKey($key, $order = 'DESC') {
    return function($a, $b) use ($key, $order) {

      // Swap order if necessary
      if ($order == 'DESC') {
        [$a, $b] = array($b, $a);
      }

      // Check data type
      if (is_numeric($a->$key)) {
        return $a->$key - $b->$key; // compare numeric
      } else {
        return strnatcasecmp($a->$key, $b->$key); // compare string
      }
    };
  }

}
