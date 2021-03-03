<?php

namespace Oc\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension
 *
 * @package Oc\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    : array
    {
        return [
            new TwigFilter('ocFilterDifficulty', [$this, 'ocFilterDifficulty']),
            new TwigFilter('ocFilterTerrain', [$this, 'ocFilterTerrain']),
            new TwigFilter('rot13', [$this, 'ocFilterROT13']),
            new TwigFilter('rot13gc', [$this, 'ocFilterROT13gc']),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    : array
    {
        return [];
        //        return [new TwigFunction('area', [$this, 'calculateArea']),];
    }

    /**
     * @param $number
     *
     * @return string
     */
    private function convertDifficultyTerrainRating($number)
    : string {
        if ($number % 2 === 0) {
            return number_format($number / 2, 0);
        } else {
            return number_format($number / 2, 1);
        }
    }

    /**
     * calculate and format difficulty value
     *
     * @param $number
     *
     * @return string
     */
    public function ocFilterDifficulty($number)
    : string {
        return 'D' . $this->convertDifficultyTerrainRating($number);
    }

    /**
     * calculate and format terrain value
     *
     * @param $number
     *
     * @return string
     */
    public function ocFilterTerrain($number)
    : string {
        return 'T' . $this->convertDifficultyTerrainRating($number);
    }

    /**
     * convert string via ROT13
     *
     * @param $string
     *
     * @return string
     */
    public function ocFilterROT13($string)
    : string {
        return str_rot13($string);
    }

    /**
     * convert string via ROT13, but ignore characters in [] brackets
     *
     * @param $string
     *
     * @return string
     */
    public function ocFilterROT13gc($string)
    : string {
        return str_rot13_gc($string);
    }
}
