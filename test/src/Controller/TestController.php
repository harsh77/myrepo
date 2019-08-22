<?php

namespace Drupal\test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Build a JSON page.
 */
class TestController extends ControllerBase {
  
  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;


  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }
  
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }
  
  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function checkOverviewAccess(AccountInterface $account, $apikey, NodeInterface $node) {
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    return AccessResult::allowedIf($account->hasPermission('access content') && $this->validApiKey($apikey, $node));
  }
  
  /**
   * Verify api key is valid or not.
   */
  protected function validApiKey($apikey, NodeInterface $node) {
    $site_api_key = $this->configFactory->get('system.site')->get('siteapikey');
    if ($site_api_key == $apikey && $node->getType() == 'page') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  public function overview($apikey, NodeInterface $node) {
    return new JsonResponse($node->toArray());
  }
}