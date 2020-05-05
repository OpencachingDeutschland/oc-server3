{nocache}
    {if $login.userid!=0}
        <div class="d-flex align-items-center">
            <div class="">
                <a href="myhome.php">{$login.username|escape}</a>
            </div>
            <div class="ml-2">
                <a href="login.php?action=logout">
                    <i class="mdi mdi-2x mdi-exit-to-app"></i>
                </a>
            </div>
        </div>
    {/if}
{/nocache}
