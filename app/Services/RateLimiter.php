<?php

namespace App\Services;

class RateLimiter
{
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Determine if the given key has too many attempts.
     *
     * @param string $key
     * @param int $maxAttempts
     * @return bool
     */
    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $attempts = $this->cache->get($key);

        if ($attempts !== null && (int)$attempts >= $maxAttempts) {
            // Check if there is also a lockout timer active
            if ($this->cache->timeToLive($key) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Increment the counter for a given key, setting its decay time if it is the first attempt.
     *
     * @param string $key
     * @param int $decaySeconds
     * @return int The current attempt count
     */
    public function hit(string $key, int $decaySeconds = 60): int
    {
        $attempts = $this->cache->get($key);

        if ($attempts === null) {
            $this->cache->put($key, 1, $decaySeconds);
            return 1;
        }

        $newAttempts = (int)$attempts + 1;
        // Keep the original expiration time (TTL) intact so decay is calculated from the first attempt
        $ttl = $this->cache->timeToLive($key);
        if ($ttl <= 0) {
            $ttl = $decaySeconds;
        }

        $this->cache->put($key, $newAttempts, $ttl);
        return $newAttempts;
    }

    /**
     * Get the number of seconds until the key is available again.
     *
     * @param string $key
     * @return int
     */
    public function retriesIn(string $key): int
    {
        return $this->cache->timeToLive($key);
    }

    /**
     * Clear the attempts counter for a given key.
     *
     * @param string $key
     * return bool
     */
    public function clear(string $key): bool
    {
        return $this->cache->forget($key);
    }
}
