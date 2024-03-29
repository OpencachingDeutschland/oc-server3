{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
{* OCSTYLE *}

<script type="text/javascript">
{literal}<!--

function toggle_archived()
{
    var archived = document.getElementsByName("row_archived");
    var show;
    if (archived[0].style.display == "none")
    {
        show="";
        document.getElementById("show_archived").style.display = "none";
        document.getElementById("hide_archived").style.display = "";
    }
    else
    {
        show="none";
        document.getElementById("hide_archived").style.display = "none";
        document.getElementById("show_archived").style.display = "";
    }
    for (var i=0; i<archived.length; i++)
        archived[i].style.display = show;

    var dCookieExp = new Date(2049, 12, 31);
    document.cookie = "ocprofilearchived=" + show + ";expires=" + dCookieExp.toUTCString();
}

function myHomeLoad()
{
    {/literal}{*
        The body onload attribute used to call this functions somehow disables the
        enlargit init, called via window.onload. Do an explicit init instead:
    *}{literal}
    enl_init();

    var archived = document.getElementsByName("row_archived");
    if (archived.length > 0)  // is 0 for MSIE due to getElementsByName() bug
    {
        var sCookieContent = document.cookie.split(";");
        for (var nIndex = 0; nIndex < sCookieContent.length; nIndex++)
        {
            var sCookieValue = sCookieContent[nIndex].split("=");
            if (sCookieValue[0].replace(/^\s+/,'') == "ocprofilearchived" && sCookieValue[1] == "none")
                toggle_archived();
        }
        document.getElementById("toggle_archived_option").style.display = "";
    }
}

-->{/literal}
</script>

{* Welcome *}
<div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" border="0" width="32px" height="32px" style="margin-right: 10px;" />
    {t 1=$login.username}Hello %1{/t}
</div>

{if $allpics === false}
    {* Geocaches found *}
    <div class="content2-container bg-blue02" style="margin-top:20px;">
        <p class="content-title-noshade-size3">
            <img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" width="22" height="22"  style="margin-right: 10px;" />&nbsp;
            {t 1=$found}Finds: %1{/t} &nbsp;
            {if $logs|@count > 0}<span class="content-title-link">[<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bymylastlog&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=1,7&amp;calledbysearch=0">{t}Geocaches found{/t}</a>]&nbsp; [<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bymylastlog&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=0&amp;calledbysearch=0">{t}Geocaches logged{/t}</a>]</span>{/if}
        </p>
    </div>

    {* Ocprop: (find|us|own)erid=([0-9]+) *}
    <p style="line-height: 1.6em;"><b>{t}Your latest log entries{/t}{if $morelogs} </b>(<a class="systemlink" href="ownlogs.php">{t}more{/t}</a>){t}#colonspace#{/t}:{else}{t}#colonspace#{/t}:</b>{/if}</p>

    <table class="table">
        {foreach from=$logs item=logItem}
            <tr>
                <td><nobr>
                    {include file="res_logtype.tpl" type=$logItem.type}
                    {if $logItem.oc_team_comment}<img src="resource2/{$opt.template.style}/images/oclogo/oc-team-comment.png" alt="OC-Team" title="{t}OC team comment{/t}" width="13" height="16" />{/if}
                </nobr></td>
                <td style="white-space:nowrap; text-align:center">{$logItem.date|date_format:$opt.format.datelong}</td>
                <td>
                    <a href="viewcache.php?wp={$logItem.wp_oc}">{$logItem.name|escape}</a>
                    {include file="res_oconly.tpl" oconly=$logItem.oconly}
                    {t}by{/t} <a href="viewprofile.php?userid={$logItem.userid}">{$logItem.username|escape}</a>
                    {include file="res_logflags.tpl" withRecommendation=true lfSpace=false}
                </td>
            </tr>
        {foreachelse}
            <tr><td>{t}No entries found{/t}</td></tr>
        {/foreach}
    </table>
{/if}

{* Log pictures *}
<div class="content2-container bg-blue02" style="margin-top:20px;">
    <p class="content-title-noshade-size3">
        <img src="resource2/{$opt.template.style}/images/misc/22x22-pictures.png" width="22" height="22"  style="margin-right: 10px;" />&nbsp;
        {t 1=$total_pictures}Log pictures: %1{/t} &nbsp;
        {if $pictures|@count > 0 && $allpics === false}<span class="content-title-link">[<a href="myhome.php?allpics=ownlogs">{t}Show all{/t}</a>]</span>{/if}
    </p>
</div>

{if $pictures|@count == 0}
    {if $allpics == 'owncaches'}
        <p>{t}There are no log pictures yet for your caches.{/t}</p>
    {else}
        <p>{t}You did not upload any log pictures yet.{/t}</p>
    {/if}
    <br />
{else}
    <p style="line-height: 1.6em;">
        {if $allpics == 'ownlogs'}
            {assign var=subtitle value="{t}Your log pictures:{/t}"}
            {assign var=maxlines value=0}
        {elseif $allpics == 'owncaches'}
            {assign var=subtitle value="{t}Log pictures for your caches:{/t}"}
            {assign var=maxlines value=0}
        {else}
            <b>{t}Your latest log pictures:{/t}</b>
            {assign var=maxlines value=1}
        {/if}
    </p>

    {if $allpics == 'owncaches'}
        {include file="res_logpictures.tpl" logdate=true loguser=true maxlines=$maxlines shortyear=true}
    {else}
        {include file="res_logpictures.tpl" logdate=true loguser=false maxlines=$maxlines fullyear=true}
    {/if}
    {if $allpics == 'ownlogs'}
        <p>{t}In your <a href="mydetails.php">profile settings</a> you can choose if your log pictures stat and gallery is visible for other users.{/t}</p>
    {/if}
{/if}

{if $allpics === false}
    {* Geocaches hidden *}
    <div class="content2-container bg-blue02" id="mycaches" style="margin-top:5px">
        <p class="content-title-noshade-size3">
            <img src="resource2/{$opt.template.style}/images/misc/22x22-traditional.png" width="22" height="22"  style="margin-right: 10px;" />&nbsp;
            {t 1=$hidden}Geocaches hidden: %1{/t} &nbsp;
            {if $caches|@count > 0}
                <span class="content-title-link">[
                <a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=0&calledbysearch=0">{t}Show details{/t}</a>

                {if $active < $hidden}]&nbsp; [
                    <a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=1&f_unpublished=1&calledbysearch=0">... {t}only active caches{/t}</a>
                {/if}]
                </span>
            {/if}
        </p>
    </div>

    <table class="table" style="max-width:97%">
        {if $caches|@count == 0}
            <tr><td colspan="4"><p style="margin-bottom:24px">{t}No Geocaches hidden{/t}</p></td></tr>
        {else}
            {assign var="archived" value=0}
            {foreach from=$caches item=cacheItem}
                {if $cacheItem.status > 2}
                    {assign var="archived" value=$archived+1}
                {/if}
            {/foreach}
            <tr>
                <td colspan="4">
                    <span style="line-height:2em"><b>{t}Your geocaches hidden{/t}</b><span id="toggle_archived_option" style="display:none">{if $archived>0} (<a href="javascript:toggle_archived()" style="outline:none"><span id="hide_archived">{t}hide archived{/t}</span><span id="show_archived" style="display:none">{t}show archived{/t}</span></a>){/if}</span>{t}#colonspace#{/t}:</span>
                </td>
                <td style="text-align:right">
                    <span style="line-height:2em"><img src="resource2/{$opt.template.style}/images/viewcache/cache-rate.png" width="16" height="16" title="{t}with recommendation{/t}" /></span>
                </td>
                <td style="text-align:right">
                    <span style="line-height:2em"><img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" alt="{t}Found{/t}" title="{t}Found{/t}"  /></span>
                </td>
                <td style="text-align:left"><span style="line-height:2em; text-decoration: underline;"><b><a href="myhome.php?sort=lastLog">{t}Last log{/t}</a></b></span></td>
            </tr>
            {foreach from=$caches item=cacheItem}
                {if $dotfill == ''}
                    {cycle values="listcolor1,listcolor2" assign=listcolor}
                {else}
                    {assign var="listcolor" value=""}
                {/if}
                <tr {if $cacheItem.status>2}name="row_archived"{/if}>
                    <td>{include file="res_cacheicon_22.tpl" cachetype=$cacheItem.type} {include file="res_oconly.tpl" oconly=$cacheItem.oconly size='15x21'}</td>
                    <td class="{$listcolor}" style="text-align:center"><nobr>{$cacheItem.date_hidden|date_format:$opt.format.datelong}&nbsp;</nobr></td>
                    <td class="{$listcolor}">{include file="res_cachestatus.tpl" status=$cacheItem.status}</td>
                    <td class="{$listcolor}" style="{if strlen($cacheItem.name) < 45}white-space:nowrap;{/if}min-width:300px;max-width:{if $dotfill==''}400{else}300{/if}px;overflow:hidden;"><a href="viewcache.php?wp={$cacheItem.wp_oc}">{$cacheItem.name|escape}</a>{if strlen($cacheItem.name) < 45} {include file="res_logflags.tpl" logItem=$cacheItem lfSpace=true} &nbsp;&nbsp; <span style="color:#b0b0b0">{$dotfill}</span>{/if}</td>
                    <td class="{$listcolor}" style="text-align:right;" align="right"><nobr>{if $cacheItem.toprating>0}{$cacheItem.toprating}{/if}</nobr></td>
                    <td class="{$listcolor}" style="text-align:right;" align="right"><nobr>{if $cacheItem.found>0}{$cacheItem.found}{/if}</nobr></td>
                    <td class="{$listcolor}" style="text-align:right;" align="right"><nobr><a href="viewcache.php?cacheid={$cacheItem.cache_id}#logentries">{$cacheItem.lastlog|date_format:$opt.format.date}</a>&nbsp; {include file="res_logtype.tpl" type=$cacheItem.lastlog_type}</nobr></td>
                </tr>
            {/foreach}
            <tr><td class="spacer" colspan="3"></td></tr>
            <tr>
                <td colspan="8">
                    <a class="systemlink" href="ownerlogs.php">{t}Log history{/t}</a>,
                    <a class="systemlink" href="myhome.php?allpics=owncaches">{t}Log pictures gallery{/t}</a>
                </td>
            </tr>
        {/if}

        {* ... unpublished caches *}
        {if $notpublished|@count}
            <tr>
                <td colspan="4">
                    <p style="margin-top:16px"><b>{t}Unpublished Geocaches{/t}</b></p>
                </td>
            </tr>
            {foreach from=$notpublished item=notpublishedItem}
                <tr>
                    <td>{include file="res_cacheicon_22.tpl" cachetype=$notpublishedItem.type} {include file="res_oconly.tpl" oconly=$notpublishedItem.oconly size='15x21'}</td>
                    <td>{$notpublishedItem.date_activate|date_format:$opt.format.datelong}</td>
                    <td>{include file="res_cachestatus.tpl" status=$notpublishedItem.status}</td>
                    <td><a href="viewcache.php?wp={$notpublishedItem.wp_oc}">{$notpublishedItem.name|escape}</a></td>
                </tr>
            {/foreach}
        {/if}
    </table>

    {* Other information *}
    {*
    <div class="content2-container bg-blue02" style="margin-top:20px;">
        <p class="content-title-noshade-size3">
            <img src="resource2/{$opt.template.style}/images/misc/25x25-world.png" width="25" height="25" style="margin-right: 10px;" />&nbsp;
            {t}Other information{/t}
        </p>
    </div>
    *}
{/if}

<div class="buffer">&nbsp;</div>
