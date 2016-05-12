<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Coordinate;

class FormatterCoordinate
{
    public function formatLatHem($coordinate)
    {
        return $coordinate->latitude() >= 0 ? 'N' : 'S';
    }

    public function formatLonHem($coordinate)
    {
        return $coordinate->longitude() >= 0 ? 'E' : 'W';
    }

    public function formatLatDeg($coordinate)
    {
        return sprintf("%02d", $coordinate->latDeg());
    }

    public function formatLonDeg($coordinate)
    {
        return sprintf("%03d", $coordinate->lonDeg());
    }

    public function formatLatMin($coordinate)
    {
        return $this->FormatMin($coordinate->latMin());
    }

    public function formatLonMin($coordinate)
    {
        return $this->FormatMin($coordinate->lonMin());
    }

    private function formatMin($min)
    {
        return sprintf("%06.3f", $min);
    }

    public function formatHtml($coordinate, $separator = ' ')
    {
        return $this->formatHtmlHemDegMin($this->formatLatHem($coordinate), $this->formatLatDeg($coordinate), $this->formatLatMin($coordinate))
        . $separator .
        $this->formatHtmlHemDegMin($this->formatLonHem($coordinate), $this->formatLonDeg($coordinate), $this->formatLonMin($coordinate));
    }

    private function formatHtmlHemDegMin($hem, $deg, $min)
    {
        return $hem . ' ' . $deg . '&deg; ' . $min . '\'';
    }
}
