{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
{strip}
	{foreach name=submenu from=$items item=menuitem}
		{if $menuitem.href == ''}
			{* separator headline *}
			<li class="bgcolor1" style="line-height:1.5em">&nbsp;</li>
			<li class="title">{$menuitem.menustring|escape}</li>
		{else}
			{* selectable menu option *}
			<li class="group{$menuitem.sublevel}{if $menuitem.selected} group_active{/if}"><a href="{$menuitem.href}" {$menuitem.target}>{$menuitem.menustring|escape}</a></li>
		{/if}
	{/foreach}
{/strip}