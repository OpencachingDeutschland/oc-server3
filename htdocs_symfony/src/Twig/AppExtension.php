<?php

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
     * @param $number
     *
     * @return string
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
     */
    public function ocFilterDifficulty($number): string
    {
        return 'D' . $this->convertDifficultyTerrainRating($number);
    }

    /**
     * calculate and format terrain value
     */
    public function ocFilterTerrain($number): string
    {
        return 'T' . $this->convertDifficultyTerrainRating($number);
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
     * @param $lat
     * @param $lon
     *
     * @return string
     */
    public function ocFilterCoordinatesDegMin($lat, $lon): string
    {
        $this->coordinatesRepository->setLatLon($lat, $lon);

        $result = $this->coordinatesRepository->getDegreeMinutes();

        return $result['lat'] . ' ' . $result['lon'];
    }

    /**
     * @param $lat
     * @param $lon
     *
     * @return string
     */
    public function ocFilterCoordinatesDegMinSec($lat, $lon): string
    {
        $this->coordinatesRepository->setLatLon($lat, $lon);

        $result = $this->coordinatesRepository->getDegreeMinutesSeconds();

        return $result['lat'] . ' ' . $result['lon'];
    }
}
