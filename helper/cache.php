<?php

/**
 * Get the cache file path for a given key.
 *
 * @param string $key The cache key.
 * @return string The full path to the cache file.
 */
function getCacheFilePath(string $key): string {
    return ROOT_PATH . 'cache/' . md5($key) . '.cache';
}

/**
 * Check if a cache file exists and is still valid.
 *
 * @param string $key The cache key.
 * @param int $expiration The cache expiration time in seconds.
 * @return bool True if the cache is valid, false otherwise.
 */
function isCacheValid(string $key, int $expiration): bool {
    $cacheFile = getCacheFilePath($key);
    return file_exists($cacheFile) && (time() - filemtime($cacheFile) < $expiration);
}

/**
 * Retrieve data from the cache.
 *
 * @param string $key The cache key.
 * @return mixed The cached data, or null if the cache is invalid or doesn't exist.
 */
function getCache(string $key): mixed {
    $cacheFile = getCacheFilePath($key);
    if (file_exists($cacheFile)) {
        $data = file_get_contents($cacheFile);
        return @unserialize($data); // Use @ to suppress unserialize errors
    }
    return null;
}

/**
 * Save data to the cache.
 *
 * @param string $key The cache key.
 * @param mixed $data The data to cache.
 * @return bool True on success, false on failure.
 */
function setCache(string $key, mixed $data): bool {
    $cacheFile = getCacheFilePath($key);
    $cacheDir = dirname($cacheFile);
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true); // Create directory recursively
    }
    return file_put_contents($cacheFile, serialize($data)) !== false;
}

/**
 * Delete a cache file.
 *
 * @param string $key The cache key.
 * @return bool True on success, false on failure.
 */
function clearCache(string $key): bool {
    $cacheFile = getCacheFilePath($key);
    if (file_exists($cacheFile)) {
        return unlink($cacheFile);
    }
    return true; // Consider it successful if the file doesn't exist
}