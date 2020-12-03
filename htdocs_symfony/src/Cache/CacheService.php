<?php

namespace Oc\Cache;

use Doctrine\DBAL\Connection;
use Oc\Entity\CacheEntity;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\CacheRepository;

class CacheService
{
    /**
     * @var CacheRepository
     */
    private $cacheRepository;

//    public function __construct(CacheRepository $cacheRepository)
    public function __construct(Connection $connection, SecurityRolesRepository  $securityRolesRepository) //test
    {
//        $this->cacheRepository = $cacheRepository;

//        $this->connection = $connection;
//        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * Fetches all caches.
     *
     * @return CacheEntity[]
     */
    public function fetchAll(): array
    {
        try {
            $result = $this->cacheRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetches a cache by its id.
     */
    public function fetchOneById(int $cache_id): ?CacheEntity
    {
        try {
            $result = $this->cacheRepository->fetchOneById($id);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Fetches a cache by its wp_oc.
     */
    public function fetchOneByWpOC(text $cache_wp_oc): ?CacheEntity
    {
        try {
            $result = $this->cacheRepository->fetchOneById($wp_oc);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Fetches a cache by its name.
     */
    public function fetchOneByName(text $cache_name): ?CacheEntity
    {
        try {
            $result = $this->cacheRepository->fetchOneById($name);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

}
