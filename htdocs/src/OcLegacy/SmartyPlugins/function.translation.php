<?php
/**
 * Smarty {translation key="snippet.key"} function plugin *
 *
 * @param array $params
 * @return string
 */
function smarty_function_translation($params)
{
    global $opt;

    if (!isset($params['key'])) {
        return '';
    }

    $translation = AppKernel::Container()->get(OcLegacy\Translation\TranslationService::class);
    $translation->setLocale(strtolower($opt['template']['locale']));

    return $translation->trans($params['key']);
}
