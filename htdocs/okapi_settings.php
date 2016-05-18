<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace okapi;

function get_okapi_settings()
{
    # Settings for OKAPI. See the following URLs for more info:
    #
    # [1] http://code.google.com/p/opencaching-pl/source/browse/trunk/okapi_settings.php
    # [2] http://code.google.com/p/opencaching-api/source/browse/trunk/okapi/settings.php
    # [3] http://code.google.com/p/opencaching-api/issues/detail?id=132

    $opt['rootpath'] = $GLOBALS['rootpath'];
    require $opt['rootpath'] . 'lib2/const.inc.php';   # (into the *local* scope!)
    require $opt['rootpath'] . 'config2/settings-dist.inc.php';
    require $opt['rootpath'] . 'config2/settings.inc.php';
    
    return array(

        # Settings which ARE NOT present in settings.inc.php:

        'OC_BRANCH' => 'oc.de',  # Tell OKAPI to work in "OCDE mode".

        # Settings which ARE present in settings.inc.php:

        'ADMINS'           => array($opt['db']['warn']['mail'], 'rygielski@mimuw.edu.pl'),
        'FROM_FIELD'       => $opt['mail']['contact'],
        'DATA_LICENSE_URL' => $opt['page']['absolute_url'] . $opt['logic']['license']['terms'],
        'DEBUG'            => ($opt['debug'] & DEBUG_DEVELOPER != 0),
        'DEBUG_PREVENT_SEMAPHORES'
                           => !$opt['php']['semaphores'],  # not available on old developer system
        'DB_SERVER'        => $opt['db']['servername'],
        'DB_NAME'          => $opt['db']['placeholder']['db'],
        'DB_USERNAME'      => $opt['db']['username'],
        'DB_PASSWORD'      => $opt['db']['password'],
        'DB_CHARSET'       => $opt['charset']['mysql'],
        'SITELANG'         => strtolower($opt['template']['default']['locale']),
        'TIMEZONE'         => $opt['php']['timezone'],
        'SITE_URL'         => $opt['page']['absolute_url'],
        'REGISTRATION_URL' => $opt['page']['https']['mode'] != HTTPS_DISABLED
                                  ? 'https://' . $opt['page']['domain'] . '/register.php'
                                  : $opt['page']['absolute_url'] . 'register.php',
        'VAR_DIR'          => $opt['okapi']['var_dir'],
        'IMAGES_DIR'       => rtrim($opt['logic']['pictures']['dir'], '/'),
        'IMAGES_URL'       => rtrim($opt['logic']['pictures']['url'], '/').'/',
        'IMAGE_MAX_UPLOAD_SIZE' => 2 * $opt['logic']['pictures']['maxsize'],
        'IMAGE_MAX_PIXEL_COUNT' => 786432,  # 1024 x 768; TODO: move PICTURE_MAX_LONG_SIDE to settings
        'SITE_LOGO'        => $opt['page']['absolute_url'] . 'resource2/' . $opt['template']['default']['style'] . '/images/oclogo/oc_logo_alpha3.png',
        'OC_NODE_ID'       => $opt['logic']['node']['id'],
        'OC_COOKIE_NAME'   => $opt['session']['cookiename'] . 'data',
        'OCDE_HTML_PURIFIER_SETTINGS'
                           => $opt['html_purifier'],
        'GITHUB_ACCESS_TOKEN' => $opt['okapi']['github_access_token'],
    );
}
