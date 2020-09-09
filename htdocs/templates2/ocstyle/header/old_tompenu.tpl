<header class="main__header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerOcTopmenu"
                aria-controls="navbarTogglerOcTopmenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerOcTopmenu">
            <li class="navbar-brand">
                <a class="nav-link" href="/"><i class="svg svg-oc--brand" style="width: 1rem;"></i> Opencaching</a>
            </li>
            <ul class="navbar-nav nav-pills mr-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    {nocache}
                        {include file="sys_topmenu.tpl" items="$topmenu"}
                    {/nocache}
                </li>
            </ul>
            {include file="header/login.tpl"}
            {include file="header/user.tpl"}
        </div>
    </nav>
</header>
