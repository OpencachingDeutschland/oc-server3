<?php
/****************************************************************************
                                            ./lang/de/ocstyle/editdesc.tpl.php
                                                            -------------------
        begin                : July 7 2004

        For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
          
   Unicode Reminder メモ
                                                                                   
     edit a cache listing
    
     template replacement(s):
            
            desclang
            desclang_name
            cachename
            reset
            submit
            short_desc
            desc_err
            desc
            hints
            
 ****************************************************************************/
?>


<div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-description.png" style="margin-right: 10px;" width="22" height="22" alt="" />{t}Edit cache description for <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>{/t}</div>

<form name="editform" action="editdesc.php" method="post" enctype="application/x-www-form-urlencoded" id="editcache_form" dir="ltr">
<input type="hidden" name="post" value="1"/>
<input type="hidden" name="descid" value="{descid}"/>
<input type="hidden" name="show_all_langs_value" value="{show_all_langs_value}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<input id="oldDescMode" type="hidden" name="oldDescMode" value="1" />
<input type="hidden" name="scrollposx" value="{scrollposx}" />
<input type="hidden" name="scrollposy" value="{scrollposy}" />
<table class="table">
    <tr>
        <td>{t}Language:{/t}</td>
        <td>
            <select name="desclang">
                {desclangs}
            </select>
            {show_all_langs_submit}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td>{t}Short description:{/t}</td>
        <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400" /></td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td colspan="2">{t}Description:{/t}{desc_err}</td>
    </tr>
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
        <td colspan="2">
            <span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter text only. To use HTML, or the editor, please enable JavaScript.{/t}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea id="desc" class="cachedesc" name="desc" cols="90" rows="25">{desc}</textarea>
    </td>
    </tr>
    {htmlnotice}
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">{t}Encrypted note:{/t}</td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea name="hints" class="mceNoEditor hint" cols="90" rows="10">{hints}</textarea>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">
            {t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td class="header-small" colspan="2">
            <!-- <input type="reset" name="reset" value="{reset}" class="formbutton" onclick="flashbutton('reset')" />&nbsp;&nbsp; -->
            <input type="submit" name="submitform" value="{submit}" class="formbutton" onclick="submitbutton('submitform')" />
        </td>
    </tr>
</table>
</form>

<script type="text/javascript">
<!--
    var descMode = {descMode};
    OcInitEditor();
//-->
</script>
