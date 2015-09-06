{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}Statistic picture{/t}
</div>

<form action="change_statpic.php">

	<table class="table">
		<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
		<tr>
			<td style="vertical-align:top;">{t}Statistic picture{/t}{t}#colonspace#{/t}:</td>
			<td><img src="statpics/{$opt.template.locale}/{$login.userid}.jpg" align="middle" /></td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td style="vertical-align:top;">{t}HTML-Code:{/t}</td>
			<td class="help">&lt;img src="{$opt.page.absolute_url|escape|escape}statpics/{$opt.template.locale}/{$login.userid}.jpg" alt="{t 1=$login.username|escape|escape}Opencaching.de-statstic of %1{/t}" title="{t 1=$login.username|escape|escape}Opencaching.de-statstic of %1{/t}" /></td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td style="vertical-align:top;">{t}BBCode for webforums:{/t}</td>
			<td class="help">[url={$opt.page.absolute_url|escape|escape}viewprofile.php?userid={$login.userid}][img]{$opt.page.absolute_url|escape|escape}statpics/{$opt.template.locale}/{$login.userid}.jpg[/img][/url]</td>
		</tr>
		<tr><td class="spacer" colspan="3">&nbsp;</td></tr>

		<tr>
			<td class="header-small" colspan="3">
				<input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="submitbutton('change')" />
			</td>
		</tr>
	</table>
	
</form>
	
	
