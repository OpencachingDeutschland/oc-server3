<?php

declare(strict_types=1);

namespace Oc\Entity;

/**
 * Simple data object for the `coordinates` table.
 */
class GeoCacheWaypointsEntity
{
    public int $id = 0;
    public string $dateCreated = '';
    public string $lastModified = '';
    public int $type = 0;
    public ?int $subtype = null;
    public float $latitude = 0.0;
    public float $longitude = 0.0;
    public int $cacheId = 0;
    public ?int $userId = null;
    public ?int $logId = null;
    public ?string $description = null;
    public string $logpw = '';
}
