<div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>
	<div class="newsblock">
{if !$includetext}
		<table class='narrowtable' style='margin-top:0'>
{/if}

{for $i=0 to count($rss)}
{if $includetext}
		<p class="content-title-noshade-size15" style="display: inline;">{$rss.$i.pubDate} - {$rss.$i.title}</p>
		<p style="line-height: 1.6em;display: inline;">&emsp;[<b><a class="link" href="{$rss.$i.link}">mehr...</a></b>]</p>
		<div class="rsstext">{$rss.$i.description}</div>
{else}
			<tr>
				<td style="text-align:right">{$rss.$i.pubDate}</td>
				<td><a class="links" href="{$rss.$i.link}">{$rss.$i.title}</a></td>
			</tr>
{/if}

{/for}

{if !$includetext}
		</table>
{/if}
	</div>
<div class="buffer" style="width: 500px;">&nbsp;</div>