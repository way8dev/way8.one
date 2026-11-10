<?php

namespace Drupal\domain_config;

use Drupal\Core\Asset\LibraryDiscoveryCollector;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Provides services overrides for Domain Config.
 */
class DomainConfigServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // The library discovery decorator adds the active domain to the library
    // info cache id, so it has to decorate the service that builds that cache.
    // Core moved the cache collector from library.discovery.collector to
    // library.discovery itself, so which one to decorate is read from the
    // container rather than from the core version. Decoration is resolved
    // during compilation, after this runs, so the definition read here is
    // still the one core registered.
    if (!$container->hasDefinition('domain_config.library.discovery.collector')
      || !$container->hasDefinition('library.discovery')) {
      return;
    }
    $class = $container->getDefinition('library.discovery')->getClass();
    if ($class !== NULL && is_a($class, LibraryDiscoveryCollector::class, TRUE)) {
      $container->getDefinition('domain_config.library.discovery.collector')
        ->setDecoratedService('library.discovery');
    }
  }

}
