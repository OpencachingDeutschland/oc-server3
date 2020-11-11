<?php

namespace Oc\GeoCache\Persistence\GeoCache;

use Oc\GeoCache\Enum\WaypointType;
use Oc\GeoCache\Exception\UnknownWaypointTypeException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeoCacheService
{
    /**
     * @var GeoCacheRepository
     */
    private $geoCacheRepository;

    public function __construct(GeoCacheRepository $geoCacheRepository)
    {
        $this->geoCacheRepository = $geoCacheRepository;
    }

    /**
     * Fetches all GeoCaches.
     *
     * @return GeoCacheEntity[]
     */
    public function fetchAll(): array
    {
        try {
            $result = $this->geoCacheRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetch a waypoint by given type.
     */
    public function fetchByWaypoint(string $waypoint): ?GeoCacheEntity
    {
        $waypointEntity = null;

        try {
            $waypointType = WaypointType::guess($waypoint);

            if ($waypointType === WaypointType::OC) {
                $waypointEntity = $this->geoCacheRepository->fetchOneBy([
                    'wp_oc' => $waypoint,
                ]);
            } elseif ($waypointType === WaypointType::GC) {
                $waypointEntity = $this->geoCacheRepository->fetchGCWaypoint($waypoint);
            }
        } catch (RecordNotFoundException $e) {
            $waypointEntity = null;
        } catch (UnknownWaypointTypeException $e) {
            $waypointEntity = null;
        }

        return $waypointEntity;
    }

    /**
     * Creates a GeoCache in the database.
     */
    public function create(GeoCacheEntity $entity): GeoCacheEntity
    {
        return $this->geoCacheRepository->create($entity);
    }

    /**
     * Update a GeoCache in the database.
     */
    public function update(GeoCacheEntity $entity): GeoCacheEntity
    {
        return $this->geoCacheRepository->update($entity);
    }

    /**
     * Removes a GeoCache from the database.
     */
    public function remove(GeoCacheEntity $entity): GeoCacheEntity
    {
        return $this->geoCacheRepository->remove($entity);
    }
}
