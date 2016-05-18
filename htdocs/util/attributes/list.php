<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$rootpath = __DIR__ . '/../../';
require_once $rootpath . 'lib/common.inc.php';

?>
<html>
<head>
    <script type="text/javascript">
        <!--
        function select(wp) {
            parent.frames['settings'].location.href = '<?php echo $absolute_server_URI; ?>util/attributes/top.php?wp=' + wp;
        }
        //-->
    </script>
</head>
<body>
<form method="get" action="top.php" target="settings">
    <input type="text" name="wp" size="8">
    <input type="submit" value="OK">
</form>
<?php


$rsCaches = sql(
    'SELECT DISTINCT `caches`.`wp_oc`
    FROM `caches`
    INNER JOIN `cache_desc`
        ON `caches`.`cache_id`=`cache_desc`.`cache_id`
    LEFT JOIN `caches_attributes`
        ON `caches`.`cache_id`=`caches_attributes`.`cache_id`
        AND `caches_attributes`.`attrib_id` = 7
    WHERE LENGTH(`cache_desc`.`desc`) < 20
    AND ISNULL(`caches_attributes`.`cache_id`) ORDER BY `caches`.`wp_oc`'
);
while ($rCache = sql_fetch_assoc($rsCaches)) {
    echo '<a href="javascript:select(\'' . $rCache['wp_oc'] . '\')">' . $rCache['wp_oc'] . '<br />';
}
mysql_free_result($rsCaches);
?>
</body>
</html>
