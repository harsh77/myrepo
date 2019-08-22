<?php

namespace Drupal\test\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class ApiKeyRouteSubscriber extends RouteSubscriberBase {
  
  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.site_information_settings')) {
      $route->setDefault('_form', 'Drupal\test\Form\ApiKeySiteInformationForm');
    }
  } 
}