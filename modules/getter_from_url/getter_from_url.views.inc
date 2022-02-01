<?php

/**
 * @file
 * Contains getter_from_url\getter_from_url.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */


/**
* Implements hook_views_data().
*/
function getter_from_url_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = [
      // #global is a special flag which allows a table to appear all the time.
      '#global' => [],
    ];

    $data['views']['current_tags_views_field'] = [
        'title' => t('Current tags views field'),
        'help' => t('Provide the current tag/s.'),
        'field' => [
            'id' => 'current_tags_views_field',
        ],
    ];

    $data['views']['query_parameters_views_field'] = [
        'title' => t('Query parameters views field'),
        'help' => t('Provide currents get variables from URL.'),
        'field' => [
            'id' => 'query_parameters_views_field',
        ],
    ];

    return $data;
}