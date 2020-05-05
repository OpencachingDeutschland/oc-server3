{***************************************************************************
 * You can find the license in the docs directory
 ***************************************************************************}
{* OCSTYLE *}
<footer class="footer__container">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-4 col-xs-12 mt-4 mb-4">
                <h4>{t}Country sites{/t}</h4>
                <a href="http://www.opencaching.cz" target="_blank" rel="nofollow">
                    <img src="resource2/{$opt.template.style}/images/nodes/oc-cz.png"></a>
                <a href="http://www.opencaching.nl" target="_blank" rel="nofollow">
                    <img src="resource2/{$opt.template.style}/images/nodes/oc-nl.png"></a>
                <a href="https://opencaching.pl" target="_blank" rel="nofollow">
                    <img src="resource2/{$opt.template.style}/images/nodes/oc-pl.png"></a>
                <a href="http://www.opencaching.ro" target="_blank" rel="nofollow">
                    <img src="resource2/{$opt.template.style}/images/nodes/oc-ro.png"></a>
                <a href="https://opencache.uk" target="_blank" rel="nofollow">
                    <img src="resource2/{$opt.template.style}/images/nodes/oc-org-uk.png"></a>
                <a href="http://www.opencaching.us" target="_blank" rel="nofollow">
                    <img src="resource2/{$opt.template.style}/images/nodes/oc-us.png"></a>
            </div>

            <div class="col-md-4 col-xs-12 mt-4 mb-4">
                <div class="container-fluid">
                    <div class="row">
                        <h4>{t}Follow us:{/t}</h4>
                    </div>

                    <div class="row">
                        <a href="http://blog.opencaching.de/feed">
                            <div class="d-flex svg-rss--icon">&nbsp;</div>
                            <div class="d-flex">RSS Feed</div>
                        </a>
                    </div>

                    <div class="row">
                        <a href="https://twitter.com/opencaching">
                            <div class="d-flex svg-twitter--icon">&nbsp;</div>
                            <div class="d-flex">@Opencaching</div>
                        </a>
                    </div>

                    <div class="row">
                        <a href="https://www.facebook.com/opencaching.de">
                            <div class="d-flex svg-facebook--icon">&nbsp;</div>
                            <div class="d-flex">@Opencaching.de</div>
                        </a>
                    </div>

                </div>
            </div>

            <div class="col-md-4 col-xs-12 mt-4 mb-4">
                <div class="container-fluid">
                    <div class="row">
                        <h4>{t}Join discussions:{/t}</h4>
                    </div>

                    <div class="row">
                        <a href="http://forum.opencaching.de/">
                            <div class="d-flex svg-community--icon">&nbsp;</div>
                            <div class="d-flex">OC Community</div>
                        </a>
                    </div>

                    <div class="row">
                        <a href="https://www.facebook.com/groups/198752500146032/">
                            <div class="d-flex svg-facebook--icon--primary">&nbsp;</div>
                            <div class="d-flex">{t}Facebook Group{/t}</div>
                        </a>
                    </div>

                    <div class="row">
                        <a href="https://github.com/OpencachingDeutschland/oc-server3">
                            <div class="d-flex svg-github--icon">&nbsp;</div>
                            <div class="d-flex">GitHub</div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="row mt-4 mb-4">
            <div class="d-flex mr-4 footer__datalicence--title">{t}Datalicense{/t}</div>
            <div class="d-flex footer__datalicence--text">{$license_disclaimer}</div>
        </div>
        <div class="row mt-4 mb-4">
            <div class="footer__small">
                {nocache}
                    {t}Page timing:{/t} {$sys_runtime|sprintf:"%1.3f"} {t}sec{/t}
                    {if ($opt.template.caching == true)}
                        {t}Page cached:{/t} {if $sys_cached==true}{t}Yes{/t}{else}{t}No{/t}{/if}
                    {/if}

                {/nocache}
                {t}Created at:{/t} {"0"|date_format:$opt.format.datetime}
            </div>
        </div>

    </div>

</footer>
