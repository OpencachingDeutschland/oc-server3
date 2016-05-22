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
    const lat_hem = 'lat_hem';
    const lat_deg = 'lat_deg';
    const lat_min = 'lat_min';
    const lon_hem = 'lon_hem';
    const lon_deg = 'lon_deg';
    const lon_min = 'lon_min';
    const coord_error = 'coord_error';

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
        return $this->request->get(self::lat_hem, $this->coordinate->latHem() ? 'N' : 'S') == 'N';
    }

    private function getLonHem()
    {
        return $this->request->get(self::lon_hem, $this->coordinate->lonHem() ? 'E' : 'W') == 'E';
    }

    private function getLatDeg()
    {
        return $this->request->get(self::lat_deg, $this->coordinate->latDeg());
    }

    private function getLonDeg()
    {
        return $this->request->get(self::lon_deg, $this->coordinate->lonDeg());
    }

    private function getLatMin()
    {
        return $this->request->get(self::lat_min, $this->coordinate->latMin());
    }

    private function getLonMin()
    {
        return $this->request->get(self::lon_min, $this->coordinate->lonMin());
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

        $template->assign(self::lat_hem, $formatter->formatLatHem($coordinate));
        $template->assign(self::lat_deg, $formatter->formatLatDeg($coordinate));
        $template->assign(self::lat_min, $formatter->formatLatMin($coordinate));
        $template->assign(self::lon_hem, $formatter->formatLonHem($coordinate));
        $template->assign(self::lon_deg, $formatter->formatLonDeg($coordinate));
        $template->assign(self::lon_min, $formatter->formatLonMin($coordinate));

        if (!$this->valid) {
            $template->assign(self::coord_error, $this->translator->translate('Invalid coordinate'));
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
        return $this->getCoordinate() != new CoordinateCoordinate(0, 0);
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
            self::lat_hem => new RegexValidator('[NS]$'),
            self::lat_deg => new IntegerValidator(0, 90),
            self::lat_min => new RealValidator(0, 59.999, '{1,2}', '{1,3}'),
            self::lon_hem => new RegexValidator('[EW]$'),
            self::lon_deg => new IntegerValidator(0, 180),
            self::lon_min => new RealValidator(0, 59.999, '{1,2}', '{1,3}')
        ];
    }
}
