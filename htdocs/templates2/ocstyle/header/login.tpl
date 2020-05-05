{nocache}
    {if $login.userid==0}
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalOcLogin">
            Login
        </button>
        <!-- Modal -->
        <div class="modal  fade" id="modalOcLogin" data-backdrop="static" data-keyboard="false" tabindex="-1"
             role="dialog" aria-labelledby="modalOcLoginLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalOcLoginLabel">Welcome to OC</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">
                            <form
                                    action="{$opt.page.login_url}"
                                    method="post"
                                    enctype="application/x-www-form-urlencoded"
                                    name="login"
                                    dir="ltr"
                                    class="form-inline"
                            />
                            <input type="hidden" name="action" value="login"/>
                            <input type="hidden" name="target" value="{$opt.page.target|escape}"/>
                            <input type="hidden" name="source" value="titlebar"/>

                            <div class="container-fluid">
                                <div class="row mb-3">
                                    <div class="col-sm-6 col-xs-12 mb-1">

                                        <input
                                                name="email"
                                                placeholder="{t}User{/t}"
                                                type="text"
                                                class="form-control"
                                                id="LoginInput"
                                        />

                                    </div>

                                    <div class="col-sm-6 col-xs-12 mb-1">

                                        <input name="password"
                                               placeholder="{t}Password{/t}"
                                               type="password"
                                               class="form-control"
                                               id="PasswordInput"
                                               value=""/>

                                    </div>
                                </div>
                            </div>

                            <div class="container-fluid">
                                <div class="row mb-3">
                                    <div class="col-sm-6 col-xs-12 d-none d-sm-none d-md-block d-lg-block">
                                        <button name="LogMeIn"
                                                class="btn btn-xs btn-outline-success btn-block"
                                                type="submit"
                                                data-dismiss="modal"
                                                onclick="window.location.href ='register.php'"/>
                                        <i class="mdi mdi-edit"></i> Register
                                        </button>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 mb-3">
                                        <button name="LogMeIn"
                                                class="btn btn-xs btn-outline-primary btn-block"
                                                type="submit"
                                                onclick="submitbutton('LogMeIn')"/>
                                        <i class="mdi mdi-exit-to-app"></i> Login
                                        </button>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 d-sm-block d-md-none d-lg-none mb-3">
                                        <button name="LogMeIn"
                                                class="btn btn-xs btn-outline-success btn-block"
                                                type="submit"
                                                data-dismiss="modal"
                                                onclick="window.location.href ='register.php'"/>
                                        <i class="mdi mdi-edit"></i> Register
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/nocache}
