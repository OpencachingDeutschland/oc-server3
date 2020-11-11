<?php
/***************************************************************************
 * for license information see LICENSE.md
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

    protected function onDoSubmit($coordinate, $description): void
    {
        $this->childWpHandler->delete($this->childId);
    }

    protected function onPrepare($template): void
    {
        $template->assign(parent::tpl_disabled, true);
        $template->assign(parent::tpl_delete_id, $this->childId);
    }

    public function validate()
    {
        return true;
    }
}
