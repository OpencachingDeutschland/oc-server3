{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_balloon.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_centerwindow.js"></script>
<script type="text/javascript">
<!--
var cache_needs_maintenance = {$cache_needs_maintenance + 0};
var cachetype = {$cachetype};
var logtype_allows_nm = [{$logtype_allows_nm}];
var tip_general_nm = "{t}Select <i>needs maintenance</i> if the geocache was in poor condition at the<br />specified date and in urgent need of maintenance. Please explain why.{/t}<br /><div style='height:0.3em'></div>{t}Select <i>ok</i> if you have found or checked the cache and everything is ok.{/t}";
var tip_general_lo = "{if $gcwp}{t}Select <i>is outdated</i> if the geocache search is hampered by outdated information<br />in the description, e.g. the location has severely changed or the description lacks<br />important information which has been added at another geocaching website.<br />Please give details in your log.{/t}{else}{t}Select <i>is outdated</i> if the geocache search is hampered by outdated information<br />in the description, e.g. because the location has severely changed. Please give<br />details in your log.{/t}{/if}{if $ownerlog || $cache_listing_is_outdated}<br /><div style='height:0.3em'></div>{if $gcwp}{t 1=$gcwp}Select <i>up to date</i> if you have checked the complete description &ndash; from the<br />container size to encoded hints and additoional wayoints &ndash;, have compared<br />it to the geocaching.com listing (%1) and can confirm that everything is<br />up-to-date.{/t}{else}{t}Select <i>up to date</i> if you have checked the complete description &ndash; from the<br />container size to encoded hints and additoional wayoints &ndash; and can confirm<br />that everything is up-to-date.{/t}{/if}{/if}";
var tip_activate_nm = '{t}By logging "Available", you also confirm that the geocache is in good condition.{/t}';
var tip_activate_lo = '{t}By logging "Available", you also confirm that the geocache description is up-to-date.{/t}';
var tip_disable_nm = "{t}You may indicate here what is the current maintenance state of the geocache.{/t}";
var tip_disable_lo = "{t}You may indicate here if the cache description is up-to-date.{/t}";
var tip_dnf_nm = "{t}If you are sure that the geocache is gone, and the owner does not<br />react to your log entry, you may report it to the Opencaching team.<br />Use the 'Report this cache' button above the cache description.{/t}";

var cache_listing_is_outdated = {$cache_listing_is_outdated} + 0;
var ownerlog = {$ownerlog} + 0;
var dnf_by_logger = {$dnf_by_logger+0} && !ownerlog;

{literal}

function insertSmiley(smileySymbol, smileyPath)
{
  var myText = document.editform.logtext;
  var insertText = (descMode == 1 ? smileySymbol : '<img src="' + smileyPath + '" alt="" border="0" width="18px" height="18px" />');
  myText.focus();

  /* for IE and Webkit */
  if(typeof document.selection != 'undefined') {
    var range = document.selection.createRange();
    var selText = range.text;
    range.text = insertText + selText;
  }
  /* for Firefox/Mozilla */
  else if (typeof myText.selectionStart != 'undefined')
  {
    var start = myText.selectionStart;
    var end = myText.selectionEnd;
    var selText = myText.value.substring(start, end);
    myText.value = myText.value.substr(0, start) + insertText + selText + myText.value.substr(end);
    /* Cursorposition hinter Smiley setzen */
    myText.selectionStart = start + insertText.length;
    myText.selectionEnd = start + insertText.length;
  }
  /* other Browsers */
  else
  {
    alert(navigator.appName + ": {/literal}{t}Setting smilies is not supported{/t}{literal}");
  }
}

function logtype_changed()
{
    {/literal}
    var logtype = parseInt(document.editform.logtype.value);
    var datecomment = document.getElementById('datecomment');
    var hint = '<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" />';

    if (logtype == 1)
        datecomment.innerHTML = hint + "{t}When did you find the geocache?{/t}";
    else if (logtype == 2)
        datecomment.innerHTML = hint + "{t}When did you abort the cache search?{/t}";
    else
        datecomment.innerHTML = "";
    {literal}

    if (logtype == 1 || logtype == 7)
    {
        if (document.editform.rating)
            document.editform.rating.disabled = false;
    }
    else
    {
        if (document.editform.rating)
            document.editform.rating.disabled = true;
    }

    var condition_logging = false;
    if (cachetype != 6 && logtype_allows_nm.indexOf(logtype) >= 0)
    {
        document.getElementById('cache_condition').style.display = '';
        document.getElementById('cache_condition_spacer').style.display = '';
        condition_logging = true;
    }
    else
    {
        document.getElementById('cache_condition').style.display = 'none';
        document.getElementById('cache_condition_spacer').style.display = 'none';
    }

    var new_logtype = parseInt(document.editform.logtype.value);
    var nm = document.getElementById('needs_maintenance');
    var lo = document.getElementById('listing_outdated');
    var confirm_Lo = document.getElementById('confirm_listing_ok');

    if (((new_logtype == 2) != (old_logtype == 2)) ||
        (dnf_by_logger && (new_logtype == 3) != (old_logtype == 3)))
    {
        nm.value = "0";
        var nmdisable = !ownerlog && ((new_logtype == 2) || (new_logtype == 3 && dnf_by_logger));
        nm.disabled = nmdisable;
        nm.className = (nmdisable ? 'disabled' : '');

        lo.value = "0";
        lo.disabled = (!ownerlog && new_logtype == 2);
        lo.className = (!ownerlog && new_logtype == 2 ? 'disabled' : '');
    }

    if ((new_logtype == 10) != (old_logtype == 10))
    {
        nm.value = (old_logtype == 10 ? "0" : "1");
        nm.disabled = (new_logtype == 10);
        nm.className = (new_logtype == 10 ? 'disabled' : '');

        lo.value = (old_logtype == 10 ? "0" : "1");
        lo.disabled = (new_logtype == 10);
        lo.className = (new_logtype == 10 ? 'disabled' : '');
        confirm_Lo.value = (new_logtype == 10 ? "1" : "0");
    }

    old_logtype = new_logtype;

    // This allows us to post also disabled fields' values:
    document.getElementById('needs_maintenance2').value = nm.value;
    document.getElementById('listing_outdated2').value = lo.value;

    var clo_spacer = document.getElementById('confirm_listing_ok_spacer');
    var clo_row = document.getElementById('confirm_listing_ok_row');

    if (!condition_logging || lo.value != 1 || ownerlog || !cache_listing_is_outdated)
    {
        clo_spacer.style.display = 'none';
        clo_row.style.display = 'none';
    }
    else
    {
        clo_spacer.style.display = '';
        clo_row.style.display = '';
    }

    return false;
}

function show_nm_tip()
{
    var logtype = document.editform.logtype.value;
    if (logtype == "10")
        show_tip(tip_activate_nm);
    else if (logtype == "11")
        show_tip(tip_disable_nm);
    else if (!ownerlog && (logtype == "2" || (dnf_by_logger && logtype == 3)))
        show_tip(tip_dnf_nm);
    else
        show_tip(tip_general_nm);
}

function show_lo_tip()
{
    var logtype = document.editform.logtype.value;
    if (logtype == "10")
        show_tip(tip_activate_lo);
    else if (logtype == "11")
        show_tip(tip_disable_lo);
    else if (logtype != "2" || ownerlog)
        show_tip(tip_general_lo);
}

function show_tip(text)
{
    Tip(text, DELAY, 0, FADEIN, false, FADEOUT, false, BGCOLOR, "#fffedf", BORDERCOLOR, "grey");
}

//-->
{/literal}
{*
 * capture allows us to "eval" the link tag with the variable values
 * and save the complete link in the variable "cachelink" to use it in translation
 *}
{capture name=cachelink assign=cachelink}<a href="viewcache.php?cacheid={$cacheid}">{$cachename|escape}</a>{/capture}
</script>

<div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" style="margin-right: 10px;" width="22" height="22" alt="" />
    {t 1=$cachelink}Add log-entry for the cache %1{/t}
</div>
<form action="log.php" method="post" enctype="application/x-www-form-urlencoded" name="editform" dir="ltr">
{if $masslog==true}
<p class="redtext">
    {t 1=$masslogCount}You submitted more than %1 identical logs. Please make sure that you are entering the date of your cache visit, not the current date - also when "late logging" old finds.{/t}
</p>
<p>
    {t}Wrong log dates can impair several OC functions like searching by last log date. Also, the owner and other caches may think that the cache has been currently found (date and type of the last log are shown in the owner's caches list!), which can adversely affect cache maintenance and lead to more DNFs.{/t}
</p>
<p class="spacer_before">
    <input type="checkbox" name="suppressMasslogWarning" value="1" class="checkbox" id="suppressMasslogWarning" /> <label for="suppressMasslogWarning">{t}I know what I am doing, do not show this advice again today.{/t}</label>
</p>
{/if}
{if $showstatfounds==true}
<p class="align-right">
    <b>{t 1=$userFound}You found %1 caches until now.{/t}</b>
</p>
{/if}
<input type="hidden" name="cacheid" value="{$cacheid}"/>
<input type="hidden" name="version3" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<input id="oldDescMode" type="hidden" name="oldDescMode" value="1" />
<input type="hidden" name="scrollposx" value="{$scrollposx}" />
<input type="hidden" name="scrollposy" value="{$scrollposy}" />
<input type="hidden" id="needs_maintenance2" name="needs_maintenance2" value="0" />
<input type="hidden" id="listing_outdated2" name="listing_outdated2" value="0" />
<table class="table">
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr>
        <td width="180px">{t}Type of log-entry:{/t}</td>
        <td>
            <select name="logtype" onChange="return logtype_changed()">
                {foreach from=$logtypes item=logtypeoption}
                <option value="{$logtypeoption.id}"{if $logtypeoption.selected} selected="selected"{/if}>{$logtypeoption.name|escape}</option>
                {/foreach}
            </select>
            {if $octeamcommentallowed}
            &nbsp; <input type="checkbox" name="teamcomment" value="1" class="checkbox" {if $octeamcomment}checked{/if} id="teamcomment" /> <label for="teamcomment"><span class="{$octeamcommentclass}">{t}OC team comment{/t}</span></label>
            {/if}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td width="180px">{t}Date / time:{/t}</td>
        <td>
            <input class="input20" type="text" id="logday" name="logday" maxlength="2" value="{$logday}" />.
            <input class="input20" type="text" id="logmonth" name="logmonth" maxlength="2" value="{$logmonth}" />.
            <input class="input40" type="text" id="logyear" name="logyear" maxlength="4" value="{$logyear}" />
            &nbsp;&nbsp;&nbsp;
            <input class="input20" type="text" id="loghour" name="loghour" maxlength="2" value="{$loghour}" /> :
            <input class="input20" type="text" id="logminute" name="logminute" maxlength="2" value="{$logminute}" />
            &nbsp;&nbsp;&nbsp; <span id="datecomment"></span>
            {if $validate.dateOk==false}<br /><span class="errormsg">{t}date or time is invalid{/t}</span>{/if}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr id="cache_condition">
        <td>{t}Geocache condition:{/t}</td>
        <td>
            <span id="nmtip" onmouseover='show_nm_tip()' onmouseout="UnTip()">
            <select id="needs_maintenance" name="needs_maintenance" onchange="logtype_changed()">
                <option value="0" {if $needs_maintenance==0}selected="selected"{/if}>{t}not specified{/t}</option>
                <option value="2" {if $needs_maintenance==2}selected="selected"{/if}>{t}needs maintenance{/t}</option>
                <option value="1" {if $needs_maintenance==1}selected="selected"{/if}>{t}ok{/t}</option>
            </select>
            </span>
            &nbsp; &nbsp; &nbsp; &nbsp;
            {t}Description:{/t}&nbsp;
            <span id="lotip" onmouseover='show_lo_tip()' onmouseout="UnTip()">
            <select id="listing_outdated" name="listing_outdated" onchange="logtype_changed()">
                <option value="0" {if $listing_outdated==0}selected="selected"{/if}>{t}not specified{/t}</option>
                <option value="2" {if $listing_outdated==2}selected="selected"{/if}>{t}outdated{/t}</option>
                {if $ownerlog || $cache_listing_is_outdated}<option value="1" {if $listing_outdated==1}selected="selected"{/if}>{t}up to date{/t}</option>{/if}
            </select>
            </span>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2" id="confirm_listing_ok_spacer" style="display:none"></td></tr>
    <tr id="confirm_listing_ok_row" style="display:none">
        <td style="vertical-align:top">
            {if $validate.confirmListingOk===false}<span class="errormsg">{t}Please confirm:{/t}</span>{/if}
        </td>
        <td>
            <input type="checkbox" id="confirm_listing_ok" name="confirm_listing_ok" value="1" class="checkbox" {if $ownerlog}checked{/if}/> <label for="confirm_listing_ok">{t 1=$cache_listing_outdated_log}The problems of the cache description as mentioned in the <a href="%1" target="_blank"><img src="resource2/ocstyle/images/log/16x16-listing-outdated.png" /> log entries</a> do no longer exist.{/t} {if $gcwp}{t}All information (coordinates, container size, difficulty, terrain, description text, encoded hints, additional waypoints) is at least up-to-date with{/t} <a href="http://www.geocaching.com/seek/cache_details.aspx?wp={$gcwp}" target="_blank">{$gcwp}</a>.{/if}
        </td>
    </tr>
    <tr id="cache_condition_spacer"><td class="spacer" colspan="2"></td></tr>
    {if $isowner==false}
    <tr>
        <td valign="top">{t}Recommendations:{/t}</td>
        <td valign="top">
            {if ($ratingallowed==true || $israted==true)}<input type="hidden" name="ratingoption" value="1"><input type="checkbox" id="rating" name="rating" value="1" class="checkbox" {if $israted==true}checked{/if}/>&nbsp;<label for="rating">{t}This cache is one of my recommendations.{/t}</label><br />
                {t 1=$givenratings 2=$maxratings}You have given %1 of %2 possible recommendations.{/t}
            {else}
                {t 1=$foundsuntilnextrating}You need additional %1 finds, to make another recommendation.{/t}
                {if ($givenratings > 0 && $givenratings==$maxratings && $israted==false)}<br />{t}Alternatively, you can withdraw a <a href="mytop5.php">existing recommendation</a>.{/t}{/if}
            {/if}
            <noscript><br />{t}A recommendation can only be made with a "found" or "attended" log!{/t}</noscript>
        </td>
    </tr>
    {/if}
</table>

<table class="table">
    <tr>
        <td colspan="2">
            <div class="menuBar">
                <span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">{t}Editor{/t}</span>
                <span class="buttonSplitter">|</span>
                <span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">{t}&lt;html&gt;{/t}</span>
                <span class="buttonSplitter">|</span>
                <span id="descText" class="buttonNormal" onclick="btnSelect(1)" onmouseover="btnMouseOver(1)" onmouseout="btnMouseOut(1)">{t}Text{/t}</span>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter text only. To use HTML, or the editor, please enable JavaScript.{/t}</span>
        </td>
    </tr>
    <tr>
        <td>
            <textarea name="logtext" id="logtext" cols="68" rows="15" class="logs">{$logtext|escape}</textarea>
    </td>
    </tr>
    {if $descMode!=3}
    <tr>
        <td colspan="2">
            {strip}
            {foreach from=$smilies item=smiley}
                {if $smiley.show}
                    <a href="javascript:insertSmiley('{$smiley.text}','{$smileypath}{$smiley.file}')">{$smiley.image}</a> &nbsp;
                {/if}
            {/foreach}
            {/strip}
        </td>
    </tr>
    {/if}
    <tr><td class="spacer" colspan="2"></td></tr>
    {if $logpw}
    <tr>
        <td colspan="2">{t}passwort to log:{/t}
            <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> {if !$validate.logPw}<span class="errormsg">{t}Invalid password!{/t}</span>{else}({if $cachetype==6}{t}only for attended-logs{/t}{else}{t}only for found logs{/t}{/if}){/if}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {/if}
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">
            {t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td class="header-small" colspan="2">
            <input type="submit" name="submitform" value="{t}Log this cache{/t}" class="formbutton" onclick="submitbutton('submitform')" />
        </td>
    </tr>
</table>
</form>

<script type="text/javascript">
<!--
    var descMode = {$descMode};
    OcInitEditor();
    var old_logtype = parseInt(document.editform.logtype.value);
    logtype_changed();
//-->
</script>
