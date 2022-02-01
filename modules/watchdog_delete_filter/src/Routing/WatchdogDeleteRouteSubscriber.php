<?php

namespace Drupal\watchdog_delete_filter\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class WatchdogDeleteRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    if ($route = $collection->get('dblog.confirm')) {
      $route->setDefault('_form', '\Drupal\watchdog_delete_filter\Form\WatchdogDeleteForm');
    }
  }

}
