{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/misc/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
    {t}Login{/t}
</div>

<div class="systemlink">
<form action="{$opt.page.login_url}" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;">
    <input type="hidden" name="target" value="{$target|escape}" />
    <input type="hidden" name="action" value="login" />
    <div class="content-txtbox-noshade">
        <p style="line-height: 1.6em;" {if $error!=LOGIN_OK && $error!=LOGIN_LOGOUT_OK}class="errormsg"{/if}>
            {if $error!=LOGIN_OK}
                {if $error==LOGIN_BADUSERPW}
                    {t}The login was not successfull.{/t}<br />
                    {t}The entered username or password did not match.{/t}<br />
                {elseif $error==LOGIN_TOOMUCHLOGINS}
                    {t}The login was not successfull.{/t}<br />
                    {t 1=$opt.page.max_logins_per_hour}Your tried to login more than %1 times in the last hour. The next login will not be allowed before one hour since the first try has passed.{/t}<br />
                {elseif $error==LOGIN_USERNOTACTIVE}
                    {t}Your account is not acivated.{/t}<br />
                    {t}&gt;<a href="activation.php">Here</a>&lt; you can activate your account.{/t}<br />
                {elseif $error==LOGIN_EMPTY_USERPASSWORD}
                    {t}Please fill in username and password!{/t}
                {elseif $error==LOGIN_LOGOUT_OK}
                    {t}Logout was successful.{/t}
                {else}
                    {t}The login was not successfull.{/t}<br />
                    {t 1=$opt.mail.contact}If this problem persists over a longer time, please contact us at <a href="mailto:%1">%1</a>.{/t}<br />
                {/if}
            {else}
                {t}Please login to continue:{/t}
            {/if}
        </p>
        <div class="buffer" style="width: 500px;">&nbsp;</div>
    </div>

    <table class="table">
        <tr>
            <td>{t}Username:{/t}</td>
            <td><input name="email" maxlength="80" type="text" value="{$username}" class="input200" /></td>
        </tr>
        <tr>
            <td>{t}Password:{/t}</td>
            <td><input name="password" maxlength="60" type="password" value="" class="input200" /></td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td></td>
            <td class="header-small">
                <!-- <input type="reset" name="reset" value="{t}Reset{/t}" class="formbutton" onclick="flashbutton('reset')" />&nbsp;&nbsp; -->
                <input type="submit" name="submit" value="{t}Login{/t}" class="formbutton" onclick="submitbutton('submit')" />
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
    </table>
</form>

<div class="content-txtbox-noshade">
    <p style="line-height: 1.6em;">
        <br />
        {t}Not registered?{/t} &nbsp;&rarr;&nbsp; <a href="register.php">{t}Register{/t}</a><br />
        {t}Forgotten your password?{/t} &nbsp;&rarr;&nbsp; <a href="newpw.php">{t}Create a new password{/t}</a><br />
        {t}Forgotten your E-Mail-Address?{/t} &nbsp;&rarr;&nbsp; <a href="remindemail.php">{t}Remind me{/t}</a><br />
    </p>
    <p>
        {t}Here you can find more troubleshooting:{/t} {$loginhelplink}{t}Problems with login{/t}</a>.
    </p>
    <div class="buffer" style="width: 500px;">&nbsp;</div>
</div>

</div>
