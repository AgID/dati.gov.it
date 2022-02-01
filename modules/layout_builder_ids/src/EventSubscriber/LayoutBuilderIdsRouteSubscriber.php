<?php

namespace Drupal\layout_builder_ids\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 */
class LayoutBuilderIdsRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    $configureSectionRoute = $collection->get('layout_builder.configure_section');
    if ($configureSectionRoute) {
      $configureSectionRoute->setDefault('_form', '\Drupal\layout_builder_ids\Form\ConfigureSectionForm');
    }
  }

}
