<?php

namespace okapi;

class Locales
{
    public static $languages = array(
        'pl' => array('lang' => 'pl', 'locale' => 'pl_PL.utf8', 'name' => 'Polish'),
        'en' => array('lang' => 'en', 'locale' => 'en_US.utf8', 'name' => 'English'),
        'nl' => array('lang' => 'nl', 'locale' => 'nl_NL.utf8', 'name' => 'Dutch'),
        'de' => array('lang' => 'de', 'locale' => 'de_DE.utf8', 'name' => 'German'),
        'it' => array('lang' => 'it', 'locale' => 'it_IT.utf8', 'name' => 'Italian'),
    );

    /**
     * Get the list of locales that should be installed on the system in order
     * for all translations to work properly.
     */
    public static function get_required_locales()
    {
        $arr = array('POSIX');
        foreach (self::$languages as $key => $value)
            $arr[] = $value['locale'];
        return $arr;
    }

    /**
     * Get the list of locales installed on the current system.
     */
    public static function get_installed_locales()
    {
        $arr = array();
        foreach (explode("\n", shell_exec("locale -a")) as $item)
            if ($item)
                $arr[] = $item;
        return $arr;
    }

    private static function get_locale_for_language($lang)
    {
        if (isset(self::$languages[$lang]))
            return self::$languages[$lang]['locale'];
        return null;
    }

    public static function get_best_locale($langprefs)
    {
        foreach ($langprefs as $lang)
        {
            $locale = self::get_locale_for_language($lang);
            if ($locale != null)
                return $locale;
        }
        return self::$languages['en']['locale'];
    }
}
