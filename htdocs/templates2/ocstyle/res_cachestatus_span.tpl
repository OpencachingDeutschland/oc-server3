{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
{if $status==1}
	text-decoration: none
{elseif $status==2}
	text-decoration: line-through
{elseif $status==3}
	text-decoration: line-through; color: red;
{elseif $status==6}
	text-decoration: line-through; color: red;
{elseif $status==7}
	text-decoration: line-through; color: red;
{else}
	&nbsp;
{/if}
