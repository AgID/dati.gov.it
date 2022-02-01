<?php

/**
 * @file
 */
namespace Drupal\menu_ckan\Plugin\Block;

use Drupal\Core\Block\BlockBase;

// use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates a 'Menu base dati enti' Block
 * @Block(
 * id = "block_menubasedati_enti",
 * admin_label = @Translation("Block Menu base dati enti"),
 * )
 */
class CategorieBaseDatiBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  public function build() {
    $param['Cerca'] = $_GET['Cerca'];
    $param['groups'] = $_GET['groups'];
    $param['organization'] = $_GET['organization'];
    $param['holder_name'] = $_GET['holder_name'];
    $param['format'] = $_GET['format'];
    $param['licenze'] = $_GET['licenze'];
    $param['tags'] = $_GET['tags'];

    $current_uri = \Drupal::request()->getRequestUri();
    $current_path_no_alias = \Drupal::service('path.current')->getPath();
    $current_path = \Drupal::service('path.alias_manager')->getAliasByPath($current_path_no_alias);

    if (substr($current_path, -1) !== '?') {
      $current_path .= '?';
    }
    if ($current_path[0] == '/') {
      $current_path = substr($current_path, 1);
    }

    $uri = getenv('CKAN_HOST_BASE_DATI') . ':' . getenv('CKAN_PORT_BASE_DATI') . '/api/3/action/package_search?facet.field=["categoria","macrocategoria","regione","provincia","comune","holder_name"]&facet.limit=-1&rows=0';

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

    $uri = $uri . $query;
    try {
      $response = _menu_ckan_fetch_file($uri);
    }
    catch(RequestException $e) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    /////////////////// CATEGORIE //////////////////////
    $result = $response
      ->result
      ->search_facets
      ->categoria->items;

    $s .= '<div class="collapse-header mt-2 pl-3 border-0" id="headingA2">';
    $s .= '<button class="pl-0 border-0" data-toggle="collapse" data-target="#accordion2" aria-expanded="false" aria-controls="accordion2">';
    $s .= '<h2>Categorie Enti</h2>';
    $s .= '</button>';
    $s .= '</div>';
    $s .= '<div id="accordion2" class="collapse" role="tabpanel" aria-labelledby="headingA2""><div class="collapse-body">';
    $s .= '<div class="link-list-wrapper"> <ul class="link-list">';

    $elencoCataloghi = array();
    $c = 0;
    $max = 25;
    $cresult = count($result);
    foreach ($result as $results) {
      $holder = '"' . $results->name . '"';
      if ($_GET['holder_name'] == $holder) {
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
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }

        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }
        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);
        if ($cresult < $max) {
          $elencoCataloghi[] = '<li class="leaf"> <a href=\'' . $url . '\'  class="button-menu-dkan list-item focus-element"><span> (-)</span></a><span>' . $results->display_name . '</span></li>';
        }
        else {
          $elencoCataloghi[] = '<li class="leaf hidden load-more"> <a href=\'' . $url . '\'  class="button-menu-dkan list-item focus-element"><span> (-)</span></a><span>' . $results->display_name . '</span></li>';
        }
      }
      else {
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
        if ($_GET['provincia']) {
          $url .= $sep . 'provincia=' . $_GET['provincia'];
          $sep = '&';
        }
        if ($_GET['comune']) {
          $url .= $sep . 'comune=' . $_GET['comune'];
          $sep = '&';
        }

        if ($_GET['regione']) {
          $url .= $sep . 'regione=' . $_GET['regione'];
          $sep = '&';
        }

        if ($_GET['Cerca']) {
          $url .= $sep . 'Cerca=' . $_GET['Cerca'];
          $sep = '&';
        }

        if ($results->name) {
          $url .= $sep . 'categoria="' . $results->name . '"';
          $sep = '&';
        }
        $url = str_replace("'", "%27", $url);
        if ($cresult < $max) {
          $elencoCataloghi[] = '<li class="leaf"><a class="list-item focus-element" href=\'' . $url . '\'>' . $results->display_name . ' (' . $results->count . ')' . '</span></a></li>';
        }
        else {
          $elencoCataloghi[] = '<li class="leaf hidden load-more"><a class="list-item focus-element" href=\'' . $url . '\'>' . $results->display_name . ' (' . $results->count . ')' . '</span></a></li>';

        }
      }
      $cresult--;
    }

    if (count($result) <= 0) {
      $s .= 'Non ci sono elementi da selezionare.';
    }

    for ($i = count($elencoCataloghi);$i >= 0;$i--) {
      $s .= $elencoCataloghi[$i];
    }

    $s .= '<li class="button-load-more"><button class="btn-load-more-filter btn btn-outline"><span class="label-more">Mostra tutto</span><span class="label-less" style="display:none;">Mostra meno</span></button></li>';
    $s .= '</ul>';
    $s .= '</div>';
    $s .= '</div>';
    $s .= '</div>'; 

    return ['#markup' => \Drupal\Core\Render\Markup::create($s)];
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
