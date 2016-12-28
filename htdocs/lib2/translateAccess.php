<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 ***************************************************************************/

require_once __DIR__ . '/logic/const.inc.php';

/**
 * Class translateAccess
 */
class translateAccess
{
    private $languages = false;

    /**
     * @return bool
     */
    public function hasAccess()
    {
        global $login;

        if (isset($login)) {
            return $login->hasAdminPriv(ADMIN_TRANSLATE);
        } else {
            return false;
        }
    }

    /**
     * @param $language
     *
     * @return bool
     */
    public function mayTranslate($language)
    {
        global $login;

        if (isset($login)) {
            return $login->hasAdminPriv(ADMIN_ROOT) || in_array($language, $this->getLanguages());
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function getLanguages()
    {
        if ($this->languages === false) {
            $this->loadLanguages();
        }

        return $this->languages;
    }

    /**
     *
     */
    private function loadLanguages()
    {
        global $login;

        $options = new useroptions($login->userid);

        $this->languages = explode(',', $options->getOptValue(USR_OPT_TRANSLANG));
    }
}
