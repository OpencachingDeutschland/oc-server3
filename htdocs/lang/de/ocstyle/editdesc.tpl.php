<?php
/***************************************************************************
											./lang/de/ocstyle/editdesc.tpl.php
															-------------------
		begin                : July 7 2004
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
                                   				                                
	 edit a cache listing
	
	 template replacement(s):
			
			desclang
			desclang_name
			cachename
			reset
			submit
			short_desc
			desc_err
			desc
			hints
			
 ****************************************************************************/
?>


		  <div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-description.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}New cache{/t}" />{t}Edit cache description for <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>{/t}</div>

<form name="descform" action="editdesc.php" method="post" enctype="application/x-www-form-urlencoded" id="editcache_form" dir="ltr">
<input type="hidden" name="post" value="1"/>
<input type="hidden" name="descid" value="{descid}"/>
<input type="hidden" name="show_all_langs_value" value="{show_all_langs_value}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="table">
	<tr>
		<td>{t}Language:{/t}</td>
		<td>
			<select name="desclang">
				{desclangs}
			</select>{show_all_langs_submit}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td>{t}Short description:{/t}</td>
		<td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400" /></td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td colspan="2">{t}Description:{/t}{desc_err}</td>
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
		<td colspan="2">
			<span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter text only. To use HTML, or the editor, please enable JavaScript.{/t}</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea id="desc" name="desc" cols="80" rows="25">{desc}</textarea>
    </td>	
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="help" colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}"> 
			{t}Your HTML code will be changed again by a special filter. This is nacessary to avoid dangerous HTML-tags, such as &lt;script&gt;.
				 A list of allowed HTML tags can be find <a href="http://www.opencaching.de/articles.php?page=htmltags">here</a>.{/t}<br />
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}">
			{t}Please do not use any images that are hosted on geocaching.com. Upload your fotos instead on our server as well.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{t}Encrypted note:{/t}</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="hints" class="mceNoEditor" cols="80" rows="15">{hints}</textarea>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
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
	
	// set descMode to 1 or 2 ... when editor is loaded, set back to 3
	if (descMode == 3)
	{
		if (document.getElementById("desc").value == '')
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
			document.descform.submit();
	}

	function SwitchToHtmlDesc()
	{
		document.getElementById("descMode").value = 2;

		if (use_tinymce == 1)
			document.descform.submit();
	}

	function SwitchToHtmlEditDesc()
	{
		document.getElementById("descMode").value = 3;

		if (use_tinymce == 0)
			document.descform.submit();
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
			var desc = document.getElementById("desc").value;

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

			document.getElementById("desc").value = desc;
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