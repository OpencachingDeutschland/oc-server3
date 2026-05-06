<?php

/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\GeoCache;

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Oc\GeoCache\Enum\GeoCacheType;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheEntity;

class Util
{
    public function generateIcsStringFromGeoCache(GeoCacheEntity $geoCache): string
    {
        if ($geoCache->type !== GeoCacheType::EVENT) {
            throw new \InvalidArgumentException('the given geoCache is not an event cache!');
        }

        $vCalendar = new Calendar('https://www.opencaching.de/' . $geoCache->wpOc);
        $vEvent = new Event();

        $vEvent
            ->setDtStart($geoCache->dateHidden)
            ->setDtEnd($geoCache->dateHidden->add(new \DateInterval('PT1H')))
            ->setNoTime(true)
            ->setSummary($geoCache->name)
            ->setDescription('https://www.opencaching.de/viewcache.php?cacheid=' . $geoCache->cacheId);

        $vCalendar->addComponent($vEvent);

        return $vCalendar->render();
    }

    public function generateQrCodeFromString(string $qrCodeValue): string
    {
        $qrCode = new QrCode(
            data: $qrCodeValue,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $label = new Label(
            text: 'www.opencaching.de',
            font: new Font(__DIR__ . '/../../../theme/frontend/fonts/OpenSans-Regular.ttf', 16),
            alignment: LabelAlignment::Center,
            margin: new Margin(0, 10, 10, 10),
            textColor: new Color(255, 255, 255) // Beispiel-Farbe
        );

        $logoPath = __DIR__ . '/../../../theme/frontend/images/logo/qr-code-oc-logo.png';
        $logo = new Logo($logoPath, null, 100); // Resize to width of 100 pixels

        $writer = new PngWriter();
        $result = $writer->write($qrCode, $logo, $label);

        return $result->getString();
    }
}
