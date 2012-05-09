<?php
/***************************************************************************
													    ./lib/sqldebugger.inc.php
															--------------------
		begin                : Mon June 27 2006
		copyright            : (C) 2006 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

		Unicode Reminder メモ

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

require_once($opt['rootpath'] . 'lib/bench.inc.php');

$sqldbg_cmdNo = 0;
$sqldbg_sumTimes = 0;

function sqldbg_begin()
{
	header('Content-type: text/html; charset=utf-8');
?>
<html>
  <head>
   <title></title>
   <style type="text/css">
   <!--
    .sqlno
    {
			font-size:medium;
    }
    
    .sqlcommand
    {
    }
    
    .selrows
    {
			margin-bottom:15px;
    }
    
    .firstresultrow
    {
    }
    
    .result
    {
			display:none;
    }
    
    .explain
    {
    }
    
    .runtime
    {
    }
    
    .affectedrows
    {
    }
    
    .allruntime
    {
      font-size:medium;
    }
    
    .comments
    {
		 color:gray;
    }
    
    .white
    {
			color:white;
    }
    
    .error
    {
			color:red;
			font-size:medium;
    }
    
    .errormsg
    {
    }
    
    table
    {
     margin-bottom:15px;
    }
    
    td
    {
			color:gray;
			font-size:x-small;
			white-space:nowrap;
			padding:1px 5px 1px 5px;
    }
    
    th
    {
			color:gray;
    }

		.slave_title
		{
			font-style:italic;
			color:blue;
		}

		.slave_sql
		{
			color:blue;
		}
   -->
   </style>
   <script type="text/javascript">
   <!--
		function switchOpt(id)
		{
      var cssRules = "";
      if (document.all)
        cssRules = "rules";
      else
        cssRules = "cssRules";
        
      var value = document.getElementById(id).checked ? "block" : "none";

      for (var i = 0; i < document.styleSheets.length; i++)
        for (var j = 0; j < document.styleSheets[i][cssRules].length; j++)
          if (document.styleSheets[i][cssRules][j].selectorText == "." + id)
            document.styleSheets[i][cssRules][j].style["display"] = value;
		}
   //-->
   </script>
  </head>
  <body>
		<div class="white">/*</div>
		<table>
			<tr>
				<td><input checked="checked" onclick="switchOpt('sqlno')" id="sqlno" type="checkbox" /><label for="sqlno">Command number</label></td>
				<td><input checked="checked" onclick="switchOpt('sqlcommand')" id="sqlcommand" type="checkbox" /><label for="sqlcommand">Sql command</label></td>
				<td><input checked="checked" onclick="switchOpt('selrows')" id="selrows" type="checkbox" /><label for="selrows">Selected rows</label></td>
				<td><input checked="checked" onclick="switchOpt('firstresultrow')" id="firstresultrow" type="checkbox" /><label for="firstresultrow">First result row</label></td>
			</tr>
			<tr>
				<td><input onclick="switchOpt('result')" id="result" type="checkbox" /><label for="result">Result rows</label></td>
				<td><input checked="checked" onclick="switchOpt('explain')" id="explain" type="checkbox" /><label for="explain">Explain query</label></td>
				<td><input checked="checked" onclick="switchOpt('runtime')" id="runtime" type="checkbox" /><label for="runtime">Runtime</label></td>
				<td><input checked="checked" onclick="switchOpt('affectedrows')" id="affectedrows" type="checkbox" /><label for="affectedrows">Affected rows</label></td>
			</tr>
			<tr>
				<td><input checked="checked" onclick="switchOpt('allruntime')" id="allruntime" type="checkbox" /><label for="allruntime">Runtime sum</label></td>
				<td><input checked="checked" onclick="switchOpt('comments')" id="comments" type="checkbox" /><label for="comments">Comments</label></td>
			</tr>
		</table>
		<div class="white">*/</div>
<?php
}

function sqldbg_execute($sql, $bSlave)
{
	global $dblink;
	global $sqldbg_cmdNo;
	global $sqldbg_sumTimes;

	$sqldbg_cmdNo++;

	echo '<p class="sqlno"><span class="white">/*</span> SQL command ' . $sqldbg_cmdNo . ' ';
	if ($bSlave)
		echo '<span class="slave_title">(slave)</span>';
	echo '<span class="white">*/</span>';
	echo '</p>';
	echo '<p class="sqlcommand">';
	
	if ($bSlave)
		echo '<span class="slave_sql">';
	
	echo htmlspecialchars($sql, ENT_COMPAT, 'UTF-8');
	
	if ($bSlave)
		echo '</span>';

	echo ' ;</p>';

	echo '<div class="comments"><div class="white">/*</div><br>';
	
	// Explains
	$bUseExplain = true;
	$sqlexplain = $sql;
	$usebr = false;

	if (mb_strtoupper(mb_substr($sqlexplain, 0, 6)) == 'ALTER ')
		$bUseExplain = false;
	else if (mb_strtoupper(mb_substr($sqlexplain, 0, 7)) == 'DELETE ')
	{
		$sqlexplain = sqldbg_strip_from($sqlexplain);
	}
	else if ((mb_strtoupper(mb_substr($sqlexplain, 0, 12)) == 'INSERT INTO ') || 
			      (mb_strtoupper(mb_substr($sqlexplain, 0, 19)) == 'INSERT IGNORE INTO '))
	{
		$sqlexplain = sqldbg_strip_temptable($sqlexplain);
		if ($sqlexplain == '')
			$bUseExplain = false;
	}
	else if (mb_strtoupper(mb_substr($sqlexplain, 0, 7)) == 'INSERT ')
		$bUseExplain = false;
	else if (mb_strtoupper(mb_substr($sqlexplain, 0, 7)) == 'UPDATE ')
		$bUseExplain = false;
	else if (mb_strtoupper(mb_substr($sqlexplain, 0, 11)) == 'DROP TABLE ')
		$bUseExplain = false;
	else if (mb_strtoupper(mb_substr($sqlexplain, 0, 23)) == 'CREATE TEMPORARY TABLE ')
	{
		$sqlexplain = sqldbg_strip_temptable($sqlexplain);
		if ($sqlexplain == '')
			$bUseExplain = false;
	}
	
	if ($bUseExplain == true)
	{
		$bFirstLine = true;
		$nLine = 0;
		$rs = mysql_query($sqlexplain, $dblink);
		echo '<div class="selrows">Number of selected rows: ' . mysql_num_rows($rs) . '</div>';

		echo '<table class="firstresultrow" border="1">';

		while ($r = sql_fetch_assoc($rs))
		{
			$usebr = true;
			$nLine++;
			if ($bFirstLine == true)
			{
				echo '<tr>' . "\n";
				foreach ($r AS $field => $value)
				{
					echo '<th>' . htmlspecialchars($field, ENT_COMPAT, 'UTF-8') . '</th>' . "\n";
				}
				echo '</tr>' . "\n";
			}

			if ($bFirstLine)
				echo '<tr>';
			else
				echo '<tr class="result">';

			foreach ($r AS $value)
			{
				echo '<td>' . htmlspecialchars(($value != null) ? $value : 'NULL', ENT_COMPAT, 'UTF-8') . '</td>';
			}
			echo '</tr>' . "\n";
			
			if ($nLine == 25) break;
			$bFirstLine = false;
		}
		echo '</table>';
		mysql_free_result($rs);

		echo '<table class="explain" border="1">';

		$bFirstLine = true;
		$rs = mysql_query('EXPLAIN EXTENDED ' . $sqlexplain);
		while ($r = sql_fetch_assoc($rs))
		{
			if ($bFirstLine == true)
			{
				echo '<tr>';
				foreach ($r AS $field => $value)
				{
					echo '<th>' . htmlspecialchars($field, ENT_COMPAT, 'UTF-8') . '</th>';
				}
				echo '</tr>' . "\n";
				
				$bFirstLine = false;
			}
			
			echo '<tr>';
			foreach ($r AS $value)
			{
				echo '<td>' . htmlspecialchars(($value != null) ? mb_ereg_replace('\*/', '* /', $value) : 'NULL', ENT_COMPAT, 'UTF-8') . '</td>';
			}
			echo '</tr>' . "\n";
		}
		echo '</table>';
		$usebr = true;
	}

	// dont use query cache!
	$sql = sqldbg_insert_nocache($sql);

	$bSqlExecution = new Cbench; 
	$bSqlExecution->start();
	$rsResult = mysql_query($sql, $dblink);
	$bError = ($rsResult == false);
	$bSqlExecution->stop();
	$sqldbg_sumTimes += $bSqlExecution->Diff();

	if ($bError == true)
	{
		echo '<div class="error">Error while executing SQL command!</div>';
		echo '<div class="errormsg">';
		echo '<table>';
		$rs = mysql_query('SHOW WARNINGS', $dblink);
		while ($r = sql_fetch_assoc($rs))
			echo '<tr><td>' . htmlspecialchars($r['Message'], ENT_COMPAT, 'UTF-8') . '</td></tr>';
		echo '</table>';
		echo '</div>';
	}

	echo '<div class="runtime">Runtime: ' . sprintf('%01.5f', $bSqlExecution->Diff()) . ' sek.</div>';
	echo '<div class="affectedrows">Number of affected rows: ' . mysql_affected_rows($dblink) . '</div>';

	echo '<div class="white">*/</div></div>';
	
	return $rsResult;
}

function sqldbg_end()
{
	global $sqldbg_sumTimes;
	
	echo '<span class="white">/*</span><div class="allruntime"><hr>';
	echo 'Runtime sum: ' . sprintf('%01.5f', $sqldbg_sumTimes) . ' sek.<span class="white">*/</span></div>';

	echo '</body></html>';
	exit;
}

function sqldbg_strip_temptable($sql)
{
	$start = mb_strpos(mb_strtoupper($sql), 'SELECT ');
	
	if ($start === false)
		return '';
	
	return mb_substr($sql, $start);
}

function sqldbg_strip_from($sql)
{
	$start = mb_strpos(mb_strtoupper($sql), 'FROM ');
	
	if ($start === false)
		return '';
	
	return 'SELECT * ' . mb_substr($sql, $start);
}

function sqldbg_insert_nocache($sql)
{
	if (mb_strtoupper(mb_substr($sql, 0, 7)) == 'SELECT ')
		$sql = 'SELECT SQL_NO_CACHE ' . mb_substr($sql, 7);
	
	return $sql;
}
?>