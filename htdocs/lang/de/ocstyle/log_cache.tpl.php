<?php
/***************************************************************************
											./lang/de/ocstyle/log_cache.tpl.php
															-------------------
		begin                : July 4 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 log a cache visit

	 template replacements:

		cacheid
		logtypeoptions
		logdate
		logtext
		reset
		submit

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
function insertSmiley(parSmiley) {
  var myText = document.logform.logtext;
  myText.focus();
  /* fuer IE */
  if(typeof document.selection != 'undefined') {
    var range = document.selection.createRange();
    var selText = range.text;
    range.text = parSmiley + selText;
  }
  /* fuer Firefox/Mozilla-Browser */
  else if(typeof myText.selectionStart != 'undefined')
  {
    var start = myText.selectionStart;
    var end = myText.selectionEnd;
    var selText = myText.value.substring(start, end);
    myText.value = myText.value.substr(0, start) + parSmiley + selText + myText.value.substr(end);
    /* Cursorposition hinter Smiley setzen */
    myText.selectionStart = start + parSmiley.length;
    myText.selectionEnd = start + parSmiley.length;
  }
  /* fuer die anderen Browser */
  else
  {
    alert(navigator.appName + ': {t}Setting smilies is not supported{/t}');
  }
}

function _chkFound () {
  if (document.logform.logtype.value == "1" || document.logform.logtype.value == "7")
	{
		if (document.logform.rating)
	    document.logform.rating.disabled = false;
  }
  else
  {
		if (document.logform.rating)
	    document.logform.rating.disabled = true;
  }
  return false;
}

//-->
</script>

		  <div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-logs.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}New log-entry{/t}" />{t}Add log-entry for the cache <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>{/t}</div>

<form action="log.php" method="post" enctype="application/x-www-form-urlencoded" name="logform" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" name="version3" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="table">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td width="180px">{t}Type of log-entry:{/t}</td>
		<td>
			<select name="logtype" onChange="return _chkFound()">
				{logtypeoptions}
			</select>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px">{t}Date:{/t}</td>
		<td>
			<input class="input20" type="text" name="logday" maxlength="2" value="{logday}"/>.
			<input class="input20" type="text" name="logmonth" maxlength="2" value="{logmonth}"/>.
			<input class="input40" type="text" name="logyear" maxlength="4" value="{logyear}"/>
			{date_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{rating_message}
</table>

<table class="table">
	<tr>
		<td colspan="2">{t}Log-entry:{/t}</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="menuBar">
				<span id="descText" class="buttonNormal" onclick="btnSelect(1)" onmouseover="btnMouseOver(1)" onmouseout="btnMouseOut(1)">{t}Text{/t}</span>
				<span class="buttonSplitter">|</span>
				<span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">{t}&lt;html&gt;{/t}</span>
				<span class="buttonSplitter">|</span>
				<span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">{t}Editor{/t}</span>
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
			<textarea name="logtext" id="logtext" cols="68" rows="25" >{logtext}</textarea>
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
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="{reset}" style="width:120px"/>&nbsp;&nbsp;
			<input type="submit" name="submitform" value="{submit}" style="width:120px"/>
		</td>
	</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
<!--
	/*
		1 = Text
		2 = HTML
		3 = HTML-Editor
	*/
	var use_tinymce = 0;
	var descMode = {descMode};
	document.getElementById("scriptwarning").firstChild.nodeValue = "";

	// set descMode to 1 or 2 ... when editor is loaded set to 3
	if (descMode == 3)
	{
		if (document.getElementById("logtext").value == '')
			descMode = 1;
		else
			descMode = 2;
	}

	document.getElementById("descMode").value = descMode;
	mnuSetElementsNormal();

	function postInit()
	{
		descMode = 3;
		use_tinymce = 1;
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function SwitchToTextDesc()
	{
		document.getElementById("descMode").value = 1;

		if (use_tinymce == 1)
			document.logform.submit();
	}

	function SwitchToHtmlDesc()
	{
		document.getElementById("descMode").value = 2;

		if (use_tinymce == 1)
			document.logform.submit();
	}

	function SwitchToHtmlEditDesc()
	{
		document.getElementById("descMode").value = 3;

		if (use_tinymce == 0)
			document.logform.submit();
	}

	function mnuSelectElement(e)
	{
		e.backgroundColor = '#D4D5D8';
		e.borderColor = '#6779AA';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuNormalElement(e)
	{
		e.backgroundColor = '#F0F0EE';
		e.borderColor = '#F0F0EE';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuHoverElement(e)
	{
		e.backgroundColor = '#B6BDD2';
		e.borderColor = '#0A246A';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuUnhoverElement(e)
	{
		mnuSetElementsNormal();
	}

	function mnuSetElementsNormal()
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (descMode)
		{
			case 1:
				mnuSelectElement(descText);
				mnuNormalElement(descHtml);
				mnuNormalElement(descHtmlEdit);

				break;
			case 2:
				mnuNormalElement(descText);
				mnuSelectElement(descHtml);
				mnuNormalElement(descHtmlEdit);

				break;
			case 3:
				mnuNormalElement(descText);
				mnuNormalElement(descHtml);
				mnuSelectElement(descHtmlEdit);

				break;
		}
	}

	function btnSelect(mode)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		var oldMode = descMode;
		descMode = mode;
		mnuSetElementsNormal();

		if ((oldMode == 1) && (descMode != 1))
		{
			// convert text to HTML
			var desc = document.getElementById("logtext").value;

			if ((desc.indexOf('&amp;') == -1) &&
			    (desc.indexOf('&quot;') == -1) &&
			    (desc.indexOf('&lt;') == -1) &&
			    (desc.indexOf('&gt;') == -1) &&
			    (desc.indexOf('<p>') == -1) &&
			    (desc.indexOf('<i>') == -1) &&
			    (desc.indexOf('<strong>') == -1) &&
			    (desc.indexOf('<br />') == -1))
			{
				desc = desc.replace(/&/g, "&amp;");
				desc = desc.replace(/"/g, "&quot;");
				desc = desc.replace(/</g, "&lt;");
				desc = desc.replace(/>/g, "&gt;");
				desc = desc.replace(/\r\n/g, "\<br />");
				desc = desc.replace(/\n/g, "<br />");
				desc = desc.replace(/<br \/>/g, "<br />\n");
			}

			document.getElementById("logtext").value = desc;
		}

		switch (mode)
		{
			case 1:
				SwitchToTextDesc();
				break;
			case 2:
				SwitchToHtmlDesc();
				break;
			case 3:
				SwitchToHtmlEditDesc();
				break;
		}
	}

	function btnMouseOver(id)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuHoverElement(descText);
				break;
			case 2:
				mnuHoverElement(descHtml);
				break;
			case 3:
				mnuHoverElement(descHtmlEdit);
				break;
		}
	}

	function btnMouseOut(id)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuUnhoverElement(descText);
				break;
			case 2:
				mnuUnhoverElement(descHtml);
				break;
			case 3:
				mnuUnhoverElement(descHtmlEdit);
				break;
		}
	}
//-->
</script>