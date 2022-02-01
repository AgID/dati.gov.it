<?php


namespace Drupal\filter_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use \GuzzleHttp\Client;

/**
 * Creates a 'Filter Ckan Counter' Block
 * @Block(
 * id = "block_filterckan_results_counter",
 * admin_label = @Translation("Filter block counter for search results"),
 * )
 */
class FilterCkanResultsCounter extends BlockBase {

  /**
   * @inheritDoc
   */
  public function build() {
      $html  = '<div class="view-dkan-datasets col-xs-12 border dataset-list-ml mb-3 mt-3 p-0">';
      $html .= '    <div class="form-group mb-3 mt-3 pl-4">';
      $html .= '        <p class="font-22">Risultati Trovati:&nbsp;&nbsp;<span class="font-weight-bold" id="countDataset">795</span></p>';
      $html .= '    </div>';
      $html .= '</div>';

      return [ '#markup' => \Drupal\Core\Render\Markup::create($html) ];
    }
}
