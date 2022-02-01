<?php

/**
 * @file
 */
namespace Drupal\menu_ckan\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\menu_ckan\Controller\JsonController;

// use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates a 'Menu base dati' Block
 * @Block(
 * id = "block_menubasedati",
 * admin_label = @Translation("Block Menu base dati"),
 * )
 */
class MenuBaseDatiBlock extends BlockBase {

  private $jsonController; 

  /**
   * {@inheritdoc}
   */

  public function build() {
    $this->jsonController = new JsonController();

    $_GET['Cerca'] = trim($_GET['Cerca']);
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

    $uri = getenv('CKAN_HOST_BASE_DATI') . ':' . getenv('CKAN_PORT_BASE_DATI') . '/api/3/action/package_search?facet.field=["categoria","macrocategoria","regione","provincia","comune","holder_name","tags"]&facet.limit=-1&rows=0';

    // if (isset($_GET['holder_name'])) {
    //   $ret_org = 'holder_name:' . $_GET['holder_name'];
    // }

    // if ($ret_org) {
    //   $query .= '&fq=(' . $ret_org;
    // }

    // if (isset($_GET['macrocategoria'])) {
    //   $ret_macrocategoria = 'macrocategoria:' . $_GET['macrocategoria'];
    // }

    // if ($ret_macrocategoria) {
    //   if ($ret_org) $query .= '+' . $ret_macrocategoria;
    //   else $query .= '&fq=(' . $ret_macrocategoria;
    // }

    // if (isset($_GET['categoria'])) {
    //   $ret_categoria = 'categoria:' . $_GET['categoria'];
    // }

    // if ($ret_categoria) {
    //   if ($ret_macrocategoria || $ret_org) $query .= '+' . $ret_categoria;
    //   else $query .= '&fq=(' . $ret_categoria;
    // }

    // if (isset($_GET['regione'])) {
    //   $ret_regione = 'regione:' . $_GET['regione'];
    // }

    // if ($ret_regione) {
    //   if ($ret_macrocategoria || $ret_org || $ret_categoria) $query .= '+' . $ret_regione;
    //   else $query .= '&fq=(' . $ret_regione;
    // }

    // if (isset($_GET['provincia'])) {
    //   $ret_provincia = 'provincia:' . $_GET['provincia'];
    // }

    // if ($ret_provincia) {
    //   if ($ret_macrocategoria || $ret_org || $ret_categoria || $ret_regione) $query .= '+' . $ret_provincia;
    //   else $query .= '&fq=(' . $ret_provincia;
    // }

    // if (isset($_GET['comune'])) {
    //   $ret_comune = 'comune:' . $_GET['comune'];
    // }

    // if ($ret_comune) {
    //   if ($ret_macrocategoria || $ret_org || $ret_categoria || $ret_regione || $ret_provincia) $query .= '+' . $ret_comune;
    //   else $query .= '&fq=(' . $ret_comune;
    // }

    // if ($ret_macrocategoria || $ret_org || $ret_categoria || $ret_regione || $ret_provincia || $ret_comune) $query .= ')';

    // if ($param['Cerca']) {
    //   $query .= '&q=' . $param['Cerca'];
    // }

    // $uri = $uri . $query;

    try {
      if(!$this->jsonController->checkJsonFile('json_fetch_file.txt')) {
        $response = _menu_ckan_fetch_file($uri);
        $this->jsonController->writeJsonFile('json_fetch_file.txt', json_encode($response));
      } else {
        $response = json_decode($this->jsonController->getJsonFileContent('json_fetch_file.txt')); 
      }
      
    }
    catch(RequestException $e) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

    }

    $s .= '<head>
            <script src="https://code.jquery.com/jquery-2.1.3.js"></script>
            <script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
          </head>';

    $s .= '<div class="pl-3 pr-3 pt-2">';
    $s .= '<div class="row d-flex justify-content-center pr-0 pl-0 pt-3" style="margin-top: 3px;" id="rigaRicercaAvanzata">';
    $s .= '<div class="col">';

    ///// TAGS //////
    $sep_array = '';
    $obj = array();
    $result = $response
      ->result
      ->search_facets
      ->tags->items;

    $value = '';
    $cresult = count($result);

    if(!$this->jsonController->checkJsonFile('json_tags.txt')) {
      foreach ($result as $results) {
        $holder = '' . $results->display_name . '';
        if ($_GET['tags'] == $holder) {
          $url = $current_path;
          $sep = '';
          if ($_GET['holder_name']) {
            $url .= $sep . 'holder_name=' . $_GET['holder_name'];
            $sep = '&';
          }
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
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
          if ($_GET['macrocategoria']) {
            $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
            $sep = '&';
          }
          if ($_GET['Cerca']) {
            $url .= $sep . 'Cerca=' . $_GET['Cerca'];
            $sep = '&';
          }
          if ($results->name) {
            $url .= $sep . "tags=" . $results->display_name . "";
            $sep = '&';
          }

          $url = str_replace('\"', '', $url);
          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
	    'link' => $url
          );
          
          $value = $results->display_name;

        }
        else {
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
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
            $sep = '&';
          }
          if ($_GET['provincia']) {
            $url .= $sep . 'provincia=' . $_GET['provincia'];
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
            $url .= $sep . "tags=" . $results->display_name . "";
            $sep = '&';
          }
          $url = str_replace('"', '', $url);

          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => $url
          );
        }
      }

      $b = array();
      $b = array_reverse($obj);
      $a = '';
      $a = json_encode($b);
      // $a = str_replace("'", "&apos;", $a);
      $a = str_replace("%27", "&apos;", $a);
      $a = str_replace("%20", " ", $a);

      $this->jsonController->writeJsonFile('json_tags.txt', $a);
    } else {
      $a = $this->jsonController->getJsonFileContent('json_tags.txt');
    }

     // Aggiunta del field per la ricerca del titolo e della descrizione. 
     $s .= '
	<h4 class="mb-4 d-block" style="margin-top: -20px !important;">Cerca le basi di dati</h4>
	<div class="form-group mb-2">
		<form method="get" id="research" name="research" action="/base-dati">
			<input value="'.$_GET['Cerca'].'" type="text" class="autocomplete border pt-4 pb-4 pr-0 sidebar-input-search" placeholder="Per titolo e descrizione" id="Cerca" name="Cerca">
			<span class="autocomplete-search-icon pr-0 mr-2" aria-hidden="true">
				<button class="btn p-0" type="submit" id="button-1"><svg class="icon icon-sm"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-search"></use></svg></button>
			</span><label for="Cerca" class="sr-only">Per titolo e descrizione</label>'; 

	$s .= '<script>
			$("#research").submit(function(){
				$(this).find("input:text").each(function(){
					$(this).val($.trim($(this).val()));
				});
			});
		</script>';

	$tags_hidden = str_replace('"', '', $_GET['tags']);
	$holder_name_hidden = str_replace('"', '', $_GET['holder_name']);
	$holder_name_hidden = str_replace('%22', '', $holder_name_hidden);
	$macrocategoria_hidden = str_replace('"', '', $_GET['macrocategoria']);
	$categoria_hidden = str_replace('"', '', $_GET['categoria']);
	$comune_hidden = str_replace('"', '', $_GET['comune']);
	$provincia_hidden = str_replace('"', '', $_GET['provincia']);
	$regione_hidden = str_replace('"', '', $_GET['regione']);

	$s .= isset($_GET['tags']) ? '<input type="hidden" value='.$tags_hidden.' name="tags" />' : '';
	$s .= isset($_GET['holder_name']) ? '<input type="hidden" value="%22'.$holder_name_hidden.'%22" name="holder_name" />' : '';
	$s .= isset($_GET['macrocategoria']) ? '<input type="hidden" value="%22'.$macrocategoria_hidden.'%22" name="macrocategoria" />' : '';
	$s .= isset($_GET['categoria']) ? '<input type="hidden" value="%22'.$categoria_hidden.'%22" name="categoria" />' : '';
	$s .= isset($_GET['comune']) ? '<input type="hidden" value="%22'.$comune_hidden.'%22" name="comune" />' : '';
	$s .= isset($_GET['provincia']) ? '<input type="hidden" value="%22'.$provincia_hidden.'%22" name="provincia" />' : '';
	$s .= isset($_GET['regione']) ? '<input type="hidden" value="%22'.$regione_hidden.'%22" name="regione" />' : '';
	
	$s .= '</form>
	    </div>';

    // Aggiunta del field per la ricerca delle keywords.
    $s .= '<div class="form-group mb-4"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 sidebar-input-search" placeholder="Per parola chiave" id="keywords" name="autocomplete-Keyword"><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-Keyword" class="sr-only">Per parola chiave</label></div>';

    $s .= '<script>
            var tags = '.$a.';
            
            $(\'#keywords\').autocomplete({
                minLength: 3,
                source: function (request, response) {
                  response($.map(tags, function (obj, key) {
                    var text = obj.text.toUpperCase();
                    var link = obj.link;
                    if (text.indexOf(request.term.toUpperCase()) != -1) {				
                      return {
                        label: obj.text,
                        link: obj.link
                      }
                    } else {
                      return null;
                    }
                  }));			
                },

                focus: function(event, ui) {
                  event.preventDefault();
                },

                select: function(event, ui) {
                  event.preventDefault();
                    
                  var target = ui.item.link;;
                  var location = window.location;
              
                  var currentUrl = new URL(location, window.location.origin);
                  var currentUrlParams = new URLSearchParams(currentUrl.search);
              
                  var tempURL = new URL(target, window.location.origin);
                  var tempURLParams = new URLSearchParams(tempURL.search);
              
                  for(var pair of tempURLParams.entries()) {
                    currentUrlParams.set(pair[0], pair[1]);
                  }
              
                  currentUrlParams.set(\'page\', 0);
              
                  var newURL = location.pathname + "?" + currentUrlParams.toString();
              
                  window.location.href = newURL;
                }
            });	
          </script>';

    ///// AMMINISTRAZIONI //////
    // $s .= '<h4 class="mb-4 mt-0 d-block">Ammistrazioni</h4>';
    $sep_array = '';
    $obj = array();
    $result = $response
      ->result
      ->search_facets
      ->holder_name->items;

    $value = '';
    $elencoCataloghi = array();
    $cresult = count($result);

    if(!$this->jsonController->checkJsonFile('json_administrations.txt')) {
      foreach ($result as $results) {
        $holder = '"' . $results->display_name . '"';
        if ($_GET['holder_name'] == $holder) {
          $url = $current_path;
          $sep = '';
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
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
          if ($_GET['macrocategoria']) {
            $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
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
          if ($results->name) {
            $url .= $sep . "holder_name=%22" . $results->display_name . "%22";
            $sep = '&';
          }

          $url = str_replace('\"', '%22', $url);
          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => $url
          );
          $value = $results->display_name;

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
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
            $sep = '&';
          }
          if ($_GET['provincia']) {
            $url .= $sep . 'provincia=' . $_GET['provincia'];
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
          if ($_GET['tags']) {
            $url .= $sep . 'tags=' . $_GET['tags'];
            $sep = '&';
          }
          if ($results->name) {
            $url .= $sep . "holder_name=%22" . $results->display_name . "%22";
            $sep = '&';
          }
          $url = str_replace('"', '%22', $url);

          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => $url
          );
        }
      }

      $hng = str_replace('"', '', $_GET['holder_name']); 
      $b = array();
      $b = array_reverse($obj);
      $a = '';
      $a = json_encode($b);
      // $a = str_replace("'", "&apos;", $a);
      $a = str_replace("%27", "&apos;", $a);
      $a = str_replace("%20", " ", $a);

      $this->jsonController->writeJsonFile('json_administrations.txt', $a);
    } else {
      $a = $this->jsonController->getJsonFileContent('json_administrations.txt');
    }

    // $s .= '<div class="form-group mb-3"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 sidebar-input-search" style="margin-top: -35px !important;" placeholder="Per nome amministrazione" id="autocomplete-Amministrazioni" name="autocomplete-Amministrazioni" data-autocomplete=\'' . $a . '\'><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-Amministrazioni" class="sr-only">Amministrazioni</label></div>';
    $s .= '<div class="form-group mb-4 textfield-base-dati-first-block"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 sidebar-input-search" placeholder="Per nome amministrazione" id="amministrazioni" name="autocomplete-Amministrazioni"><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-Amministrazioni" class="sr-only">Amministrazioni</label></div>';

    $s .= '<script>
            var amministrazioni = '.$a.';
            
            $(\'#amministrazioni\').autocomplete({
                minLength: 3,
                source: function (request, response) {
                  response($.map(amministrazioni, function (obj, key) {
                    var text = obj.text.toUpperCase();
                    var link = obj.link;
                    if (text.indexOf(request.term.toUpperCase()) != -1) {				
                      return {
                        label: obj.text,
                        link: obj.link
                      }
                    } else {
                      return null;
                    }
                  }));			
                },

                focus: function(event, ui) {
                  event.preventDefault();
                },

                select: function(event, ui) {
                  event.preventDefault();
                  
                  var target = ui.item.link;;
                  var location = window.location;
              
                  var currentUrl = new URL(location, window.location.origin);
                  var currentUrlParams = new URLSearchParams(currentUrl.search);
              
                  var tempURL = new URL(target, window.location.origin);
                  var tempURLParams = new URLSearchParams(tempURL.search);
              
                  for(var pair of tempURLParams.entries()) {
                    currentUrlParams.set(pair[0], pair[1]);
                  }
              
                  currentUrlParams.set(\'page\', 0);
              
                  var newURL = location.pathname + "?" + currentUrlParams.toString();
 
                  window.location.href = newURL;
                }
            });	
          </script>';

    ////// REGIONI ///////////
    $sep_array = '';
    $obj = array();
    $result = $response
      ->result
      ->search_facets
      ->regione
      ->items;

    $value = '';
    $elencoCataloghi = array();
    $c = 0;
    $max = 21;
    $cresult = count($result);

    if(!$this->jsonController->checkJsonFile('json_regions.txt')) {
      foreach ($result as $results) {
        $holder = '"' . $results->display_name . '"';
        if ($_GET['regione'] == $holder) {
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
          if ($_GET['provincia']) {
            $url .= $sep . 'provincia=' . $_GET['provincia'];
            $sep = '&';
          }
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
            $sep = '&';
          }
          if ($_GET['macrocategoria']) {
            $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
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
          if ($results->name) {
            $url .= $sep . "regione=%22" . $results->display_name . "%22";
            $sep = '&';
          }
          $url = str_replace('"', '%22', $url);

          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => "'" . $url . "'"
          );
          $value = $results->display_name;

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
          if ($_GET['holder_name']) {
            $url .= $sep . 'holder_name=' . $_GET['holder_name'];
            $sep = '&';
          }
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
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
          if ($results->name) {
            $url .= $sep . "regione=%22" . $results->display_name . "%22";
            $sep = '&';
          }
          $url = str_replace('"', '%22', $url);
          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => $url
          );

        }
        $c++;
        $sep_array = ',';
      }

      $b = array();
      $b = array_reverse($obj);
      $a = json_encode($b);
      // $a = str_replace("'", "&apos;", $a);
      $a = str_replace("%27", "&apos;", $a);
      $a = str_replace("%20", " ", $a);

      $this->jsonController->writeJsonFile('json_regions.txt', $a);
    } else {
      $a = $this->jsonController->getJsonFileContent('json_regions.txt');
    }

    $s .= '<h4 class="mb-4 mt-0 d-block">Cerca per territorio</h4>';
    // $s .= '<div class="form-group mb-0"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 mb-2 sidebar-input-search" placeholder="Regione " id="autocomplete-regioni" name="autocomplete-regioni" data-autocomplete=\'' . $a . '\'><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-regioni" class="sr-only">Regioni</label></div>';
    $s .= '<div class="form-group mb-0"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 mb-2 sidebar-input-search" placeholder="Regionale" id="regioni" name="autocomplete-regioni"><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-regioni" class="sr-only">Regioni</label></div>';

    $s .= '<script>
            var regioni = '.$a.';
            
            $(\'#regioni\').autocomplete({
                minLength: 3,
                source: function (request, response) {
                  response($.map(regioni, function (obj, key) {
                    var text = obj.text.toUpperCase();
                    var link = obj.link;
                    if (text.indexOf(request.term.toUpperCase()) != -1) {				
                      return {
                        label: obj.text,
                        link: obj.link
                      }
                    } else {
                      return null;
                    }
                  }));			
                },

                focus: function(event, ui) {
                  event.preventDefault();
                },

                select: function(event, ui) {
                  event.preventDefault();

                  var target = ui.item.link;;
                  var location = window.location;
              
                  var currentUrl = new URL(location, window.location.origin);
                  var currentUrlParams = new URLSearchParams(currentUrl.search);
              
                  var tempURL = new URL(target, window.location.origin);
                  var tempURLParams = new URLSearchParams(tempURL.search);
              
                  for(var pair of tempURLParams.entries()) {
                    currentUrlParams.set(pair[0], pair[1]);
                  }
              
                  currentUrlParams.set(\'page\', 0);
              
                  var newURL = location.pathname + "?" + currentUrlParams.toString();
              
                  window.location.href = newURL;
                }
            });	
          </script>';

    ///// PROVINCIA //////
    $sep_array = '';
    $obj = array();
    $result = $response
      ->result
      ->search_facets
      ->provincia->items;

    $value = '';
    $elencoCataloghi = array();

    $cresult = count($result);

    if(!$this->jsonController->checkJsonFile('json_provinces.txt')) {
      foreach ($result as $results) {
        $holder = '"' . $results->display_name . '"';
        if ($_GET['provincia'] == $holder) {
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
          if ($_GET['comune']) {
            $url .= $sep . 'comune=' . $_GET['comune'];
            $sep = '&';
          }
          if ($_GET['macrocategoria']) {
            $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
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
          if ($results->name) {
            $url .= $sep . "provincia=%22" . $results->display_name . "%22";
            $sep = '&';
          }

          $url = str_replace('\"', '%22', $url);

          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => "'" . $url . "'"
          );

          $value = $results->display_name;
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
          if ($_GET['holder_name']) {
            $url .= $sep . 'holder_name=' . $_GET['holder_name'];
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
          if ($_GET['tags']) {
            $url .= $sep . 'tags=' . $_GET['tags'];
            $sep = '&';
          }
          if ($results->name) {
            $url .= $sep . "provincia=%22" . $results->display_name . "%22";
            $sep = '&';
          }

          $url = str_replace('"', '%22', $url);

          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => $url
          );        
        }

        $c++;
        $sep_array = ',';
      }

      $b = array();
      $b = array_reverse($obj);
      $a = '';
      $a = json_encode($b);
      // $a = str_replace("'", "&apos;", $a);
      $a = str_replace("%27", "&apos;", $a);
      $a = str_replace("%20", " ", $a);

      $this->jsonController->writeJsonFile('json_provinces.txt', $a);
    } else {
      $a = $this->jsonController->getJsonFileContent('json_provinces.txt');
    }

    // $s .= '<div class="form-group mb-0"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 mb-2 sidebar-input-search" placeholder="Provincia" id="autocomplete-provincia" name="autocomplete-provincia" data-autocomplete=\'' . $a . '\'><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-provincia" class="sr-only">Provincia</label></div>';
    $s .= '<div class="form-group mb-0"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 mb-2 sidebar-input-search" placeholder="Provinciale" id="provincia" name="autocomplete-provincia"><span class="autocomplete-icon" aria-hidden="true"></span><label for="autocomplete-provincia" class="sr-only">Provincia</label></div>';

    $s .= '<script>
            var provincia = '.$a.';
            
            $(\'#provincia\').autocomplete({
                minLength: 3,
                source: function (request, response) {
                  response($.map(provincia, function (obj, key) {
                    var text = obj.text.toUpperCase();
                    var link = obj.link;
                    if (text.indexOf(request.term.toUpperCase()) != -1) {				
                      return {
                        label: obj.text,
                        link: obj.link
                      }
                    } else {
                      return null;
                    }
                  }));			
                },

                focus: function(event, ui) {
                  event.preventDefault();
                },

                select: function(event, ui) {
                  event.preventDefault();
                  
                  var target = ui.item.link;;
                  var location = window.location;
              
                  var currentUrl = new URL(location, window.location.origin);
                  var currentUrlParams = new URLSearchParams(currentUrl.search);
              
                  var tempURL = new URL(target, window.location.origin);
                  var tempURLParams = new URLSearchParams(tempURL.search);
              
                  for(var pair of tempURLParams.entries()) {
                    currentUrlParams.set(pair[0], pair[1]);
                  }
              
                  currentUrlParams.set(\'page\', 0);
              
                  var newURL = location.pathname + "?" + currentUrlParams.toString();
              
                  window.location.href = newURL;
                }
            });	
          </script>';

    ///// COMUNE //////
    $sep_array = '';
    $obj = array();
    $result = $response
      ->result
      ->search_facets
      ->comune->items;

    $value = '';
    $elencoCataloghi = array();

    $cresult = count($result);

    if(!$this->jsonController->checkJsonFile('json_municipalities.txt')) {
      foreach ($result as $results) {
        $holder = '"' . $results->display_name . '"';
        if ($_GET['comune'] == $holder) {
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
          if ($_GET['macrocategoria']) {
            $url .= $sep . 'macrocategoria=' . $_GET['macrocategoria'];
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
          if ($results->name) {
            $url .= $sep . "comune=%22" . $results->display_name . "%22";
            $sep = '&';
          }

          $url = str_replace('\"', '%22', $url);
          
          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
            'text' => $results->display_name,
            'link' => $url
          );
          
          $value = $results->display_name;

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
          if ($_GET['holder_name']) {
            $url .= $sep . 'holder_name=' . $_GET['holder_name'];
            $sep = '&';
          }
          if ($_GET['provincia']) {
            $url .= $sep . 'provincia=' . $_GET['provincia'];
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
          if ($_GET['tags']) {
            $url .= $sep . 'tags=' . $_GET['tags'];
            $sep = '&';
          }
          if ($results->name) {
            $url .= $sep . "comune=%22" . $results->display_name . "%22";
            $sep = '&';
          }
          $url = str_replace('"', '%22', $url);

          $obj[] = (object)array(
            // 'text' => $results->display_name . "(" . $results->count . ")",
	    'text' => $results->display_name,
            'link' => $url
          );        
        }
      }

      $b = array();
      $b = array_reverse($obj);
      $a = '';
      $a = json_encode($b);
      // $a = str_replace("'", "&apos;", $a);
      $a = str_replace("%27", "&apos;", $a);
      $a = str_replace("%20", " ", $a);

      $this->jsonController->writeJsonFile('json_municipalities.txt', $a);
    } else {
      $a = $this->jsonController->getJsonFileContent('json_municipalities.txt');
    }

    // $s .= '<div class="form-group mb-0"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 mb-2 sidebar-input-search" placeholder="Comune" id="autocomplete-comune" name="autocomplete-comune" data-autocomplete=\'' . $a . '\'><span class="autocomplete-icon" aria-hidden="true"></span> <label for="autocomplete-comune" class="sr-only">Comune</label></div>';
    $s .= '<div class="form-group mb-0"><input value="" type="search" class="autocomplete border pt-4 pb-4 pr-0 mb-2 sidebar-input-search" placeholder="Comunale" id="comune" name="autocomplete-comune"><span class="autocomplete-icon" aria-hidden="true"></span> <label for="autocomplete-comune" class="sr-only">Comune</label></div>';

    $s .= '<script>
            var comune = '.$a.';
            
            $(\'#comune\').autocomplete({
                minLength: 3,
                source: function (request, response) {
                  response($.map(comune, function (obj, key) {
                    var text = obj.text.toUpperCase();
                    var link = obj.link;
                    if (text.indexOf(request.term.toUpperCase()) != -1) {				
                      return {
                        label: obj.text,
                        link: obj.link
                      }
                    } else {
                      return null;
                    }
                  }));			
                },

                focus: function(event, ui) {
                  event.preventDefault();
                },

                select: function(event, ui) {
                  event.preventDefault();
                  
                  var target = ui.item.link;;
                  var location = window.location;
              
                  var currentUrl = new URL(location, window.location.origin);
                  var currentUrlParams = new URLSearchParams(currentUrl.search);
              
                  var tempURL = new URL(target, window.location.origin);
                  var tempURLParams = new URLSearchParams(tempURL.search);
              
                  for(var pair of tempURLParams.entries()) {
                    currentUrlParams.set(pair[0], pair[1]);
                  }
              
                  currentUrlParams.set(\'page\', 0);
              
                  var newURL = location.pathname + "?" + currentUrlParams.toString();
              
                  window.location.href = newURL;
                }
            });	
          </script>';
    
    $s .= '</div>';
    $s .= '</div>';

    /////////////////// CATEGORIE //////////////////////
    $result = $response
      ->result
      ->search_facets
      ->categoria->items;

    $s .= '</div>';

    return ['#markup' => \Drupal\Core\Render\Markup::create($s)];
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
