<?php
/****************************************************************************
											./lang/de/ocstyle/editlog.tpl.php
															-------------------
		begin                : Mon July 5 2004
		copyright            : (C) 2004 The OpenCaching Group

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 edit a log listing

	 template replacement(s):

			cachename
			logid
			logtypeoptions
			date_message
			logday
			logmonth
			logyear
			logtext
			reset
			submit

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
function insertSmiley(parSmiley) {
  var myText = document.editform.logtext;
  var smileyHtml = '<img src="resource2/tinymce/plugins/emotions/img/smiley-' + parSmiley + '.gif" alt="" border="0" width="18px" height="18px" />';
  myText.focus();

  /* fuer IE */
  if(typeof document.selection != 'undefined') {
    var range = document.selection.createRange();
    var selText = range.text;
    range.text = smileyHtml + selText;
  }
  /* fuer Firefox/Mozilla-Browser */
  else if(typeof myText.selectionStart != 'undefined')
  {
    var start = myText.selectionStart;
    var end = myText.selectionEnd;
    var selText = myText.value.substring(start, end);
    myText.value = myText.value.substr(0, start) + smileyHtml + selText + myText.value.substr(end);
    /* Cursorposition hinter Smiley setzen */
    myText.selectionStart = start + smileyHtml.length;
    myText.selectionEnd = start + smileyHtml.length;
  }
  /* fuer die anderen Browser */
  else
  {
    alert(navigator.appName + ': Setting smilies is not supported');
  }
}

function _chkFound () {
  if(document.editform.rating) {
	  if (document.editform.logtype.value == "1" || document.editform.logtype.value == "7") {
		document.editform.rating.disabled = false;
	  }
	  else
	  {
		document.editform.rating.disabled = true;
	  }
  }
  return false;
}

//-->
</script>

		  <div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-logs.png" style="margin-right: 10px;" width="22" height="22" alt="{t}Register{/t}" />{t}Edit log entry for the cache <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>{/t}</div>

<form action="editlog.php" method="post" enctype="application/x-www-form-urlencoded" name="editform" dir="ltr">
<input type="hidden" name="logid" value="{logid}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px">{t}Type of log-enrty{/t}</td>
		<td align="left">
			<select name="logtype" onChange="return _chkFound()">
				{logtypeoptions}
			</select>
			{teamcommentoption}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td width="180px">{t}Date / time:{/t}</td>
		<td align="left">
			<input class="input20" type="text" name="logday" maxlength="2" value="{logday}"/>.
			<input class="input20" type="text" name="logmonth" maxlength="2" value="{logmonth}"/>.
			<input class="input40" type="text" name="logyear" maxlength="4" value="{logyear}"/>
			&nbsp;&nbsp;&nbsp;
			<input class="input20" type="text" name="loghour" maxlength="2" value="{loghour}" /> :
			<input class="input20" type="text" name="logminute" maxlength="2" value="{logminute}" />
			&nbsp;&nbsp;{date_message}
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" />
			{t}For 'Found' and 'Not found' logs: Date and (optional) time of the cache search.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{rating_message}
</table>
<table class="table">
	<tr>
		<td colspan="2">
			<div class="menuBar">
				<span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">{t}Editor{/t}</span>
				<span class="buttonSplitter">|</span>
				<span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">{t}&lt;html&gt;{/t}</span>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter text only. To use HTML, or the editor, please enable JavaScript.{/t}</span>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="logtext" id="logtext" cols="68" rows="25" class="logs" >{logtext}</textarea>
    </td>
	</tr>
	<tr>
		<td colspan="2">
			{smilies}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

		{log_pw_field}

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			{t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<!-- <input type="reset" name="reset" value="{reset}" class="formbutton" onclick="flashbutton('reset')" />&nbsp;&nbsp; -->
			<input type="submit" name="submitform" value="{submit}" class="formbutton" onclick="submitbutton('submitform')" />
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
<!--
	OcInitEditor();
	_chkFound();
//-->
</script>
