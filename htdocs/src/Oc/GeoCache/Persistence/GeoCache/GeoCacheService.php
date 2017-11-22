<?php

namespace Oc\GeoCache\Persistence\GeoCache;

use Oc\GeoCache\Enum\WaypointType;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeoCacheService
{
    /**
     * @var GeoCacheRepository
     */
    private $geoCacheRepository;

    /**
     * @param GeoCacheRepository $geoCacheRepository
     */
    public function __construct(GeoCacheRepository $geoCacheRepository)
    {
        $this->geoCacheRepository = $geoCacheRepository;
    }

    /**
     * Fetches all GeoCaches.
     *
     * @return GeoCacheEntity[]
     */
    public function fetchAll()
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
     *
     * @param string $waypoint
     *
     * @return GeoCacheEntity|null
     */
    public function fetchByWaypoint($waypoint)
    {
        $waypointType = WaypointType::guess($waypoint);

        $waypointEntity = null;

        try {
            if ($waypointType === WaypointType::OC) {
                $waypointEntity = $this->geoCacheRepository->fetchOneBy([
                    'wp_oc' => $waypoint,
                ]);
            } elseif ($waypointType === WaypointType::GC) {
                $waypointEntity = $this->geoCacheRepository->fetchGCWaypoint($waypoint);
            }
        } catch (RecordNotFoundException $e) {
            $waypointEntity = null;
        }

        return $waypointEntity;
    }

    /**
     * Creates a GeoCache in the database.
     *
     * @param GeoCacheEntity $entity
     *
     * @return GeoCacheEntity
     */
    public function create(GeoCacheEntity $entity)
    {
        return $this->geoCacheRepository->create($entity);
    }

    /**
     * Update a GeoCache in the database.
     *
     * @param GeoCacheEntity $entity
     *
     * @return GeoCacheEntity
     */
    public function update(GeoCacheEntity $entity)
    {
        return $this->geoCacheRepository->update($entity);
    }

    /**
     * Removes a GeoCache from the database.
     *
     * @param GeoCacheEntity $entity
     *
     * @return GeoCacheEntity
     */
    public function remove(GeoCacheEntity $entity)
    {
        return $this->geoCacheRepository->remove($entity);
    }
}
