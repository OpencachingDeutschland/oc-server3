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
            new TwigFilter('ocFilterD', [$this, 'oc_Filter_D']),
            new TwigFilter('ocFilterT', [$this, 'oc_Filter_T']),
            new TwigFilter('rot13', [$this, 'oc_Filter_rot13']),
            new TwigFilter('rot13gc', [$this, 'oc_Filter_rot13_gc']),
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
     * calculate and format difficulty value
     *
     * @param $number
     *
     * @return string
     */
    public function oc_Filter_D($number)
    : string {
        if ($number % 2 === 0) {
            $value = 'D' . number_format($number / 2, 0);
        } else {
            $value = 'D' . number_format($number / 2, 1);
        }

        return $value;
    }

    /**
     * calculate and format terrain value
     *
     * @param $number
     *
     * @return string
     */
    public function oc_Filter_T($number)
    : string {
        if ($number % 2 === 0) {
            $value = 'T' . number_format($number / 2, 0);
        } else {
            $value = 'T' . number_format($number / 2, 1);
        }

        return $value;
    }

    /**
     * convert string via ROT13
     *
     * @param $string
     *
     * @return string
     */
    public function oc_Filter_rot13($string)
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
    public function oc_Filter_rot13_gc($string)
    : string {
        return str_rot13_gc($string);
    }
}
