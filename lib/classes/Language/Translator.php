<?php

require_once($opt['rootpath'] . 'lib2/translate.class.php');

class Language_Translator
{
  public function substitute($message)
  {
    $translate = createTranslate(1);

    return $translate->v($message);
  }

  public function translate($lang_string)
  {
    $translate = createTranslate(1);

    return $translate->t($lang_string, '', '', '');
  }

  public function translateArgs($lang_string, $args)
  {
    $lang_string = $this->translate($lang_string);

    if (mb_ereg_search_init($lang_string))
    {
      while (false != ($vars = mb_ereg_search_regs("{[^{]*}")))
      {
        foreach ($vars as $curly_pattern)
        {
          $pattern = mb_substr($curly_pattern, 1 , mb_strlen($curly_pattern) - 2);
          $value = $args[$pattern];

          if (!isset($value))
            $value = $pattern . '-missing';

          $lang_string = mb_ereg_replace($curly_pattern, $value, $lang_string);
        }
      }
    }

    return $lang_string;
  }
}

?>
