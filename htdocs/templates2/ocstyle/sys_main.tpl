<!DOCTYPE html>
<html lang="{$opt.template.locale}">
<head>

    <title>
        {if ($opt.template.title=="")}
            {$opt.page.subtitle1|escape} {$opt.page.subtitle2|escape}
        {else}
            {$opt.template.title|escape} - {$opt.page.subtitle1|escape} {$opt.page.subtitle2|escape}
        {/if}
    </title>

    <meta name="keywords" content="{$opt.page.meta.keywords|escape}"/>
    <meta name="description" content="{$opt.page.meta.description|escape}"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta http-equiv="Content-Language" content="{$opt.template.locale}"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>

    <base href="/"/>

    <link rel="SHORTCUT ICON" href="favicon.ico"/>
    <link rel="apple-touch-icon" href="resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-iphone.png"/>
    <link rel="apple-touch-icon" sizes="72x72"
          href="resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-ipad.png"/>
    <link rel="apple-touch-icon" sizes="114x114"
          href="resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-iphone-retina.png"/>
    <link rel="apple-touch-icon" sizes="144x144"
          href="resource2/{$opt.template.style}/images/oclogo/apple-touch-icon-ipad-retina.png"/>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="web/assets/css/style.min.css">

    {* Cookie Consent Tool 06.2020 *}
    <link href="../../resource2/ocstyle/css/klaro.css" rel="stylesheet">
    <script type="application/javascript" src="resource2/ocstyle/js/klaro_config.js"></script>
    <script type="application/javascript" src="resource2/ocstyle/js/klaro.js"></script>

    {foreach from=$opt.page.header_javascript item=scriptItem}
        <script type="text/javascript" src="{$scriptItem}"></script>
    {/foreach}
</head>

{* JS onload() onunload() *}
<body{if $opt.session.url==true} onload="initSessionTimeout()"{/if}
        {foreach from=$opt.page.body_load item=loadItem name=bodyload}{if $smarty.foreach.bodyload.first} onload="{/if}{$loadItem};{if $smarty.foreach.bodyload.last}"{/if}{/foreach}
        {foreach from=$opt.page.body_unload item=unloadItem name=bodyunload}{if $smarty.foreach.bodyunload.first} onunload="{/if}{$unloadItem};{if $smarty.foreach.bodyunload.last}"{/if}{/foreach}
>

{include file="header/old_tompenu.tpl"}
{*{include file="header/topmenu.tpl"}*}

<main>
    {include file="$template.tpl"}
</main>

<section role="footer">
    {include file="sys_footer.tpl"}
</section>

{literal}
<script type="text/javascript">
    // Set to the same value as the web property used on the site
    var gaProperty = '{/literal}{$opt.tracking.googleAnalytics}{literal}';

    // Disable tracking if the opt-out cookie exists.
    var disableStr = 'ga-disable-' + gaProperty;
    if (document.cookie.indexOf(disableStr + '=true') > -1) {
        window[disableStr] = true;
    }

    // Opt-out function
    function gaOptout() {
        document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
        window[disableStr] = true;
        if (document.cookie.indexOf(disableStr + '=true') > -1) {
            alert('Google Analytics is now deactivated!');
        }
    }
</script>
{/literal}

{if !$smarty.server.HTTP_DNT}
{literal}
    <script type="text/javascript">
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', gaProperty, 'auto');
        ga('set', 'anonymizeIp', true);
        ga('send', 'pageview');
    </script>
{/literal}
{/if}

{*Rendered by GRUNT in DEV*}
<script src="web/assets/js/main.js"></script>

{*Import by composer*}
<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

{*External ressources while composer update is blocking implementation *}
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js" async></script>

{*Legacy OC custom script*}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/enlargeit/enlargeit.js" async></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tools.js" async></script>

</body>
</html>
