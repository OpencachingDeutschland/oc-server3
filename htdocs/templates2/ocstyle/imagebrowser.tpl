{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE - keine Änderungen *}
<script type="text/javascript">
{literal}
<!--
	// Function sets image URL in FCKeditor	        
	function SelectFile(fileUrl, thumbUrl)
	{
		if (document.getElementById("insertthumb").checked == true)
			opener.fileBrowserReturn(thumbUrl);
		else
			opener.fileBrowserReturn(fileUrl);
		
		window.close();
	}
	
	function CancelSelect()
	{
		window.close();
	}
//-->
{/literal}
</script>
<br />
<table width="100%" class="table">
	<tr>
		<td><img src="resource2/{$opt.template.style}/images/description/22x22-image.png" height="22px" width="22px" alt="{t}Picture selection{/t}" /> {t 1=$cachename|escape}Picture selection for %1{/t}</td>
	</tr>
</table>
<table class="table">
	{foreach from=$pictures item=pictureItem name=pictures}
		{cycle values="1,2" assign=cc}
		{if $cc==1}
			<tr>
		{/if}
			<td valign="middle" align="center" width="{$thumbwidth+5}px"><a href="javascript:SelectFile('{$pictureItem.url|escape:'js'}', '{$opt.page.absolute_url}thumbs.php?showspoiler=1&uuid={$pictureItem.uuid|escape:'js'}');"><img border="0" src="thumbs.php?showspoiler=1&uuid={$pictureItem.uuid|escape}" title="{$pictureItem.title|escape}" alt="{$pictureItem.title|escape}" /></a></td>
		{if $cc==2 || $smarty.foreach.pictures.last}
			</tr>
		{/if}
	{foreachelse}
		<tr><td>{t}There are no pictures for the Geocache.{/t}</td></tr>
	{/foreach}
</table>
<p><input type="checkbox" id="insertthumb" style="border:0;" /> <label for="insertthumb">{t}As preview picture{/t}</label></p>
<p><a href="javascript:CancelSelect();"><a href="javascript:CancelSelect();">{t}Cancel{/t}</a></p>