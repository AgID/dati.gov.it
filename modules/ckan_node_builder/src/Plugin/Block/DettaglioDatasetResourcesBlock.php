<?php
namespace Drupal\ckan_node_builder\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use GuzzleHttp\Client;


/**
 * Provides a 'menu ckan research' block.
 *
 * @Block(
 *   id = "dettaglio_dataset_resources_block",
 *   admin_label = @Translation("Dettaglio Dataset Resources Block"),
 *
 * )
 */

class DettaglioDatasetResourcesBlock extends BlockBase {


  /**
   * @inheritDoc
   */
  public function build() {
      $id = $_GET['id'] ?? NULL;
      $html = '';

      //var_dump($id);
      if(!$id){
        return [ '#markup' => \Drupal\Core\Render\Markup::create($html) ];
      }


    $url = '/api/3/action/package_show?id=' . $id;

      $objRes = $this->callCKAN($url)->result;
      $random_div = mt_rand();

      foreach ($objRes->resources as $key => $value){
        $html .= '<li class="myHover">';
        $html .= '<div class="container-fluid">';
        $html .= '<div class="row">';

        //
        $html .= '<div class="col-12 col-lg-2 col-md-2 d-flex align-items-center">';
          $html .= '<div class="it-rounded-icon w-100 ml-0">';
              $html .= '<span class="text-truncate w-100 dataset-format" data-format="'.$value->format.'">';
                $html .= $value->format;
              $html .= '</span>';
          $html .= '</div>';
        $html .= '</div>';
        //
	$random_a_1 = mt_rand();
	$random_a_2 = mt_rand();

        $html .= '<div class="col-12 col-lg-10 col-md-10">';
          $html .= '<div class="it-right-zone border-0 ml-0">';
        $html .= '<div class="container-fluid">';
              $html .= '<div class="row">';
                  $html .= '<div class="col-12 col-lg-9 col-md-9 d-flex align-items-center">';
                    $html .= '<span class="text">'.$value->name;
                    if($value->description !== ''){
                      $html .= '<em>Descrizione:&nbsp;&nbsp;'.$value->description.'</em>';
                    }
                    if($value->last_modified !== NULL){
                      $html .= '<em>Ultima modifica:&nbsp;&nbsp;'.$value->last_modified.'</em>';
                    }
                    if($value->size !== null){
                      $html .= '<em>Dimensione in byte:&nbsp;&nbsp;'.$value->size. '&nbsp;byte</em>';
                    }
                    $html .= '</span>';
                  $html .= '</div>'; //<div class="col-12 col-lg-10 col-md-10">

                $html .= '<div class="col-12 col-lg-3 col-md-3 d-flex justify-content-center">';
                  $html .= '<span class="it-multiple">';
                    $html .= '<a id="'.$random_a_1.'" data-placement="top" title="Copia link" data-toggle="tooltip" onclick="copyLinkRisorse(this,\''.$value->url.'\');"   class="btn pl-3 pr-3" aria-labelledby="'.$random_a_1.' '.$random_div.'">';
                      $html .= '<svg class="icon icon-lg ml-0">';
                        $html .= '<use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-copy"></use>';
                      $html .= '</svg>';
                    $html .= '</a>';
                    $html .= '<a id="'.$random_a_2.'" data-toggle="tooltip" data-placement="top" title="Scarica risorsa" class="btn pl-3 pr-3" href="'.$value->url.'" aria-labelledby="'.$random_a_2.' '.$random_div.'">';
                      $html .= '<svg class="icon icon-lg ml-0">';
                        $html .= '<use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-download"></use>';
                      $html .= '</svg>';
                    $html .= '</a>';
                  $html .= '</span>';
                $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
          $html .= '</div>';
        $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

    $html .= '</li>';
}

      $html .= '
        <script>
            function copyLinkRisorse(a, link){
                var textArea = document.createElement("textarea");
                textArea.value = link;
  
                // Avoid scrolling to bottom
                textArea.style.top = "0";
                textArea.style.left = "0";
                textArea.style.position = "fixed";

                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                var successful = document.execCommand(\'copy\');

                document.body.removeChild(textArea);
            }

        </script>
      ';

      $htmlPrima = '
          <div class="container test-docs" >
                <div class="row" >
                    <div class="col-12 col-md-6" id="myNotificaCopy">
                        
                    </div>
                </div>
	        </div>  
      ';
      
      $htmlPrima .= '<div class="it-list-wrapper mt-4" id='.$random_div.'>';
      $htmlPrima .= '<ul class="it-list">';

      $htmlDopo = '</ul>';
      $htmlDopo .= '</div>';

      return [ '#markup' => \Drupal\Core\Render\Markup::create( $htmlPrima. $html . $htmlDopo ) ];

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
