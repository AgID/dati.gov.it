<?php
/**
 * @file
 * Definition of Drupal\views_json_query\Plugin\views\field\ExternalImage
 */

namespace Drupal\views_json_query\Plugin\views\query;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\Core\Form\FormStateInterface;
use \GuzzleHttp\Client;

/**
 * External API views query plugin which wraps calls to the external API in order to
 * expose the results to views.
 *
 * @ViewsQuery(
 *   id = "external_json_api",
 *   title = @Translation("External Query"),
 *   help = @Translation("Query against the exposed API.")
 * )
 */
class ExternalQuery extends QueryPluginBase {

  public function ensureTable($table, $relationship = NULL) {
    return '';
  }

  public function addField($table, $field, $alias = '', $params = array()) {
    return $field;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ViewExecutable $view) {
    try {
      if ($contents = $this->fetch_file($this->options['json_file'], $view)) {
        $ret = $this->parse($view, $contents);
      }
    }
    catch(\Exception $e) {
      drupal_set_message(t('Views Json Query') . ': ' . $e->getMessage() , 'error');

      return;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['json_file'] = array(
      '#type' => 'textfield',
      '#title' => t('Json File') ,
      '#default_value' => $this->options['json_file'],
      '#description' => t("The URL or path to the Json file.") ,
      '#maxlength' => 1024,
    );
    $form['row_apath'] = array(
      '#type' => 'textfield',
      '#title' => t('Row Apath') ,
      '#default_value' => $this->options['row_apath'],
      '#description' => t("Apath to records.<br />Apath is just a simple array item find method. Ex:<br /><pre>array('data' => \n\tarray('records' => \n\t\tarray(\n\t\t\tarray('name' => 'yarco', 'sex' => 'male'),\n\t\t\tarray('name' => 'someone', 'sex' => 'male')\n\t\t)\n\t)\n)</pre><br />You want 'records', so Apath could be set to 'data/records'. <br />Notice: prefix '/' or postfix '/' will be trimed, so never mind you add it or not.") ,
      '#required' => true,
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options['json_file'] = array(
      'default' => ''
    );
    $options['row_apath'] = array(
      'default' => ''
    );
    return $options;
  }

  /**
   * Parse.
   */
  function parse(&$view, $contents) {

    $conts = json_decode($contents, false);
    $ret = $conts;
    if (!$ret) {
      return false;
    }

    // Get rows.
    $ret = ($this->options['row_apath']) ? $this->apath($this->options['row_apath'], $ret) : $ret;
    if (!is_array($ret)) {
      $rr[] = $ret;
      $ret = $rr;
    }

    if ($view->getPath() == 'amministrazioni') {
      //var_dump($ret); die();
      $x = $ret;
      $ret = array();
      for ($i = count($x);$i > 0;$i--) {
        $ret[] = $x[$i];
      }
    }

    if ($view->getPath() == 'organizzazioni') {
      //var_dump($ret); die();
      $x = $ret;
      $ret = array();
      for ($i = count($x);$i > 0;$i--) {
        $ret[] = $x[$i];
      }
    }

    if ($view->getPath() == 'view-dataset' || $view->getPath() == 'base-dati') {
      if ($conts->result->count != NULL) {
        $view->total_rows = $conts->result->count;
      } else {
        $view->total_rows = count($conts->result);
      }
    }

    elseif ($view->getPath() == 'amministrazioni') {
      // var_dump(count($conts->result->search_facets->holder_name->items));die('pippo22');
      // var_dump($view); die();
      // $view->pager->total_items = $conts->result->count;
      $view->total_rows = count($conts
        ->result
        ->search_facets
        ->holder_name
        ->items);
    }

    elseif ($view->getPath() == 'organizzazioni') {
      // var_dump(count($conts->result->search_facets->holder_name->items));die('pippo22');
      // $view->current_page=$conts->result->count;
      $view->total_rows = count($conts
        ->result
        ->search_facets
        ->organization
        ->items);
    }

    if (isset($view->pager->options['items_per_page'])) {
      // Hackish execute_count_query implementation.
      $view->pager->total_items = $conts->result->count;

      // var_dump($view->pager->total_items); die();
      if (!empty($this->pager->options['offset'])) {
        $view->pager->total_items -= $view->pager->options['offset'];
      }

      $view->pager->updatePageInfo();
    }

    // Deal with offset & limit.
    $offset = !empty($this->offset) ? intval($this->offset) : 0;
    $limit = !empty($this->limit) ? intval($this->limit) : 0;

    if (count($ret) > $limit) {
      $ret = $limit ? array_slice($ret, $offset, $limit) : array_slice($ret, $offset);
    }

    try {
      $result = array();
      $index = 0;
      foreach ($ret as $row) {
        $result_row = (array)$this->parse_row(NULL, $row, $row);
        $result_row['index'] = $index++;
        $result[] = new ResultRow($result_row);        
      }
      
      if ($view->getPath() == 'amministrazioni' || $view->getPath() == 'organizzazioni') {
        foreach ($result as $res) {
          $res->display_name = trim($res->display_name);
        }
        usort($result, function ($a, $b) {
          return strcasecmp($a->display_name, $b->display_name);
        });
      }

      $view->result = $result;

      if ($view->getPath() == 'view-dataset') $view->total_rows = $conts->result->count;

      return true;
    }
    catch(\Exception $e) {
      $view->result = array();
      if (!empty($view->live_preview)) {
        drupal_set_message(time());
        drupal_set_message($e->getMessage() , 'error');
      }
      else {
        debug($e->getMessage() , 'Views Json Backend');
      }
    }
  }

  /**
   * Fetch data in array according to apath.
   *
   * @param string $apath
   *   Something like '1/name/0'
   *
   * @param array $array
   *
   * @return array
   */
  function apath($apath, $array) {
    $r = & $array;
    $paths = explode('/', trim($apath, '//'));
    foreach ($paths as $path) {
      if (is_array($r) && isset($r[$path])) {
        $r = & $r[$path];
      }
      elseif (is_object($r)) {
        $r = & $r->$path;
      }
      else {
        break;
      }
    }

    return $r;
  }
  /**
   * Parse row.
   *
   * A recursive function to flatten the json object.
   * Example:
   * {person:{name:{first_name:"John", last_name:"Doe"}}}
   * becomes:
   * $row->person/name/first_name = "John",
   * $row->person/name/last_name = "Doe"
   */
  function parse_row($parent_key, $parent_row, &$row) {
    $props = get_object_vars($parent_row);

    foreach ($props as $key => $value) {

      if (is_object($value)) {
        unset($row->$key);
        $this->parse_row(is_null($parent_key) ? $key : $parent_key . '/' . $key, $value, $row);
      }
      else {
        if ($parent_key) {
          $new_key = $parent_key . '/' . $key;
          $row->$new_key = $value;
        }
        else {
          $row->$key = $value;
        }
      }
    }

    return $row;
  }

  /**
   * Fetch file.
   */
  function fetch_file($uri, $view) {
    if((isset($_GET['tags']) && $_GET['tags'] != null) || 
      (isset($_GET['licenze']) && $_GET['licenze'] != null) || 
      (isset($_GET['format']) && $_GET['format'] != null) || 
      (isset($_GET['groups']) && $_GET['groups'] != null) || 
      (isset($_GET['organization']) && $_GET['organization'] != null) || 
      (isset($_GET['rows']) && $_GET['rows'] != null) || 
      (isset($_GET['start']) && $_GET['start'] != null) || 
      (isset($_GET['page']) && $_GET['page'] != null) || 
      (isset($_GET['Cerca']) && $_GET['Cerca'] != null && $_GET['Cerca'] != '') || 
      (isset($_GET['holder_name']) && $_GET['holder_name'] != null) || 
      (isset($_GET['macrocategoria']) && $_GET['macrocategoria'] != null) || 
      (isset($_GET['categoria']) && $_GET['categoria'] != null) || 
      (isset($_GET['regione']) && $_GET['regione'] != null) || 
      (isset($_GET['provincia']) && $_GET['provincia'] != null) || 
      (isset($_GET['comune']) && $_GET['comune'] != null) || 
      (isset($_GET['ordinamento']) && $_GET['ordinamento'] != null)) {

      $parts = parse_url($uri);
      $query = '';
      $query2 = '';

      if (isset($parts['query'])) {
        $query = $parts['query'] . '&';
      }

      if (isset($_GET['Cerca']) && $_GET['Cerca'] !== "") {
        $query2 = $sep2 . 'q=' . implode('+AND+', explode(' ', $_GET['Cerca']));
        $sep2 = '&';
      }

      if (isset($_GET['page'])) {
        $page = (int)$_GET['page'];
        if ($page == '0') {
          $query2 .= $sep2 . 'rows=10&start=0';
          $sep2 = '&';
        }
        else {
          $start = (int)$page;
          $start = $start * 10;
          $query2 .= $sep2 . 'rows=10';
          $query2 .= '&start=' . $start;
          $sep2 = '&';
        }
      }

      if (isset($_GET['groups'])) {
        if (strpos($_GET['groups'], '|') !== false) {
          $ret_group = '(';
          $gruppi = explode('|', $_GET['groups']);
          foreach ($gruppi as $gruppo) {
            $ret_group .= '+' . $gruppo;
          }
          $ret_group = $ret_group . ')';
        }
        else {
          $ret_group = $_GET['groups'];
        }
        $ret_group = 'groups:' . $ret_group;
      }

      if ($ret_group) {
        if ($query) {
          $query .= '&fq=' . $ret_group;
        }
        else {
          $query .= '&fq=' . $ret_group;
        }
      }

      if (isset($_GET['organization'])) {
        $ret_org = 'organization:' . $_GET['organization'];
      }

      if ($ret_org) {
        if ($ret_group) $query .= '+' . $ret_org;
        else $query .= '&fq=' . $ret_org;
      }

      if (isset($_GET['macrocategoria'])) {
        $ret_macrocategoria = 'macrocategoria:' . $_GET['macrocategoria'];
      }

      if ($ret_macrocategoria) {
        if ($ret_group || $ret_org) $query .= '+' . $ret_macrocategoria;
        else $query .= '&fq=' . $ret_macrocategoria;
      }

      if (isset($_GET['categoria'])) {
        $ret_categoria = 'categoria:' . $_GET['categoria'];
      }

      if ($ret_categoria) {
        if ($ret_macrocategoria || $ret_group || $ret_org) $query .= '+' . $ret_categoria;
        else $query .= '&fq=' . $ret_categoria;
      }

      if (isset($_GET['regione'])) {
        $ret_regione = 'regione:' . $_GET['regione'];
      }

      if ($ret_regione) {
        if ($ret_macrocategoria || $ret_group || $ret_org || $ret_categoria) $query .= '+' . $ret_regione;
        else $query .= '&fq=' . $ret_regione;
      }

      if (isset($_GET['provincia'])) {
        $ret_provincia = 'provincia:' . $_GET['provincia'];
      }

      if ($ret_provincia) {
        if ($ret_macrocategoria || $ret_group || $ret_org || $ret_categoria || $ret_regione) $query .= '+' . $ret_provincia;
        else $query .= '&fq=' . $ret_provincia;
      }

      if (isset($_GET['comune'])) {
        $ret_comune = 'comune:' . $_GET['comune'];
      }

      if ($ret_comune) {
        if ($ret_macrocategoria || $ret_group || $ret_org || $ret_categoria || $ret_regione || $ret_provincia) $query .= '+' . $ret_comune;
        else $query .= '&fq=' . $ret_comune;
      }

      if (isset($_GET['ordinamento'])) {
        if ($_GET['ordinamento'] === "2") {
          $query2 .= '&sort=title_string+asc';
        }

        if ($_GET['ordinamento'] === "3") {
          $query2 .= '&sort=title_string+desc';
        }
      }

      if (isset($_GET['holder_name'])) {
        $ret_hol = 'holder_name:' . $_GET['holder_name'];
      }

      if ($ret_hol) {
        if ($ret_macrocategoria || $ret_group || $ret_org || $ret_categoria || $ret_regione || $ret_provincia || $ret_comune) $query .= '+' . $ret_hol;
        else $query .= '&fq=' . $ret_hol;
      }

      if (isset($_GET['licenze'])) {
      	$ret_licen = 'license_title:"' .$_GET['licenze'] . '"';
      }

      if ($ret_licen) {
        if ($ret_group || $ret_org || $ret_hol || $ret_macrocategoria || $ret_group || $ret_org || $ret_categoria || $ret_regione || $ret_provincia || $ret_comune) $query .= '+' . $ret_licen;
        else $query .= '&fq=' . $ret_licen;
      }

      if (isset($_GET['tags']) && trim($_GET['tags']) != '' && $_GET['tags'] != NULL) {
        if (isset($_GET['tags'])) {
          if (strpos($_GET['tags'], '|') !== false) {
            $ret_tag = '(';
            $ret_tags = explode('|', $_GET['tags']);
            $s = '';
            foreach ($ret_tags as $tag) {
              $ret_tag .= $s . 'tags:"' . $tag . '"';
              $s = ' OR ';
            }
            $ret_tag = $ret_tag . ')';
          }
          else {
            $ret_tag = $_GET['tags'];
            $ret_tag = 'tags:"' . $ret_tag . '"';
          }

        }

        if ($ret_tag) {
          if ($query) {
            $query .= '+' . $ret_tag;
          }
          else {
            $query .= '&fq=' . $ret_tag;
          }
        }
      }

      if (isset($_GET['format'])) {
        if (strpos($_GET['format'], '|') !== false) {
          $ret_format = '(';
          $formati = explode('|', $_GET['format']);
          foreach ($formati as $formato) {
            $ret_format .= '+' . $formato;
          }
          $ret_format = $ret_format . ')';
          $ret_format = 'res_format:"' . $ret_format . '"';
        }
        else {
          $ret_format = 'res_format:"' . $_GET['format'] . '"';
        }
      }

      if ($ret_format) {
        if ($query) $query .= '+' . $ret_format;
        else $query .= '&fq=' . $ret_format;
      }

      $parts['query'] = $query . "&" . $query2;

      $uri = (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') . ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') . (isset($parts['user']) ? "{$parts['user']}" : '') . (isset($parts['pass']) ? ":{$parts['pass']}" : '') . (isset($parts['user']) ? '@' : '') . (isset($parts['host']) ? "{$parts['host']}" : '') . (isset($parts['port']) ? ":{$parts['port']}" : '') . (isset($parts['path']) ? "{$parts['path']}" : '') . (isset($parts['query']) ? "?{$parts['query']}" : '') . (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');

    }

    $parsed = parse_url($uri);
    // Check for local file.
    if (empty($parsed['host'])) {
      if (!file_exists($uri)) {
        throw new Exception(t('Local file not found.'));
      }
      return file_get_contents($uri);
    }

    // $destination = 'public://views_json_query';
    // if (!file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
    //   throw new Exception(t('Files directory either cannot be created or is not writable.'));
    // }

    $headers = array();
    // $cache_file = 'views_json_query_' . md5($uri);
    // if ($cache = \Drupal::cache()->get($cache_file)) {
    //   $last_headers = $cache->data;

    //   if (!empty($last_headers['etag'])) {
    //     $headers['If-None-Match'] = $last_headers['etag'];
    //   }
    //   if (!empty($last_headers['last-modified'])) {
    //     $headers['If-Modified-Since'] = $last_headers['last-modified'];
    //   }
    // }

    // Rebuild the JSON file URL.
    $request_options = array(
      'headers' => empty($headers) ? null : $headers
    );
    $request_context_options = array();

    if (parse_url($uri, PHP_URL_SCHEME) == 'https') {
      foreach ($this->options as $option => $value) {
        if (strpos($option, 'ssl_') === 0 && $value) {
          $request_context_options['ssl'][substr($option, 4) ] = $value;
        }
      }
    }
    if ($request_context_options) {
      $request_options['context'] = stream_context_create($request_context_options);
    }
    $guzzle = new Client();
//  $result = drupal_http_request($uri, $request_options);
    $option = ['verify' => false, 'headers' => empty($request_options) ? null : $request_options, ];

    $response = $guzzle->get($uri, $option);

    if ($response->getStatusCode() >= 400) {
      $args = array(
        '%error' => $response->getStatusCode() ,
        '%uri' => $uri
      );
      $message = t('HTTP response: %error. URI: %uri', $args);
      throw new \Exception($message);
    }

    // $cache_file_uri = "$destination/$cache_file";
    // if ($response->getStatusCode() == 304) {
    //   if (file_exists($cache_file_uri)) {
    //     return file_get_contents($cache_file_uri);
    //   }
    //   // We have the headers but no cache file. :(
    //   // Run it back.
    //   //cache_clear_all($cache_file, 'cache');
    //   \Drupal::cache('cache')->invalidateAll();
    //   return $this->fetch_file($uri);
    // }

    // As learned from Feeds caching mechanism, save to file.
    // file_unmanaged_save_data((string)$response->getBody() , $cache_file_uri, FILE_EXISTS_REPLACE);
    // cache_set($cache_file, $result->headers);
    // \Drupal::cache()->set($cache_file, $response->getHeaders());

    return $response->getBody();
  }

  /**
   * Add field.
   */
  function add_field($table, $field, $alias = '', $params = array()) {
    $alias = $field;

    // Add field info array.
    if (empty($this->fields[$field])) {
      $this->fields[$field] = array(
        'field' => $field,
        'table' => $table,
        'alias' => $alias,
      ) + $params;
    }

    return $field;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ViewExecutable $view) {
    // Mostly modeled off of \Drupal\views\Plugin\views\query\Sql::build()
    // Store the view in the object to be able to use it later.
    $this->view = $view;

    $view->initPager();

    // Let the pager modify the query to add limits.
    $view
      ->pager
      ->query();

    $view->build_info['query'] = $this->query();
    $view->build_info['count_query'] = $this->query(true);

  }

  /**
   * {@inheritdoc}
   */
  public function query($get_count = false) {
    // Fill up the $query array with properties that we will use in forming the
    // API request.
    $query = [];

    // Iterate over $this->where to gather up the filtering conditions to pass
    // along to the API. Note that views allows grouping of conditions, as well
    // as group operators. This does not apply to us, as the Fitbit API has no
    // such concept, nor do we support this concept for filtering connected
    // Fitbit Drupal users.
    if (isset($this->where)) {
      foreach ($this->where as $where_group => $where) {
        foreach ($where['conditions'] as $condition) {
          // Remove dot from begining of the string.
          $field_name = ltrim($condition['field'], '.');
          $query[$field_name] = $condition['value'];
        }
      }
    }

    return $query;
  }

  /**
   * Adds a simple condition to the query. Collect data on the configured filter
   * criteria so that we can appropriately apply it in the query() and execute()
   * methods.
   *
   * @param $group
   *   The WHERE group to add these to; groups are used to create AND/OR
   *   sections. Groups cannot be nested. Use 0 as the default group.
   *   If the group does not yet exist it will be created as an AND group.
   * @param $field
   *   The name of the field to check.
   * @param $value
   *   The value to test the field against. In most cases, this is a scalar. For more
   *   complex options, it is an array. The meaning of each element in the array is
   *   dependent on the $operator.
   * @param $operator
   *   The comparison operator, such as =, <, or >=. It also accepts more
   *   complex options such as IN, LIKE, LIKE BINARY, or BETWEEN. Defaults to =.
   *   If $field is a string you have to use 'formula' here.
   *
   * @see \Drupal\Core\Database\Query\ConditionInterface::condition()
   * @see \Drupal\Core\Database\Query\Condition
   */
  public function addWhere($group, $field, $value = NULL, $operator = NULL) {
    // Ensure all variants of 0 are actually 0. Thus '', 0 and NULL are all
    // the default group.
    if (empty($group)) {
      $group = 0;
    }

    // Check for a group.
    if (!isset($this->where[$group])) {
      $this->setWhereGroup('AND', $group);
    }

    $this->where[$group]['conditions'][] = ['field' => $field, 'value' => $value, 'operator' => $operator, ];
  }

  public function placeholder() {

  }

  public function addWhereExpression() {
  }

}

