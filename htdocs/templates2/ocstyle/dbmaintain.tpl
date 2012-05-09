{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
 {* OCSTYLE - ohne Änderungen *}
<form action="dbmaintain.php" method="post">
	<table class="content">
		<colgroup>
			<col width="150">
			<col>
		</colgroup>
		<tr><td class="header" colspan="2"><img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" border="0" width="32" height="32" align="middle" /><font size="4">  <b>{t}Database Maintainance{/t}</b></font></td></tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		{foreach from=$procedures item=procItem}
			<tr><td colspan="2"><input id="{$procItem|escape}" type="radio" name="action" value="{$procItem|escape}"> <label for="{$procItem|escape}">{$procItem|escape}</label></td></tr>
		{/foreach}

		{if $executed==true}
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr colspan="2"><td class="successmsg">{t 1=$proc|escape 2=$count}%1 corrected: %2 entries{/t}</td></tr>
		{/if}

		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td width="150px">&nbsp;</td>
			<td>
				<input type="submit" name="ok" value="{t}Execute{/t}" class="formbuttons" />
			</td>
		</tr>
	</table>
</form>