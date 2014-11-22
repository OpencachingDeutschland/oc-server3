<?

namespace okapi\services\caches\formatters\gpx;

use okapi\Okapi;

echo '<?xml version="1.0" encoding="utf-8"?>'."\n";

?>
<gpx xmlns="http://www.topografix.com/GPX/1/0" version="1.0" creator="OKAPI r<?= $vars['installation']['okapi_revision'] ?>"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="
http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd
http://www.opencaching.com/xmlschemas/opencaching/1/0 http://www.opencaching.com/xmlschemas/opencaching/1/0/opencaching.xsd
http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd
http://geocaching.com.au/geocache/1 http://geocaching.com.au/geocache/1/geocache.xsd
http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd
">
    <name><?= $vars['installation']['site_name'] ?> Geocache Search Results</name>
    <desc><?= $vars['installation']['site_name'] ?> Geocache Search Results, downloaded via OKAPI - <?= $vars['installation']['okapi_base_url'] . ($vars['alt_wpts'] && $vars['ns_gsak'] ? ' (HasChildren)' : '') ?></desc>
    <author><?= $vars['installation']['site_name'] ?></author>
    <url><?= $vars['installation']['site_url'] ?></url>
    <urlname><?= $vars['installation']['site_name'] ?></urlname>
    <time><?= date('c') ?></time>
    <? foreach ($vars['caches'] as &$cache_ref) { ?>
        <?
            if (isset($cache_ref['ggz_entry'])) {
                /* The base parts of the GGZ index are the offset and length of the entry
                 * in the GPX file. This needs to be calculated here. */
                $cache_ref['ggz_entry']['file_pos'] = ob_get_length();
            }
            $c = $cache_ref;
            list($lat, $lon) = explode("|", $c['location']);
        ?>
        <wpt lat="<?= $lat ?>" lon="<?= $lon ?>">
            <time><?= $c['date_created'] ?></time>
            <name><?= $c['code'] ?></name>
            <desc><?= Okapi::xmlescape(isset($c['name_2']) ? $c['name_2'] : $c['name']) ?> <?= _("hidden by") ?> <?= Okapi::xmlescape($c['owner']['username']) ?> :: <?= ucfirst($c['type']) ?> Cache (<?= $c['difficulty'] ?>/<?= $c['terrain'] ?><? if ($c['size'] !== null) { echo "/".$c['size']; } else { echo "/X"; } ?>/<?= $c['rating'] ?>)</desc>
            <url><?= $c['url'] ?></url>
            <urlname><?= Okapi::xmlescape($c['name']) ?></urlname>
            <sym><?= ($vars['mark_found'] && $c['is_found']) ? "Geocache Found" : "Geocache" ?></sym>
            <type>Geocache|<?= $vars['cache_GPX_types'][$c['type']] ?></type>
            <? if ($vars['ns_ground']) { /* Does user want us to include Groundspeak's <cache> element? */ ?>
                <groundspeak:cache archived="<?= ($c['status'] == 'Archived') ? "True" : "False" ?>" available="<?= ($c['status'] == 'Available') ? "True" : "False" ?>" id="<?= $c['internal_id'] ?>" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
                    <groundspeak:name><?= Okapi::xmlescape(isset($c['name_2']) ? $c['name_2'] : $c['name']) ?></groundspeak:name>
                    <groundspeak:placed_by><?= Okapi::xmlescape($c['owner']['username']) ?></groundspeak:placed_by>
                    <groundspeak:owner id="<?= $vars['user_uuid_to_internal_id'][$c['owner']['uuid']] ?>"><?= Okapi::xmlescape($c['owner']['username']) ?></groundspeak:owner>
                    <groundspeak:type><?= $vars['cache_GPX_types'][$c['type']] ?></groundspeak:type>
                    <groundspeak:container><?= $vars['cache_GPX_sizes'][$c['size2']] ?></groundspeak:container>
                    <? if ($vars['gc_attrs'] || $vars['gc_ocde_attrs']) { /* Does user want us to include groundspeak:attributes? */ ?>
                        <groundspeak:attributes>
                            <?
                                foreach ($c['gc_attrs'] as $gc_id => $gc_attr) {
                                    print "<groundspeak:attribute id=\"".$gc_id."\" ";
                                    print "inc=\"".$gc_attr['inc']."\">";
                                    print Okapi::xmlescape($gc_attr['name']);
                                    print "</groundspeak:attribute>";
                                }
                            ?>
                        </groundspeak:attributes>
                    <? } ?>
                    <groundspeak:difficulty><?= $c['difficulty'] ?></groundspeak:difficulty>
                    <groundspeak:terrain><?= $c['terrain'] ?></groundspeak:terrain>
                    <? if ($c['short_description']) { ?>
                        <groundspeak:short_description html="False"><?= Okapi::xmlescape($c['short_description']) ?></groundspeak:short_description>
                    <? } ?>
                    <groundspeak:long_description html="True">
                        <? if (isset($c['warning_prefix'])) { ?>
                            &lt;p style='font-size: 120%'&gt;<?= Okapi::xmlescape($c['warning_prefix']) ?>&lt;/p&gt;
                        <? } ?>
                        &lt;p&gt;
                            &lt;a href="<?= $c['url'] ?>"&gt;<?= Okapi::xmlescape($c['name']) ?>&lt;/a&gt;
                            <?= _("hidden by") ?> &lt;a href='<?= $c['owner']['profile_url'] ?>'&gt;<?= Okapi::xmlescape($c['owner']['username']) ?>&lt;/a&gt;&lt;br/&gt;
                            <? if ($vars['recommendations'] == 'desc:count') { /* Does user want us to include recommendations count? */ ?>
                                <?= sprintf(ngettext("%d recommendation", "%d recommendations", $c['recommendations']), $c['recommendations']) ?>
                                (<?= sprintf(ngettext("found %d time", "found %d times", $c['founds']), $c['founds']) ?>).
                            <? } ?>
                            <? if ($vars['trackables'] == 'desc:count') { /* Does user want us to include trackables count? */ ?>
                                <?= sprintf(ngettext("%d trackable", "%d trackables", $c['trackables_count']), $c['trackables_count']) ?>.
                            <? } ?>
                        &lt;/p&gt;
                        <? if ((in_array('desc:text', $vars['my_notes'])) && ($c['my_notes'] != null)) { /* Does user want us to include personal notes? */ ?>
                            &lt;p&gt;&lt;b&gt;<?= _("Personal notes") ?>:&lt;/b&gt;&lt;br&gt;<?= Okapi::xmlescape(nl2br($c['my_notes'])) ?>&lt;/p&gt;
                        <? } ?>

                        <? if (in_array('desc:text', $vars['attrs']) && count($c['attrnames']) > 0) { /* Does user want us to include attributes? */ ?>
                            &lt;p&gt;<?= _("Attributes") ?>:&lt;/p&gt;
                            &lt;ul&gt;&lt;li&gt;<?= implode("&lt;/li&gt;&lt;li&gt;", $c['attrnames']) ?>&lt;/li&gt;&lt;/ul&gt;
                        <? } ?>
                        <? if ($vars['trackables'] == 'desc:list' && count($c['trackables']) > 0) { /* Does user want us to include trackables list? */ ?>
                            &lt;p&gt;<?= _("Trackables") ?>:&lt;/p&gt;
                            &lt;ul&gt;
                            <? foreach ($c['trackables'] as $t) { ?>
                                &lt;li&gt;&lt;a href='<?= Okapi::xmlescape($t['url']) ?>'&gt;<?= Okapi::xmlescape($t['name']) ?>&lt;/a&gt; (<?= $t['code'] ?>)&lt;/li&gt;
                            <? } ?>
                            &lt;/ul&gt;
                        <? } ?>
                        <?= Okapi::xmlescape($c['description']) ?>
                        <? if ((strpos($vars['images'], "descrefs:") === 0) && count($c['images']) > 0) { /* Does user want us to include <img> references in cache descriptions? */
                            if ($vars['images'] == "descrefs:thumblinks") { ?>
                                &lt;h2&gt;<?= _("Images") ?> (<?= count($c['images']) ?>)&lt;/h2&gt;
                                &lt;div&gt;
                                <? foreach ($c['images'] as $img) { ?>
                                    &lt;div style='float:left; padding:6px'&gt;&lt;a href='<?= Okapi::xmlescape($img['url']) ?>'&gt;&lt;img src='<?= Okapi::xmlescape($img['thumb_url']) ?>'&gt;&lt;/a&gt;&lt;br&gt;
                                    <?= Okapi::xmlescape($img['caption']) ?>&lt;/div&gt;
                                <? } ?>
                                &lt;/div&gt;
                            <? } else {
                                # We will split images into two subcategories: spoilers and nonspoilers.
                                $spoilers = array();
                                $nonspoilers = array();
                                foreach ($c['images'] as $img)
                                    if ($img['is_spoiler']) $spoilers[] = $img;
                                    else $nonspoilers[] = $img;
                                ?>
                                <? if (count($nonspoilers) > 0) { ?>
                                    &lt;h2&gt;<?= _("Images") ?> (<?= count($nonspoilers) ?>)&lt;/h2&gt;
                                    <? foreach ($nonspoilers as $img) { ?>
                                        &lt;p&gt;&lt;img src='<?= Okapi::xmlescape($img['url']) ?>'&gt;&lt;br&gt;
                                        <?= Okapi::xmlescape($img['caption']) ?>&lt;/p&gt;
                                    <? } ?>
                                <? } ?>
                                <? if (count($spoilers) > 0 && $vars['images'] == 'descrefs:all') { ?>
                                    &lt;h2&gt;<?= _("Spoilers") ?> (<?= count($spoilers) ?>)&lt;/h2&gt;
                                    <? foreach ($spoilers as $img) { ?>
                                        &lt;p&gt;&lt;img src='<?= Okapi::xmlescape($img['url']) ?>'&gt;&lt;br&gt;
                                        <?= Okapi::xmlescape($img['caption']) ?>&lt;/p&gt;
                                    <? } ?>
                                <? } ?>
                            <? } ?>
                        <? } ?>
                        <? if ((strpos($vars['images'], "ox:") === 0) && count($c['images']) > 0) { /* Include image descriptions (for ox:image numbers)? */ ?>
                            &lt;p&gt;<?= _("Image descriptions") ?>:&lt;/p&gt;
                            &lt;ul&gt;
                                <? foreach ($c['images'] as $no => $img) { ?>
                                    &lt;li&gt;<?= $img['unique_caption'] ?>. <?= Okapi::xmlescape($img['caption']) ?>&lt;/li&gt;
                                <? } ?>
                            &lt;/ul&gt;
                        <? } ?>
                        <? if ($vars['protection_areas'] == 'desc:text' && count($c['protection_areas'])) { ?>
                            &lt;p&gt;<?= _("The cache probably is located in the following protection areas:") ?>&lt;/p&gt;
                            &lt;ul&gt;
                            <? foreach($c['protection_areas'] as $protection_area) { ?>
                                &lt;li&gt;<?= Okapi::xmlescape($protection_area['type'])." - ".Okapi::xmlescape($protection_area['name']) ?>&lt;/li&gt;
                            <? } ?>
                            &lt;/ul;&gt;
                        <? } ?>
                    </groundspeak:long_description>
                    <groundspeak:encoded_hints><?= Okapi::xmlescape($c['hint2']) ?></groundspeak:encoded_hints>
                    <? if ((in_array('gc:personal_note', $vars['my_notes'])) && ($c['my_notes'] != null)) { /* Does user want us to include personal notes? -> Issue 294 */ ?>
                        <groundspeak:personal_note><?= Okapi::xmlescape($c['my_notes']) ?></groundspeak:personal_note>
                    <? } ?>
                    <? if ($vars['latest_logs']) { /* Does user want us to include latest log entries? */ ?>
                        <groundspeak:logs>
                            <? foreach ($c['latest_logs'] as $log) { ?>
                                <groundspeak:log id="<?= $log['internal_id'] ?>">
                                    <groundspeak:date><?= $log['date'] ?></groundspeak:date>
                                    <groundspeak:type><?= $log['type'] ?></groundspeak:type>
                                    <groundspeak:finder id="<?= $vars['user_uuid_to_internal_id'][$log['user']['uuid']] ?>"><?= Okapi::xmlescape($log['user']['username']) ?></groundspeak:finder>
                                    <groundspeak:text encoded="False"><?= $log['was_recommended'] ? "(*) ": "" ?><?= Okapi::xmlescape($log['comment']) ?></groundspeak:text>
                                </groundspeak:log>
                            <? } ?>
                        </groundspeak:logs>
                    <? } ?>
                </groundspeak:cache>
            <? } ?>
            <? if ($vars['ns_ox']) { /* Does user want us to include Garmin's <opencaching> element? */ ?>
                <ox:opencaching xmlns:ox="http://www.opencaching.com/xmlschemas/opencaching/1/0">
                    <ox:ratings>
                        <? if ($c['rating'] !== null) { ?><ox:awesomeness><?= $c['rating'] ?></ox:awesomeness><? } ?>
                        <ox:difficulty><?= $c['difficulty'] ?></ox:difficulty>
                        <? if ($c['oxsize'] !== null) { ?><ox:size><?= $c['oxsize'] ?></ox:size><? } ?>
                        <ox:terrain><?= $c['terrain'] ?></ox:terrain>
                    </ox:ratings>
                    <? if (in_array('ox:tags', $vars['attrs']) && count($c['attrnames']) > 0) { /* Does user want us to include ox:tags? */ ?>
                        <ox:tags><ox:tag><?= implode("</ox:tag><ox:tag>", $c['attrnames']) ?></ox:tag></ox:tags>
                    <? } ?>
                    <? if ((strpos($vars['images'], "ox:") === 0) && count($c['images']) > 0) { /* Does user want us to include ox:image references? */ ?>
                        <ox:images>
                            <? foreach ($c['images'] as $no => $img) { ?>
                                <ox:image>
                                    <ox:name><?= $img['unique_caption'] ?>.jpg</ox:name>
                                    <ox:size>0</ox:size>
                                    <ox:required>false</ox:required>
                                    <ox:spoiler><?= ($img['is_spoiler'] ? "true" : "false") ?></ox:spoiler>
                                </ox:image>
                            <? } ?>
                        </ox:images>
                    <? } ?>
                </ox:opencaching>
            <? } ?>
        </wpt>
        <?
            if (isset($cache_ref['ggz_entry'])) {
                $cache_ref['ggz_entry']['file_len'] = ob_get_length() - $cache_ref['ggz_entry']['file_pos'];
            }
        ?>
        <? if ($vars['alt_wpts']) { ?>
            <? foreach ($cache_ref['alt_wpts'] as &$wpt_ref) { ?>
                <?
                    if (isset($wpt_ref['ggz_entry'])) {
                        $wpt_ref['ggz_entry']['file_pos'] = ob_get_length();
                    }
                    $wpt = $wpt_ref;
                    list($lat, $lon) = explode("|", $wpt['location']);
                ?>
                <wpt lat="<?= $lat ?>" lon="<?= $lon ?>">
                    <time><?= $c['date_created'] ?></time>
                    <name><?= Okapi::xmlescape($wpt['name']) ?></name>
                    <cmt><?= Okapi::xmlescape($wpt['description']) ?></cmt>
                    <desc><?= Okapi::xmlescape($wpt['type_name']) ?></desc>
                    <url><?= $c['url'] ?></url>
                    <urlname><?= Okapi::xmlescape($c['name']) ?></urlname>
                    <sym><?= $wpt['sym'] ?></sym>
                    <type>Waypoint|<?= $wpt['sym'] ?></type>
                    <? if ($vars['ns_gsak']) { ?>
                        <gsak:wptExtension xmlns:gsak="http://www.gsak.net/xmlv1/5">
                            <gsak:Parent><?= $c['code'] ?></gsak:Parent>
                        </gsak:wptExtension>
                    <? } ?>
                </wpt>
                <?
                    if (isset($wpt_ref['ggz_entry'])){
                        $wpt_ref['ggz_entry']['file_len'] = ob_get_length() - $wpt_ref['ggz_entry']['file_pos'];
                    }
                ?>
            <? } ?>
        <? } ?>
    <? } ?>
</gpx>
