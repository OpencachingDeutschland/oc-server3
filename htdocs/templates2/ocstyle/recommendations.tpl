{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Cache-Recommendations{/t}" />
	{t}Cache-Recommendations{/t}
</div>

<table class="table">
	<tr>
		<td>
			{capture name=cache}
				<a href="viewcache.php?cacheid={$cache.id}">{$cache.name|escape}</a>
			{/capture}
			{capture name=user}
				<a href="viewprofile.php?userid={$cache.userid}">{$cache.username|escape}</a>
			{/capture}
			{t 1=$smarty.capture.cache 2=$smarty.capture.user}User that recommended %1 by %2 also recommended the following geocaches:{/t}
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<table class="null" border="0" cellspacing="0" width="100%">
				<tr>
					<td width="50px"><b>{t}Quota{/t}</b></td>
					<td width="10px">&nbsp;</td>
					<td><b>{t}Name{/t}</b></td>
				</tr>
				{foreach from=$cacheRatings item=cacheRatingsItem}
					{cycle values="#eeeeee,#e0e0e0" assign=bgcolor}
					<tr>
						<td bgcolor="{$bgcolor}">{$cacheRatingsItem.quote|sprintf:"%0d"}%</td>
						<td bgcolor="{$bgcolor}">&nbsp;</td>
						<td bgcolor="{$bgcolor}">
							<a href="viewcache.php?wp={$cacheRatingsItem.wp}">{$cacheRatingsItem.name|escape}</a> {t}by{/t}
							<a href="viewprofile.php?userid={$cacheRatingsItem.cacheuserid}">{$cacheRatingsItem.cacheusername|escape}</a>
						</td>
					</tr>
					<tr><td class="spacer" colspan="3" bgcolor="{$bgcolor}"></td></tr>
				{foreachelse}
					<tr><td colspan="3">{t}No recommendations found.{/t}</td></tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>