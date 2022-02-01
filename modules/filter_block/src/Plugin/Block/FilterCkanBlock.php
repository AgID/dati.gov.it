<?php


namespace Drupal\filter_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use \GuzzleHttp\Client;


/**
 * Creates a 'Filter block  Ckan' Block
 * @Block(
 * id = "block_filterckan",
 * admin_label = @Translation("Filter block"),
 * )
 */
class FilterCkanBlock extends BlockBase {

  /**
   * @inheritDoc
   */
  public function build() {
      $cerca = $_GET['Cerca'] ?? NULL;
      $groups = $_GET['groups'] ?? NULL;
      $organization = $_GET['organization'] ?? NULL;
      $holder_name = $_GET['holder_name'] ?? NULL;
      $format = $_GET['format'] ?? NULL;
      $licenze = $_GET['licenze'] ?? NULL;
      $tags = $_GET['tags'] ?? NULL;
      $ordinamento = $_GET['ordinamento'] ?? NULL;

      $uri = '/view-dataset';

      $html = '';

      $separator = '?';

      if(!$cerca && !$groups && !$organization && !$holder_name && !$format && !$licenze && !$tags){
        return [ '#markup' => \Drupal\Core\Render\Markup::create($html) ];
      }

      /**  **/
      if ($cerca){
        $uri .= $separator . 'Cerca=' . $cerca;
        $separator = '&';
      }

//      if ($tags){
//        $uri .= $separator . 'tags=' . $tags;
//        $separator = '&';
//      }

      if ($holder_name){
        $uri .= $separator . 'holder_name=' . $holder_name;
        $separator = '&';
      }

      if($ordinamento){
         $uri .= $separator . 'ordinamento=' . $ordinamento;
         $separator = '&';
      }


    /** CKAN */
    $urlCkanAll = '/api/3/action/package_search?facet.field=["organization","groups","license_id","res_format","holder_name"]&facet.limit=-1&rows=0';
    $urlCkanLicenze = '/api/3/action/license_list';
    $objResAll = $this->callCKAN($urlCkanAll)->result->search_facets;
    $objResLicenze = $this->callCKAN($urlCkanLicenze)->result;


      $arrayCroups = $groups ? explode('|',$groups) : [];
      $arrayOrganization = $organization ? explode('|',$organization) : [];
      $arrayFormat = $format ? explode('|',$format) : [];
      $arrayLicenze = $licenze ? explode('|',$licenze) : [];
      $arrayTags = $tags ? explode('|',$tags) : [];

      if ($tags){
        $arrayTemp = [
          0 => [ 'array' => $arrayOrganization, 'type' => 'organization=' ],
          1 => [ 'array' => $arrayFormat, 'type' => 'format=' ],
          2 => [ 'array' => $arrayLicenze, 'type' => 'licenze=' ],
          3 => [ 'array' => $arrayCroups, 'type' => 'groups=' ],
        ];
        $this->myfunc($html,$separator,$uri,$arrayTags,$arrayTemp,[], 'tags=', 'Parole chiave:');
      }

      if ($groups){
        $arrayTemp = [
          0 => [ 'array' => $arrayOrganization, 'type' => 'organization=' ],
          1 => [ 'array' => $arrayFormat, 'type' => 'format=' ],
          2 => [ 'array' => $arrayLicenze, 'type' => 'licenze=' ],
          3 => [ 'array' => $arrayTags, 'type' => 'tags=' ],
        ];
        $this->myfunc($html,$separator,$uri,$arrayCroups,$arrayTemp,$this->getArrayCkan($objResAll->groups->items), 'groups=', 'Tema:');
      }

      if ($organization){
        $arrayTemp = [
          0 => [ 'array' => $arrayCroups, 'type' => 'groups=' ],
          1 => [ 'array' => $arrayFormat, 'type' => 'format=' ],
          2 => [ 'array' => $arrayLicenze, 'type' => 'licenze=' ],
          3 => [ 'array' => $arrayTags, 'type' => 'tags=' ],
        ];
        $this->myfunc($html,$separator,$uri,$arrayOrganization,$arrayTemp,$this->getArrayCkan($objResAll->organization->items), 'organization=', 'Catalogo:');
      }

      if ($licenze){
        $arrayTemp = [
          0 => [ 'array' => $arrayCroups, 'type' => 'groups=' ],
          1 => [ 'array' => $arrayFormat, 'type' => 'format=' ],
          2 => [ 'array' => $arrayOrganization, 'type' => 'organization=' ],
          3 => [ 'array' => $arrayTags, 'type' => 'tags=' ],
        ];
        $this->myfunc($html,$separator,$uri,$arrayLicenze,$arrayTemp,$this->getArrayCkan($objResLicenze,2), 'licenze=', 'Licenze:');
      }

      if ($format){
        $arrayTemp = [
          0 => [ 'array' => $arrayCroups, 'type' => 'groups=' ],
          1 => [ 'array' => $arrayOrganization, 'type' => 'organization=' ],
          2 => [ 'array' => $arrayLicenze, 'type' => 'licenze=' ],
          3 => [ 'array' => $arrayTags, 'type' => 'tags=' ],
        ];
        $this->myfunc($html,$separator,$uri,$arrayFormat,$arrayTemp,$this->getArrayCkan($objResAll->res_format->items), 'format=', 'Formati:');
      }

      $htmlPrima .= '<div class="p-3 border-top">';
      $htmlPrima .= '<div class="link-list-wrapper">';
      $htmlPrima .= '<ul class="link-list mb-0">';

      $htmlDopo = '</ul>';
      $htmlDopo .= '</div>';
      $htmlDopo .= '</div>';

      return [ '#markup' => \Drupal\Core\Render\Markup::create($htmlPrima.$html.$htmlDopo) ];
    }


  /**
   * @param $ckanResult
   * @param int $type 1(default) = ->name, ->display_name; 2 = ->id , ->title
   *
   * @return array
   */
  private function getArrayCkan($ckanResult, int $type = 1){
        $arrayReturn = [];
        if ($type === 1){
          foreach ($ckanResult as $value){
            $arrayReturn[$value->name] = $value->display_name;
          }
        } else if ($type === 2) {
          foreach ($ckanResult as $value){
            $arrayReturn[$value->id] = $value->title;
          }
        }
        return $arrayReturn;
    }

    private function myfunc(string &$html,String $separator ,String $url,Array $array, array $arrayForUrl,array $arrayCkanValue,String $get ,String $name){
      $this->createUrl($url, $separator, $arrayForUrl[0]['array'], $arrayForUrl[0]['type']);
      $this->createUrl($url, $separator, $arrayForUrl[1]['array'], $arrayForUrl[1]['type']);
      $this->createUrl($url, $separator, $arrayForUrl[2]['array'], $arrayForUrl[2]['type']);
      $this->createUrl($url, $separator, $arrayForUrl[3]['array'], $arrayForUrl[3]['type']);
      foreach ($array as $key => $value){
        $this->addHtmlLi($html, $this->concatGet($url,$separator,$array,$get,$key), $arrayCkanValue[$value] ?? $value,$name);
      }
    }

    private function addHtmlLi(String &$html, $url, $value, $name){
        $html .=  '<li class="leaf d-flex">';
        $html .=  '<a href="'.$url.'"  class="list-item p-0 focus-element">';
        $html .=  '<svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg>';
        $html .=  '</a>';        
        $html .= '<div class="d-flex flex-column bd-highlight mb-3">
                    <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">'.$name.'</strong></div>
                    <div class="p-0 bd-highlight pl-3 pr-1">'.$value.'</div>
                  </div>';
        $html .=  '</li>';
    }

    private function concatGet(String $url,string $separator , array $array , $get, $tag){
        $separatorTmp = $separator;
        foreach ($array as $key => $value){
          if ($tag !== $key){
            $url .=  $separatorTmp .$get . $value;
            $get = '|';
            $separatorTmp = '';
          }
        }
        if ($separator !== $separatorTmp) { $separator = '&'; }
        return $url;
    }

    private function createUrl(string &$url, string &$separator, array $array, String $type){
        $separatorTemp = $separator;
        foreach ($array as $value){
          $url .= $separatorTemp . $type . $value;
          $type = '|';
          $separatorTemp = '';
        }
      if ($separator !== $separatorTemp) { $separator = '&'; }
    }

    private function callCKAN($url){
      $urLCKAN =  getenv('CKAN_HOST').':'. getenv('CKAN_PORT'). $url;

      $guzzle = new Client();
      $response = $guzzle->get($urLCKAN, ['verify' => false]);
      if ($response->getStatusCode() >= 400) {
          $args = array('%error' => $response->getStatusCode(), '%uri' => $urLCKAN);
          $message = t('HTTP response: %error. URI: %uri', $args);
          throw new \Exception($message);
      }
      return json_decode($response->getBody());
    }

}
