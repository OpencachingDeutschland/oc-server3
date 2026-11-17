<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

/**
 * Fetches and caches donation progress data from the cloud.
 */
class Donation
{
    private const REMOTE_URL = 'https://cloud.opencaching.de/public.php/dav/files/Y6oTFgPSWptYCWm/';
    private const CACHE_FILE = __DIR__ . '/../var/cache2/donation-progress.json';
    private const CACHE_LIFETIME = 3600; // 1 hour

    /**
     * Get the current donation progress data (cached).
     *
     * @return array{updateDate: string, goalYear: int, goal: int, current: float}|null
     */
    public static function getProgress(): ?array
    {
        $data = self::loadFromCache();
        if ($data !== null) {
            return $data;
        }

        $data = self::fetchFromRemote();
        if ($data !== null) {
            self::saveToCache($data);
            return $data;
        }

        // If fetch fails, try to use stale cache
        return self::loadFromCache(true);
    }

    private static function loadFromCache(bool $ignoreExpiry = false): ?array
    {
        if (!file_exists(self::CACHE_FILE)) {
            return null;
        }

        if (!$ignoreExpiry && (time() - filemtime(self::CACHE_FILE)) > self::CACHE_LIFETIME) {
            return null;
        }

        $content = file_get_contents(self::CACHE_FILE);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return null;
        }

        return $data;
    }

    private static function fetchFromRemote(): ?array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
            ],
        ]);

        $content = @file_get_contents(self::REMOTE_URL, false, $context);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data)
            || !isset($data['updateDate'], $data['goalYear'], $data['goal'], $data['current'], $data['active'])
            || !self::validate($data)
        ) {
            return null;
        }

        return [
            'updateDate' => (string) $data['updateDate'],
            'goalYear' => (int) $data['goalYear'],
            'goal' => (int) $data['goal'],
            'current' => (float) $data['current'],
            'active' => (bool) $data['active'],
        ];
    }

    private static function validate(array $data): bool
    {
        // updateDate must be in YYYY-MM-DD format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['updateDate'])) {
            return false;
        }

        // goalYear must be a 4-digit number
        if (!is_numeric($data['goalYear']) || (int) $data['goalYear'] < 1000 || (int) $data['goalYear'] > 9999) {
            return false;
        }

        // goal must be a positive number
        if (!is_numeric($data['goal']) || $data['goal'] <= 0) {
            return false;
        }

        // current must be a number (float)
        if (!is_numeric($data['current'])) {
            return false;
        }

        // active must be a boolean
        if (!is_bool($data['active'])) {
            return false;
        }

        return true;
    }

    private static function saveToCache(array $data): void
    {
        file_put_contents(self::CACHE_FILE, json_encode($data), LOCK_EX);
    }
}

