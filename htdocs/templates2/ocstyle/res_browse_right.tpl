{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{if $startat<$maxstart}
	<a href="{$page}.php?startat={$startat+$perpage}"><img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-next.png" width="16px" height="16px" alt="&gt;"></a>
	<a href="{$page}.php?startat={$maxstart}"><img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-last.png" width="16px" height="16px" alt="&gt;&gt;"></a>
{else}
	<img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-next-inactive.png" width="16px" height="16px" alt="&gt;">
	<img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-last-inactive.png" width="16px" height="16px" alt="&gt;&gt;">
{/if}
