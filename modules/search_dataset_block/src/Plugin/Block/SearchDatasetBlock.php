<?php

/**
 * @file
 */
namespace Drupal\search_dataset_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

// use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates a 'Menu Ckan' Block
 * @Block(
 * id = "block_menuckan",
 * admin_label = @Translation("Block Menu Ckan"),
 * )
 */
class SearchDatasetBlock extends BlockBase {
 
    /**
     * {@inheritdoc}
     */
    
   $s=' <form action="/" method="post" id="dkan-sitewide-dataset-search-form--2" accept-charset="UTF-8"><div><div class="form-item form-item-search form-type-textfield form-group"> <label class="control-label" for="edit-search--2">Search</label>
<input placeholder="Cerca tra i dati della pubblica amministrazione" class="form-control form-text" type="text" id="edit-search--2" name="search" value="" size="30" maxlength="128"></div><button type="submit" id="edit-submit--2" name="op" value="" class="btn btn-default form-submit glyphicon glyphicon-search"></button>
<input type="hidden" name="form_build_id" value="form-cCYFdCezowo634pkqlPNUpOQSPozPUYMd2hmg374Al8" placeholder="Cerca tra i dati della pubblica amministrazione">
<input type="hidden" name="form_id" value="dkan_sitewide_dataset_search_form" placeholder="Cerca tra i dati della pubblica amministrazione">
</div></form>';
return [
  '#markup' => $s,
  ];

}
