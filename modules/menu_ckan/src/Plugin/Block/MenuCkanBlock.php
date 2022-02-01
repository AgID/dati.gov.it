<?php

/**
 * @file
 */
namespace Drupal\menu_ckan\Plugin\Block;

use Drupal\Core\Block\BlockBase;
// use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates a 'Menu Ckan' Block
 * @Block(
 * id = "block_menuckan",
 * admin_label = @Translation("Block Menu Ckan"),
 * )
 */
class MenuCkanBlock extends BlockBase {
 
    /**
     * {@inheritdoc}
     */
    
    public function build() {
      $param['Cerca']        = $_GET['Cerca'];
      $param['groups']       = $_GET['groups'];
      $param['organization'] = $_GET['organization'];
      $param['holder_name']  = $_GET['holder_name'];
      $param['format']       = $_GET['format'];
      $param['licenze']      = $_GET['licenze'];
      $param['tags']         = $_GET['tags'];
      $current_uri           = \Drupal::request()->getRequestUri();

      if (strpos($current_uri,'?') === false){
        $current_path = $current_uri . '?';
      } else {
        $current_path = substr($current_uri, 0, strpos($current_uri,'?')+1);
      }

      $uri = getenv('CKAN_HOST').':'. getenv('CKAN_PORT').'/api/3/action/package_search?facet.field=["organization","groups","license_id","res_format","holder_name"]&facet.limit=-1&rows=0';
         
      if(isset($_GET['groups'])) {
        if(strpos($_GET['groups'], '|') !== false) { 
          $ret_group='(';
          $gruppi=explode('|', $_GET['groups']);
          
          foreach($gruppi as $gruppo){
            $ret_group .='+' .$gruppo;            
          }
          
          $ret_group= $ret_group .')';
        } else {
          $ret_group=$_GET['groups'];
        }
              
        $ret_group='groups:'. $ret_group;
      }

      if($ret_group) {
        if($query) {
          $query .= '&fq='. $ret_group;
        } else {
          $query .= '&fq='. $ret_group;
        }
      }

          if (isset($_GET['organization'])) {
          $ret_org = 'organization:'.$_GET['organization'];
          }
          if($ret_org){
          if($ret_group)
            $query .= '+'. $ret_org;
          else
          $query .= '&fq='. $ret_org;
          }
          if (isset($_GET['holder_name'])) {
            $ret_hol = 'holder_name:'.$_GET['holder_name'];
          }
          if($ret_hol){
            if($ret_group||$ret_org)
              $query .= '+'. $ret_hol;
            else
            $query .= '&fq='. $ret_hol;
          }
          if (isset($_GET['licenze'])) {
          $ret_licen = 'license_id:' .$_GET['licenze'];
          }
          if($ret_licen){
          if($ret_group||$ret_org)
            $query .= '+'. $ret_licen;
          else
          $query .= '&fq='. $ret_licen;
          }
          if (isset($_GET['format'])){
          if(strpos($_GET['format'], '|')!==false){ 
              $ret_format='(';
              $formati=explode('|', $_GET['format']);
              foreach($formati as $formato){
                $ret_format .='+' .$formato;            
              }
              $ret_format= $ret_format .')';
              $ret_format= 'res_format:' .$ret_format;
            }
          else{
              $ret_format='res_format:' .$_GET['format'];
            }
          }
          if($ret_format){
            if($query)
              $query .='+'.$ret_format;
            else
            $query .='&fq='.$ret_format;
          }
          if(isset($_GET['tags']) && trim($_GET['tags']) != '' && $_GET['tags'] != NULL) {
            if(strpos($_GET['tags'], '|')!==false){ 
                $ret_tag='(';
                $ret_tags=explode('|', $_GET['tags']);
               $s='';
                foreach($ret_tags as $tag){
                  $ret_tag .=$s . 'tags:' .$tag;     
                  $s = ' OR ';       
                }
                $ret_tag= $ret_tag .')';
              }
           else{
                $ret_tag=$_GET['tags'];
                $ret_tag='tags:'. $ret_tag;
              }
              
             if($ret_tag){
               if($query){
                 $query .= '+'. $ret_tag;
               }
               else{
                $query .= '&fq='. $ret_tag;
               }
             }
            }
      
             if($param['Cerca']){
              $query .='&q='.$param['Cerca'];
             }
             
          $uri=$uri .$query;

         
    
      	try { 
          $response = _menu_ckan_fetch_file($uri);
      	} catch(RequestException $e) {
      		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
      	}

        $result = $response->result->search_facets->groups->items;
        $expanded = count($result) > 0 ? 'true' : 'false';
        $show = $expanded === 'true' ? 'show' : ''; 

        $s  = '';
        $s .= '<div id="accordionDiv1" class="collapse-div" role="tablist">
                <div class="collapse-header" id="headingA1">';
        $s .=   '<button data-toggle="collapse" data-target="#accordion1" aria-expanded="'.strval($expanded).'" aria-controls="accordion1">';
        $s .=    '<h2> Temi </h2>';
        $s .=    '</button>';
        $s .=     '</div>
                <div id="accordion1" class="collapse '.strval($show).'" role="tabpanel" aria-labelledby="headingA1" data-parent="#accordionDiv1">
                <div class="collapse-body">';
        $s .= '<div class="link-list-wrapper"> <ul class="link-list font-menu-ckan">';
                 $elencoGruppi= array();
        foreach ($result as $key => $results) {
         
          if (in_array($results->name, $gruppi) || $_GET['groups']==$results->name){ 
            
            if($gruppi){            
              $group_param=$gruppi;              
            if(in_array($results->name, $group_param))
              unset($group_param[array_search($results->name, $group_param)]);
            $groups=implode('|', $group_param );
          }
          else
           $groups=NULL;
            $url= $current_path;
            $sep='';
             if($_GET['organization']){
              $url .=$sep .'organization=' .$_GET['organization'];
              $sep='&';
             }   
             if($_GET['holder_name']){
              $url .=$sep .'holder_name='.$_GET['holder_name'];
              $sep='&';
             } 
             if($_GET['format']){
              $url .=$sep .'format=' .$_GET['format'];
              $sep='&';
             }
             if($_GET['Cerca']){
              $url .=$sep .'Cerca=' .$_GET['Cerca'];
              $sep='&';
             }
            if($_GET['ordinamento']){
              $url .=$sep .'ordinamento=' .$_GET['ordinamento'];
              $sep='&';
            }
             if($_GET['licenze']){
              $url .=$sep .'licenze=' .$_GET['licenze'];
              $sep='&';
             }
             if(isset($_GET['tags']) && trim($_GET['tags']) != '' && $_GET['tags'] != NULL) {
              $url .=$sep .'tags=' .$_GET['tags'];
              $sep='&';
             }
             if($groups){
              $url .=$sep .'groups=' .$groups;
              $sep='&';
             }
             
            $elencoGruppi[]= '<li class="leaf d-flex"> <a href=\''.$url .'\'  class="pr-0 list-item focus-element"><span class="mr-2">(-)</span></a><span class="d-flex align-items-center">' . $results->display_name . '</span></li>';
          }
           else {
            $url= $current_path;
            $sep='';
             if($_GET['organization']){
              $url .=$sep .'organization=' .$_GET['organization'];
              $sep='&';
             }   
             if($_GET['holder_name']){
              $url .=$sep .'holder_name='.$_GET['holder_name'];
              $sep='&';
             } 
             if($_GET['format']){
              $url .=$sep .'format=' .$_GET['format'];
              $sep='&';
             }
             if($_GET['licenze']){
              $url .=$sep .'licenze=' .$_GET['licenze'];
              $sep='&';
             }
             
             if($_GET['Cerca']){
              $url .=$sep .'Cerca=' .$_GET['Cerca'];
              $sep='&';
             }
             if($_GET['ordinamento']){
               $url .=$sep .'ordinamento=' .$_GET['ordinamento'];
               $sep='&';
             }
             if(isset($_GET['tags']) && trim($_GET['tags']) != '' && $_GET['tags'] != NULL) {
              $url .=$sep .'tags=' .$_GET['tags'];
              $sep='&';
             }
            
             if($_GET['groups']){             
                $groups= $_GET['groups'] .'|' .$results->name; 
             }
             else
              $groups=$results->name;
             if($groups){
              $url .=$sep .'groups=' .$groups;
              $sep='&';
             }
             $elencoGruppi[]= '<li class="leaf"><a class="list-item focus-element" href=\''.$url .'\'> <span > <span class="font-icon-select-1 mr-1 icon-'. $results->name.' "></span> ' . $results->display_name . ' (' . $results->count .')' . '</span></a></li>';
         
           }
           }

           if(count($result) <= 0) {
             $s .= 'Non ci sono elementi da selezionare.';
           }

           for($i=count($elencoGruppi); $i>=0; $i--){
             $s.=$elencoGruppi[$i];
           }
   
        $s .= '</ul></div>';
        $s .= ' </div> </div>';


        /*** ORGANIZZAZIONI ***/

        try{
          $response = _menu_ckan_fetch_file($uri);
        } catch(RequestException $e) {
           throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $result = $response->result->search_facets->organization->items;
        $s .= ' <div class="collapse-header" id="headingA2">';
        $s .=   '<button data-toggle="collapse" data-target="#accordion2" aria-expanded="false" aria-controls="accordion2">';
        $s .=    '<h2> Cataloghi </h2>';
        $s .=    '</button>';
        $s .=     '</div>
                <div id="accordion2" class="collapse" role="tabpanel" aria-labelledby="headingA2" data-parent="#accordionDiv1">
                <div class="collapse-body">';
        $s .= '<div class="link-list-wrapper"> <ul class="link-list font-menu-ckan">';
        $elencoCataloghi=array();
        $c=0;
        $max=25; // Abbiamo incrementato questa variabile per mostrare 100 cataloghi.
        $cresult=count($result);
        foreach ($result as $results) {
         if($_GET['organization']==$results->name){
          $url= $current_path;
          $sep='';
           if($_GET['groups']){
            $url .=$sep .'groups=' .$_GET['groups'];
            $sep='&';
           }
           if(isset($_GET['tags']) && trim($_GET['tags']) != '' && $_GET['tags'] != NULL) {
            $url .=$sep .'tags=' .$_GET['tags'];
            $sep='&';
           } 
           if($_GET['holder_name']){
            $url .=$sep .'holder_name='.$_GET['holder_name'];
            $sep='&';
           }   
           if($_GET['format']){
            $url .=$sep .'format=' .$_GET['format'];
            $sep='&';
           }
           
           if($_GET['Cerca']){
            $url .=$sep .'Cerca=' .$_GET['Cerca'];
            $sep='&';
           }
           if($_GET['ordinamento']){
             $url .=$sep .'ordinamento=' .$_GET['ordinamento'];
             $sep='&';
           }
           if($_GET['licenze']){
            $url .=$sep .'licenze=' .$_GET['licenze'];
            $sep='&';
           }
           if($cresult<$max){
          $elencoCataloghi[]= '<li class="leaf d-flex"> <a href=\''.$url .'\'  class="pr-0 list-item focus-element"><span class="mr-2">(-)</span></a><span class="d-flex align-items-center">' . $results->display_name . '</span></li>';
           }
           else{
            $elencoCataloghi[]= '<li class="leaf hidden load-more"> <a href=\''.$url .'\'  class="button-menu-dkan list-item focus-element"><span> (-)</span></a><span>' . $results->display_name . '</span></li>';         
             
           }
        }
              else{
                $url= $current_path;
                $sep='';
                if($_GET['groups']){
                  $url .=$sep .'groups=' .$_GET['groups'];
                  $sep='&';
                }   
                if($_GET['format']){
                  $url .=$sep .'format=' .$_GET['format'];
                  $sep='&';
                }
                if($_GET['holder_name']){
                  $url .=$sep .'holder_name='.$_GET['holder_name'];
                  $sep='&';
                }
                if(isset($_GET['tags']) && trim($_GET['tags']) != '' && $_GET['tags'] != NULL) {
                  $url .=$sep .'tags=' .$_GET['tags'];
                  $sep='&';
                }
                
             if($_GET['Cerca']){
              $url .=$sep .'Cerca=' .$_GET['Cerca'];
              $sep='&';
             }

                if($_GET['ordinamento']){
                  $url .=$sep .'ordinamento=' .$_GET['ordinamento'];
                  $sep='&';
                }
                if($_GET['licenze']){
                  $url .=$sep .'licenze=' .$_GET['licenze'];
                  $sep='&';
                }

                if($results->name){
                  $url .=$sep .'organization=' .$results->name;
                  $sep='&';
                }   
                if($cresult<$max){
                $elencoCataloghi[]= '<li class="leaf"><a class="list-item focus-element" href=\''.$url .'\'>' . $results->display_name . ' (' . $results->count .')' . '</span></a></li>';
                }
                else{
                  $elencoCataloghi[]= '<li class="leaf hidden load-more"><a class="list-item focus-element" href=\''.$url .'\'>' . $results->display_name . ' (' . $results->count .')' . '</span></a></li>';
               
                }
              }
              
              $cresult--;
          }

        if(count($result) <= 0) {
          $s .= 'Non ci sono elementi da selezionare.';
        }

        for($i=count($elencoCataloghi); $i>=0; $i--){
          $s.=$elencoCataloghi[$i];
        }

        $s .= '       <li class="button-load-more"><button class="btn-load-more-filter btn btn-outline"><span class="label-more" id="more-button-dataset">Mostra tutto</span><span class="label-less" style="display:none;" id="less-button-dataset">Mostra meno</span></button></li></ul>';
        $s .= '     </div>
                  </div> 
                </div>
              </div>';
        
        return [
          '#markup' => \Drupal\Core\Render\Markup::create($s),

          // '#markup' => $s,
          // '#allowed_tags' => ['div', 'i', 'span', 'a', 'button', 'ul', 'li', 'h1', 'h2', 'h3', 'img'],
        ];
    }

    public function getCacheMaxAge() {
      return 0;
    }

    

}
