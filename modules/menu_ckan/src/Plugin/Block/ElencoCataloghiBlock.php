<?php

/**
 * @file
 */
namespace Drupal\menu_ckan\Plugin\Block;

use Drupal\Core\Block\BlockBase;

// use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates a 'Blocco elenco sorgente' Block
 * @Block(
 * id = "block_elenco_catologhi",
 * admin_label = @Translation("Blocco elenco sorgente"),
 * )
 */
class ElencoCataloghiBlock extends BlockBase {
 
    /**
     * {@inheritdoc}
     */
    
  public function build() {
   
      $uri =  getenv('CKAN_HOST').':'. getenv('CKAN_PORT').'/api/3/action/package_search?facet.field=["organization"]&facet.limit=-1&rows=0';
     	try{ 
        $response = _menu_ckan_fetch_file($uri);
      	} catch(RequestException $e) {
      			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
      	  }
      $results = $response->result->search_facets->organization->items;
     
        $s='';
        $s .= '<div id="block-bootstrap-barrio-subtheme-content" class="block block-system block-system-main-block">
                <div class="content">
                  <article data-history-node-id="46" role="article" about="/elenco-harvest-sources" typeof="schema:WebPage" class="node node--type-page node--view-mode-full clearfix">
                    <header>
                      <span property="schema:name" content="Elenco cataloghi sorgente" class="rdf-meta hidden"></span>
                    </header>
                    <div class="node__content clearfix">
                      <div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">
                        <p>I dati presenti provengono da <strong>'. count($results) .'</strong> cataloghi sorgente. In totale sono presenti <strong>'. $response->result->count .'</strong> dataset</p>
                        <table class="views-table cols-5 table table-hover table-striped">
                        <thead><tr>
                          <th class="views-field views-field-title">
                           <a style="font-size:18px" ,="" href="/elenco-harvest-sources?order=title&amp;sort=asc" title="ordina per titolo" class="active">Nome Catalogo</a>          </th>
                          <th class="views-field views-field-field-dkan-harvest-source-uri">
                            <a style="font-size:17px" ,="" href="/elenco-harvest-sources?order=title&amp;sort=asc" title="ordina per Url" class="active"> url endpoint </a>     </th>
                          <th class="views-field views-field-dkan-harvest-harvest-count">
                            <a href="/elenco-harvest-sources?order=dkan_harvest_harvest_count&amp;sort=asc" title="ordina per Conteggio dataset" class="active">Conteggio dataset</a>          </th>
                          <th class="views-field views-field-dkan-harvest-harvest-date">
                        </th></tr>
                        </thead>
                        <tbody>';
        $a=array();
        $class='even';
        foreach($results as $result){
          $name=$result->name;
          $display_name=$result->display_name;
          $count=$result->count;
          $uri =  getenv('CKAN_HOST').':'. getenv('CKAN_PORT').'/api/3/action/harvest_source_show?id='.$name;
          try{ 
              $response2 = _menu_ckan_fetch_file($uri);
             } catch(RequestException $e) {
              throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
             }
             catch(\GuzzleHttp\Exception\ClientException $e1){
                $response2='';
                $error_response = $e1->getResponse();
                $status=$error_response->getStatusCode();
                
             }
             if($response2==''){
              $url='';
             }
             else{
                $url=$response2->result->url;
             }
         
          if($url!=''||$url!=NULL){
            $urltemp="";
            if(strlen($url) > 100){
               $urltemp=substr($url,0,50)."...";
            }
            else{
               $urltemp=$url;
            }
            //********************
            if($display_name=='Dati.gov.it'){
             $url='https://dati.gov.it';
             $urltemp='https://dati.gov.it';
            }
            //********************

            $a[]='<tr class="'.$class.'">
                      <td class="views-field views-field-title">
                          <a style="font-size:16px" ,="" href="/view-dataset?organization='.$name.'">'.$display_name.'</a>
                          <td class="views-field views-field-field-dkan-harvest-source-uri">
                          <a href="'.$url.'" title="'.$url.'">'.$urltemp.'</a>  </td>
                          <td class="views-field views-field-dkan-harvest-harvest-count">
                          '.$count.'  </td>
                        </tr>';
          }


          }


         /*if($url!=''||$url!=NULL){
              $urltemp="";
              if(strlen($url) > 100 ){
                $urltemp=substr($url,0,100); 
              }
              else{
                $urltemp=$url; 
                $a[]='<tr class="'.$class.'">    
                        <td class="views-field views-field-title">
                            <a style="font-size:16px" ,="" href="/view-dataset?organization='.$name.'">'.$display_name.'</a>  </td>
                            <td class="views-field views-field-field-dkan-harvest-source-uri">
                            <a href="'.$url.'" title="'.$url.'">'.$urltemp.'</a> </td>
                            <td class="views-field views-field-dkan-harvest-harvest-count">
                            '.$count.'  </td>
                          </tr>'; 
              }
            } */

         /*if($url!=''||$url!=NULL){
              $urltemp="";
              if(strlen($url) > 100 ){
                $urltemp=substr($url,0,100); 
              }
              else{
                $urltemp=$url; 
              }
              $a[]='<tr class="'.$class.'">    
                        <td class="views-field views-field-title">
                            <a style="font-size:16px" ,="" href="/view-dataset?organization='.$name.'">'.$display_name.'</a>  </td>
                            <td class="views-field views-field-field-dkan-harvest-source-uri">
                            <a href="'.$url.'" title="'.$url.'">'.$urltemp.'</a> </td>
                            <td class="views-field views-field-dkan-harvest-harvest-count">
                            '.$count.'  </td>
                          </tr>'; 
            }*/ 


        


        
        for($i=count($a); $i>=0; $i--){
          $s.=$a[$i];
        }
        $s.='</tbody></table></div></div></article></div></div>';
      
        

        return [
          '#markup' => \Drupal\Core\Render\Markup::create($s),
      //  '#markup' => $s,
       // '#allowed_tags' => ['div', 'i', 'span', 'a', 'button', 'ul', 'li', 'h1', 'h2', 'h3', 'img'],
        ];


    }
    public function getCacheMaxAge() {
      return 0;
  }

    

}
