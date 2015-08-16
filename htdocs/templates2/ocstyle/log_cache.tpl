{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<script type="text/javascript">
{literal}
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
  /* other Browser */
  else
  {
    alert(navigator.appName + ": {/literal}{t}Setting smilies is not supported{/t}{literal}");
  }
}

function _chkFound () {
  if (document.editform.logtype.value == "1" || document.editform.logtype.value == "7")
	{
		if (document.editform.rating)
	    document.editform.rating.disabled = false;
  }
  else
  {
		if (document.editform.rating)
	    document.editform.rating.disabled = true;
  }
  return false;
}

//-->
{/literal}
{* 
 * capture allows us to "eval" the link tag with the variable values
 * and save the complete link in the variable "cachelink" to use it in translation
 *}
{capture name=cachelink assign=cachelink}<a href="viewcache.php?cacheid={$cacheid}">{$cachename|escape}</a>{/capture}
</script>

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" style="margin-right: 10px;" width="22" height="22" alt="{t}New log-entry{/t}" />
	{t 1=$cachelink}Add log-entry for the cache %1{/t}
</div>
<form action="log.php" method="post" enctype="application/x-www-form-urlencoded" name="editform" dir="ltr">
{if $masslog==true}
<p class="redtext"> 
	{t 1=$masslogCount}You submitted more than %1 identical logs. Please make sure that you are entering the date of your cache visit, not the current date - also when "late logging" old finds.{/t} 
</p>
<p>
	{t}Wrong log dates can impair several OC functions like searching by last log date. Also, the owner and other caches may think that the cache has been currently found (date and type of the last log are shown in the owner's caches list!), which can adversely affect cache maintenance and lead to more DNFs.{/t}
</p>
<p class="spacer_before">
	<input type="checkbox" name="suppressMasslogWarning" value="1" class="checkbox" id="suppressMasslogWarning" /> <label for="suppressMasslogWarning">{t}I know what I am doing, do not show this advice again today.{/t}</label>
</p>
{/if}
{if $showstatfounds==true}
<p class="align-right">
	<b>{t 1=$userFound}You found %1 caches until now.{/t}</b>
</p>
{/if}
<input type="hidden" name="cacheid" value="{$cacheid}"/>
<input type="hidden" name="version3" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="3" />
<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr><td colspan="2"></td></tr>
	<tr>
		<td width="180px">{t}Type of log-entry:{/t}</td>
		<td>
			<select name="logtype" onChange="return _chkFound()">
				{foreach from=$logtypes item=logtypeoption}
				<option value="{$logtypeoption.id}"{if $logtypeoption.selected} selected="selected"{/if}>{$logtypeoption.name|escape}</option>
				{/foreach}
			</select>
			{if $octeamcommentallowed}
			&nbsp; <input type="checkbox" name="teamcomment" value="1" class="checkbox" {if $octeamcomment}checked{/if} id="teamcomment" /> <label for="teamcomment"><span class="{$octeamcommentclass}">{t}OC team comment{/t}</span></label>
			{/if}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td width="180px">{t}Date / time:{/t}</td>
		<td>
			<input class="input20" type="text" name="logday" maxlength="2" value="{$logday}"/>.
			<input class="input20" type="text" name="logmonth" maxlength="2" value="{$logmonth}"/>.
			<input class="input40" type="text" name="logyear" maxlength="4" value="{$logyear}"/>
		  &nbsp;&nbsp;&nbsp;
			<input class="input20" type="text" name="loghour" maxlength="2" value="{$loghour}" /> :
			<input class="input20" type="text" name="logminute" maxlength="2" value="{$logminute}" />
			&nbsp;&nbsp;{if $validate.dateOk==false}<span class="errormsg">{t}date or time is invalid{/t}</span>{/if}
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" />
			{t}For 'Found' and 'Not found' logs: Date and (optional) time of the cache search.{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{if $isowner==false}
	<tr>
		<td valign="top">{t}Recommendations:{/t}</td>
		<td valign="top">
			{if ($ratingallowed==true || $israted==true)}<input type="hidden" name="ratingoption" value="1"><input type="checkbox" name="rating" value="1" class="checkbox" {if $israted==true}checked{/if}/>&nbsp;{t}This cache is one of my recommendations.{/t}<br />
				{t 1=$givenratings 2=$maxratings}You have given %1 of %2 possible recommendations.{/t}
			{else}
				{t 1=$foundsuntilnextrating}You need additional %1 finds, to make another recommendation.{/t}
				{if ($givenratings > 0 && $givenratings==$maxratings && $israted==false)}<br />{t}Alternatively, you can withdraw a <a href="mytop5.php">existing recommendation</a>.{/t}{/if}
			{/if}
			<noscript><br />{t}A recommendation can only be made with a "found" or "attended" log!{/t}</noscript>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{/if}
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
			<textarea name="logtext" id="logtext" cols="68" rows="25" class="logs">{$logtext|escape}</textarea>
    </td>
	</tr>
	{if $descMode==2}
	<tr>
		<td colspan="2">
			<a href="javascript:insertSmiley('smile')"><img src="resource2/tinymce/plugins/emotions/img/smiley-smile.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('wink')"><img src="resource2/tinymce/plugins/emotions/img/smiley-wink.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('laughing')"><img src="resource2/tinymce/plugins/emotions/img/smiley-laughing.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('cool')"><img src="resource2/tinymce/plugins/emotions/img/smiley-cool.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('innocent')"><img src="resource2/tinymce/plugins/emotions/img/smiley-innocent.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('surprised')"><img src="resource2/tinymce/plugins/emotions/img/smiley-surprised.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('frown')"><img src="resource2/tinymce/plugins/emotions/img/smiley-frown.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('embarassed')"><img src="resource2/tinymce/plugins/emotions/img/smiley-embarassed.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('cry')"><img src="resource2/tinymce/plugins/emotions/img/smiley-cry.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('kiss')"><img src="resource2/tinymce/plugins/emotions/img/smiley-kiss.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('toungue-out')"><img src="resource2/tinymce/plugins/emotions/img/smiley-tongue-out.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('undecided')"><img src="resource2/tinymce/plugins/emotions/img/smiley-undecided.gif" alt="" height="18px" width="18px" border="0" /></a>&nbsp;
			<a href="javascript:insertSmiley('yell')"><img src="resource2/tinymce/plugins/emotions/img/smiley-yell.gif" alt="" height="18px" width="18px" border="0" /></a>
		</td>
	</tr>
	{/if}
	<tr><td class="spacer" colspan="2"></td></tr>
	{if $logpw}
	<tr>
		<td colspan="2">{t}passwort to log:{/t}
			<input class="input100" type="text" name="log_pw" maxlength="20" value="" /> {if !$validate.logPw}<span class="errormsg">{t}Invalid password!{/t}</span>{else}({if $cachetype==6}{t}only for attended-logs{/t}{else}{t}only for found logs{/t}{/if}){/if}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	{/if}
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			{t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td class="header-small" colspan="2">
			<input type="submit" name="submitform" value="{t}Log this cache{/t}" class="formbutton" onclick="submitbutton('submitform')" />
		</td>
	</tr>
</table>
</form>

<script language="javascript" type="text/javascript">
<!--
	_chkFound();
	OcInitEditor();
//-->
</script>
