{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{if $startat <= 0}
	<img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-first-inactive.png" width="16px" height="16px" alt="&lt;&lt;">
	<img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-prev-inactive.png" width="16px" height="16px" alt="&lt;">
{else}
	<a href="{$page}.php?startat=0"><img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-first.png" width="16px" height="16px" alt="&lt;&lt;"></a>
	<a href="{$page}.php?startat={$startat-$perpage}"><img src="resource2/{$opt.template.style}/images/navigation/16x16-browse-prev.png" width="16px" height="16px" alt="&lt;"></a>
{/if}
