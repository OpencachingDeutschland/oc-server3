<?php

/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\GeoCache;

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
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

    public function generateQrCodeFromString(string $qrCodeValue)
    {
        $image = null;
        $qrCode = new QrCode($qrCodeValue);
        $qrCode->setSize(400);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
        $qrCode->setLabel('www.opencaching.de', 16, null, LabelAlignment::CENTER);
        $qrCode->setValidateResult(false);

        $logo = imagecreatefrompng(__DIR__ . '/../../../theme/frontend/images/logo/qr-code-oc-logo.png');
        $qrCodeGenerated = imagecreatefromstring($qrCode->writeString());

        imagecopy(
            $qrCodeGenerated,
            $logo,
            150,
            150,
            0,
            0,
            imagesx($logo),
            imagesy($logo)
        );

        ob_start();
        imagepng($qrCodeGenerated);
        $image = ob_get_contents();
        ob_end_clean();

        imagedestroy($qrCodeGenerated);

        return $image;
    }
}
