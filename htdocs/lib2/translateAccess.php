<?php
/***************************************************************************
 * for license information see LICENSE.md
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
        }

        return false;
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
        }

        return false;
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
