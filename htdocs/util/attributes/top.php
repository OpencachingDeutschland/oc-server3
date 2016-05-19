<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$rootpath = __DIR__ . '/../../';
require_once $rootpath . 'lib/common.inc.php';

$wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';

if (isset($_REQUEST['post'])) {
    $attrs_set = isset($_REQUEST['cache_attribs']) ? $_REQUEST['cache_attribs'] : '';
    $attrs = mb_split(';', $attrs_set);

    $cache_id = sqlValue("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='" . sql_escape($wp) . "'", 0);
    if ($cache_id == 0) {
        die('cache_id unknown');
    }

    sql("DELETE FROM `caches_attributes` WHERE `cache_id`='&1'", $cache_id);
    foreach ($attrs as $attr) {
        sql("INSERT INTO `caches_attributes` (`cache_id`, `attrib_id`) VALUES ('&1', '&2')", $cache_id, $attr);
    }
}

$attrs = array();
$rsAttribs = sql(
    "SELECT `caches_attributes`.`attrib_id`
    FROM `caches_attributes`, `caches`
    WHERE `caches_attributes`.`cache_id`=`caches`.`cache_id`
    AND `caches`.`wp_oc`='&1'",
    $wp
);
while ($rAttribs = sql_fetch_assoc($rsAttribs)) {
    $attrs[$rAttribs['attrib_id']] = $rAttribs['attrib_id'];
}
mysql_free_result($rsAttribs);

$attr_js = '';
$rs = sql("SELECT `id`, `icon_large`, `icon_undef` FROM `cache_attrib`");
while ($r = sql_fetch_assoc($rs)) {
    if ($attr_js != '') {
        $attr_js .= ',';
    }
    $attr_js .= "new Array(" . $r['id'] . ", " . (isset($attrs[$r['id']]) ? 1 : 0) . ", '../../" . $r['icon_undef'] . "', '../../" . $r['icon_large'] . "')";
}
mysql_free_result($rs);
?>
<html>
<head>
    <script type="text/javascript">
        <!--
        var maAttributes = new Array(<?php echo $attr_js; ?>);

        function rebuildCacheAttr() {
            var i = 0;
            var sAttr = '';
            for (i = 0; i < maAttributes.length; i++) {
                if (maAttributes[i][1] == 1) {
                    if (sAttr != '') sAttr += ';';
                    sAttr = sAttr + maAttributes[i][0];

                    document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][3];
                }
                else
                    document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][2];

                document.getElementById('cache_attribs').value = sAttr;
            }
        }

        function toggleAttr(id) {
            var i = 0;
            for (i = 0; i < maAttributes.length; i++) {
                if (maAttributes[i][0] == id) {
                    if (maAttributes[i][1] == 0)
                        maAttributes[i][1] = 1;
                    else
                        maAttributes[i][1] = 0;

                    rebuildCacheAttr();
                    break;
                }
            }
        }

        function load(wp) {
            parent.frames['viewcache'].location.href = '<?php echo $absolute_server_URI; ?>viewcache.php?popup=y&wp=' + wp;
        }
        //-->
    </script>
</head>
<body onload="load('<?php echo $wp; ?>')">
<form method="post" name="attr">
    <?php
    $rs = sql("SELECT `id`, `icon_large`, `icon_undef` FROM `cache_attrib`");
    while ($r = sql_fetch_assoc($rs)) {
        echo '<img id="attr' . $r['id'] . '" onmousedown="toggleAttr(' . $r['id'] . ')" src="../../';

        if (isset($attrs[$r['id']])) {
            echo $r['icon_large'];
        } else {
            echo $r['icon_undef'];
        }

        echo '" />&nbsp;';
    }
    mysql_free_result($rs);
    ?>
    <input type="hidden" name="wp" value="<?php echo $wp; ?>"/>
    <input type="hidden" id="cache_attribs" name="cache_attribs" value=""/>
    <input type="submit" name="post" value="OK"/>
</form>
</body>
</html>
