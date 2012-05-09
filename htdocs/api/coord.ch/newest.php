<?php
  $opt['rootpath'] = '../../';
  header('Content-type: text/html; charset=utf-8');
  require($opt['rootpath'] . 'lib2/web.inc.php');

  $bFirstRow = true;

  $rs = sql("SELECT SQL_BUFFER_RESULT `caches`.`wp_oc` , `caches`.`name` , `user`.`user_id` , `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id` = `user`.`user_id` INNER JOIN `cache_status` ON `caches`.`status` = `cache_status`.`id` WHERE `cache_status`.`allow_user_view` =1 ORDER BY `caches`.`date_created` DESC LIMIT 3");
  while ($r = sql_fetch_assoc($rs))
  {
    if ($bFirstRow == true)
    {
      $bFirstCol = true;
      foreach ($r AS $k => $v)
      {
        if ($bFirstCol == false) echo ';';
	echo str_getcsv($k);
        $bFirstCol = false;
      }
      echo "\n";

      $bFirstRow = false;
    }

    $bFirstCol = true;
    foreach ($r AS $k => $v)
    {
      if ($bFirstCol == false) echo ';';
      echo str_getcsv($v);
      $bFirstCol = false;
    }

    echo "\n";
  }
  sql_free_result($rs);

  function str_getcsv($str)
  {
    return '"' . mb_ereg_replace('"', '\"', $str) . '"';
  }
?>
