<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class MapsRepository
{
    private CachesRepository $cachesRepository;

    public function __construct(CachesRepository $cachesRepository)
    {
        $this->cachesRepository = $cachesRepository;
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
}
