<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class MapsRepository
 *
 * @package Oc\Repository
 */
class MapsRepository
{
    private Connection $connection;

    private CachesRepository $cachesRepository;

    /**
     * CachesRepository constructor.
     *
     * @param Connection $connection
     * @param CachesRepository $cachesRepository
     */
    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @param string $lat
     * @param string $lon
     * @param bool $centerView
     *
     * @return array
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     */
    public function determineMapCenterPoint(string $lat = '', string $lon = '', bool $centerView = false)
    : array {
        $mapCenterViewLat = '48.3585';
        $mapCenterViewLon = '10.8613';
        $mapWP = $this->cachesRepository->fetchAll();

        // Mittelpunkt für die Kartenzentrierung bestimmen. Sonst obiger Standard.
        if ($lat != '' && $lon != '') {
            $mapCenterViewLat = $lat;
            $mapCenterViewLon = $lon;
        } elseif ($centerView) {
            $centerPoint = ['lat' => 0, 'lon' => 0, 'count' => 0];

            // Schleife, die alle WP aufsummiert, damit danach der Durchschnitt gebildet werden kann
            foreach ($mapWP as $WP) {
                $centerPoint['lat'] += $WP->latitude;
                $centerPoint['lon'] += $WP->longitude;
                $centerPoint['count'] ++;
            }
            if ($centerPoint['count'] > 0) {
                $mapCenterViewLat = $centerPoint['lat'] / $centerPoint['count'];
                $mapCenterViewLon = $centerPoint['lon'] / $centerPoint['count'];
            }
        }

        return [$mapCenterViewLat, $mapCenterViewLon, $mapWP];
    }
}
