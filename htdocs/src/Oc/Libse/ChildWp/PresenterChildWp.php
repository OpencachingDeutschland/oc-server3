<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\ChildWp;

use Oc\Libse\Coordinate\PresenterCoordinate;
use Oc\Libse\Validator\AlwaysValidValidator;
use Oc\Libse\Validator\ArrayValidator;

require_once(__DIR__ . '/../../../../lib2/error.inc.php');

abstract class PresenterChildWp
{
    const REQ_WP_TYPE = 'wp_type';
    const REQ_WP_DESC = 'desc';
    const TPL_PAGE_TITLE = 'pagetitle';
    const TPL_CACHE_ID = 'cacheid';
    const TPL_CHILD_ID = 'childid';
    const TPL_DELETE_ID = 'deleteid';
    const TPL_WP_TYPE = 'wpType';
    const TPL_WP_DESC = 'wpDesc';
    const TPL_WP_TYPE_IDS = 'wpTypeIds';
    const TPL_WP_TYPE_NAMES = 'wpTypeNames';
    const TPL_WP_NAME_IMAGES = 'wpNameImages';
    const TPL_WP_TYPE_ERROR = 'wpTypeError';
    const TPL_SUBMIT_BUTTON = 'submitButton';
    const TPL_DISABLED = 'disabled';

    private $request;
    private $translator;
    protected $coordinate;
    private $waypointTypes = array();
    private $waypointTypeValid = true;
    private $typeImages = array();
    protected $type = '0';
    protected $description;
    protected $cacheId;
    protected $childId;
    protected $childWpHandler;

    public function __construct($request = false, $translator = false)
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->coordinate = new PresenterCoordinate($this->request, $this->translator);
    }

    public function doSubmit()
    {
        $this->onDoSubmit($this->coordinate->getCoordinate(), $this->getDesc());
    }

    abstract protected function onDoSubmit($coordinate, $description);

    protected function getType()
    {
        return $this->request->get(self::REQ_WP_TYPE, $this->type);
    }

    private function getDesc()
    {
        return $this->request->get(self::REQ_WP_DESC, $this->description);
    }

    public function init($childWpHandler, $cacheId)
    {
        $this->childWpHandler = $childWpHandler;
        $this->cacheId = $cacheId;
        $this->waypointTypes = $childWpHandler->getChildWpIdAndNames();
        $this->typeImages = $childWpHandler->getChildNamesAndImages();
    }

    public function initCoordinate($coords)
    {
        $this->coordinate->init($coords->latitude(), $coords->longitude());
    }

    public function initChildWp($childId, $childWp)
    {
        $this->childId = $childId;
        $this->type = $childWp['type'];
        $this->description = $childWp['description'];
        $this->coordinate->init($childWp['latitude'], $childWp['longitude']);
    }

    public function prepare($template)
    {
        $template->assign(self::TPL_PAGE_TITLE, $this->translator->Translate($this->getTitle()));
        $template->assign(self::TPL_SUBMIT_BUTTON, $this->translator->Translate($this->getSubmitButton()));
        $template->assign(self::TPL_CACHE_ID, $this->cacheId);
        $template->assign(self::TPL_WP_DESC, $this->getDesc());
        $template->assign(self::TPL_WP_TYPE, $this->getType());
        $template->assign(self::TPL_DISABLED, false);
        $this->prepareTypes($template);
        $this->coordinate->prepare($template);

        if (!$this->waypointTypeValid) {
            $template->assign(self::TPL_WP_TYPE_ERROR, $this->translator->translate('Select waypoint type'));
        }

        $this->onPrepare($template);
    }

    protected function onPrepare($template)
    {
    }

    private function prepareTypes($template)
    {
        $template->assign(self::TPL_WP_TYPE_IDS, $this->getWaypointTypeIds());
        $template->assign(self::TPL_WP_TYPE_NAMES, $this->waypointTypes);
        $template->assign(self::TPL_WP_NAME_IMAGES, $this->typeImages);
    }

    abstract protected function getTitle();

    abstract protected function getSubmitButton();

    private function getWaypointTypeIds()
    {
        return array_keys($this->waypointTypes);
    }

    public function validate()
    {
        $wpTypeValidator = new ArrayValidator($this->getWaypointTypeIds());

        $this->request->validate(self::REQ_WP_DESC, new AlwaysValidValidator());
        $this->waypointTypeValid = $this->request->validate(self::REQ_WP_TYPE, $wpTypeValidator);
        $coordinateValid = $this->coordinate->validate();

        return $this->waypointTypeValid && $coordinateValid;
    }

    public function getCacheId()
    {
        return $this->cacheId;
    }
}
