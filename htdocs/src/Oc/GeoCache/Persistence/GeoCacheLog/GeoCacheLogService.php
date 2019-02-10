<?php

namespace Oc\GeoCache\Persistence\GeoCacheLog;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeoCacheLogService
{
    /**
     * @var GeoCacheLogRepository
     */
    private $geoCacheRepository;

    public function __construct(GeoCacheLogRepository $geoCacheRepository)
    {
        $this->geoCacheRepository = $geoCacheRepository;
    }

    /**
     * Fetches all GeoCacheLogs.
     *
     * @return GeoCacheLogEntity[]
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
     * Fetches the latest geo cache log for given user id.
     */
    public function getLatestUserLog(int $userId): ?GeoCacheLogEntity
    {
        try {
            $result = $this->geoCacheRepository->getLatestUserLog($userId);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Creates a GeoCacheLog in the database.
     */
    public function create(GeoCacheLogEntity $entity): GeoCacheLogEntity
    {
        return $this->geoCacheRepository->create($entity);
    }

    /**
     * Update a GeoCacheLog in the database.
     */
    public function update(GeoCacheLogEntity $entity): GeoCacheLogEntity
    {
        return $this->geoCacheRepository->update($entity);
    }

    /**
     * Removes a GeoCacheLog from the database.
     */
    public function remove(GeoCacheLogEntity $entity): GeoCacheLogEntity
    {
        return $this->geoCacheRepository->remove($entity);
    }
}
