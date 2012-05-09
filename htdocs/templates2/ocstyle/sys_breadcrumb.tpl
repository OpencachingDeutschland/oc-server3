{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{strip}
	{foreach name=breadcrumb from=$breadcrumb item=menuitem}
		{if !$smarty.foreach.breadcrumb.first}&nbsp;&gt;&nbsp;{/if}
		{$menuitem.menustring|escape}
	{/foreach}
{/strip}