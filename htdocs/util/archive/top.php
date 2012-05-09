<?php

	// Unicode Reminder メモ

	$rootpath = '../../';
	header('Content-type: text/html; charset=utf-8');
	require_once($rootpath . 'lib/common.inc.php');

  $wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';

  if (isset($_REQUEST['post']))
	{
		sql("UPDATE `caches` SET `status`=3, `last_modified`=NOW() WHERE `wp_oc`='&1'", $wp);
	}
?>
<html>
	<head>
		<script type="text/javascript">
		<!--
		function load(wp)
		{
			parent.frames['viewcache'].location.href = '<?php echo $absolute_server_URI; ?>viewcache.php?popup=y&wp=' + wp;
		}
		//-->
		</script>
	</head>
	<body onload="load('<?php echo $wp; ?>')">
    <a href="top.php?post=1&wp=<?php echo $wp; ?>">Archivieren</a>
	</body>
</html>