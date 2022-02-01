<?php


namespace Drupal\gestioneutenti\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;

class GestioneutentiSaveDatasetController extends ControllerBase {

    /**
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'gestioneutenti.settings';

    public function saveDataset(){
      $titolo = $_POST['titolo'] ?? '';
      $JSON['name'] = $_POST['name'] ?? strtolower($this->compattaStringa('--',$this->sostituisci($titolo),'-'));
      $JSON['title'] = $titolo;
      $JSON['notes'] = $_POST['note'] ?? '';
      $JSON['uri'] = $_POST['uri'] ?? '';
      $JSON['url'] = $_POST['pagina'] ?? '';
      $JSON['language'] = $_POST['lingua'] ?? '';
      $JSON['private'] = $_POST['visibilita'] ?? '';
      $JSON['version'] = $_POST['versione'] ?? '';
      $JSON['publisher_name'] = $_POST['soggetti_nome'] ?? '';
      $JSON['publisher_identifier'] = $_POST['soggetti_codice'] ?? '';
      $JSON['issued'] = $_POST['dataRilascio'] ?? '';
      $JSON['modified'] = $_POST['dataModifica'] ?? '';
      $JSON['frequency'] = $_POST['frequenza'] ?? '';
//      $JSON['geographical_name'] = $_POST['coperturaGeografica'] ?? '';
      $JSON['license_id'] = $_POST['licenzaRisorsa'] ?? '';
      $JSON['geographical_geonames_url'] = $_POST['urlGeografico'] ?? '';

      $config = $this->config(static::SETTINGS);

      //CKAN
      $idCkan = (int)$_POST['idCkan'];
      $datiCKAN = $this->getDatiCkan($idCkan);
      //Da sostituire
      $orgaResponse = $this->show($datiCKAN['url'],$datiCKAN['api_key'],$datiCKAN['id_orga']);

      $JSON['identifier'] = $_POST['uuid'];
      $JSON['owner_org'] = $orgaResponse->result->id;

      //conforms_to
      $conPOST = $_POST['con'] ?? [];
      $con = '[]';
      if (isset($conPOST)) {
        $avanti = false;
        foreach ($conPOST as $value){
          if ($value['titolostandard'] !== '' || $value['urlstandard'] !== '') {
            $avanti = true;
            $desc[] = [
              'title' => [
                'fr' => '',
                'it' => $value['titolostandard'],
                'de' => '',
                'en' => '',
              ],
              'identifier' => $value['titolostandard'],
              'uri' => $value['urlstandard'],
            ];
          }
        }
        if($avanti){
          $con = str_replace('\"', '"', json_encode($desc, JSON_THROW_ON_ERROR));
        }
      }

      $arrayCreator[] = [
          'creator_identifier' => $_POST['autore_codice'] ?? '',
          'creator_name' => [
            'fr' => '',
            'it' => $_POST['autore_nome'] ?? '',
            'de' => '',
            'en' => '',
          ]
      ];

      if ($idCkan == 1) {
        $catalogo_descrizione = $config->get('catalogo_descrizione');
        $catalogo_homepage = $config->get('catalogo_homepage');
        $catalogo_language = $config->get('catalogo_language');
        $catalogo_modified = $config->get('catalogo_modified');
        $catalogo_titolo = $config->get('catalogo_titolo');
        $catalogo_publisher_url = $config->get('catalogo_publisher_url');
        $catalogo_publisher_email = $config->get('catalogo_publisher_email');
        $catalogo_publisher_type = $config->get('catalogo_publisher_type');
        $catalogo_publisher_uri = $config->get('catalogo_publisher_uri');
        $catalogo_publisher_name = $config->get('catalogo_publisher_name');
      } elseif($idCkan == 2){
        $catalogo_descrizione = $config->get('catalogo_descrizione_basi');
        $catalogo_homepage = $config->get('catalogo_homepage_basi');
        $catalogo_language = $config->get('catalogo_language_basi');
        $catalogo_modified = $config->get('catalogo_modified_basi');
        $catalogo_titolo = $config->get('catalogo_titolo_basi');
        $catalogo_publisher_url = $config->get('catalogo_publisher_url_basi');
        $catalogo_publisher_email = $config->get('catalogo_publisher_email_basi');
        $catalogo_publisher_type = $config->get('catalogo_publisher_type_basi');
        $catalogo_publisher_uri = $config->get('catalogo_publisher_uri_basi');
        $catalogo_publisher_name = $config->get('catalogo_publisher_name_basi');
      }


      $JSON['creator'] = str_replace('\"', '"', json_encode($arrayCreator, JSON_THROW_ON_ERROR));

      $JSON['conforms_to'] = $con;
      //Campi Extra
      $extra[] = [
        "key" => "source_catalog_description",
        "value" => $catalogo_descrizione,
      ];
      $extra[] = [
        "key" => "source_catalog_homepage",
        "value" => $catalogo_homepage,
      ];
      $extra[] = [
        "key" => "source_catalog_language",
        "value" => $catalogo_language,
      ];
      $extra[] = [
        "key" => "contact_name",
        "value" => $_POST['nomeContatto'] ?? ''
      ];
      $extra[] = [
        "key" =>  'contact_email',
        "value" => $_POST['emailContatto'] ?? ''
      ];
//      $extra[] = [
//        "key" =>  'uri',
//        "value" => $_POST['uri'] ?? ''
//      ];
      $extra[] = [
        "key" => "source_catalog_modified",
        "value" => $catalogo_modified,
      ];
      $extra[] = [
        "key" => "source_catalog_title",
        "value" => $catalogo_titolo,
      ];
      $extra[] = [
        "key" =>  "source_catalog_publisher",
        "value" => '{"url": "'.$catalogo_publisher_url.'", "email": "'.$catalogo_publisher_email.'", "type": "'.$catalogo_publisher_type.'", "uri": "'.$catalogo_publisher_uri.'", "name": "'.$catalogo_publisher_name.'"}'
      ];

      if ($idCkan === 2) {
        $comune = $_POST['comune'] ?? '';
        $provincia = $_POST['provincia'] ?? '';
        $regione = $_POST['regione'] ?? '';
        $comune = ucfirst(strtolower($comune));
        $provincia = ucfirst(strtolower($provincia));
        $regione = ucfirst(strtolower($regione));
        $extra[] = [
          "key" => "comune",
          "value" => $comune
        ];
        $extra[] = [
          "key" => "provincia",
          "value" => $provincia
        ];
        $extra[] = [
          "key" => "regione",
          "value" => $regione
        ];

      }

      $JSON['extras'] = $extra;


      //Estensione temporale
      $extPOST = $_POST['ext'] ?? [];
      $ext = '[]';
      if (isset($extPOST)) {
          $arrayExt = [];
          foreach ($extPOST as $value){
            if ($value['datainizio'] !== '') {
              $arrayExt[] =[
                'temporal_start' => $value['datainizio'],
                'temporal_end' => $value['datafine'],
              ];
            }
          }
          $ext = str_replace('\"', '"', json_encode($arrayExt, JSON_THROW_ON_ERROR));
      }
      $JSON['temporal_coverage'] = $ext;


      $temiPOST = $_POST['temi'] ?? [];
      $temi = '[]';
      if (isset($temiPOST)) {
          $arrayTemi = [];
          foreach ($temiPOST as $value){
            if ($value['tema'] !== '') {
              $arraySotto = [];
              foreach ($value['sotto'] as $sotto){
                if ($sotto !== '') {
                  $arraySotto[] = $this->getCkanValueTema($sotto);
                }
              }
              $arrayTemi[] = [
                'subthemes' => $arraySotto,
                'theme' => $this->getCkanValueTema($value['tema']),
              ];
            }
          }
          $temi = str_replace('\"', '"', json_encode($arrayTemi, JSON_THROW_ON_ERROR));
      }
      $JSON['theme'] = $temi;

      $tagsPOST = $_POST['tags'] ?? [];
      $arrayTags = [];
      if (isset($tagsPOST)) {
          foreach ($tagsPOST as $value) {
            if ($value !== '') {
              $arrayTags[] = [
                'vocabulary_id' => NULL,
                'state' => 'active',
                'display_name' => $value,
                'name' => $value,
                'id' => $this->v4()
              ];
            }
          }
      }
      $JSON['tags'] = $arrayTags;

      $operation = (int)$_POST['operation'];
      $url = ''; //$datiCKAN['url'],$datiCKAN['api_key']
      if ($operation === 1) {
          $url = $datiCKAN['url'] . '/api/3/action/package_create';
      } elseif ($operation === 2) {
          $url = $datiCKAN['url'] . '/api/3/action/package_update';
          $dataset = $this->show($datiCKAN['url'],$datiCKAN['api_key'],$JSON['name'],2);
          $arrayResource = [];
          foreach ($dataset->result->resources as $resource){
            $arrayResource[] = (array)$resource;
          }
          $JSON['id'] = $dataset->result->id;
          $JSON['resources'] = $arrayResource;
      }
      $this->aggiornaCKAN($url,$datiCKAN['api_key'],json_encode($JSON));
//      exit();
      return $this->redirect('gestioneutenti.dataset');
    }

    private function getCkanValueTema($id){
      $query = \Drupal::database()->select('taxonomy_term_revision__field_ckan', 'ckan');
      $query->fields('ckan',['field_ckan_value']);
      $query->condition('ckan.entity_id', $id, '=');
      $result = $query->execute();
      if ($result !== NULL) {
        $riga = $result->fetchAssoc();
        return $riga['field_ckan_value'];
      }
      return false;
    }

    public function delete(){
      //CKAN
      $idCkan = (int)$_POST['idCkan'];
      $datiCKAN = $this->getDatiCkan($idCkan);
      $titolo = $_POST['name'];
      $this->deletePackage($datiCKAN['url'], $titolo,$datiCKAN['api_key']);
      //exit();
      return $this->redirect('gestioneutenti.dataset');
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
      $header = [
        'Authorization: '.$ckanapi,
        'X-CKAN-API-Key: '.$ckanapi,
      ];

    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $risultato = curl_exec ($ch);
    curl_close ($ch);
  }

    /* Da rivedere  ????? */
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
    /* Da rivedere */
    private function sostituisci($stringa){
      $arrayReplace = [
        "^",
        ">",
        "<",
        "@",
        "/",
        "?",
        "|",
        "&",
        "+",
        "*",
        ")",
        "(",
        "!",
        "%",
        "#",
        " ",
        "£",
        "\\",
        "\'",
        "\""
      ];
      $stringa = str_replace($arrayReplace,'-',$stringa);
      $stringa= str_replace(["à","è","è","ò","ù","&","§"], ["a", "e", "e", "o", "u", "-", "s"], $stringa);
      return $stringa;
    }

  /**
   * @param $urlckan
   * @param $ckanapi
   * @param $id
   * @param int $type 1 = organization , 2 = dataset
   *
   * @return mixed
   */
  private function show($urlckan, $ckanapi, $id, $type = 1){

	var_dump("URL: " . $urlckan . " - API: " . $ckanapi . " ID: " .$id. " TYPE: " . $type);

      $post = [
        "id" =>	$id,
      ];
      if ($type === 1) {
        $url= $urlckan. "/api/3/action/organization_show";
      } elseif ($type === 2) {
        $url= $urlckan. "/api/3/action/package_show";
      }


      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL =>  $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_HTTPHEADER => array(
          'Authorization: '. $ckanapi,
          'X-CKAN-API-Key: '. $ckanapi
        ),
      ));

      $response = curl_exec($curl);

      return json_decode($response, FALSE, 512, JSON_THROW_ON_ERROR);
  }

    /**
     * @param $idCkan
     *
     * @return string[]
     */
    private function getDatiCkan($idCkan): array {
      $arrayReturn = [
        'url' => '',
        'api_key' => '',
        'id_orga' => '',
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
        if ($idCkan === 1) {
          $arrayReturn['api_key'] = $rigaUrl['ckankeyDatigov'];
          $arrayReturn['id_orga'] = $rigaUrl['email'];
        }
        if ($idCkan === 2) {
          $arrayReturn['api_key'] = $rigaUrl['ckankeyBasigov'];
          $arrayReturn['id_orga'] = $rigaUrl['telefono'];
        }
      }
      return $arrayReturn;
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


    private function aggiornaCKAN($url, $apiKey, $json){

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

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
      $header = [
        'Authorization: '.$apiKey,
        'X-CKAN-API-Key: '.$apiKey,
        'Content-Type: application/json',
        'Accept: */*',
      ];
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_URL,$url);

  //    echo '<pre>';
  //        var_dump($json);
  //    echo '</pre>';

      $risultato = curl_exec ($ch);
  //    echo '<pre>';
  //        var_dump($risultato);
  //    echo '</pre>';
      curl_close ($ch);
    }

    public function checkNameDataset($nameDataset, $idCkan){
        $datiCkan = $this->getDatiCkan($idCkan);
        $nameDataset = strtolower($this->compattaStringa('--',$this->sostituisci($nameDataset),'-'));

        $url = $datiCkan['url'] . '/api/3/action/package_show?id=' . $nameDataset;

        $config = $this->config(static::SETTINGS);
        $apiKey = '';
        if ($idCkan == 1) {
          $apiKey = $config->get('api_key_dati');
        } elseif ($idCkan == 2) {
          $apiKey =  $config->get('api_key_basi');
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL =>  $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => false,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: '. $apiKey,
            'X-CKAN-API-Key: '. $apiKey
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        return new JsonResponse([
          'status' => $response->success,
        ]);
    }

}
