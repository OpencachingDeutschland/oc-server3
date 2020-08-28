{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

<section class="main__topstage main__lostplace d-flex align-items-center justify-content-center">

    <div class="main__login-box p-3">

        <h2 class="oc-title__primary">Opencaching Login</h2>

        <hr class="hr--primary">

        <form
                action="{$opt.page.login_url}"
                method="post"
                enctype="application/x-www-form-urlencoded"
                name="login"
                dir="ltr"
                class="form"
        >
            <input type="hidden" name="action" value="login"/>
            <input type="hidden" name="target" value="{$opt.page.target|escape}"/>
            <input type="hidden" name="source" value="titlebar"/>

            <div class="container-fluid">
                <div class="row">
                    <label for="LoginInputField"></label>
                    <input
                            name="email"
                            placeholder="{t}OC Username{/t}"
                            type="text"
                            class="form-control m-2"
                            id="LoginInputField"
                    />

                    <label for="PasswordInputField"></label>
                    <input name="password"
                           placeholder="{t}Password{/t}"
                           type="password"
                           class="form-control m-2"
                           id="PasswordInputField"
                           value=""/>

                    <button name="LogMeIn"
                            class="btn btn-xs btn-outline-oc-primary btn-block m-2"
                            type="submit"
                            onclick="submitbutton('LogMeIn')">
                        <i class="svg svg--login"></i> {t}Login{/t}
                    </button>

                </div>
            </div>
        </form>


        {if $error!=LOGIN_OK}
            <div class="container-fluid">
                <div class="alert alert-danger text-center col-12" role="alert">
                    {if $error==LOGIN_BADUSERPW}
                        {t}The login was not successfull.{/t}
                        <br/>
                        {t}The entered username or password did not match.{/t}
                        <br/>
                    {elseif $error==LOGIN_TOOMUCHLOGINS}
                        {t}The login was not successfull.{/t}
                        <br/>
                        {t 1=$opt.page.max_logins_per_hour}Your tried to login more than %1 times in the last hour. The next login will not be allowed before one hour since the first try has passed.{/t}
                        <br/>
                    {elseif $error==LOGIN_USERNOTACTIVE}
                        {t}Your account is not acivated.{/t}
                        <br/>
                        {t}&gt;
                            <a href="activation.php">Here</a>
                            &lt; you can activate your account.{/t}
                        <br/>
                    {elseif $error==LOGIN_EMPTY_USERPASSWORD}
                        {t}Please fill in username and password!{/t}
                    {elseif $error==LOGIN_LOGOUT_OK}
                        {t}Logout was successful.{/t}
                    {else}
                        {t}The login was not successfull.{/t}
                        <br/>
                        {t 1=$opt.mail.contact}If this problem persists over a longer time, please contact us at
                            %1
                            .{/t}
                        <br/>
                    {/if}
                </div>
            </div>
        {/if}

        <hr class="hr--primary">

        <div class="container-fluid">

            <button name="RememberPassword"
                    class="btn btn-xs btn-block btn-outline-oc-main m-2"
                    type="submit"
                    onclick="window.location.href ='newpw.php'">
                <i class="svg svg--security-shield"></i> {t}New password{/t}
            </button>

            <button name="RememberAccount"
                    class="btn btn-xs btn-block btn-outline-oc-main m-2"
                    type="submit"
                    onclick="window.location.href ='remindemail.php'">
                <i class="svg svg--account-request"></i> {t}Remember Account{/t}
            </button>

            <button name="RegisterMe"
                    class="btn btn-xs btn-block btn-outline-success m-2"
                    type="submit"
                    onclick="window.location.href ='register.php'">
                <i class="svg svg--register"></i> {t}Register{/t}
            </button>

        </div>
    </div>

</section>
