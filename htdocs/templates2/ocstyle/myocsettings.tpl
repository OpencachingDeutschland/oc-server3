{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="" />
    {t}My OC-website settings{/t}
</div>

{include file="settingsmenu.tpl"}

<form action="myocsettings.php" method="post" style="display:inline;">
    <input type="hidden" name="action" value="change" />

    <table class="table">
        <tr>
            <td colspan="3">
                <span class="boldtext">{t}Your settings for various OC-website-features:{/t}</span>
            </td>
        </tr>

        {if $error==true || $errorlen==true}
            <tr>
                <td class="errormsg" colspan="3">
                    {t}Error while saving.{/t}<br />
                    {if $error==true}{t}Illegal characters found in{/t}{$errormsg|escape}<br />{/if}
                    {if $errorlen==true}{t}Field values too long in{/t}{$errormsglen|escape}<br />{/if}
                    {t}Original values were restored.{/t}
                </td>
            </tr>
        {/if}
        <tr><td class="spacer" colspan="3"></td></tr>

        {include file="displayuseroptions.tpl" dUseroptions=$useroptions4}

        <tr>
            <td valign="top">{t}Auto-Logout:{/t}
            <td valign="top">
                {if $edit==true}
                    <input type="checkbox" name="permanentLogin" value="1" {if $permanentLogin==true}checked="checked"{/if} id="l_using_permanent_login" class="checkbox" />
                    <label for="l_using_permanent_login">{t}Don't log me out after 15 minutes inaktivity.{/t}</label><br/>
                    <div style="padding-left:25px;">
                        <img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
                        <span style="color:red; font-size:10px; line-height:1.3em">{t}Attention: If you are using this option, don't forget to log out before other persons can use your computer.
                        Otherwise, they can use and modify your personal data.{/t}</span>
                    </div>
                {else}
                    {if $permanentLogin==true}
                        {t}Don't log me out after 15 minutes inaktivity.{/t}
                    {else}
                        {t}Log me out after 15 minutes inaktivity.{/t}
                    {/if}
                {/if}
            </td>
        </tr>
        <tr>
            <td valign=top>{t}Texteditor:{/t}</td>
            <td>
                {if $edit==true}
                    <input type="checkbox" name="noHTMLEditor" value="1" {if $noHTMLEditor==true}checked="checked"{/if} id="l_no_htmledit" class="checkbox" />
                    <label for="l_no_htmledit">{t}Use the plain editor by default.{/t}</label>
                    <br />
                {else}
                    {if $noHTMLEditor}
                        {t}Use the plain editor by default.{/t}
                    {else}
                        {t}Use an HTML editor by default.{/t}
                    {/if}
                {/if}
            </td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>

        {if $errorUnknown==true}
            <tr>
                <td colspan="2">
                    <span class="errormsg">{t}An unknown error occured.{/t}</span>
                </td>
            </tr>
        {/if}

        <tr>
            <td class="header-small" colspan="3">
                {if $edit==true}
                    <input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp;
                    <input type="submit" name="save" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('save')" />
                {else}
                    <input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('change')" />
                {/if}
            </td>
        </tr>

        <tr><td class="spacer" colspan="3"></td></tr>

        <tr>
            <td colspan="3">
                <span class="boldtext">{t}You can edit your map settings(<img src="resource2/ocstyle/images/map/35x35-configure.png" width="16px">) on the <a href="map2.php">map</a> directly.{/t}</span> <!-- TODO: Translation -->
            </td>
        </tr>
    </table>

</form>