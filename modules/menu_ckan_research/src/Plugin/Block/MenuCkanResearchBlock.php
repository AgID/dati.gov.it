<?php

namespace Drupal\menu_ckan_research\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'menu ckan research' block.
 *
 * @Block(
 *   id = "menu_ckan_research_block",
 *   admin_label = @Translation("Menu Ckan Research Block"),
 *
 * )
 */

class MenuCkanResearchBlock extends BlockBase {

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {

    $cerca        = isset($_GET['Cerca']) ? $_GET['Cerca'] : NULL;
    $tags         = isset($_GET['tags']) ? $_GET['tags'] : NULL; 
    $groups       = isset($_GET['groups']) ? $_GET['groups'] : NULL;
    $organization = isset($_GET['organization']) ? $_GET['organization'] : NULL;
    $ordinamento  = isset($_GET['ordinamento']) ? (string)$_GET['ordinamento'] : NULL;
    
    $holder_name  = $_GET['holder_name'] ?? NULL;
    $format       = $_GET['format'] ?? NULL;
    $licenze      = $_GET['licenze'] ?? NULL;

    $html = '<head>
              <script src="https://code.jquery.com/jquery-2.1.3.js"></script>
              <script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
            </head>';

    $html .= '<form method="get" name="research" action="/view-dataset">';
      $html .= '<div class="row d-flex justify-content-center pr-3 pl-3 pt-3 pb-3" id="rigaRicercaAvanzata">';
        $html .= '<div class="col">';
          $html .= '<div class="form-group"><h4 class="mb-4 d-block" style="margin-top: -10px !important;">Cerca tra i dataset</h4>';
            $html .= '<input type="search" class="autocomplete border pt-4 pb-4 pr-3 sidebar-input-search" placeholder="Per titolo e descrizione" id="Cerca" name="Cerca" value="'.$cerca.'" title="Per titolo e descrizione">';
            $html .= '<span class="autocomplete-search-icon pr-0 mr-2" aria-hidden="true">';
              $html .= '<button class="btn p-0" type="button" id="button-1" onclick="this.form.submit()">';
                $html .= '<svg class="icon icon-sm"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-search"></use></svg>';
              $html .= '</button>';
            $html .= '</span>';
          $html .= '</div>';
          $html .= '<div class="form-group">';
            $html .= '<input type="search" class="autocomplete border pt-4 pb-4 pr-3 sidebar-input-search" style="margin-top: -35px !important;" placeholder="Per parola chiave" id="tags_set" name="tags_set" title="Per parola chiave">';
            $html .= '<input type="hidden" id="tags" name="tags" value="'.$tags.'">'; 
            $html .= '<span class="autocomplete-search-icon pr-0 mr-2" style="margin-top: -2px !important;" aria-hidden="true">';
              $html .= '<button class="btn p-0" type="button" id="button-2" onclick="this.form.submit()">';
                $html .= '<svg class="icon icon-sm"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-search"></use></svg>';
              $html .= '</button>';
            $html .= '</span>';
          $html .= '</div>';
          $html .= '<div class="bootstrap-select-wrapper">';
            $html .= '<label>Ordina per:</label>';
            $html .= '<select title="Scegli una opzione" id="ordinamento" name="ordinamento" onchange="this.form.submit()">';
              $html .= '<option value="" title="Scegli una opzione" data-content="Annulla ordinamento"><span class="reset-label"></span></option>';
              $html .= '<option value="2" for="ordinamento"';
              if ($ordinamento === '2'){ $html .= 'selected'; }
              $html .= '>Titolo Dataset [A-Z]</option>';
              $html .= '<option value="3" for="ordinamento"';
              if ($ordinamento === '3'){ $html .= 'selected'; }
              $html .= '>Titolo Dataset [Z-A]</option>';
            $html .= '</select>';
          $html .= '</div>';
        $html .= '</div>';
      $html .= '</div>';
      
      if($groups) {
        $html .= '<input type="hidden" name="groups" value="'.$groups.'" title="groups">';
      }

      if($organization) {
        $html .= '<input type="hidden" name="organization" value="'.$organization.'" title="organization">';
      }

      if($holder_name) {
        $html .= '<input type="hidden" name="holder_name" value="'.$holder_name.'" title="holder_name">';
      }

      if($format) {
        $html .= '<input type="hidden" name="format" value="'.$format.'" title="format">';
      }

      if($licenze) {
        $html .= '<input type="hidden" name="licenze" value="'.$licenze.'" title="licenze">';
      }

      $html .= '<input type="submit" style="visibility: hidden;" name="sort" title="sort">'; 
      $html .= '</form>';
      $html .= '<script>
                  $(document).ready(function() {
                    $("#tags_set").change(function() {
                      var value = $("#tags_set").val();
                      $("#tags").val(value);
                    });

                    $("#tags_set").keyup(function() {
                      if($(this).val().length > 2) {
                        $.ajax({
                          type: "GET",
                          url: "https://93.147.186.231/opendata/api/3/action/tag_autocomplete?query=" + $(this).val(),
                          success: function(data) {
                            $("#tags_set").autocomplete({
                              source: data["result"]
                            })
                          }
                        });
                      }
                    });
                  });
                </script>';
    return [
      '#markup' => \Drupal\Core\Render\Markup::create($html),
    ];
  }

}
