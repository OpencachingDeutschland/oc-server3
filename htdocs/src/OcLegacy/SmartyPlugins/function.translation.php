<?php
/**
 * Smarty {translation key="snippet.key"} function plugin
 */
function smarty_function_translation($params, &$smarty)
{
    global $opt;
    $translation = AppKernel::Container()->get(OcLegacy\Translation\TranslationService::class);
    $translation->setLocale(strtolower($opt['template']['locale']));

    return $translation->trans($params['key']);
}
