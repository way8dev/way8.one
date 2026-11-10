<?php

namespace Drupal\Tests\domain_source\Unit;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\PathProcessor\PathProcessorManager;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\State\StateInterface;
use Drupal\domain\DomainNegotiationContext;
use Drupal\domain_source\HttpKernel\DomainSourceRouteProvider;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that route cache IDs name the domain the path was resolved for.
 *
 * @coversDefaultClass \Drupal\domain_source\HttpKernel\DomainSourceRouteProvider
 * @group domain_source
 */
#[Group('domain_source')]
class DomainSourceRouterProviderTest extends UnitTestCase {

  /**
   * The domain source route provider.
   *
   * @var \Drupal\domain_source\HttpKernel\DomainSourceRouteProvider
   */
  protected $routeProvider;

  /**
   * The ID of the domain the stubbed negotiation context reports as active.
   *
   * @var string
   */
  protected string $activeDomainId = 'und';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Mock all the dependencies required by RouteProvider.
    $database = $this->createMock(Connection::class);
    $state = $this->createMock(StateInterface::class);
    $current_path = $this->createMock(CurrentPathStack::class);
    $cache = $this->createMock(CacheBackendInterface::class);
    $path_processor = $this->createMock(PathProcessorManager::class);
    $cache_tags_invalidator = $this->createMock(CacheTagsInvalidatorInterface::class);

    // Mock language manager to return a language.
    $language = $this->createMock(LanguageInterface::class);
    $language->method('getId')->willReturn('en');

    $language_manager = $this->createMock(LanguageManagerInterface::class);
    $language_manager->method('getCurrentLanguage')->willReturn($language);

    // Configure path processor to return the path as-is.
    $path_processor->method('processInbound')
      ->willReturnCallback(function ($path) {
        return $path;
      });

    // As of Drupal 11.4 the parent RouteProvider constructor resolves
    // cache.routes from the container via its deprecated argument path, so a
    // container must be available before the provider is instantiated.
    $pre_container = new ContainerBuilder();
    $pre_container->set('cache.routes', $cache);
    \Drupal::setContainer($pre_container);

    // Create the actual DomainSourceRouteProvider with all dependencies.
    $this->routeProvider = new DomainSourceRouteProvider(
      $database,
      $state,
      $current_path,
      $cache,
      $path_processor,
      $cache_tags_invalidator,
      'router',
      $language_manager
    );

    // Report whichever domain the test has made active, so that a test can
    // switch domains the way a multi-domain process does.
    $domain_negotiation_context = $this->createMock(DomainNegotiationContext::class);
    $domain_negotiation_context->method('getDomainId')
      ->willReturnCallback(fn() => $this->activeDomainId);
    $this->routeProvider->setDomainNegotiationContext($domain_negotiation_context);
  }

  /**
   * Tests that cache ID includes the domain ID.
   *
   * @covers ::getRouteCollectionCacheId
   */
  public function testCacheIdIncludesDomainId() {
    $this->activeDomainId = 'example_com';

    $cache_id = $this->getRouteCollectionCacheId(Request::create('/test-path'));

    // Verify that the cache ID contains the domain identifier.
    $this->assertStringContainsString('[domain]=example_com', $cache_id, 'Cache ID should contain the domain ID.');
    $this->assertStringContainsString('/test-path', $cache_id, 'Cache ID should contain the path.');
  }

  /**
   * Tests that the cache ID follows a domain switch within one process.
   *
   * A process that serves several domains in turn, such as cron generating a
   * sitemap per domain, resolves each path for the domain active at that
   * moment. The cache ID has to name that domain, or the path resolved for one
   * domain is stored under another one's key and served to its visitors.
   *
   * @covers ::getRouteCollectionCacheId
   */
  public function testCacheIdFollowsDomainSwitch() {
    $request = Request::create('/test-path');

    $this->activeDomainId = 'example_com';
    $cache_id1 = $this->getRouteCollectionCacheId($request);

    // Switch domains without touching the route provider, which is what a
    // multi-domain process does.
    $this->activeDomainId = 'test_com';
    $cache_id2 = $this->getRouteCollectionCacheId($request);

    $this->assertStringContainsString('[domain]=example_com', $cache_id1);
    $this->assertStringContainsString('[domain]=test_com', $cache_id2);
    $this->assertNotEquals($cache_id1, $cache_id2, 'Cache IDs should be different for different domains.');
  }

  /**
   * Helper method to get the route collection cache ID.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return string
   *   The cache ID.
   */
  protected function getRouteCollectionCacheId(Request $request): string {
    $reflection = new \ReflectionClass($this->routeProvider);
    $method = $reflection->getMethod('getRouteCollectionCacheId');
    return $method->invoke($this->routeProvider, $request);
  }

}
