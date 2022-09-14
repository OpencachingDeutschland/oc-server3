{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
<div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>
    <div class="newsblock">
{if !$includetext}
        <table class='narrowtable' style='margin-top:0'>
{/if}

{*xxx015 - "'item' and 'from' may not have same variable name 'rss'"*}
{foreach name=rssLoop from=$rss item=rss_item}
{if $includetext}
        <p class="content-title-noshade-size15" style="display: inline;">{$rss_item.pubDate} - {$rss_item.title}</p>
        <p style="line-height: 1.6em;display: inline;">&emsp;[<b><a class="link" href="{$rss_item.link}">mehr...</a></b>]</p>
        <div class="rsstext">{$rss_item.description}</div>
{else}
            <tr>
                <td style="text-align:right; white-space:nowrap;">{$rss_item.pubDate|date_format:$opt.format.datelong}</td>
                <td><a class="links" href="{$rss_item.link}">{$rss_item.title}</a></td>
            </tr>
{/if}

{/foreach}

{if !$includetext}
        </table>
{/if}
    </div>
