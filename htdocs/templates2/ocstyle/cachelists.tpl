{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-list.png" style="align: left; margin-right: 10px;" width="32" height="32" />
		{t}Cache lists{/t}
	</div>

	<p>
		{t}Since July 2015, all registered Opencaching users can create and publish own geocache lists via their <a href="mylists.php">user profile</a>. The following lists have been published so far:{/t}
	</p>

	<table>
		<tr>
			<td colspan="3" class="header-small">
				{include file="res_pager.tpl"}
			</td>
		</tr>
		<tr><td class="spacer"></td></tr>
	</table>

	{include file="res_cachelists.tpl"}
