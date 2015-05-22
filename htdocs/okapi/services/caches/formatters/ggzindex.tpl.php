<?

namespace okapi\services\caches\formatters\ggz;

use okapi\Okapi;

echo '<?xml version="1.0" encoding="utf-8"?>'."\n";

?>
<ggz xmlns="http://www.opencaching.com/xmlschemas/ggz/1/0">
    <time><?= date('c') ?></time>
    <? foreach ($vars['files'] as $f) { ?>
        <file>
            <name><?= $f['name'] ?></name>
            <crc><?= $f['crc32'] ?></crc>
            <time><?= date('c') ?></time>
            <?
                foreach ($f['caches'] as $c) {
            ?><gch>
                <code><?= $c['code'] ?></code>
                <name><?= Okapi::xmlescape($c['name']) ?></name>
                <type><?= Okapi::xmlescape($c['type']) ?></type>
                <lat><?= $c['lat'] ?></lat>
                <lon><?= $c['lon'] ?></lon>
                <file_pos><?= $c['file_pos'] ?></file_pos>
                <file_len><?= $c['file_len'] ?></file_len>
                <? if (isset($c['ratings'])) {
                ?><ratings>
                    <?
                        foreach ($c['ratings'] as $rating_key => $rating_val){
                            echo "<$rating_key>$rating_val</$rating_key>\n";
                        }
                    ?>
                </ratings><?
                }
                if (isset($c['found']) && $c['found']) { ?>
                    <found>true</found>
                <? } ?>
            </gch>
            <? } ?>
        </file>
    <? } ?>
</ggz>
