{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Cache-Recommendations{/t}" />
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
			<table class="table" border="0" cellspacing="0" width="100%">
				<tr>
					<th width="50px">{t}Quota{/t}</th>
					<th width="10px">&nbsp;</th>
					<th>{t}Name{/t}</th>
				</tr>
				{foreach from=$cacheRatings item=cacheRatingsItem}
					{cycle values="listcolor1,listcolor2" assign=listcolor}
					<tr>
						<td class="{$listcolor}">{$cacheRatingsItem.quote|sprintf:"%0d"}%</td>
						<td class="{$listcolor}">&nbsp;</td>
						<td class="{$listcolor}">
							<span style="{include file="res_cachestatus_span.tpl" status=$cacheRatingsItem.status}"><a href="viewcache.php?wp={$cacheRatingsItem.wp}">{$cacheRatingsItem.name|escape}</a></span> {t}by{/t}
							<a href="viewprofile.php?userid={$cacheRatingsItem.cacheuserid}">{$cacheRatingsItem.cacheusername|escape}</a>
						</td>
					</tr>
				{foreachelse}
					<tr><td colspan="3">{t}No recommendations found.{/t}</td></tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>