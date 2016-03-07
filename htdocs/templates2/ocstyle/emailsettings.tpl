{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
    {t}My email settings{/t} <!-- TODO: Translation -->
</div>

{include file="settingsmenu.tpl"}

<form action="emailsettings.php" method="post" style="display:inline;">
    <input type="hidden" name="action" value="change" />
    <input type="hidden" name="showAllCountries" value="{$showAllCountries}" />
    <table class="table">
        <tr>
            <td colspan="3">
                <span class="boldtext">{t}Your E-Mail settings:{/t}</span> <!-- TODO: Translation -->
            </td>
        </tr>
        <tr>
            <td valign=top >{t}Notifications:{/t}</td>
            <td>
                {if $edit==true}
                    {capture name=inputfield}
                        <input type="text" name="notifyRadius" maxlength="3" value="{$notifyRadius|escape}" class="input30" />
                    {/capture}
                    {t 1=$smarty.capture.inputfield}I want to be notified about new Geocaches within an radius of %1 km.{/t}
                    {if $notifyRadiusError==true}
                        <span class="errormsg">{t}The entered radius is not valid.{/t}</span>
                    {/if}<br />
                    <img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
                    <span style="font-size:10px;">
                        {t}The notification radius must be not more than 150 km. To deaktivate notifications, set the radius to 0.{/t}
                    </span>
                    <br />
                    <input type="checkbox" name="notifyOconly" value="1" class="checkbox" {if $notifyOconly}checked="checked"{/if} id="notifyOconly" />
                    <label for="notifyOconly">{t 1=$oconly_helpstart 2=$oconly_helpend}Also notify about newly marked %1OConly%2 caches.{/t}</label>
                {else}
                    {if $notifyRadius>0}
                        {t 1=$notifyRadius|escape}Notify about new Geocaches in a radius of %1 km.{/t}
                        <br />
                        {if $notifyOconly}
                            {t 1=$oconly_helpstart 2=$oconly_helpend}Notify about newly marked %1OConly%2 caches.{/t}
                        {else}
                            {t 1=$oconly_helpstart 2=$oconly_helpend}Do not notify about newly marked %1OConly%2 caches.{/t}
                        {/if}
                    {else}
                        {t}Do not notify about new Geocaches.{/t}
                    {/if}
                {/if}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td valign="top">{t}Newsletter:{/t}</td>
            <td valign="top">
                {if $edit==true}
                    <input type="checkbox" name="accMailing" value="1" {if $accMailing}checked="checked"{/if} id="acc_Mailing" class="checkbox" />
                    <label for="acc_Mailing">{t 1=$opt.page.sitename}Please send me mailings about news and campaigns on %1. (max. 2-5 per year){/t}</label>
                    <br />
                {else}
                    {if $accMailing==true}
                        {t 1=$opt.page.sitename}Yes, I want to recieve mailings about news and campaigns on %1. (max. 2-5 per year){/t}<br />
                    {else}
                        {t 1=$opt.page.sitename}No, I dont't want any mailings about news and campaigns on %1.{/t}
                    {/if}
                {/if}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td valign="top">{t}Contact Form:{/t}</td> <!-- TODO: Translation -->
            <td valign="top">
                {if $edit==true}
                    <input type="checkbox" name="sendUsermailAddress" value="1" {if $sendUsermailAddress==true}checked="checked"{/if} id="l_send_usermail_address" class="checkbox" />
                    <label for="l_send_usermail_address">{t}Disclose my e-mail address by default when sending e-mails to other users.{/t}</label>
                    <br />
                {else}
                    {if $sendUsermailAddress}
                        {t}Disclose my e-mail address by default when sending e-mails to other users.{/t}
                    {else}
                        {t}Don't disclose my e-mail address by default when sending e-mails to other users.{/t}
                    {/if}
                {/if}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        {if $errorUnknown==true}
            <tr>
                <td colspan="2">
                    <span class="errormsg">{t}An unknown error occured.{/t}</span>
                </td>
            </tr>
        {/if}

        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td class="header-small" colspan="2">
                {if $edit==false}
                    <input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('change')" />
                {else}
                    <input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="flashbutton('cancel')" />&nbsp;&nbsp;
                    <input type="submit" name="save" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('save')" />
                {/if}
            </td>
        </tr>
    </table>
</form>

{if $edit==false}
    <form action="emailsettings.php" method="post" style="display:inline;">
        <input type="hidden" name="action" value="changeemail" />
        <table class="table">
            <tr><td class="spacer" colspan="2">&nbsp;</td></tr>
            <tr>
                <td colspan="3">
                    <span class="boldtext">{t}Your E-Mail adress:{/t}</span> <!-- TODO: Translation -->
                </td>
            </tr>
            <tr>
                <td>{t}E-Mail-Address:{/t}</td>
                <td>{$email|escape}</td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>
            <tr>
                <td class="header-small" colspan="2">
                    {if $edit==false}
                        <input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('change')" />
                    {/if}
                </td>
            </tr>
        </table>
    </form>
{/if}
