{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

<div class="content-txtbox-noshade">
    <div class="content-txtbox-noshade">
        <p class="startmessage">{$message}</p>
        <div class="buffer" style="width: 500px;">&nbsp;</div>
    </div>
</div>

{if $core_hq_message.message !=""}
    <div class="note note-{$core_hq_message.type}">
        {$core_hq_message.message}
    </div>
    <p></p>
{/if}

{foreach from=$sections item=section}

    {* news or blog *}
    {if $section == 'news'}
        <div>
            <div class="content2-container bg-blue02">
                <table class="none" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td>
                            <p class="content-title-noshade-size3">
                                <img src="resource2/{$opt.template.style}/images/misc/22x22-news.png"
                                     style="margin-right: 10px;" width="22" height="22" alt=""/>
                                <a href="https://blog.opencaching.de/"
                                   style="color:rgb(88,144,168); text-decoration: none;">{t}News{/t}</a>
                                &nbsp; <span class="content-title-link">[<a
                                            href="https://blog.opencaching.de/">{t}more{/t}...</a>]</span>
                            </p>
                        </td>
                        {if "$newsfeed" != ""}
                            <td style="text-align:right">
                                <a href="{$newsfeed}"><img src="resource2/ocstyle/images/media/22x22-feed.png"
                                    width="22" height="22"/></a>
                            </td>
                            <td width="4px"></td>
                        {/if}
                    </tr>
                </table>
            </div>
        </div>
        {if $extern_news}
            <div id="blog">
                {if $news|@count}
                    {include file="res_rssparser.tpl" rss=$news}
                {else}
                    <p><em>{t}currently not available{/t}</em></p>
                {/if}
            </div>
            <div class="buffer" style="width: 500px;">&nbsp;</div>
        {/if}

        {* next events *}
    {elseif $section == 'events'}
        <div class="content2-container bg-blue02 content2-section-no-p">
            <p class="content-title-noshade-size3">
                <img src="resource2/{$opt.template.style}/images/misc/22x22-event.png" style="margin-right: 10px;"
                     width="22" height="22" alt=""/>
                {t 1=$usercountry|escape}The next events in %1{/t}
                {if $total_events > $events|@count}
                    &nbsp;
                    <span class="content-title-link">[<a href="newcaches.php?cachetype=6">{t}more{/t}...</a>]</span>
                {/if}
            </p>
        </div>
        <div class="content2-section-no-p">
            {include file="res_newevents.tpl" events=$events}
        </div>
        {* new logpix *}
    {elseif $section == 'logpics'}
        <div class="content2-container bg-blue02" style="margin-bottom:6px">
            <p class="content-title-noshade-size3">
                <img src="resource2/{$opt.template.style}/images/misc/22x22-pictures.png" style="margin-right: 10px;"
                     width="22" height="22"/>
                <a href="newlogpics.php"
                   style="color:rgb(88,144,168); text-decoration: none;">{t}New log pictures{/t}</a>
                &nbsp; <span class="content-title-link">[<a href="newlogpics.php">{t}more{/t}...</a>]</span>
            </p>
        </div>
        <div style="height:2px"></div>
        {include file="res_logpictures.tpl" logdate=true loguser=true}

        {* recommendations *}
    {elseif $section == 'recommendations'}
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size3">
                <img src="resource2/{$opt.template.style}/images/misc/22x22-winner.png" style="margin-right: 10px;"
                     width="22" height="22" alt=""/>
                <a href="tops.php" style="color:rgb(88,144,168); text-decoration: none;">{t}Current top ratings{/t}</a>
            </p>
        </div>
        <p style="line-height: 1.6em;">{t 1=$usercountry|escape 2=$toprating_days}Geocaches with most ratings in the last %2 days in %1.{/t}</p>
        <div style="margin-bottom:16px">
            {include file="res_newratings.tpl" topratings=$topratings}
        </div>
        {* forum news *}
    {elseif $section == 'forum'}
        {if $forum_enabled==true}
            <div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size3">
                    <img src="resource2/ocstyle/images/misc/22x22-news.png" style="margin-right: 10px;" alt=""
                         width="22" height="22"/>
                    <a href="{$forum_link|escape}"
                       style="color: rgb(88, 144, 168); text-decoration: none;">{t 1=$forum_name|escape}New forum topcis (%1){/t}</a>
                </p>
            </div>
            <div id="forum">
                {if $forum|@count}
                    {include file="res_rssparser.tpl" rss=$forum}
                {else}
                    <p><em>{t}currently not available{/t}</em></p>
                {/if}
            </div>
            <div class="buffer" style="width: 500px;">&nbsp;</div>
        {/if}

        {* new caches *}
    {elseif $section == 'newcaches'}
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size3">
                <img src="resource2/{$opt.template.style}/images/misc/22x22-traditional.png" style="margin-right: 10px;"
                     width="22" height="22" alt=""/>
                <a href="newcaches.php?country={$usercountryCode|escape}"
                   style="color:rgb(88,144,168); text-decoration: none;">{t 1=$usercountry|escape}Newest caches in %1{/t}</a>
                &nbsp; <span class="content-title-link">[<a href="newcaches.php">{t}more{/t}...</a>]</span>
            </p>
        </div>
        <p style="line-height: 1.6em;">
            ({t 1=$count_hiddens 2=$count_founds 3=$count_users}Total of %1 active Caches and %2 founds by %3 users{/t}
            )</p>
        <div class="content2-section-no-p">
            {include file="res_newcaches.tpl" newcaches=$newcaches}
        </div>
    {/if}
{/foreach}
