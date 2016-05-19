<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Coordinate;

use Oc\Libse\Http\RequestHttp;
use Oc\Libse\Language\TranslatorLanguage;
use Oc\Libse\Validator\IntegerValidator;
use Oc\Libse\Validator\RealValidator;
use Oc\Libse\Validator\RegexValidator;

class PresenterCoordinate
{
    const LAT_HEM = 'lat_hem';
    const LAT_DEG = 'lat_deg';
    const LAT_MIN = 'lat_min';
    const LON_HEM = 'lon_hem';
    const LON_DEG = 'lon_deg';
    const LON_MIN = 'lon_min';
    const COORD_ERROR = 'coord_error';

    private $coordinate;
    private $request;
    private $translator;
    private $valid = true;

    public function __construct($request = false, $translator = false)
    {
        $this->request = $this->initRequest($request);
        $this->translator = $this->initTranslator($translator);
        $this->init(0, 0);
    }

    private function initRequest($request)
    {
        if ($request) {
            return $request;
        }

        return new RequestHttp();
    }

    private function initTranslator($translator)
    {
        if ($translator) {
            return $translator;
        }

        return new TranslatorLanguage();
    }

    private function getLatHem()
    {
        return $this->request->get(self::LAT_HEM, $this->coordinate->latHem() ? 'N' : 'S') == 'N';
    }

    private function getLonHem()
    {
        return $this->request->get(self::LON_HEM, $this->coordinate->lonHem() ? 'E' : 'W') == 'E';
    }

    private function getLatDeg()
    {
        return $this->request->get(self::LAT_DEG, $this->coordinate->latDeg());
    }

    private function getLonDeg()
    {
        return $this->request->get(self::LON_DEG, $this->coordinate->lonDeg());
    }

    private function getLatMin()
    {
        return $this->request->get(self::LAT_MIN, $this->coordinate->latMin());
    }

    private function getLonMin()
    {
        return $this->request->get(self::LON_MIN, $this->coordinate->lonMin());
    }

    public function init($latitude, $longitude)
    {
        $this->coordinate = new CoordinateCoordinate($latitude, $longitude);
    }

    /**
     * @todo add phpdoc parameter for $template
     *
     * @param $template
     */
    public function prepare($template)
    {
        $formatter = new FormatterCoordinate();
        $coordinate = $this->getCoordinate();

        $template->assign(self::LAT_HEM, $formatter->formatLatHem($coordinate));
        $template->assign(self::LAT_DEG, $formatter->formatLatDeg($coordinate));
        $template->assign(self::LAT_MIN, $formatter->formatLatMin($coordinate));
        $template->assign(self::LON_HEM, $formatter->formatLonHem($coordinate));
        $template->assign(self::LON_DEG, $formatter->formatLonDeg($coordinate));
        $template->assign(self::LON_MIN, $formatter->formatLonMin($coordinate));

        if (!$this->valid) {
            $template->assign(self::COORD_ERROR, $this->translator->translate('Invalid coordinate'));
        }
    }

    public function getCoordinate()
    {
        return CoordinateCoordinate::fromHemDegMin(
            $this->getLatHem(),
            $this->getLatDeg(),
            $this->getLatMin(),
            $this->getLonHem(),
            $this->getLonDeg(),
            $this->getLonMin()
        );
    }

    public function hasCoordinate()
    {
        return $this->getCoordinate() !== new CoordinateCoordinate(0, 0);
    }

    public function validate()
    {
        $validators = $this->getValidators();
        $this->valid = true;

        foreach ($validators as $key => $validator) {
            if (!$this->request->validate($key, $validator)) {
                $this->valid = false;
            }
        }

        if (!$this->hasCoordinate()) {
            $this->valid = false;
        }

        return $this->valid;
    }

    private function getValidators()
    {
        return [
            self::LAT_HEM => new RegexValidator('[NS]$'),
            self::LAT_DEG => new IntegerValidator(0, 90),
            self::LAT_MIN => new RealValidator(0, 59.999, '{1,2}', '{1,3}'),
            self::LON_HEM => new RegexValidator('[EW]$'),
            self::LON_DEG => new IntegerValidator(0, 180),
            self::LON_MIN => new RealValidator(0, 59.999, '{1,2}', '{1,3}')
        ];
    }
}
