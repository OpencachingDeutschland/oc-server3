{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle"><img src="resource2/{$opt.template.style}/images/misc/32x32-world.png" style="margin-right: 10px;" width="32" height="32" alt="World" />{t}Sitemap{/t}</div>
	
<ul style="list-style-type: none; font-size: 120%">
	{foreach from=$sites item=siteItem}
			<li style="padding-left:{$siteItem.sublevel*20}px;">
				{if $siteItem.sitemap==1}
					<a href="{$siteItem.href}" {if $siteItem.blanktarget=="1"}target="_blank"{/if}>
				{/if}
				{$siteItem.name|escape}
				{if $siteItem.sitemap==1}
					</a>
				{/if}
			</li>
	{/foreach}
</ul>
