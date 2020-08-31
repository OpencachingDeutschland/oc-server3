{nocache}
    <header class="container-fluid d-flex justify-content-between align-items-center main__header">

        <div class="oc-topmenu-brand">Opencaching</div>

        <nav role="navigation">
            <div class="oc--topmenu-toggle">

                <input type="checkbox" />
                <!-- spans need for hamburger menu -->
                <span></span>
                <span></span>
                <span></span>

                <ul class="oc--topmenu-items">
                    <a href="/"><li>Start</li></a>
                    <a href="/myhome.php"><li>Mein Profil</li></a>
                    <a href="/search.php"><li>Suche</li></a>
                    <a href="/map2.php"><li>Karte</li></a>
                </ul>
            </div>
        </nav>


        <div>
            {include file="header/login.tpl"}
            {include file="header/user.tpl"}
        </div>
    </header>
{/nocache}
