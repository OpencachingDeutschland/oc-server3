<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once __DIR__ . '/logic/const.inc.php';

class translateAccess
{
    private $languages = false;

    public function hasAccess()
    {
        global $login;

        if (isset($login)) {
            return $login->hasAdminPriv(ADMIN_TRANSLATE);
        } else {
            return false;
        }
    }

    public function mayTranslate($language)
    {
        global $login;

        if (isset($login)) {
            return $login->hasAdminPriv(ADMIN_ROOT) || in_array($language, $this->getLanguages());
        } else {
            return false;
        }
    }

    private function getLanguages()
    {
        if ($this->languages === false) {
            $this->loadLanguages();
        }

        return $this->languages;
    }

    private function loadLanguages()
    {
        global $login;

        $options = new useroptions($login->userid);

        $this->languages = explode(',', $options->getOptValue(USR_OPT_TRANSLANG));
    }
}
