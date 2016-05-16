<?php
/****************************************************************************
                                            ./lang/de/ocstyle/newdesc.tpl.php
                                                            -------------------
        begin                : July 7 2004

        For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
         
   Unicode Reminder メモ
                                                                                    
     new cache description
     
     replacements:
        name
        reset
        submit
        cacheid
        langoptions
        show_all_langs_submit
        short_desc
        desc
        hints
     
 ****************************************************************************/
?>

<div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-description.png" style="margin-right: 10px;" width="22" height="22" alt="" />
    {t}Add new cache description to <a href="viewcache.php?cacheid={cacheid}">{name}</a>{/t}
</div>

<form action="newdesc.php" method="post" enctype="application/x-www-form-urlencoded" name="editform" dir="ltr">
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="1" />
<input id="oldDescMode" type="hidden" name="oldDescMode" value="1" />
<input type="hidden" name="scrollposx" value="{scrollposx}" />
<input type="hidden" name="scrollposy" value="{scrollposy}" />
<table class="table">
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>{t}Language:{/t}</td>
        <td>
            <select name="desc_lang">
                <option value="0" {nolangselected}>{t}-- Please select --{/t}</option>
                {langoptions}
            </select>
            {show_all_langs_submit} {lang_message}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td>{t}Short description:{/t}</td>
        <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400" /></td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">{t}Description:{/t}</td>
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
        <td colspan="2">{t}Additional note:{/t}</td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea name="hints" class="hint mceNoEditor">{hints}</textarea>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
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
