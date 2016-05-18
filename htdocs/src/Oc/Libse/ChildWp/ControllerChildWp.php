<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\ChildWp;

use Oc\Libse\Coordinate\CoordinateCoordinate;
use Oc\Libse\Http\RequestHttp;
use Oc\Libse\Language\TranslatorLanguage;

require_once __DIR__ . '/../../../../lib2/error.inc.php';

class ControllerChildWp
{
    const req_cache_id = 'cacheid';
    const req_child_id = 'childid';
    const req_delete_id = 'deleteid';

    private $request;
    private $translator;

    public function __construct($request = false, $translator = false)
    {
        $this->request = $this->initRequest($request);
        $this->translator = $this->initTranslator($translator);
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

    public function createPresenter($template, $cacheManager, $childWpHandler)
    {
        $cacheId = $this->request->getForValidation(self::req_cache_id);

        $this->verifyCacheId($template, $cacheId, $cacheManager);

        $presenter = false;
        $childId = $this->request->getForValidation(self::req_child_id);
        $deleteId = $this->request->getForValidation(self::req_delete_id);

        if ($childId || $deleteId) {
            $presenter = $this->createEditDeletePresenter($template, $childWpHandler, $cacheId, $childId, $deleteId);
        } else {
            $presenter = $this->createAddPresenter($template, $childWpHandler, $cacheId);
        }

        $presenter->init($childWpHandler, $cacheId);

        return $presenter;
    }

    private function createEditDeletePresenter($template, $childWpHandler, $cacheId, $childId, $deleteId)
    {
        $id = $childId ? $childId : $deleteId;

        $childWp = $childWpHandler->getChildWp($id);

        $this->verifyChildWp($template, $childWp, $cacheId);

        if ($childId) {
            $presenter = new EditPresenterChildWp($this->request, $this->translator);
        } else {
            $presenter = new DeletePresenterChildWp($this->request, $this->translator);
        }

        $presenter->initChildWp($id, $childWp);

        return $presenter;
    }

    private function createAddPresenter($template, $childWpHandler, $cacheId)
    {
        $presenter = new AddPresenterChildWp($this->request, $this->translator);
        /* set default waypoint coordinates to cache coordinates */
        $presenter->initCoordinate(CoordinateCoordinate::getFromCache($cacheId));

        return $presenter;
    }

    private function verifyCacheId($template, $cacheId, $cacheManager)
    {
        if (!$cacheManager->exists($cacheId) || !$cacheManager->userMayModify($cacheId)) {
            $template->error(ERROR_CACHE_NOT_EXISTS);
        }
    }

    private function verifyChildWp($template, $childWp, $cacheId)
    {
        if (empty($childWp) || $cacheId != $childWp['cacheid']) {
            $template->error(ERROR_CACHE_NOT_EXISTS);
        }
    }
}
