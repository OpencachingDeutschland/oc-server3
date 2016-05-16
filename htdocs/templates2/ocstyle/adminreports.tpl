{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

<script type="text/javascript">
{literal}

var oldcomment;

function editcomment(edit)
{
    var comment = document.getElementById('comment');
    var editcomment_btn = document.getElementById('editcomment_btn');
    var commenteditor = document.getElementById('commenteditor');
    var canceledit = document.getElementById('canceledit');
    var savecomment = document.getElementById('savecomment');

    if (edit)
    {
        comment.style.display = 'none';
        editcomment_btn.style.display = 'none';

        commenteditor.style.display = '';
        commenteditor.focus();
        commenteditor.selectionStart = commenteditor.selectionEnd = commenteditor.value.length;
        oldcomment = commenteditor.value;

        canceledit.style.display = '';
        savecomment.style.display = '';
    }
    else  // cancel
    {
        resetbutton('canceledit');
        canceledit.style.display = 'none';
        resetbutton('savecomment');
        savecomment.style.display = 'none';
        commenteditor.value = oldcomment;
        commenteditor.style.display = 'none';

        comment.style.display = '';
        editcomment_btn.style.display = '';
    }
}

{/literal}
</script>

{strip}
<form method="POST" action="adminreports.php">
    <input type="hidden" name="rid" value="{$id}" />
    <input type="hidden" name="cacheid" value="{$cacheid}" />
    <input type="hidden" name="ownerid" value="{$ownerid}" />

    <div class="content2-pagetitle">
        <img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="" />
        {t}Reported caches{/t}
    </div>

    <div class="content2-container">
    {if $error == 1}
        <p style="line-height: 1.6em;">{t}The report is already assigned to another admin!{/t}</p>
    {elseif $error == 2}
        <p style="line-height: 1.6em;">{t}The report is already assigned to you!{/t}</p>
    {elseif $error == 3}
        <p style="line-height: 1.6em;">{t}You can not work on this report! Another admin is already pursuing it.{/t}</p>
    {elseif $error == 4}
        <p style="line-height: 1.6em;">{t}To work on a report you have to assign it to you!{/t}</p>
    {/if}

    {if $list == true}
        <table class="narrowtable">
        <tr><th>{t}ID{/t}</th><th>{t}Name{/t}</th><th>{t}Owner{/t}</th><th>{t}Reporter{/t}</th><th>{t}Date{/t}</th></tr>
        {assign var="otheradmins" value=0}
        {foreach from=$reportedcaches item=rc}
            <tr>
            {if $rc.otheradmin > $otheradmins}
                <td colspan="5"><p style="line-height: 2.5em;">{t}(*) New reports{/t}</p>
                </td></tr>
                <tr><th>{t}ID{/t}</th><th>{t}Name{/t}</th><th>{t}Owner{/t}</th><th>{t}Reporter{/t}</th><th>{t}Admin{/t}</th><th>{t}Date{/t}</th></tr>
                {assign var="otheradmins" value=$rc.otheradmin}
            {/if}
            <td><a href="adminreports.php?id={$rc.id}">{$rc.id}</td>
            <td><a href="adminreports.php?id={$rc.id}">{$rc.new|escape}{$rc.name|escape}</a></td>
            <td>{$rc.ownernick|escape}</td>
            <td>{$rc.username|escape}</td>
            {if $otheradmins}
                <td>{$rc.adminname|escape}</td>
            {/if}
            <td style="white-space: nowrap;">{$rc.lastmodified|date_format:$opt.format.date}</td></tr>
        {foreachelse}
            <tr><td colspan=5>{t}No reported caches{/t}</td></tr>
        {/foreach}
        </table>
        {if $reportedcaches != NULL and $otheradmins==0}
            <p style="line-height: 2.5m;">{t}(*) New reports{/t}</p>
        {/if}
    {else}
        <table class="table" style="width:98%">
            <tr>
                <td colspan="4">
                    <p>{t}Details for report of {/t} <a href="viewcache.php?cacheid={$cacheid}" target="_blank">{$cachename|escape}</a> {t} by {/t} <a href="viewprofile.php?userid={$userid}" target="_blank">{$usernick|escape}</a>
                    &nbsp; &nbsp;
                    [<a href="http://www.geocaching.com/seek/nearest.aspx?t=k&origin_lat={$cache.latitude}&amp;origin_long={$cache.longitude}&amp;dist=1&amp;submit3=Search" target="_blank">{t}Nearby search at geocaching.com{/t}</a>]
                    &nbsp; &nbsp;
                    {foreach from=$cachexternal key=extname item=exturl}
                        [<a href="{$exturl|replace:_cache_id_:$cacheid}" target="_blank">{$extname}</a>] &nbsp;
                    {/foreach}
                    {$external_maintainer_msg}
                    </p>
                </td>
            </tr>
            <tr>
                <td style="width:10px"><nobr><b>{t}Created at:{/t}</b></nobr></td>
                <td style="width:250px">{$created|date_format:$opt.format.datelong}</td>
                {if $lastmodified != $created}
                    <td style="width:10px"><nobr><b>{t}Last modified{/t}{t}#colonspace#{/t}:</b></nobr></td>
                    <td style="width:440px">{$lastmodified|date_format:$opt.format.datelong}</td>
                {else}
                    <td style="width:10px"><nobr><b>{t}State:{/t}</b></nobr></td>
                    <td style="width:440px">{$status}</td>
                {/if}
            </tr>
            <tr>
                <td><b>{t}Reason:{/t}</b></td>
                <td>{$reason|escape|nl2br}</td>
                <td>{if $lastmodified != $created}<nobr><b>{t}State:{/t}</b></nobr>{/if}</td>
                <td>{if $lastmodified != $created}{$status}{if $adminnick!=''} &nbsp;&nbsp; <b>Admin</b>{t}#colonspace#{/t}: {if $otheradmin}<font color="red"><b>{/if}{$adminnick|escape}{if $otheradmin}</b></font>{/if}{/if}{/if}</td>
            </tr>
            <tr><td class="spacer"></td></tr>
            <tr>
                <td style="vertical-align:text-top; padding-top:0.6em"><b><p>{t}Comment:{/t}</p></td>
                <td colspan="3" style="padding-top: 0.6em"><p>{$note}</p></td>
            </tr>
            <tr><td class="spacer"></td></tr>
            <tr>
                <td style="vertical-align:text-top"><b><p>{t}Admin<br />comment:{/t}</p></b></td>
                <td colspan="4">
                    <p id="comment">{$admin_comment|escape|nl2br}</p>
                    <span id="editcomment_btn">[<a href="javascript:editcomment(true)">{if $admin_comment}{t}Edit{/t}{else}{t}Add{/t}{/if}</a>]</span>
                    <textarea id="commenteditor" name="commenteditor" cols="90" rows="7" style="margin-bottom:1em; display:none" class="default">{$admin_comment|escape}</textarea>
                    <br />
                    <input id="canceledit" type="button" name="canceledit" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('canceledit'); editcomment(false)" style="display:none" />
                    &nbsp; &nbsp;
                    <input id="savecomment" type="submit" name="savecomment" value="{t}Save{/t}" class="formbutton" onclick="submitbutton('savecomment')" style="display:none" />
                </td>
            </tr>
            <tr><td class="spacer"></td></tr>
            <tr><td class="spacer"></td></tr>
        </table>

        <div class="content2-container bg-blue02">
          <p class="content-title-noshade-size2">
                <img src="resource2/{$opt.template.style}/images/description/22x22-misc.png" style="margin-right: 10px;" width="22" height="22" alt="" />
              {t}Action{/t}
          </p>
      </div>

        <p style="line-height: 1.6em; margin-bottom:24px">
        {if !$ownreport}
            <input type="submit" name="assign" value="{t}Assign to me{/t}" class="formbutton" onclick="submitbutton('assign')" />
        {else}
            &nbsp;<input type="submit" name="contact" value="{t}Contact owner{/t}" class="formbutton" onclick="submitbutton('contact')" />&nbsp;&nbsp;<input type="submit" name="contact_reporter" value="{t}Contact reporter{/t}" class="formbutton" onclick="submitbutton('contact_reporter')" />&nbsp;&nbsp;<input type="submit" name="done" value="{t}Mark as finished{/t}" class="formbutton" onclick="submitbutton('done')" />
            </p>

            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size2">
                    <img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" style="margin-right: 10px;" width="22" height="22" alt="" />
                    {t}Set state{/t}
                </p>
            </div>

            <p style="line-height: 1.6em;">
                <a href="log.php?cacheid={$cacheid}&logtype=10&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-active.png" />{t}Available{/t}</a>
                &nbsp; &nbsp;
                <a href="log.php?cacheid={$cacheid}&logtype=11&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-disabled.png" />{t}Temporarily not available{/t}</a>
                &nbsp; &nbsp;
                <a href="log.php?cacheid={$cacheid}&logtype=9&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-archived.png" />{t}Archived{/t}</a>
                &nbsp; &nbsp;
                <a href="log.php?cacheid={$cacheid}&logtype=13&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-locked.png" />{t}Locked, visible{/t}</a>
                &nbsp; &nbsp;
                <a href="log.php?cacheid={$cacheid}&logtype=14&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-locked-invisible.png" />{t}Locked, invisible{/t}</a>
            </p>
            {if $otheradmin}
                </p><br />{t}Warning: This report is already assigned to another admin. Consult him first before you assume the report!{/t}
            {/if}
        {/if}
        <br />

        {include file=adminhistory.tpl reportdisplay=true showhistory=true}
    {/if}
    </div>

</form>
{/strip}
