<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\ChildWp;

class DeletePresenterChildWp extends PresenterChildWp
{
    public function __construct($request = false, $translator = false)
    {
        parent::__construct($request, $translator);
    }

    protected function getTitle()
    {
        global $translate;

        return $translate->t('Delete waypoint', '', '', 0);
    }

    protected function getSubmitButton()
    {
        global $translate;

        return $translate->t('Delete', '', '', 0);
    }

    protected function onDoSubmit($coordinate, $description)
    {
        $this->childWpHandler->delete($this->childId);
    }

    protected function onPrepare($template)
    {
        $template->assign(parent::TPL_DISABLED, true);
        $template->assign(parent::TPL_DELETE_ID, $this->childId);
    }

    public function validate()
    {
        return true;
    }
}
