{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

<script type="text/javascript">
<!--

{literal}

function insertSmiley(smileySymbol, smileyPath)
{
  var myText = document.editform.logtext;
  var insertText = (descMode == 1 ? smileySymbol : '<img src="' + smileyPath + '" alt="" border="0" width="18px" height="18px" />');
  myText.focus();

  /* for IE and Webkit */
  if (typeof document.selection != 'undefined') {
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

function _chkFound () {
  if(document.editform.rating) {
      if (document.editform.logtype.value == "1" || document.editform.logtype.value == "7") {
        document.editform.rating.disabled = false;
      }
      else
      {
        document.editform.rating.disabled = true;
      }
  }
  return false;
}

{/literal}
//-->
</script>

{capture name=cachelink assign=cachelink}<a href="viewcache.php?cacheid={$cacheid}">{$cachename|escape}</a>{/capture}

<div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-logs.png" style="margin-right: 10px;" width="22" height="22" alt="" />{t 1=$cachelink}Edit log entry for the cache %1{/t}</div>

<form action="editlog.php" method="post" enctype="application/x-www-form-urlencoded" name="editform" dir="ltr">
<input type="hidden" name="logid" value="{$logid}"/>
<input type="hidden" name="version2" value="1"/>
<input id="oldDescMode" type="hidden" name="oldDescMode" value="1" />
<input id="descMode" type="hidden" name="descMode" value="1" />
<input type="hidden" name="scrollposx" value="{$scrollposx}" />
<input type="hidden" name="scrollposy" value="{$scrollposy}" />

<table class="table">
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td width="180px">{t}Type of log-enrty{/t}</td>
        <td align="left">
            <select name="logtype" onChange="return _chkFound()" {if $type_edit_disabled}disabled class="disabled"{/if}>
                {$logtypeoptions}
            </select>
            {if $teamcommentoption}
                &nbsp;
                <input type="checkbox" name="teamcomment" value="1" class="checkbox" {if $is_teamcomment}checked{/if} id="teamcomment" />
                <label for="teamcomment">{t}OC team comment{/t}</label>
            {/if}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td width="180px">{t}Date / time:{/t}</td>
        <td align="left">
            <input class="input20" type="text" name="logday" maxlength="2" value="{$logday}"/>.
            <input class="input20" type="text" name="logmonth" maxlength="2" value="{$logmonth}"/>.
            <input class="input40" type="text" name="logyear" maxlength="4" value="{$logyear}"/>
            &nbsp;&nbsp;&nbsp;
            <input class="input20" type="text" name="loghour" maxlength="2" value="{$loghour}" /> :
            <input class="input20" type="text" name="logminute" maxlength="2" value="{$logminute}" />
            &nbsp;&nbsp;{if !$date_ok}<span class="errormsg">{t}date or time is invalid{/t}</span>{/if}
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <img src="lang/de/ocstyle/images/misc/hint.gif" border="0" width="15" height="11" />
            {t}For 'Found' and 'Not found' logs: Date and (optional) time of the cache search.{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
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
    <tr><td class="spacer" colspan="2"></td></tr>
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
            <textarea name="logtext" id="logtext" cols="68" rows="25" class="logs" >{$logtext}</textarea>
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
        {if $use_log_pw}
            <tr>
                <td colspan="2">
                    {t}passwort to log:{/t}
                    <input class="input100" type="text" name="log_pw" maxlength="20" value="" />
                    ({t}only for found logs{/t})
                    {if $wrong_log_pw}
                        &nbsp; <span class="errormsg">{t}Invalid password!{/t}</span>
                    {/if}
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
            <input type="submit" name="submitform" value="{t}Save{/t}" class="formbutton" onclick="submitbutton('submitform')" />
        </td>
    </tr>
</table>
</form>

<script type="text/javascript">
<!--
    var descMode = {$descMode};
    OcInitEditor();
    _chkFound();
//-->
</script>
