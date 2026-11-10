<?php

namespace Drupal\Tests\domain_config\Kernel;

use Drupal\Core\Asset\LibraryDiscoveryCollector;
use Drupal\KernelTests\KernelTestBase;
use Drupal\domain\Entity\Domain;
use Drupal\domain_config\DomainConfigLibraryDiscoveryCollector;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Asserts that library definitions are discovered and cached per domain.
 *
 * Library definitions are built from hook_library_info_build() and
 * hook_library_info_alter() implementations, which may read configuration that
 * domain_config overrides per domain. The library info cache is keyed by theme
 * only, so domain_config decorates the library discovery cache collector to
 * add the active domain to that key.
 *
 * Which service holds that collector depends on the core version, and this
 * branch supports both: it is library.discovery.collector up to Drupal 11.0
 * and library.discovery from Drupal 11.1 on. DomainConfigServiceProvider picks
 * the one the running core uses, so the test resolves the collector the same
 * way and asserts the decoration is in place either way.
 *
 * @group domain_config
 *
 * @see \Drupal\domain_config\DomainConfigServiceProvider
 * @see \Drupal\domain_config\DomainConfigLibraryDiscoveryCollector
 * @see https://www.drupal.org/project/domain/issues/3616863
 */
#[Group('domain_config')]
#[RunTestsInSeparateProcesses]
class DomainConfigLibraryDiscoveryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'language',
    'domain',
    'domain_config',
    'domain_config_test',
  ];

  /**
   * The library definition the test module ships and its hook rewrites.
   */
  private const LIBRARY = 'domain_probe';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('domain');
    $this->installEntitySchema('user');
    $this->installSchema('user', ['users_data']);
    $this->installConfig(['system']);

    Domain::create([
      'id' => 'one_example_com',
      'hostname' => 'one.example.com',
      'name' => 'One',
      'scheme' => 'http',
      'status' => 1,
      'weight' => 0,
      'is_default' => TRUE,
    ])->save();
    Domain::create([
      'id' => 'two_example_com',
      'hostname' => 'two.example.com',
      'name' => 'Two',
      'scheme' => 'http',
      'status' => 1,
      'weight' => 1,
      'is_default' => FALSE,
    ])->save();

    // The library version the test module's hook_library_info_alter() reads:
    // base for every domain, overridden on the second one.
    $this->container->get('config.factory')
      ->getEditable('domain_config_test.typed')
      ->set('name', 'base version')
      ->save();
    $this->container->get('domain.config_factory_override')
      ->getOverrideEditable('two_example_com', 'domain_config_test.typed')
      ->set('name', 'two version')
      ->save();
  }

  /**
   * Tests that the library discovery cache collector is decorated.
   */
  public function testLibraryDiscoveryIsDecorated(): void {
    self::assertInstanceOf(
      DomainConfigLibraryDiscoveryCollector::class,
      $this->libraryDiscoveryCollector(),
      'The library discovery cache collector carries the per-domain cache id.'
    );
  }

  /**
   * Tests that a domain does not read another domain's library definitions.
   */
  public function testLibraryDefinitionsAreCachedPerDomain(): void {
    $collector = $this->libraryDiscoveryCollector();

    // First request: the default domain, with the base configuration.
    $this->activateDomain('http://one.example.com/');
    self::assertSame(
      'base version',
      $collector->get('domain_config_test')[self::LIBRARY]['version'],
      'The first domain builds its library definitions from the base configuration.'
    );

    // End that request: the needs_destruction tag writes the definitions to
    // the discovery cache.
    $collector->destruct();

    // Second request, on the domain that overrides the configuration. Without
    // the domain in the cache id this reads the entry the first domain just
    // wrote, and reports the base version.
    $this->activateDomain('http://two.example.com/');
    $collector->reset();
    self::assertSame(
      'two version',
      $collector->get('domain_config_test')[self::LIBRARY]['version'],
      'The second domain builds its library definitions from its own configuration override.'
    );
  }

  /**
   * Returns the cache collector the running core builds library info with.
   *
   * @return \Drupal\domain_config\DomainConfigLibraryDiscoveryCollector
   *   The decorated collector.
   */
  private function libraryDiscoveryCollector(): DomainConfigLibraryDiscoveryCollector {
    $discovery = $this->container->get('library.discovery');
    if (!$discovery instanceof LibraryDiscoveryCollector) {
      // Up to Drupal 11.0, library.discovery only wraps the collector.
      $discovery = $this->container->get('library.discovery.collector');
    }
    self::assertInstanceOf(DomainConfigLibraryDiscoveryCollector::class, $discovery);

    return $discovery;
  }

  /**
   * Makes a domain the active one, as a request for its hostname would.
   *
   * @param string $url
   *   The URL of the request to negotiate the domain from.
   */
  private function activateDomain(string $url): void {
    $request = Request::create($url);
    $request->setSession(new Session(new MockArraySessionStorage()));
    $this->container->get('request_stack')->push($request);
    $this->container->get('domain.negotiator')->getActiveDomain(TRUE);
    $this->container->get('config.factory')->reset();
  }

}
