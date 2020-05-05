{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}


{foreach name=rss from=$rss item=rss}
    <div class="rss__content d-flex">

        <div class="rss__date">
            <div class="rss__day">{$rss.pubDate|date_format:"%d."}</div>
            <div class="rss__month">{$rss.pubDate|date_format:"%b"}</div>
        </div>


        <a href="{$rss.link}" rel="nofollow" class="rss__text" target="_blank">
            {$rss.title}
        </a>


    </div>
{/foreach}
