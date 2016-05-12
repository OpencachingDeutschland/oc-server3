<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\ChildWp;

class AddPresenterChildWp extends PresenterChildWp
{
    public function __construct($request = false, $translator = false)
    {
        parent::__construct($request, $translator);
    }

    protected function getTitle()
    {
        global $translate;

        return $translate->t('Add waypoint', '', '', 0);
    }

    protected function getSubmitButton()
    {
        global $translate;

        return $translate->t('Add new', '', '', 0);
    }

    protected function onDoSubmit($coordinate, $description)
    {
        $this->childWpHandler->add(
            $this->cacheId,
            $this->getType(),
            $coordinate->latitude(),
            $coordinate->longitude(),
            $description
        );
    }
}
