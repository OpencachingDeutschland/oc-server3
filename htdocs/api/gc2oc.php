<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  returns GC -> OC waypoint translation table, if available,
 *  or reports a GC waypoint to the team
 *
 *  file format: gcwp,ocwp,checked
 *  checked = 1 if the GC waypoint has been verified,
 *            0 if it is directly taken from an OC cache listing.
 *
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../';
require $opt['rootpath'] . 'lib2/web.inc.php';

if (isset($_REQUEST['report']) && $_REQUEST['report']) {
    header('Content-type: text/plain');

    if ($opt['cron']['gcwp']['report']) {
        if (isset($_REQUEST['ocwp']) && isset($_REQUEST['gcwp']) && isset($_REQUEST['source'])) {
            $ocwp = trim($_REQUEST['ocwp']);
            $gcwp = trim($_REQUEST['gcwp']);
            $source = trim($_REQUEST['source']);

            if (!preg_match("/^OC[0-9A-F]{4,6}$/", $ocwp)) {
                echo "error: invalid ocwp\n";
            } elseif (!sql_value("SELECT 1 FROM `caches` WHERE `wp_oc`='&1'", 0, $ocwp)) {
                echo "error: unknown ocwp\n";
            } elseif (!preg_match("/^GC[0-9A-HJ-NPQRTVWXYZ]{3,7}$/", $gcwp)) {
                echo "error: invalid gcwp\n";
            } else {
                sql(
                    "
                    INSERT INTO `waypoint_reports`
                    (`date_reported`, `wp_oc`, `wp_external`, `source`)
                    VALUES (NOW(), '&1', '&2', '&3')",
                    $ocwp,
                    $gcwp,
                    $source
                );
                echo "ok";
            }
        } else {
            echo "error: missing parameter(s)";
        }
    }
} else {
    header('Content-type: application/x-gzip');
    header('Content-Disposition: attachment; filename=gc2oc.gz');

    /*
     * caches.wp_gc_maintained is intended for map and search filtering and
     * therefore e.g. does not contain WPs of active OC listings that are
     * archived at GC. So it is not useful for GC->OC translation.
     * If a better external source is available, we can use data from there.
     *
     * This may be refined by combining different sources and/or internal data.
     * Also, it may be optimized by allowing to request a single GC code or a
     * list of GC codes.
     *
     * Note that it is not possible to create a 100% reliable translation table.
     * There are many wrong GC wps in listings, and maintained tables always
     * are incomplete. DO NOT RELY ON THE CORRECTNESS OF SUCH DATA!
     */

    if ($opt['cron']['gcwp']['fulllist']) {
        $gzipped_data = '';
        $cachefile = '../cache2/gc2oc.gz';
        if (!file_exists($cachefile) || time() - filemtime($cachefile) > 3600 * 4) {
            $gc2oc = file_get_contents($opt['cron']['gcwp']['fulllist']);
            if ($gc2oc) {
                $gzipped_data = gzencode($gc2oc);
                file_put_contents($cachefile, $gzipped_data);
            }
        }

        if (!$gzipped_data && file_exists($cachefile)) {
            $gzipped_data = file_get_contents($cachefile);
        }

        echo $gzipped_data;
    }
}
