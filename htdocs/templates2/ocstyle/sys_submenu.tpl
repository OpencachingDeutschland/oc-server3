{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
{strip}
	{foreach name=submenu from=$items item=menuitem}
		<li class="group{$menuitem.sublevel}{if $menuitem.selected} group_active{/if}"><a href="{$menuitem.href}" {$menuitem.target}>{$menuitem.menustring|escape}</a></li>	
	{/foreach}
{/strip}