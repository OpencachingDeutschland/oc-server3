<?php

require_once($opt['rootpath'] . 'lib2/logic/const.inc.php');
require_once($opt['rootpath'] . 'lib2/logic/useroptions.class.php');

class translateAccess
{
  private $languages = false;

  public function hasAccess()
  {
    global $login;

    return $login->hasAdminPriv(ADMIN_TRANSLATE);
  }

  public function mayTranslate($language)
  {
    global $login;

    return $login->hasAdminPriv(ADMIN_ROOT) || in_array($language, $this->getLanguages());
  }

  private function getLanguages()
  {
    if ($this->languages === false)
      $this->loadLanguages();

    return $this->languages;
  }

  private function loadLanguages()
  {
    global $login;

    $options = new useroptions($login->userid);

    $this->languages = explode(',', $options->getOptValue(USR_OPT_TRANSLANG));
  }
}

?>
