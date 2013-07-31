{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* check if actualpath is set *}
{if !isset($actualpath)}{assign var='actualpath' value=''}{/if}

{if $oconly}
	{if $opt.help.oconly != ''}{$opt.help.oconly}{/if}{if $size=='15x21'}<img src="{$actualpath}resource2/ocstyle/images/misc/15x21-oc.png" width="15" height="21" >{else}<img src="{$actualpath}resource2/ocstyle/images/misc/15x15-oc.png" width="15" height="15" >{/if}{if $opt.help.oconly != ''}</a>{/if}
{/if}
