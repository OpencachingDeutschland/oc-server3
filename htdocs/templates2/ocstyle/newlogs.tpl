{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{if $ownerlogs || $ownlogs}
    <div class="content2-pagetitle">
        <img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="32" height="32" alt="" />
        {if $ownerlogs}
            {if $ownlogs}
                {t}Log entries for your geocaches{/t}
            {else}
                {capture name=ownerlink}<a href="viewprofile.php?userid={$ownerid}">{$ownername|escape}</a>{/capture}
                {t 1=$smarty.capture.ownerlink}Newest log entries for caches of %1{/t}
            {/if}
        {else $ownlogs}
            {t}Your log entries{/t}
        {/if}
    </div>
{else}
    <script type="text/javascript">
        {literal}
        <!--
        function logSelectionChanged()
        {
            var new_logselection = document.getElementById('logselection').value;
            var url = window.location.href;
            url = url.replace(/[&?]logselection=[123]/, "");
            if (url.indexOf('?') > 0)
                url += "&";
            else
                url += "?";
            url += "logselection=" + new_logselection;
            window.location.href = url;
        }
        //-->
        {/literal}
    </script>
    <table cellspacing="0" cellpadding="0" style="width:98.1%">
        <tr>
            <td class="nav4">
                <ul>
                    <li class="group noicon {if $countryCode === ''}selected{/if}"><a href="newlogs.php">{t}All new logs{/t}</a></li>
                    <li class="group noicon {if !$rest && $countryCode}selected{/if}"><a href="newlogs.php?country={$opt.template.country}">{t 1=$countryName}New logs in %1{/t}</a>
                    <li class="group noicon {if $rest}selected{/if}"><a href="newlogsrest.php">{t 1=$mainCountryName}New logs without %1{/t}</a></li>
                </ul>
            </td>
            <td class="default" style="text-align:right; vertical-align:top; padding-top:0.2em">
                <select id="logselection" onchange="logSelectionChanged()" >
                    <option value="1" {if $logselection==1}selected="selected"{/if}>{t}Current new entries{/t}</option>
                    <option value="2" {if $logselection==2}selected="selected"{/if}>{t}All new entries{/t}</option>
                    <option value="3" {if $logselection==3}selected="selected"{/if}>{t}... by log date{/t}</option>
                </select>
            </td>
        </tr>
    </table>
    <p>
        {if $rest || !$countryCode}
            <br />
            {include file="res_countrylinks.tpl" newCaches=$newLogs}
        {/if}
    </p>
{/if}

{if !$rest && $countryCode}
    <div style="height:4px"></div>
{/if}
{if $total_found + $total_attended + $total_dnf + $total_recommended}
    <div style="float:right">
        <p style="line-height:2em">
            &nbsp;&nbsp;{t}total{/t}&nbsp;
            {if $total_recommended}
                {$total_recommended}  <img src="images/rating-star.gif" width="17" height="16" title="{t}recommended{/t}" />&nbsp;
            {/if}
            {if $total_found}
                {$total_found} <img src="resource2/{$opt.template.style}/images/log/16x16-found.png" alt="{t}found{/t}" title="{t}found{/t}"  />&nbsp;
            {/if}
            {if $total_dnf}
                {$total_dnf} <img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" alt="{t}not&nbsp;found{/t}" title="{t}not&nbsp;found{/t}"  />&nbsp;
            {/if}
            {if $total_attended}
                {$total_attended} <img src="resource2/{$opt.template.style}/images/log/16x16-attended.png" alt="{t}attended{/t}" title="{t}attended{/t}"  />&nbsp;
            {/if}
        </p>
    </div>
{/if}
<p style="line-height:2em">
    {if $paging}
        {include file="res_pager.tpl"}
        &nbsp; &nbsp;
    {/if}
    {if $ownerlogs && $ownlogs}
        {if $show_own_logs}
            <a class="systemlink" href="ownerlogs.php?ownlogs=0">{t}Hide own logs{/t}</a>
        {else}
            <a class="systemlink" href="ownerlogs.php?ownlogs=1">{t}Show own logs{/t}</a>
        {/if}
    {/if}
</p>

<table width="100%" class="table">
    <tr><td class="spacer"></td></tr>
    {assign var='lastCountry' value=''}

    {foreach name=newLogs from=$newLogs item=newLog}
        {if $newLogsPerCountry && ($rest || !$countryCode)}
            {if $newLog.country_name!=$lastCountry}
                <tr><td class="spacer" id="country_{$newLog.country}"></td></tr>
                <tr><td colspan="3">
                    <table cellspacing="0" cellpadding="0"><tr>
                        <td class="content-title-flag" ><img src="images/flags/{$newLog.country|lower}.gif" /></td>
                        <td><b class="content-title-noshade-size08">{$newLog.country_name|escape}</b>&nbsp;</b></td>
                    </tr></table>
                </td></tr>
            {/if}
        {/if}
        <tr>
            <td style="width:1px">
                {$newLog.date|date_format:$opt.format.date}
            </td>
            <td class="listicon"><nobr>
                {if $newLog.type==1}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==2}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==3}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-note.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==7}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-attended.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==8}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-will_attend.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==9}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-archived.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==10}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-active.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==11}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-disabled.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==13}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-locked.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {elseif $newLog.type==14}
                    <img src="resource2/{$opt.template.style}/images/log/16x16-locked-invisible.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" />
                {/if}
            </nobr></td>
            <td>
                {if $newLog.oc_team_comment}<img src="resource2/{$opt.template.style}/images/oclogo/oc-team-comment.png" alt="OC-Team" title="{t}OC team comment{/t}" />{/if}
                {capture name=cachename}
                    <a href="viewcache.php?wp={$newLog.wp_oc}&log=A#log{$newLog.id}">{if $newLog.first}<b>{$newLog.cachename|escape}</b>{else}{$newLog.cachename|escape}{/if}</a>
                    {include file="res_oconly.tpl" oconly=$newLog.oconly}
                {/capture}
                {capture name=username}
                    <a href="viewprofile.php?userid={$newLog.user_id}">{$newLog.username|escape}</a>
                {/capture}

                {if $newLog.type==1}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 found %1{/t}
                {elseif $newLog.type==2}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 didn't find %1{/t}
                {elseif $newLog.type==3}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 wrote a note for %1{/t}
                {elseif $newLog.type==7}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 visited %1{/t}
                {elseif $newLog.type==8}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 want's to visit %1{/t}
                {elseif $newLog.type==9}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has archived %1{/t}
                {elseif $newLog.type==10}
                    {if $newLog.oc_team_comment}
                        {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has activated %1{/t}
                    {else}
                        {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has maintainted %1{/t}
                    {/if}
                {elseif $newLog.type==11}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has disabled %1{/t}
                {elseif $newLog.type==13 || $newLog.type==14}
                    {t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has locked %1{/t}
                {/if}
                {include file="res_logflags.tpl" logItem=$newLog withRecommendation=true}

                {if $newLog.pics}
                    <img src="resource2/ocstyle/images/action/16x16-addimage.png" />
                    {if $newLog.picshown}&rarr;{/if}
                {/if}
            </td>
            {if $newLog.pic_uuid != ""}
                <td rowspan="{$lines_per_pic}">
                    {include file="res_logpicture.tpl" picture=$newLog logdate=false loguser=false nopicshadow=true}
                </td>
                <td></td>
            {/if}
        </tr>
        {assign var='lastCountry' value=$newLog.country_name}
    {foreachelse}
        {if $ownerlogs}
            <p>{t}There are no log entries yet for your geocaches.{/t}</p>
        {/if}
    {/foreach}
    <tr><td class="spacer" style="height:{$addpiclines}em"></td></tr>
</table>

{if $paging && $newLogs|@count > 20}
    <p>
        {include file="res_pager.tpl"}
    </p>
    <br />
{/if}
