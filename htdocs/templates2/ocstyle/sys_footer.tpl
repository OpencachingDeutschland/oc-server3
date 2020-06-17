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
                            <div class="d-flex">
                                <svg class="svg-facebook--icon" enable-background="new 0 0 512 512" id="Layer_1"
                                     version="1.1"
                                     viewBox="0 0 512 512" xml:space="preserve"
                                     xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g>
                                        <circle cx="256" cy="256" fill="#3B5998" r="256"/>
                                        <path
                                                d="M301.6,151.2c15.6,0,36.3,0,36.3,0V97c0,0-21.8,0-51.4,0c-29.6,0-68.1,19-68.1,74.2c0,10.3,0,25.4,0,43   h-49.1v56.1h49.1c0,69.9,0,146,0,146h21.8h17.3H277c0,0,0-78.8,0-146h48.8l8.1-56.1H277c0-18.4,0-31.8,0-35.7   C277,160.1,286,151.2,301.6,151.2z"
                                                fill="#FFFFFF"/>
                                    </g></svg>
                            </div>
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
                            <div class="d-flex">
                                <svg class="svg-social-icon" width="24" height="24" viewBox="0 0 24 24">
                                    <g>
                                        <circle cx="256" cy="256" fill="#3B5998" r="256"/>
                                        <path
                                                d="M301.6,151.2c15.6,0,36.3,0,36.3,0V97c0,0-21.8,0-51.4,0c-29.6,0-68.1,19-68.1,74.2c0,10.3,0,25.4,0,43   h-49.1v56.1h49.1c0,69.9,0,146,0,146h21.8h17.3H277c0,0,0-78.8,0-146h48.8l8.1-56.1H277c0-18.4,0-31.8,0-35.7   C277,160.1,286,151.2,301.6,151.2z"
                                                fill="#FFFFFF"/>
                                    </g>
                                </svg>
                            </div>
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
            <div class="col-2 footer__datalicence--title">{t}Datalicense{/t}</div>
            <div class="col-6 footer__datalicence--text">{$license_disclaimer}</div>
            <div class="col-4 footer__cookiesetup">
                <a href="#" onClick="klaro.show();return false;" style="cursor: pointer;"><i
                            class="mdi mdi-settings-applications"></i> COOKIE SETUP
                </a>
            </div>
        </div>
        <div class="row justify-content-center mt-4 mb-4">
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
