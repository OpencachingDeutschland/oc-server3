{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<script type="text/javascript">
<!--
{literal}
	function checkForm()
	{
		if (document.fpicture.title.value == "")
		{
			alert('{/literal}{t escape=js}Please give the picture a name!{/t}{literal}');
			return false;
		}
		return true;
	}
{/literal}
//-->
</script>

<form action="picture.php" method="post" enctype="multipart/form-data" name="fpicture" dir="ltr" onsubmit="return checkForm();">
	<input type="hidden" name="action" value="{$action|escape}" />
	{if $action=='add'}
		{if $objecttype==OBJECT_CACHE}
			<input type="hidden" name="cacheuuid" value="{$cacheuuid|escape}" />
		{elseif $objecttype==OBJECT_CACHELOG}
			<input type="hidden" name="loguuid" value="{$loguuid|escape}" />
		{/if}
	{else}
		<input type="hidden" name="uuid" value="{$uuid|escape}" />
	{/if}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/description/22x22-image.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Edit picture{/t}" title="{t}Edit picture{/t}" />
		{capture name="name"}
			<a href="viewcache.php?wp={$cachewp|escape}">{$cachename|escape}</a>
		{/capture}

		{if $action=='add'}
			{if $objecttype==OBJECT_CACHE}
				{t 1=$smarty.capture.name}Add picture for Geocaches %1{/t}
			{else if $objecttype==OBJECT_CACHELOG}
				{t 1=$smarty.capture.name}Add picture for Logentry %1{/t}
			{/if}
		{else}
			{if $objecttype==OBJECT_CACHE}
				{t 1=$smarty.capture.name}Edit picture for Geocaches %1{/t}
			{else if $objecttype==OBJECT_CACHELOG}
				{t 1=$smarty.capture.name}Edit picture for Logentry %1{/t}
			{/if}
		{/if}
	</div>

	<table class="table">
		<tr>
			<td valign="top">{t}Name:{/t}</td>
			<td>
				<input class="input200" name="title" type="text" value="{$title|escape}" size="43" />
				{if $errortitle==true}
					<span class="errormsg">{t}Give the picture a name!{/t}</span>
				{/if}
			</td>
		</tr>
		{if $action=='add'}
			<tr>
				<td valign="top">{t}File:{/t}</td>
				<td>
					<input type="hidden" name="MAX_FILE_SIZE" value="{$opt.logic.pictures.maxsize}">
					<input class="input200" name="file" type="file" maxlength="{$opt.logic.pictures.maxsize}" />
				</td>
			</tr>
			{if $errorfile==ERROR_UPLOAD_ERR_NO_FILE}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}No picture file given.{/t}</span></td></tr>
			{elseif $errorfile==ERROR_UPLOAD_ERR_SIZE}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}The file was too big. The maximum file size is 150 KB.{/t}</span></td></tr>
			{elseif $errorfile==ERROR_UPLOAD_UNKNOWN}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}The file was not uploaded correctly.{/t}</span></td></tr>
			{elseif $errorfile==ERROR_UPLOAD_ERR_TYPE}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}Only the following picture formats are allowed: BMP, GIF, PNG and JPEG.{/t}</span></td></tr>
			{/if}
		{/if}

		{if $objecttype==OBJECT_CACHE}
			<tr>
				<td align="right"><input class="checkbox" type="checkbox" name="spoiler" value="1" {if $spoilerchecked==true}checked="checked"{/if} /></td>
				<td>{t}This picture is a spoiler - dont show a thumbnail.{/t}</td>
			</tr>
			<tr>
				<td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1" {if $displaychecked==false}checked="checked"{/if}></td>
				<td>{t}Do not display this picture (only used in HTML description){/t}</td>
			</tr>
		{/if}
		{if $action=='add'}
			<tr>
				<td class="help" colspan="2">
					<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Note{/t}" title="{t}Note{/t}">
					{t}Only the following picture formats are allowed: BMP, GIF, PNG and JPEG. We recommend JPEG for photos.{/t}<br />
					{t}The file size of the pictures must not exeed 150 KB. We recommend 480x360 pixel of picture size.{/t}<br />
					{t}After click to upload, it can take a while, until the next page is been shown.{/t}
				</td>
			</tr>
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
				<input type="reset" name="reset" value="{t}Reset{/t}" style="width:120px"/>&nbsp;&nbsp;
				<input type="submit" name="ok" value="{if $action=='add'}{t}Upload{/t}{else}{t}Submit{/t}{/if}" style="width:120px"/>
			</td>
		</tr>
	</table>
</form>