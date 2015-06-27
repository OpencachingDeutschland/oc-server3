{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>
	<div class="newsblock">
{if !$includetext}
		<table class='narrowtable' style='margin-top:0'>
{/if}

{foreach name=rss from=$rss item=rss}
{if $includetext}
		<p class="content-title-noshade-size15" style="display: inline;">{$rss.pubDate} - {$rss.title}</p>
		<p style="line-height: 1.6em;display: inline;">&emsp;[<b><a class="link" href="{$rss.link}">mehr...</a></b>]</p>
		<div class="rsstext">{$rss.description}</div>
{else}
			<tr>
				<td style="text-align:right; white-space:nowrap;">{$rss.pubDate}</td>
				<td><a class="links" href="{$rss.link}">{$rss.title}</a></td>
			</tr>
{/if}

{/foreach}

{if !$includetext}
		</table>
{/if}
	</div>