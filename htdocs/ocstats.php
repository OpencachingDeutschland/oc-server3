<?php
/***************************************************************************
 * for license information see LICENSE.md
 * create / redirect to statpic
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

$jpegQuality = 80;
$fontFile = __DIR__ . '/resource2/' . $opt['template']['style'] . '/fonts/dejavu/ttf/DejaVuSans.ttf';

$userId = isset($_REQUEST['userid']) ? (int) $_REQUEST['userid'] : 0;
$lang = isset($_REQUEST['lang']) ? mb_strtoupper($_REQUEST['lang']) : $opt['template']['locale'];

if (!isset($opt['locale'][$lang])) {
    $lang = $opt['template']['locale'];
}

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$fileName = __DIR__ . '/images/statpics/statpic' . $userId . $lang . '.jpg';
$userStatisticPicture = (int) $connection->createQueryBuilder()
    ->select('COUNT(*)')
    ->from('user_statpic')
    ->where('user_id = :userId')
    ->andWhere('lang = :lang')
    ->setParameter('userId', $userId)
    ->setParameter('lang', $lang)
    ->execute()
    ->fetchColumn();

if ($userStatisticPicture === 0 || !file_exists($fileName)) {
    $userData = $connection->fetchAssoc(
        'SELECT `user`.`username`,
                `stat_user`.`hidden`,
                `stat_user`.`found`,
                `user`.`statpic_logo`,
                `user`.`statpic_text`
         FROM `user`
         LEFT JOIN `stat_user`
           ON `user`.`user_id`=`stat_user`.`user_id`
         WHERE `user`.`user_id`= :userId',
        ['userId' => $userId]
        );

    if (is_array($userData)) {
        $username = $userData['username'];
        $found = (int) $userData['found'];
        $hidden = (int) $userData['hidden'];
        $logo = (int) $userData['statpic_logo'];
        $logoText = (int) $userData['statpic_text'];

        $textCounterStatistic = $translate->t('Finds: %1  Hidden: %2', '', '', 0, '', 0, $lang);
        $textCounterStatistic = str_replace(['%1', '%2'], [$found, $hidden], $textCounterStatistic);
    } else {
        $userId = 0;
        $username = $translate->t('<User not known>', '', '', 0, '', 0, $lang);
        $found = 0;
        $hidden = 0;
        $logo = 0;
        $logoText = 'Opencaching';
    }

    // Bild existiert nicht => neu erstellen
    $statPics = $connection->createQueryBuilder()
        ->select('tplpath, maxtextwidth')
        ->from('statpics')
        ->where('id = :id')
        ->setParameter(':id', $logo)
        ->execute()
        ->fetch(PDO::FETCH_ASSOC);

    if (is_array($statPics)) {
        $tplPath = $opt['rootpath'] . $statPics['tplpath'];
        $maxTextWidth = $statPics['maxtextwidth'];
    } else {
        $tplPath = __DIR__ . '/images/ocstats1.gif';
        $maxTextWidth = 60;
        $logo = 1;
    }

    $im = imagecreatefromgif($tplPath);
    $clrWhite = imagecolorallocate($im, 255, 255, 255);
    $clrBorder = imagecolorallocate($im, 70, 70, 70);
    $clrBlack = imagecolorallocate($im, 0, 0, 0);
    $clrBlue = imagecolorallocate($im, 0, 0, 255);
    $drawRectangle = true;

    switch ($logo) {
        case 4:
        case 5:
        case 10:
            // write text
            $fontSize = 10;
            $text = $username;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 15, $clrBlack, $fontFile, $text);
            $fontSize = 7.5;
            $text = $textCounterStatistic;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 32, $clrBlack, $fontFile, $text);
            break;
        case 2:
            // write text
            $fontSize = 10;
            $text = $username;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 15, $clrBlack, $fontFile, $text);
            $fontSize = 7;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $logoText);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 29, $clrBlack, $fontFile, $logoText);
            $fontSize = 7.5;
            $text = $textCounterStatistic;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 45, $clrBlack, $fontFile, $text);
            break;
        case 6:
        case 7:
        case 11:
            // write text
            $fontSize = 10;
            $text = $username;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 15, $clrBlack, $fontFile, $text);
            $fontSize = 7.5;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $logoText);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 32, $clrBlack, $fontFile, $logoText);
            break;
        case 8:
            // write text
            $fontSize = 10;
            $text = $username;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 8 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 8 : $maxTextWidth, 20, $clrBlack, $fontFile, $text);
            $fontSize = 8;
            $text = $textCounterStatistic;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 12 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 12 : $maxTextWidth, 39, $clrBlack, $fontFile, $text);

            $drawRectangle = false;

            break;
        case 1:
        case 9:
        default:
            // write text
            $fontSize = 10;
            $text = $username;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 15, $clrBlack, $fontFile, $text);
            $fontSize = 7;
            $text = $textCounterStatistic;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $text);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 29, $clrBlack, $fontFile, $text);
            $fontSize = 8;
            $textSize = imagettfbbox($fontSize, 0, $fontFile, $logoText);
            imagettftext($im, $fontSize, 0, (imagesx($im) - ($textSize[2] - $textSize[0]) - 5 > $maxTextWidth) ? imagesx($im) - ($textSize[2] - $textSize[0]) - 5 : $maxTextWidth, 45, $clrBlack, $fontFile, $logoText);
    }

    if ($drawRectangle === true) {
        // draw border
        imagerectangle($im, 0, 0, imagesx($im) - 1, imagesy($im) - 1, $clrBorder);
    }
    // write output
    imagejpeg($im, $fileName, $jpegQuality);
    imagedestroy($im);

    $connection->executeQuery(
        'INSERT INTO `user_statpic` (`user_id`, `lang`)
         VALUES (:userId, :lang) ON DUPLICATE KEY UPDATE `date_created`=NOW()',
        ['userId'=> $userId, 'lang' => $lang]
    );
}

// redirect to the generated statistics picture
$tpl->redirect('images/statpics/statpic' . $userId . $lang . '.jpg');
