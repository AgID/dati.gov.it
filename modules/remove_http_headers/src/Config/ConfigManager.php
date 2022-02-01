<?php

namespace Drupal\remove_http_headers\Config;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Manages module configuration.
 *
 * @package Drupal\remove_http_headers\Configuration
 */
class ConfigManager {

  const HEADERS_TO_REMOVE_CACHE_ID = 'remove_http_headers.settings.headers_to_remove';
  const HEADERS_TO_REMOVE_CACHE_TAG = self::HEADERS_TO_REMOVE_CACHE_ID;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The module configuration.
   *
   * Should not be accessed directly.
   * Instead ConfigManager::getModuleConfig() should be used.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $moduleConfig;

  /**
   * The default cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * ConfigManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The default cache backend.
   */
  public function __construct(ConfigFactoryInterface $configFactory, CacheBackendInterface $cache) {
    $this->configFactory = $configFactory;
    $this->cache = $cache;
  }

  /**
   * Gets the HTTP headers that should be removed.
   *
   * @param bool $skipCache
   *   Flag whether or not the data should be retrieved directly from config.
   *
   * @return string[]
   *   HTTP headers that should be removed.
   *   An empty array if the header data is not saved in the correct format.
   */
  public function getHeadersToRemove($skipCache = FALSE) {
    $headersToRemove = [];

    if ($skipCache) {
      $cachedHeadersToRemoveData = FALSE;
    }
    else {
      $cachedHeadersToRemoveData = $this->getHeadersToRemoveFromCache();
    }

    /* Load the headers that should be removed from config
    if they are not in the cache or the cache is not used. */
    if ($cachedHeadersToRemoveData === FALSE) {
      $headersToRemoveData = $this->getHeadersToRemoveFromConfig();

      // Only use the data if the format is correct.
      if ($headersToRemoveData !== FALSE) {
        $headersToRemove = $headersToRemoveData;

        /* Save the data to cache so it is cached for the next access.
        Don't save it if the cache was skipped,
        because there is probably data in the cache.
        No need to save it. */
        if ($skipCache === FALSE) {
          $this->saveHeadersToRemoveToCache($headersToRemove);
        }
      }
    }
    else {
      // Use the cached data.
      $headersToRemove = $cachedHeadersToRemoveData;
    }

    return $headersToRemove;
  }

  /**
   * Returns the configured headers to remove from cache.
   *
   * @return false|array
   *   The cached data.
   *   FALSE if not in cache or invalid format.
   */
  protected function getHeadersToRemoveFromCache() {
    $cachedHeadersToRemove = FALSE;

    $headersToRemoveCacheData = $this->cache->get(self::HEADERS_TO_REMOVE_CACHE_ID);

    if ($headersToRemoveCacheData instanceof \stdClass) {
      if (property_exists($headersToRemoveCacheData, 'data')) {
        $cachedHeadersToRemove = $headersToRemoveCacheData->data;
      }
    }

    $cachedHeadersToRemove = $this->validateHeadersToRemoveDataFormat($cachedHeadersToRemove);

    return $cachedHeadersToRemove;
  }

  /**
   * Returns the configured headers to remove from configuration.
   *
   * @return false|array
   *   The config data.
   *   FALSE if not in cache or invalid format.
   */
  protected function getHeadersToRemoveFromConfig() {
    $headersToRemoveConfigData = $this->getModuleConfig()->get('headers_to_remove');
    $headersToRemoveConfigData = $this->validateHeadersToRemoveDataFormat($headersToRemoveConfigData);

    return $headersToRemoveConfigData;
  }

  /**
   * Checks if given array containing headers to remove has the correct format.
   *
   * @param mixed $headersToRemove
   *   Expected: Array with headers that should be removed.
   *
   * @return array|bool
   *   The given array.
   *   FALSE if not every array item is a string.
   */
  protected function validateHeadersToRemoveDataFormat($headersToRemove = NULL) {
    if (is_array($headersToRemove)) {
      foreach ($headersToRemove as $headerToRemove) {
        if (!is_string($headerToRemove)) {
          return FALSE;
        }
      }
    }
    else {
      return FALSE;
    }

    return $headersToRemove;
  }

  /**
   * Saves given array to the cache.
   *
   * @param array $headersToRemove
   *   Array of headers that should be removed.
   */
  protected function saveHeadersToRemoveToCache(array $headersToRemove) {
    $this->cache->set(self::HEADERS_TO_REMOVE_CACHE_ID, $headersToRemove, CacheBackendInterface::CACHE_PERMANENT, [self::HEADERS_TO_REMOVE_CACHE_TAG]);
  }

  /**
   * Returns the module config.
   *
   * Gets the config from the object property if it is set.
   * Otherwise sets and returns it.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The module config.
   */
  protected function getModuleConfig() {
    if (!$this->moduleConfig instanceof Config) {
      $this->moduleConfig = $this->configFactory->getEditable('remove_http_headers.settings');
    }

    return $this->moduleConfig;
  }

  /**
   * Saves the HTTP headers that should be removed to the configuration.
   *
   * @param string[] $headersToRemove
   *   HTTP headers that should be removed.
   */
  public function saveHeadersToRemoveToConfig(array $headersToRemove) {
    $this->getModuleConfig()->set('headers_to_remove', $headersToRemove);
    $this->getModuleConfig()->save();

    $this->invalidateHeadersToRemoveCache();
  }

  /**
   * Invalidates the headers to remove cache.
   */
  protected function invalidateHeadersToRemoveCache() {
    $this->cache->invalidate(self::HEADERS_TO_REMOVE_CACHE_TAG);
  }

  /**
   * Whether or not the route with given name should be protected.
   *
   * @param string $headerName
   *   A HTTP header name.
   *
   * @return bool
   *   Should HTTP header with given name be removed.
   */
  public function shouldHeaderBeRemoved($headerName) {
    return in_array($headerName, $this->getHeadersToRemove());
  }

}
