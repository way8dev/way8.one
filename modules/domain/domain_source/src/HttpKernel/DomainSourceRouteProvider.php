<?php

namespace Drupal\domain_source\HttpKernel;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Routing\RouteProvider;
use Drupal\domain\DomainNegotiationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides a route provider for the domain_source module.
 */
class DomainSourceRouteProvider extends RouteProvider {

  /**
   * The domain negotiation context.
   *
   * @var \Drupal\domain\DomainNegotiationContext|null
   */
  protected ?DomainNegotiationContext $domainNegotiationContext = NULL;

  /**
   * Sets the domain negotiation context.
   *
   * Injected through a setter because this service inherits its arguments
   * from router.route_provider, whose constructor signature belongs to core
   * and changes between core versions.
   *
   * @param \Drupal\domain\DomainNegotiationContext $domain_negotiation_context
   *   The domain negotiation context.
   */
  public function setDomainNegotiationContext(DomainNegotiationContext $domain_negotiation_context): void {
    $this->domainNegotiationContext = $domain_negotiation_context;
  }

  /**
   * {@inheritdoc}
   */
  protected function getRouteCollectionCacheId(Request $request) {
    // Resolve the active domain here rather than once per process, because a
    // process can serve more than one domain: cron generating a sitemap per
    // domain switches the active domain between lookups. Path processing
    // follows the domain active at this moment, so the cache ID has to as
    // well, or a path resolved for one domain is stored under another one's
    // key. Core adds its language and query parameter parts in the same
    // place, and for the same reason.
    if ($this->domainNegotiationContext !== NULL) {
      $this->addExtraCacheKeyPart('domain', $this->domainNegotiationContext->getDomainId('und'));
    }
    return parent::getRouteCollectionCacheId($request);
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteCollectionForRequest(Request $request) {
    // Cache both the system path as well as route parameters and matching
    // routes.
    // Warning: The cache is shared with the Core RouteProvider service.
    $cid = $this->getRouteCollectionCacheId($request);
    if ($cached = $this->cache->get($cid)) {
      $this->setRequestPathInfo($request, $cached->data['path']);
      if ($cached->data['routes'] === FALSE) {
        return new RouteCollection();
      }
      return $cached->data['routes'];
    }
    else {
      // Just trim on the right side.
      $path = $request->getPathInfo();
      $path = $path === '/' ? $path : rtrim($request->getPathInfo(), '/');
      $processed_path = $this->pathProcessor->processInbound($path, $request);
      $this->setRequestPathInfo($request, $processed_path);
      // Incoming path processors may also set query parameters.
      $query_parameters = $request->query->all();
      $routes = $this->getRoutesByPath(rtrim($processed_path, '/'));
      $cache_value = [
        'path' => $processed_path,
        'query' => $query_parameters,
        'routes' => $routes->count() === 0 ? FALSE : $routes,
      ];
      $this->cache->set($cid, $cache_value, CacheBackendInterface::CACHE_PERMANENT, ['route_match']);
      return $routes;
    }
  }

  /**
   * Sets the pathInfo property of the Request object.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param string $path
   *   The path to set.
   */
  protected function setRequestPathInfo(Request $request, string $path) {
    if ($request->getPathInfo() !== $path) {
      $path_info = new \ReflectionProperty($request, 'pathInfo');
      $path_info->setValue($request, $path);
    }
  }

}
