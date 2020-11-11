<?php
/**
 * This class is heavily inspired from StaticMap 0.3.1 written by Gerhard Koch
 *
 * @author Gerhard Koch <gerhard.koch AT ymail.com>
 * @link: https://github.com/dfacts/staticmaplite
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OcLegacy\Map;

class StaticMap
{
    /**
     * @var int
     */
    private $maxWidth = 1024;

    /**
     * @var int
     */
    private $maxHeight = 1024;

    /**
     * @var int
     */
    private $tileSize = 256;

    /**
     * @var string
     */
    private $tileSrcUrl = 'http://tile.openstreetmap.org/{Z}/{X}/{Y}.png';

    /**
     * @var string
     */
    private $markerBaseDir = './theme/frontend/images/markers';

    /**
     * @var string
     */
    private $attribution = '(c) OpenStreetMap contributors';

    /**
     * @var bool
     */
    private $useTileCache = true;

    /**
     * @var string
     */
    private $tileCacheBaseDir =  __DIR__ . '/../../../var/cache2/staticmap';

    /**
     * @var int
     */
    private $zoom = 0;

    /**
     * @var int
     */
    private $lat = 0;

    /**
     * @var int
     */
    private $lon = 0;

    /**
     * @var int
     */
    private $width = 100;

    /**
     * @var int
     */
    private $height = 100;

    /**
     * @var array
     */
    private $markers = [];

    /**
     * @var
     */
    private $image;

    /**
     * @var
     */
    private $centerX;

    /**
     * @var
     */
    private $centerY;

    /**
     * @var
     */
    private $offsetX;

    /**
     * @var
     */
    private $offsetY;

    private function parseParams(): void
    {
        $this->zoom = isset($_GET['zoom']) ? intval($_GET['zoom']) : 0;
        if ($this->zoom > 18) {
            $this->zoom = 18;
        }

        list($this->lat, $this->lon) = explode(',', $_GET['center']);
        $this->lat = floatval($this->lat);
        $this->lon = floatval($this->lon);

        if (isset($_GET['size']) && !empty($_GET['size'])) {
            list($this->width, $this->height) = explode('x', $_GET['size']);
            $this->width = (int) $this->width;
            if ($this->width > $this->maxWidth) {
                $this->width = $this->maxWidth;
            }
            $this->height = (int) $this->height;
            if ($this->height > $this->maxHeight) {
                $this->height = $this->maxHeight;
            }
        }
        if (isset($_GET['markers']) && !empty($_GET['markers'])) {
            foreach (explode('|', $_GET['markers']) as $marker) {
                list($markerLat, $markerLon, $markerType) = explode(',', $marker);
                $markerLat = floatval($markerLat);
                $markerLon = floatval($markerLon);
                $markerType = basename($markerType);
                $this->markers[] = ['lat' => $markerLat, 'lon' => $markerLon, 'type' => $markerType];
            }
        }
    }

    private function lonToTile($long, $zoom)
    {
        return (($long + 180) / 360) * (2 ** $zoom);
    }

    private function latToTile($lat, $zoom)
    {
        return (1 - log(tan($lat * M_PI / 180) + 1 / cos($lat * M_PI / 180)) / M_PI) / 2 * (2 ** $zoom);
    }

    private function initCoordinates(): void
    {
        $this->centerX = $this->lonToTile($this->lon, $this->zoom);
        $this->centerY = $this->latToTile($this->lat, $this->zoom);
        $this->offsetX = floor((floor($this->centerX) - $this->centerX) * $this->tileSize);
        $this->offsetY = floor((floor($this->centerY) - $this->centerY) * $this->tileSize);
    }

    private function createBaseMap(): void
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $startX = floor($this->centerX - ($this->width / $this->tileSize) / 2);
        $startY = floor($this->centerY - ($this->height / $this->tileSize) / 2);
        $endX = ceil($this->centerX + ($this->width / $this->tileSize) / 2);
        $endY = ceil($this->centerY + ($this->height / $this->tileSize) / 2);
        $this->offsetX = -floor(($this->centerX - floor($this->centerX)) * $this->tileSize);
        $this->offsetY = -floor(($this->centerY - floor($this->centerY)) * $this->tileSize);
        $this->offsetX += floor($this->width / 2);
        $this->offsetY += floor($this->height / 2);
        $this->offsetX += floor($startX - floor($this->centerX)) * $this->tileSize;
        $this->offsetY += floor($startY - floor($this->centerY)) * $this->tileSize;

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $url = str_replace(
                    ['{Z}', '{X}', '{Y}'],
                    [$this->zoom, $x, $y],
                    $this->tileSrcUrl
                );
                $tileData = $this->fetchTile($url);
                if ($tileData) {
                    $tileImage = imagecreatefromstring($tileData);
                } else {
                    $tileImage = imagecreate($this->tileSize, $this->tileSize);
                    $color = imagecolorallocate($tileImage, 255, 255, 255);
                    @imagestring($tileImage, 1, 127, 127, 'err', $color);
                }
                $destX = ($x - $startX) * $this->tileSize + $this->offsetX;
                $destY = ($y - $startY) * $this->tileSize + $this->offsetY;
                imagecopy($this->image, $tileImage, $destX, $destY, 0, 0, $this->tileSize, $this->tileSize);
            }
        }
    }

    private function placeMarkers(): void
    {
        foreach ($this->markers as $marker) {
            // set some local variables
            $markerLat = $marker['lat'];
            $markerLon = $marker['lon'];

            $markerImageOffsetX = -8;
            $markerImageOffsetY = -23;

            $markerImg = imagecreatefrompng($this->markerBaseDir . '/small-blue.png');

            // calc position
            $destinationX = floor(
                ($this->width / 2) - $this->tileSize * ($this->centerX - $this->lonToTile($markerLon, $this->zoom))
            );
            $destinationY = floor(
                ($this->height / 2) - $this->tileSize * ($this->centerY - $this->latToTile($markerLat, $this->zoom))
            );

            imagecopy(
                $this->image,
                $markerImg,
                $destinationX + intval($markerImageOffsetX),
                $destinationY + intval($markerImageOffsetY),
                0,
                0,
                imagesx($markerImg),
                imagesy($markerImg)
            );
        };
    }

    private function tileUrlToFilename($url): string
    {
        return $this->tileCacheBaseDir . '/' . str_replace(['http://'], '', $url);
    }

    private function checkTileCache($url)
    {
        $filename = $this->tileUrlToFilename($url);
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
    }

    private function mkdirRecursive($pathname, $mode): bool
    {
        is_dir(dirname($pathname)) || $this->mkdirRecursive(dirname($pathname), $mode);

        return is_dir($pathname) || mkdir($pathname, $mode) || is_dir($pathname);
    }

    private function writeTileToCache($url, $data): void
    {
        $filename = $this->tileUrlToFilename($url);
        $this->mkdirRecursive(dirname($filename), 0777);
        file_put_contents($filename, $data);
    }

    private function fetchTile($url)
    {
        if ($this->useTileCache && $cached = $this->checkTileCache($url)) {
            return $cached;
        }
        $opts = [
            'http' => [
                'method' => 'GET',
                'timeout' => 2.0,
                'header' => 'User-Agent: https://www.opencaching.de',
            ],
        ];

        $context = stream_context_create($opts);
        $tile = file_get_contents($url, false, $context);


        if ($tile && $this->useTileCache) {
            $this->writeTileToCache($url, $tile);
        }

        return $tile;
    }

    private function copyrightNotice(): void
    {
        $string = $this->attribution;
        $fontSize = 1;
        $len = strlen($string);
        $width = imagefontwidth($fontSize) * $len;
        $height = imagefontheight($fontSize);
        $img = imagecreate($width, $height);

        imagesavealpha($img, true);
        imagealphablending($img, false);
        $white = imagecolorallocatealpha($img, 200, 200, 200, 50);
        imagefill($img, 0, 0, $white);

        $color = imagecolorallocate($img, 0, 0, 0);
        $ypos = 0;
        for ($i = 0; $i < $len; $i++) {
            // Position of the character horizontally
            $xPosition = $i * imagefontwidth($fontSize);
            // Draw character
            imagechar($img, $fontSize, $xPosition, $ypos, $string, $color);
            // Remove character from string
            $string = substr($string, 1);
        }

        imagecopy(
            $this->image,
            $img,
            imagesx($this->image) - imagesx($img),
            imagesy($this->image) - imagesy($img),
            0,
            0,
            imagesx($img),
            imagesy($img)
        );
    }

    private function sendHeader(): void
    {
        header('Content-Type: image/png');
        $expires = strtotime('+14 days', 0);
        header('Cache-Control: private, maxage=' . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    }

    private function makeMap(): void
    {
        $this->initCoordinates();

        $this->createBaseMap();
        if (count($this->markers)) {
            $this->placeMarkers();
        }
        $this->copyrightNotice();
    }

    public function showMap(): bool
    {
        $this->parseParams();

        $this->makeMap();

        $this->sendHeader();

        return imagepng($this->image);
    }
}
