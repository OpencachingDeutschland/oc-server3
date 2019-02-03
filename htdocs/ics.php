<?php declare(strict_types=1);

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheRepository;

$cacheId = (int) $_GET['cacheId'];

require __DIR__ . '/lib2/web.inc.php';

/** @var GeoCacheRepository $geoCacheRepository */
$geoCacheRepository = AppKernel::Container()->get(GeoCacheRepository::class);

try {
    $geoCache = $geoCacheRepository->fetchOneBy(['cache_id' => $cacheId]);
} catch (\Oc\Repository\Exception\RecordNotFoundException $e) {
    die();
}

// no event cache
if ($geoCache->type !== 6) {
    die();
}

$vCalendar = new Calendar('https://www.opencaching.de/viewcache.php?cacheid=' . $cacheId);

$vEvent = new Event();

$vEvent
    ->setDtStart($geoCache->dateHidden)
    ->setDtEnd($geoCache->dateHidden->add(new DateInterval('PT1H')))
    ->setNoTime(true)
    ->setSummary($geoCache->name);

$vCalendar->addComponent($vEvent);


header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$geoCache->wpOc.'.ics"');

echo $vCalendar->render();
