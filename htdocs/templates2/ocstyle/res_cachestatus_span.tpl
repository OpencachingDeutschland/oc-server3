{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
 {* see also search.html.inc.php *}
{if $status==1}
    text-decoration: none
{elseif $status==2}
    text-decoration: line-through
{elseif $status==3}
    text-decoration: line-through; color: #c00000;
{elseif $status==6}
    text-decoration: line-through; color: #c00000;
{elseif $status==7}
    text-decoration: line-through; color: #c00000;
{else}
    &nbsp;
{/if}
