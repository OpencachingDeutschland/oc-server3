<?php
/****************************************************************************
											./lang/de/ocstyle/newcache.tpl.php
															-------------------
		begin                : June 24 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 submit a new cache

	 replacements:

	     name
	     typeoptions
	     sizeoptions
	     show_all_countries_submit
	     show_all_langs_submit
	     latNsel
	     latSsel
	     lat_h
	     lat_min
	     lonEsel
	     lonWsel
	     lon_h
	     lon_min
	     countryoptions
	     langoptions
	     short_desc
	     desc
	     desc_html
	     desc_message
	     hints
	     hidden_since
	     toschecked
	     reset
	     submit_value
	     hidden_since_message
	     tos_message
	     show_all_countries
	     show_all_langs
	     difficulty_options
	     terrain_options
             effort_message
             search_time
             way_length
             type_message
             size_message
             diff_message

 ****************************************************************************/
?>
<script type="text/javascript" src="resource2/ocstyle/js/wz_tooltip.js"></script>
<script type="text/javascript">
<!--
var maAttributes = new Array({jsattributes_array});

function _chkVirtual () {
  if (document.newcacheform.type.value == "4" || document.newcacheform.type.value == "5") {
    document.newcacheform.size.value = "7";
    document.newcacheform.size.disabled = true;
  }
  else
  {
    document.newcacheform.size.disabled = false;
  }
  return false;
}

function rebuildCacheAttr()
{
	var i = 0;
	var sAttr = '';
	
	for (i = 0; i < maAttributes.length; i++)
	{
		if (maAttributes[i][1] == 1)
		{
			if (sAttr != '') sAttr += ';';
			sAttr = sAttr + maAttributes[i][0];

			document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][3];
		}
		else
			document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][2];

		document.getElementById('cache_attribs').value = sAttr;
		
	}
}

function toggleAttr(id)
{
	var i = 0;
	
	for (i = 0; i < maAttributes.length; i++)
	{
		if (maAttributes[i][0] == id)
		{
			if (maAttributes[i][1] == 0)
				maAttributes[i][1] = 1;
			else
				maAttributes[i][1] = 0;

			rebuildCacheAttr();
			break;
		}
	}
}
//-->
</script>

<div class="content2-pagetitle">
	<img src="resource2/ocstyle/images/cacheicon/traditional.gif" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}New cache{/t}" />
	{t}Submit a new cache{/t}
</div>

<form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr">
<input type="hidden" name="show_all_countries" value="{show_all_countries}"/>
<input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
<input type="hidden" name="version2" value="1"/>
<input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />
<input id="descMode" type="hidden" name="descMode" value="1" />
<table class="table">
	{general_message}
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="help" colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
			{t}If this is your first cache on Opencaching.de, be sure to check out the <a href="articles.php?page=cacheinfo">description</a> of the individual fields.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{t}Name:{/t}</td>
		<td><input type="text" name="name" value="{name}" maxlength="60" class="input400" />{name_message}</td>
	</tr>
	<tr><td class="spacer" colspan="2"><img src="images/trans.gif" border="0" width="1" height="1" alt="" title="" /></td></tr>
	<tr>
		<td>{t}Cachetype:{/t}</td>
		<td>
			<select name="type" class="input200" onChange="return _chkVirtual()">
				{typeoptions}
			</select>{type_message}
		</td>
	</tr>
	<tr>
		<td>{t}Size:{/t}</td>
		<td>
			<select name="size" class="input200" onChange="return _chkVirtual()">
				{sizeoptions}
			</select>{size_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td valign="top">{t}Coordinates:{/t}</td>
		<td>
			<select name="latNS" class="input40">
				<option value="N"{latNsel}>{t}N{/t}</option>
				<option value="S"{latSsel}>{t}S{/t}</option>
			</select>
			&nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
			°&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" />&nbsp;'&nbsp;
			{lat_message}
			&nbsp;&nbsp;
			<select name="lonEW" class="input40">
				<option value="E"{lonEsel}>{t}E{/t}</option>
				<option value="W"{lonWsel}>{t}W{/t}</option>
			</select>
			&nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
			°&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" />&nbsp;'&nbsp;
			{lon_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{t}Country:{/t}</td>
		<td>
			<select name="country" class="input200">
				{countryoptions}
			</select>
			{show_all_countries_submit}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr><td>{t}Rating:{/t}</td>
		<td>
			{t}Difficulty:{/t}
			<select name="difficulty" class="input60">
				{difficulty_options}
			</select>&nbsp;&nbsp;
			{t}Terrain:{/t}
			<select name="terrain" class="input60">
				{terrain_options}
			</select>{diff_message}
		</td>
	</tr>
	<tr><td>{t}Time and effort (optional):{/t}</td>
	  <td>
			{t}Time effort:{/t}
      <input type="text" name="search_time" maxLength="10" value="{search_time}" class="input30" /> {t}h{/t}
			&nbsp;&nbsp;
			{t}Distance:{/t}
			<input type="text" name="way_length" maxlength="10" value="{way_length}" class="input30" /> {t}km{/t} &nbsp; {effort_message}
	  </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="help"><img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
			{t}The effort is of course only be estimated and can vary depending on environmental influences. 
				 If you can not make sufficiently detailed information, write 0 (zero) in both fields. 
				 (See also: <a href="articles.php?page=cacheinfo#time" target="_blank">description</a>){/t}
		</td>
	</tr>

	<tr>
		<td>{t}Waypoints:{/t}</td>
		<!-- allow wp_gc copy&paste with leading spaces; will be trimmed later -->
		<td>geocaching.com: <input type="text" name="wp_gc" value="{wp_gc}" maxlength="12" class="input50" />
			navicache.com: <input type="text" name="wp_nc" value="{wp_nc}" maxlength="6" class="input50" />
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="help"><img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
		{t}This waypoints will be used to show links in the view cache and the log page.{/t}<br />
		{t}No data will be imported automatically from these listing services.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size2">
					<img src="lang/de/ocstyle/images/description/22x22-description.png" width="22" height="22" align="middle" border="0" />
					{t}Cache attributes{/t}&nbsp;&nbsp;
				</p>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">{cache_attrib_list}{safari_message}</td>
	</tr>
	<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size2">
					<img src="lang/de/ocstyle/images/description/22x22-description.png" width="22" height="22" align="middle" border="0" />
					{t}Description{/t}
				</p>
			</div>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{t}Language:{/t}</td>
		<td>
			<select name="desc_lang" class="input200">
				{langoptions}
			</select>
			{show_all_langs_submit}
		</td>
	</tr>
	<tr>
	  <td class="help" colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
		  {t}You can add more descriptions in other languages publishing the cache.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{t}Short description:{/t}</td>
		<td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400"/></td>
	</tr>

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{t}Description:{/t} {desc_message}</td>
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
			<textarea id="desc" name="desc" class="cachedesc">{desc}</textarea>
		</td>
	</tr>

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="help" colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
			{t}Your HTML code will be changed again by a special filter. This is necessary to avoid dangerous HTML-tags, 
				 such as &lt;script&gt;. A list of allowed HTML tags, you can find 
				 <a href="http://www.opencaching.de/articles.php?page=htmltags">here</a>{/t}
		</td>
	</tr>

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">{t}Additional note:{/t}</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="hints" class="hint mceNoEditor">{hints}</textarea>
		</td>
	</tr>

	<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size2">
					<img src="lang/de/ocstyle/images/description/22x22-description.png" width="22" height="22" align="middle" border="0" />
					{t}Others{/t}
				</p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="help" colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
			{t}You can add additional pictures after creating the cache.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{t}Hidden since:{/t}</td>
		<td>
			<input class="input20" type="text" name="hidden_day" maxlength="2" value="{hidden_day}"/>.
			<input class="input20" type="text" name="hidden_month" maxlength="2" value="{hidden_month}"/>.
			<input class="input40" type="text" name="hidden_year" maxlength="4" value="{hidden_year}"/>
			{hidden_since_message}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
			{t}For Events: The date of event!{/t}
		</td>
	</tr>
				
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td>{t}Publication:{/t}</td>
		<td>
			<input type="radio" class="radio" name="publish" id="publish_now" value="now2" {publish_now_checked} />&nbsp;<label for="publish_now">{t}Publish now{/t}</label><br />
			<input type="radio" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked} />&nbsp;<label for="publish_later">{t}Publish on{/t}</label>
			<input class="input20" type="text" name="activate_day" maxlength="2" value="{activate_day}"/>.
			<input class="input20" type="text" name="activate_month" maxlength="2" value="{activate_month}"/>.
			<input class="input40" type="text" name="activate_year" maxlength="4" value="{activate_year}"/>&nbsp;
			<select name="activate_hour" class="input60">
				{activation_hours}
			</select>&nbsp;{t}#time_suffix_label#{/t}&nbsp;{activate_on_message}<br />
			<input type="radio" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked} />&nbsp;<label for="publish_notnow">{t}Do not publish now.{/t}</label>
		</td>
	</tr>

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td><nobr>{t}password for 'found' logs{/t}:</nobr></td>
		<td><input class="input100" type="text" name="log_pw" value="{log_pw}" maxlength="20"/> &nbsp; {t}(leave blank for no password){/t}</td>
	</tr>
	<tr>
		<td class="help" colspan="2">
			<img src="lang/de/ocstyle/images/misc/hint-link.gif" border="0" width="15" height="11" alt="{t}Notice{/t}" title="{t}Notice{/t}" />
			{t}Please note the <a href="articles.php?page=cacheinfo#logpw" target="_blank">description</a>{/t}
		</td>
	</tr>

	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<input class="checkbox" type="checkbox" name="TOS" value="1"{toschecked}/>
			{t}I have read and agree to the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
			{tos_message}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
		<!-- <input type="reset" name="reset" value="{reset}" class="formbutton" onclick="flashbutton('reset')" />&nbsp;&nbsp; -->
		<input type="submit" name="submitform" value="{submit}" class="formbutton" onclick="submitbutton('submitform') "/>
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>
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

	// set descMode to 1 oder 2 ... when the editor is loaded, set back to 3
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
			document.newcacheform.submit();
	}

	function SwitchToHtmlDesc()
	{
		document.getElementById("descMode").value = 2;

		if (use_tinymce == 1)
			document.newcacheform.submit();
	}

	function SwitchToHtmlEditDesc()
	{
		document.getElementById("descMode").value = 3;

		if (use_tinymce == 0)
			document.newcacheform.submit();
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
