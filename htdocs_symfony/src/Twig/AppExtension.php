<?php

declare(strict_types=1);

namespace Oc\Twig;

use Oc\Repository\CoordinatesRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private CoordinatesRepository $coordinatesRepository;

    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }

    public function getFilters(): array
    {
        return [
                new TwigFilter('ocFilterDuration', [$this, 'ocFilterDuration']),
                new TwigFilter('ocFilterDifficulty', [$this, 'ocFilterDifficulty']),
                new TwigFilter('ocFilterTerrain', [$this, 'ocFilterTerrain']),
                new TwigFilter('rot13', [$this, 'ocFilterROT13']),
                new TwigFilter('rot13gc', [$this, 'ocFilterROT13gc']),
        ];
    }

    public function getFunctions(): array
    {
        return [
                new TwigFunction('ocFilterCoordinatesDegMin', [$this, 'ocFilterCoordinatesDegMin']),
                new TwigFunction('ocFilterCoordinatesDegMinSec', [$this, 'ocFilterCoordinatesDegMinSec']),
        ];
    }

    /**
     *
     * convert a float value into a time string, like hh:mm
     *
     */
    public function ocFilterDuration(float $number): string
    {
        return floor($number) . ':' . sprintf("%'.02d", round(60 * ($number - floor($number)), PHP_ROUND_HALF_UP));
    }

    /**
     * convert database Difficulty and Terrain ratings into values used on website
     */
    private function convertDifficultyTerrainRating($number): string
    {
        if ($number % 2 === 0) {
            return number_format($number / 2, 0);
        } else {
            return number_format($number / 2, 1);
        }
    }

    /**
     * calculate and format difficulty value
     * with_prefix=true: return value is like "D1", otherwise "1"
     */
    public function ocFilterDifficulty($number, $with_prefix = true): string
    {
        if ($with_prefix) {
            return 'D' . $this->convertDifficultyTerrainRating($number);
        } else {
            return $this->convertDifficultyTerrainRating($number);
        }
    }

    /**
     * calculate and format terrain value
     * with_prefix=true: return value is like "T1", otherwise "1"
     */
    public function ocFilterTerrain($number, $with_prefix = true): string
    {
        if ($with_prefix) {
            return 'T' . $this->convertDifficultyTerrainRating($number);
        } else {
            return $this->convertDifficultyTerrainRating($number);
        }
    }

    /**
     * convert string via ROT13
     */
    public function ocFilterROT13($string): string
    {
        return str_rot13($string);
    }

    /**
     * convert string via ROT13, but ignore characters in [] brackets
     */
    public function ocFilterROT13gc($string): string
    {
        return str_rot13_gc($string);
    }

    /**
     * convert decimal coordinates into DM format
     */
    public function ocFilterCoordinatesDegMin($lat, $lon): string
    {
        $this->coordinatesRepository->setLatLon($lat, $lon);

        $result = $this->coordinatesRepository->getDegreeMinutes();

        return $result['lat'] . ' ' . $result['lon'];
    }

    /**
     * convert decimal coordinates into DMS format
     */
    public function ocFilterCoordinatesDegMinSec($lat, $lon): string
    {
        $this->coordinatesRepository->setLatLon($lat, $lon);

        $result = $this->coordinatesRepository->getDegreeMinutesSeconds();

        return $result['lat'] . ' ' . $result['lon'];
    }
}
