<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace okapi;

function get_okapi_settings()
{
    # Settings for OKAPI. See the following URLs for more info:
    #
    # [1] http://code.google.com/p/opencaching-pl/source/browse/trunk/okapi_settings.php
    # [2] http://code.google.com/p/opencaching-api/source/browse/trunk/okapi/settings.php
    # [3] http://code.google.com/p/opencaching-api/issues/detail?id=132
    date_default_timezone_set('Europe/Berlin');
    $opt['rootpath'] = isset($GLOBALS['rootpath']) ? $GLOBALS['rootpath'] : __DIR__ . '/';
    require __DIR__ . '/lib2/const.inc.php'; # (into the *local* scope!)
    require __DIR__ . '/config2/settings-dist.inc.php';
    require __DIR__ . '/config2/settings.inc.php';

    return [

        # Settings which ARE NOT present in settings.inc.php:

        'OC_BRANCH' => 'oc.de', # Tell OKAPI to work in "OCDE mode".

        # Settings which ARE present in settings.inc.php:

        'ADMINS'           => [$opt['db']['warn']['mail'], 'rygielski@mimuw.edu.pl'],
        'FROM_FIELD'       => $opt['mail']['contact'],
        'DATA_LICENSE_URL' => $opt['page']['absolute_url'] . $opt['logic']['license']['terms'],
        'DEBUG'            => ($opt['debug'] & DEBUG_DEVELOPER != 0),
        'DEBUG_PREVENT_SEMAPHORES' => !$opt['php']['semaphores'], # not available on old developer system
        'DB_SERVER'        => $opt['db']['servername'],
        'DB_NAME'          => $opt['db']['placeholder']['db'],
        'DB_USERNAME'      => $opt['db']['username'],
        'DB_PASSWORD'      => $opt['db']['password'],
        'DB_CHARSET'       => $opt['charset']['mysql'],
        'SITELANG'         => strtolower($opt['template']['default']['locale']),
        'TIMEZONE'         => $opt['php']['timezone'],
        'SITE_URL'         => $opt['page']['default_primary_url'],
        'REGISTRATION_URL' => $opt['page']['https']['mode'] != HTTPS_DISABLED
                                  ? 'https://' . $opt['page']['domain'] . '/register.php'
                                  : $opt['page']['absolute_url'] . 'register.php',
        'VAR_DIR'          => $opt['okapi']['var_dir'],
        'IMAGES_DIR'       => rtrim($opt['logic']['pictures']['dir'], '/'),
        'IMAGES_URL'       => rtrim($opt['logic']['pictures']['url'], '/') . '/',
        'IMAGE_MAX_UPLOAD_SIZE' => $opt['logic']['pictures']['maxsize'],
        'IMAGE_MAX_PIXEL_COUNT' => 786432, # 1024 x 768; TODO: move PICTURE_MAX_LONG_SIDE to settings
        'SITE_LOGO'        => $opt['page']['absolute_url'] . 'resource2/' . $opt['template']['default']['style'] . '/images/oclogo/oc_logo_alpha3.png',
        'OC_NODE_ID'       => $opt['logic']['node']['id'],
        'OC_COOKIE_NAME'   => $opt['session']['cookiename'] . 'data',
        'VERSION_FILE'     => __DIR__ . '/okapi/meta.php',
        'OCDE_HTML_PURIFIER_SETTINGS'
                           => $opt['html_purifier'],
        'GITHUB_ACCESS_TOKEN' => $opt['okapi']['github_access_token'],
    ];
}
