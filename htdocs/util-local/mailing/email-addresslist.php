<?php
	//	For license information see doc/license.txt

  $opt['rootpath'] = '../../';
  header('Content-type: text/html; charset=utf-8');
  require($opt['rootpath'] . 'lib2/web.inc.php');


  $rs = sql("SELECT email FROM user WHERE NOT ISNULL(email) and is_active_flag!=0 ORDER BY user_id DESC");
  while ($r = sql_fetch_assoc($rs))
  {
    echo $r['email'] . "\n";
  }
  sql_free_result($rs);

?>
