<?php

  $rootpath = '../';
	header('Content-type: text/html; charset=utf-8');
  require('../lib/common.inc.php');

  $rs = mysql_query('SELECT NOW()', $dblink);
  $r = mysql_fetch_array($rs);

  echo $r[0];
?>