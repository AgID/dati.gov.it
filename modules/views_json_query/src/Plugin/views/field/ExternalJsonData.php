<?php

namespace Drupal\views_json_query\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\Core\Url;
use Drupal\Component\Utility\Xss;
/**
 * Class ExternalJsonData
 *
 * @ViewsField("external_json_data")
 */
class ExternalJsonData extends FieldPluginBase {


  /**
     * Does the field supports multiple field values.
     *
     * @var bool
  */
   public $multiple;

  /**
   * {@inheritdoc}
  */

  protected function defineOptions() {
    $options = parent::defineOptions();


    $options['key'] = array('default' => '');
	  $options['is_image'] = array('default' => FALSE);

    $options['multi_type'] = [
          'default' => '',
        ];
    $options['wrapper'] = array('default' => '');

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['multi_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Display type'),
      '#options' => [
        'wrapper' => $this->t('Wrapper'),
      ],
      '#default_value' => $this->options['multi_type'],
      '#fieldset' => 'multiple_field_settings',
    ];

    $form['key'] = array(
      '#title' => t('Key Chooser'),
      '#description' => t('Choose a Single Key'),
      '#type' => 'textfield',
      '#default_value' => $this->options['key'],
      '#required' => TRUE,
    );

    $form['wrapper'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Wrapper'),
      '#maxlength' => 255,
      '#description' => t('Multiple Fields'),
      '#default_value' => $this->options['wrapper'],
      '#states' => [
        'visible' => [
          ':input[name="options[multi_type]"]' => ['value' => 'wrapper'],
        ],
      ],
      '#fieldset' => 'multiple_field_settings',
    );
    
  }

    /**
   * {@inheritdoc}
   */
  public function getValue($values, $field = NULL) {
	  if(substr($field, 0, 1) == '/'){
		$field = substr($field, 1);
	}
    $alias = isset($field) ? $this->aliases[$field] : $this->field_alias;
    //$alias = $field;
    //var_dump($values->{$alias});
   //var_dump($values);die();
    /* modifica formattazione data 25-09-19 */

    if($alias=="extra_modified"){
      // die('hello world');
      $modified='';
       if(isset($values->extras)){
        foreach($values->extras as $extra){
          if($extra->key == "modified"){
            $modified=$extra->value;
          }
        }
        if($modified){
          $data=explode('-', $modified);
          $day=$data[2];
          $month=$data[1];
          $year=$data[0];
          
          $data_string=$day. '-'.$month.'-'.$year;
          //var_dump($values->extras[10]->value); die();
          
        }
          
         return $data_string;
         }
     }

     if($alias=="extra_modified_view"){
      // die('hello world');
      $modified='';
       if(isset($values->extras)){
        foreach($values->extras as $extra){
          if($extra->key == "modified"){
            $modified=$extra->value;
	    if($modified !== NULL)
	    	$modified=date("Y-m-d", strtotime($modified));
          }
        }

         return $modified;
         }
     }



    if($alias=="metadata_modified"||$alias=="metadata_created"){
     // die('hello world');
      if(isset($values->{$alias})){
        $data_ora=explode('T', $values->{$alias});
               $data=explode('-', $data_ora[0]);
        $day=$data[2];
        $month=$data[1];
        $year=$data[0];
        $ora=explode('.', $data_ora[1]);
        $ora=explode(':', $ora[0]);
        $H=$ora[0];
        $M=$ora[1];
        $S=$ora[2];
        $data_string=$year. '-'.$month.'-'.$day. " ". $H. ':'. $M;
        return $data_string;
        }
    }


    if (isset($values->{$alias})) {
		  return $values->{$alias};
		}
    if (strpos($alias, '[') !== false) {
  		$start = strpos($alias, '[');
  		$end = strpos($alias, ']');
      $idx = substr($alias, $start+1, $end-$start-1);
     // var_dump($idx); die();
      if(isset($idx) && trim($idx)!=''){
    		$idx = intval($idx);
    		$firstPart = substr($alias, 0, $start);
    		$secondPart = substr($alias, $end+1);

    		if (isset($values->{$firstPart})) {
    			$newVal = $values->{$firstPart};

    			if(isset($newVal[$idx])){
    				$r = $this->getValue($newVal[$idx], $secondPart);
    				//var_dump($secondPart);var_dump($r);var_dump($newVal[$idx]); die();

    				return $r;
    			} else {
    				return null;
    			}
    		} else {
    			return null;
    		}
    } else {
      $firstPart = substr($alias, 0, $start);
      $secondPart = substr($alias, $end+1);
      //var_dump($secondPart); die();

      // Recupero le key di resource
      $start1 = strpos($secondPart, '{');
      $end1 = strpos($secondPart, '}');
      $firstType = substr($secondPart, $start1+1, $end1-$start1-1);

     // var_dump($firstType); die();


$keys = explode("/", $firstType);

      $r = array();
      if (isset($values->{$firstPart})) {
        $newVals = $values->{$firstPart};
        //var_dump($newVals); die();
        foreach($newVals as $k => $newVal){

          $r1 = array();
          foreach ($keys as $key) {

            $r1[$key]=$this->getValue($newVal, $key);
          }
          //var_dump($r1);
          $r[]=$r1;

        }
        //var_dump($r); die();
        return $r;

      } else {
        return null;
      }
    }
	} else if (isset($values->{$field})) {
		  return $values->{$field};
		}
  }


	/**
   * {@inheritdoc}
   */
   public function render(ResultRow $values) {
      if ($this->options['multi_type'] == 'wrapper') {
        $wrapper = $this->options['multi_type'] == 'wrapper' ? Xss::filterAdmin($this->options['wrapper']) : '';

        $pos1 = strpos($wrapper, '{');
        $pos2 = strpos($wrapper, '}');
        $firstPos = substr($wrapper, $pos1+2, $pos2-$pos1-2);

        $s ='';
        $valuesType = $this->getValue($values);
//var_dump($values); die();

  $new_value = [];

foreach ($valuesType as $i) {
  if((!isset($new_value[$i['format']])) && (!isset($new_value[$i['name']])) && (!isset($new_value[$i['description']])) )
  if(isset($i['format'])){
    $new_value[$i['format']]=$i;

  }
  else if(isset($i['name'])){
    $new_value[$i['name']]=$i;

  }else
    $new_value[$i['description']]=$i;
  
}
//var_dump($new_value); die();


        foreach ($new_value as $k => $value) {
            $wrapper1 = $wrapper;

          foreach ($value as $key => $valore) {

            $replace = '{{'. $key . '}}';

            $wrapper1 = str_replace($replace, $valore, $wrapper1);

          }

          $s .= $wrapper1;

         }

        $build = [
          '#type' => 'markup',
          '#markup' => $s,
          '#allowed_tags' => ['div', 'i', 'span', 'a', 'ul', 'li']

        ];
        $value = $this->renderer->render($build);

      }

      else {
        $value = $this->getValue($values);

      }
    return $value;

  }

  /**
   * Called to add the field to a query.
   */
  public function query() {
    // Add the field.
    
    
    $this->table_alias = 'json';

    $this->field_alias = $this->query->add_field(
      $this->table_alias,
      $this->options['key'],
      '',
      $this->options
      
    );

  }

}
