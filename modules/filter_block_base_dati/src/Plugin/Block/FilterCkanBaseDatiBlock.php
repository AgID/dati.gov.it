<?php

/**
 * @file
 */
namespace Drupal\filter_block_base_dati\Plugin\Block;

use Drupal\Core\Block\BlockBase;

// use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates a 'Filter block  Ckan' Block
 * @Block(
 * id = "block_filterbasedatickan",
 * admin_label = @Translation("Filter base dati block"),
 * )
 */
class FilterCkanBaseDatiBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
    $param['Cerca'] = $_GET['Cerca'];
    $param['groups'] = $_GET['groups'];
    $param['organization'] = $_GET['organization'];
    $param['holder_name'] = $_GET['holder_name'];
    $current_uri = \Drupal::request()->getRequestUri();
    $filter_exists = false; 

    if (strpos($current_uri,'?') === false){
      $current_path = $current_uri . '?';
    } else {
      $current_path = substr($current_uri, 0, strpos($current_uri,'?')+1);
    }

    $uri = getenv('CKAN_HOST_BASE_DATI') . ':' . getenv('CKAN_PORT_BASE_DATI') . '/api/3/action/package_search?facet.field=["categoria","macrocategoria","comune","provincia","regione","holder_name"]&facet.limit=-1&rows=0';

    if (isset($_GET['holder_name'])) {
      $ret_org = 'holder_name:' . $_GET['holder_name'];
    }
    if ($ret_org) {
      $query .= '&fq=(' . $ret_org;
    }
    if (isset($_GET['macrocategoria'])) {
      $ret_macrocategoria = 'macrocategoria:' . $_GET['macrocategoria'];
    }
    if ($ret_macrocategoria) {
      if ($ret_org) $query .= '+' . $ret_macrocategoria;
      else $query .= '&fq=(' . $ret_macrocategoria;
    }
    if (isset($_GET['categoria'])) {
      $ret_categoria = 'categoria:' . $_GET['categoria'];
    }
    if ($ret_categoria) {
      if ($ret_macrocategoria || $ret_org) $query .= '+' . $ret_categoria;
      else $query .= '&fq=(' . $ret_categoria;
    }
    if (isset($_GET['regione'])) {
      $ret_regione = 'regione:' . $_GET['regione'];
    }
    if ($ret_regione) {
      if ($ret_macrocategoria || $ret_org || $ret_categoria) $query .= '+' . $ret_regione;
      else $query .= '&fq=(' . $ret_regione;
    }
    if (isset($_GET['provincia'])) {
      $ret_provincia = 'provincia:' . $_GET['provincia'];
    }
    if ($ret_provincia) {
      if ($ret_macrocategoria || $ret_org || $ret_categoria || $ret_regione) $query .= '+' . $ret_provincia;
      else $query .= '&fq=(' . $ret_provincia;
    }
    if (isset($_GET['comune'])) {
      $ret_comune = 'comune:' . $_GET['comune'];
    }
    if ($ret_comune) {
      if ($ret_macrocategoria || $ret_org || $ret_categoria || $ret_regione || $ret_provincia) $query .= '+' . $ret_comune;
      else $query .= '&fq=(' . $ret_comune;
    }
    if ($ret_macrocategoria || $ret_org || $ret_categoria || $ret_regione || $ret_provincia || $ret_comune) $query .= ')';

    if ($param['Cerca']) {
      $query .= '&q=' . $param['Cerca'];
    }

    try {
      $response = _menu_ckan_fetch_file($uri);

    }
    catch(RequestException $e) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    $s = '';

    /*** AMMINISTRAZIONI ***/
    $result = $response
      ->result
      ->search_facets
      ->holder_name->items;
    $s .= '<div class="link-list-wrapper"><ul class="link-list">';
    $filter = array();
    foreach ($result as $results) {
      $string = urldecode($_GET['holder_name']);
      $string = str_replace('"', '', $string);
      if ($string == $results->name) {
        $url = $current_path;
        $sep = '';
        if ($_GET['macrocategoria']) {
          $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
          $sep = '&';
        }
        if ($_GET['categoria']) {
          $url .= $sep . 'categoria=' . $_GET['categoria'];
          $sep = '&';
        }
        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        if ($_GET['tags']) {
          $url .= $sep . 'tags=' . $_GET['tags'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);

        $htmltemp = '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
        $htmltemp .= '<div class="d-flex flex-column bd-highlight mb-0">
                        <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Amministrazione:</strong></div>
                        <div class="p-0 bd-highlight pl-3 pr-1">'.$results->display_name.'</div>
                      </div>';
        $htmltemp .= '</li>'; 

        $filter[] = $htmltemp; 
        $filter_exists = true; 
      }
    }
    for ($i = count($filter);$i >= 0;$i--) {
      $s .= $filter[$i];
    }
    $s .= '</ul></div>';

    /*** MACROCATEGORIA ***/
    $result = $response
      ->result
      ->search_facets
      ->macrocategoria->items;
    $s .= '<div class="link-list-wrapper"><ul class="link-list">';
    $filter = array();
    foreach ($result as $results) {
      $string = urldecode($_GET['macrocategoria']);
      $string = str_replace('"', '', $string);
      if ($string == $results->name) {
        $url = $current_path;
        $sep = '';
        if ($_GET['holder_name']) {
          $url .= $sep . 'holder_name=' . $_GET['holder_name'];
          $sep = '&';
        }
        if ($_GET['categoria']) {
          $url .= $sep . 'categoria=' . $_GET['categoria'];
          $sep = '&';
        }
        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        if ($_GET['tags']) {
          $url .= $sep . 'tags=' . $_GET['tags'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);
        $htmltemp = '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
        $htmltemp .= '<div class="d-flex flex-column bd-highlight mb-0">
                        <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Macrocategoria:</strong></div>
                        <div class="p-0 bd-highlight pl-3 pr-1">'.$results->display_name.'</div>
                      </div>';
        $htmltemp .= '</li>'; 
                          
        $filter_exists = true;
        $filter[] = $htmltemp; 
      }
    }
    for ($i = count($filter);$i >= 0;$i--) {
      $s .= $filter[$i];
    }
    $s .= '</ul></div>';

    /*** CATEGORIA ***/
    $result = $response
      ->result
      ->search_facets
      ->categoria->items;
    $s .= '<div class="link-list-wrapper"><ul class="link-list">';
    $filter = array();
    foreach ($result as $results) {
      $string = urldecode($_GET['categoria']);
      $string = str_replace('"', '', $string);
      if ($string == $results->name) {
        $url = $current_path;
        $sep = '';
        if ($_GET['holder_name']) {
          $url .= $sep . 'holder_name=' . $_GET['holder_name'];
          $sep = '&';
        }
        if ($_GET['macrocategoria']) {
          $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
          $sep = '&';
        }
        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        if ($_GET['tags']) {
          $url .= $sep . 'tags=' . $_GET['tags'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);
        
        $filter_exists = true;
        $htmltemp = '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
        $htmltemp .= '<div class="d-flex flex-column bd-highlight mb-0">
                        <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Categoria:</strong></div>
                        <div class="p-0 bd-highlight pl-3 pr-1">'.$results->display_name.'</div>
                      </div>';
        $htmltemp .= '</li>'; 

        $filter[] = $htmltemp;
      }
    }
    for ($i = count($filter);$i >= 0;$i--) {
      $s .= $filter[$i];
    }
    $s .= '</ul></div>';
    /*** REGIONE ***/
    $result = $response
      ->result
      ->search_facets
      ->regione->items;
    $s .= '<div class="link-list-wrapper"><ul class="link-list">';
    $filter = array();
    foreach ($result as $results) {
      $string = urldecode($_GET['regione']);
      $string = str_replace('"', '', $string);
      if ($string == $results->name) {
        $url = $current_path;
        $sep = '';
        if ($_GET['holder_name']) {
          $url .= $sep . 'holder_name=' . $_GET['holder_name'];
          $sep = '&';
        }
        if ($_GET['macrocategoria']) {
          $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
          $sep = '&';
        }
        if ($_GET['categoria']) {
          $url .= $sep . 'categoria=' . $_GET['categoria'];
          $sep = '&';
        }
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        if ($_GET['tags']) {
          $url .= $sep . 'tags=' . $_GET['tags'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);

        $htmltemp = '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
        $htmltemp .= '<div class="d-flex flex-column bd-highlight mb-0">
                        <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Regione:</strong></div>
                        <div class="p-0 bd-highlight pl-3 pr-1">'.$results->display_name.'</div>
                      </div>';
        $htmltemp .= '</li>'; 

        $filter[] = $htmltemp;
        $filter_exists = true;
      }
    }
    for ($i = count($filter);$i >= 0;$i--) {
      $s .= $filter[$i];
    }
    $s .= '</ul></div>';
    /*** PROVINCIA ***/
    $result = $response
      ->result
      ->search_facets
      ->provincia->items;
    $s .= '<div class="link-list-wrapper"><ul class="link-list">';
    $filter = array();
    foreach ($result as $results) {
      $string = urldecode($_GET['provincia']);
      $string = str_replace('"', '', $string);
      if ($string == $results->name) {
        $url = $current_path;
        $sep = '';
        if ($_GET['holder_name']) {
          $url .= $sep . 'holder_name=' . $_GET['holder_name'];
          $sep = '&';
        }
        if ($_GET['macrocategoria']) {
          $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
          $sep = '&';
        }
        if ($_GET['categoria']) {
          $url .= $sep . 'categoria=' . $_GET['categoria'];
          $sep = '&';
        }
        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        if ($_GET['tags']) {
          $url .= $sep . 'tags=' . $_GET['tags'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);
        
        $htmltemp = '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
        $htmltemp .= '<div class="d-flex flex-column bd-highlight mb-0">
                        <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Provincia:</strong></div>
                        <div class="p-0 bd-highlight pl-3 pr-1">'.$results->display_name.'</div>
                      </div>';
        $htmltemp .= '</li>'; 

        $filter[] = $htmltemp;
        $filter_exists = true;
      }
    }
    for ($i = count($filter);$i >= 0;$i--) {
      $s .= $filter[$i];
    }
    $s .= '</ul></div>';

    /*** COMUNE ***/
    $result = $response
      ->result
      ->search_facets
      ->comune->items;
    $s .= '<div class="link-list-wrapper"><ul class="link-list">';
    $filter = array();
    foreach ($result as $results) {
      $string = urldecode($_GET['comune']);
      $string = str_replace('"', '', $string);
      if ($string == $results->name) {
        $url = $current_path;
        $sep = '';
        if ($_GET['holder_name']) {
          $url .= $sep . 'holder_name=' . $_GET['holder_name'];
          $sep = '&';
        }
        if ($_GET['macrocategoria']) {
          $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
          $sep = '&';
        }
        if ($_GET['categoria']) {
          $url .= $sep . 'categoria=' . $_GET['categoria'];
          $sep = '&';
        }
        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        if ($_GET['tags']) {
          $url .= $sep . 'tags=' . $_GET['tags'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);
        
        $htmltemp = '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
        $htmltemp .= '<div class="d-flex flex-column bd-highlight mb-0">
                        <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Comune:</strong></div>
                        <div class="p-0 bd-highlight pl-3 pr-1">'.$results->display_name.'</div>
                      </div>';
        $htmltemp .= '</li></ul>'; 

        $filter[] = $htmltemp;
        $filter_exists = true;
      }
    }
    for ($i = count($filter);$i >= 0;$i--) {
      $s .= $filter[$i];
    }

    if(isset($_GET['tags'])) {
      $s  .= '<div class="link-list-wrapper"><ul class="link-list">';
      $url = $current_path;
      $sep = '';
      if ($_GET['holder_name']) {
        $url .= $sep . 'holder_name=' . $_GET['holder_name'];
        $sep = '&';
      }
      if ($_GET['macrocategoria']) {
        $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
        $sep = '&';
      }
      if ($_GET['categoria']) {
        $url .= $sep . 'categoria=' . $_GET['categoria'];
        $sep = '&';
      }
      if ($_GET['regione']) {
        $url .= $sep . 'regione=' . $_GET['regione'];
        $sep = '&';
      }
      if ($_GET['provincia']) {
        $url .= $sep . 'provincia=' . $_GET['provincia'];
        $sep = '&';
      }
      if ($_GET['comune']) {
        $url .= $sep . 'comune=' . $_GET['comune'];
        $sep = '&';
      }
      if ($_GET['Cerca']) {
        $url .= $sep . 'Cerca=' . $_GET['Cerca'];
        $sep = '&';
      }

      $url = str_replace("'", "%27", $url);
      $tags = str_replace('"', '', $_GET['tags']); 
      $s .= '<li class="leaf d-flex"><a href=\'' . $url . '\'  class="list-item p-0" ><svg class="icon icon-primary align-bottom mt-0"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg></a>';
      $s .= '<div class="d-flex flex-column bd-highlight mb-0">
              <div class="p-0 bd-highlight"><strong class="pl-3 pr-1">Parole chiave:</strong></div>
              <div class="p-0 bd-highlight pl-3 pr-1">'.$tags.'</div>
            </div>';
      $s .= '</li>';
      $s .= '</ul>'; 
      $s .= '</div>'; 
      $filter_exists = true;
    }

    if ($filter_exists) {
      $s = '<div class="pl-3 pr-3 pt-2"><h4 class="mb-2 mt-0 d-block">Filtri attivati</h4>' . $s . '</div>';
    }

    $s .= '</ul></div>';

    return ['#markup' => \Drupal\Core\Render\Markup::create($s),
      // We leave this empty.
    ];

  }
  public function getCacheMaxAge() {
    return 0;
  }

}

