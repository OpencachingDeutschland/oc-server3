<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class MapsRepository
{
    private CachesRepository $cachesRepository;

    private Connection $connection;

    public function __construct(CachesRepository $cachesRepository, Connection $connection)
    {
        $this->cachesRepository = $cachesRepository;
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     */
    public function determineMapCenterPoint(string $lat = '', string $lon = '', bool $centerView = false): array
    {
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
                $centerPoint['count']++;
            }
            if ($centerPoint['count'] > 0) {
                $mapCenterViewLat = $centerPoint['lat'] / $centerPoint['count'];
                $mapCenterViewLon = $centerPoint['lon'] / $centerPoint['count'];
            }
        }

        return [$mapCenterViewLat, $mapCenterViewLon, $mapWP];
    }

    /**
     * @throws Exception
     */
    public function getMovingCachesTracks(): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('cache_coordinates.cache_id', 'cache_coordinates.longitude', 'cache_coordinates.latitude')
                ->from('cache_coordinates')
                ->innerJoin('cache_coordinates', 'caches', 'caches', 'caches.cache_id = cache_coordinates.cache_id')
                ->where('caches.type = 9'); // 9 = moving caches

        $movingTracks = $qb->executeQuery()->fetchAllAssociative();

        // transform query array to one to be usable for twig template...
        $movingTracksTransformed = [];
        foreach ($movingTracks as $track) {
            if (array_key_exists($track['cache_id'], $movingTracksTransformed)) {
                $movingTracksTransformed [$track['cache_id']][] = [(float)$track['latitude'], (float)$track['longitude']];
            } else {
                $movingTracksTransformed [$track['cache_id']] = [[(float)$track['latitude'], (float)$track['longitude']]];
            }
        }
        // ... and keep only track infos with multiple entries per cache_id (otherwise it's no track..)
        foreach ($movingTracksTransformed as $value => $track) {
            if (count($track) == 1) {
                unset($movingTracksTransformed[$value]);
            }
        }

        return $movingTracksTransformed;
    }
}
