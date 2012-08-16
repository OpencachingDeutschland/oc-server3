<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$rootpath = '../../';
	header('Content-type: text/html; charset=utf-8');
	require_once($rootpath . 'lib/common.inc.php');
	
  $sUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';

?>
<html>
	<head>
		<script type="text/javascript">
			<!--
				function select(wp)
				{
					parent.frames['settings'].location.href = '<?php echo $absolute_server_URI; ?>util/archive/top.php?wp=' + wp;
				}
			//-->
		</script>
	</head>
	<body>
    <form>
      <input type="text" name="user" size="10" value="<?php echo htmlspecialchars($sUser); ?>" />
      <input type="submit" value="OK" />
    </form>
<?php
	
	$rsCaches = sql("SELECT DISTINCT `caches`.`wp_oc` FROM `caches` INNER JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id` INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id` WHERE `caches`.`status` IN (1,2) AND `cache_logs`.`type`=3 AND `user`.`username`='&1' ORDER BY `cache_logs`.`date` DESC, `caches`.`wp_oc`", $sUser);
	while ($rCache = sql_fetch_assoc($rsCaches))
	{
		echo '<a href="javascript:select(\'' . $rCache['wp_oc'] . '\')">' . $rCache['wp_oc'] . '<br />';
	}
	mysql_free_result($rsCaches);
?>
	</body>
</html>
