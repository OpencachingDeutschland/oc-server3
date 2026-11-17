{***************************************************************************
* You can find the license in the docs directory
*
*  Donation banner – displayed at the top of the page when enabled.
*  Dismissable for some days via localStorage.
***************************************************************************}
{literal}
<style>
    body.oc-has-banner #langstripe {
        position: relative !important;
        top: auto !important;
        left: auto !important;
    }
    body.oc-has-banner .page-container-1 { margin-top: 0 !important; }

    #oc-donation-banner {
        position: relative;
        z-index: 6;
        background: #e8eff2;
        color: #4b4b4b;
        font: 14px/1.4 verdana, arial, sans-serif;
        border-bottom: 3px solid #5890a8;
        box-sizing: border-box;
        overflow: hidden;
    }
    #oc-donation-banner.oc-banner--hidden { display: none; }
    #oc-donation-banner.oc-banner--dismissing {
        transition: opacity 200ms ease, max-height 200ms ease;
        opacity: 0;
        max-height: 0;
        border-bottom-width: 0;
        padding: 0;
    }
    @media (prefers-reduced-motion: reduce) {
        #oc-donation-banner.oc-banner--dismissing { transition: none; }
    }

    #oc-donation-banner .oc-banner__inner {
        display: flex;
        align-items: center;
        gap: 16px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 12px 52px 12px 16px;
        box-sizing: border-box;
    }
    #oc-donation-banner .oc-banner__icon {
        flex: 0 0 52px;
        width: 52px;
        height: 52px;
        object-fit: contain;
    }
    #oc-donation-banner .oc-banner__copy { flex: 1 1 auto; }
    #oc-donation-banner .oc-banner__headline {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: bold;
        color: #3a6d8f;
        line-height: 1.3;
    }
    #oc-donation-banner .oc-banner__subtext {
        margin: 0;
        font-size: 13px;
        line-height: 1.4;
    }
    #oc-donation-banner .oc-banner__cta {
        flex: 0 0 auto;
        padding: 9px 20px;
        background: #3a6d8f;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        text-transform: uppercase;
        white-space: nowrap;
    }
    #oc-donation-banner .oc-banner__cta:hover,
    #oc-donation-banner .oc-banner__cta:focus {
        background: #2e6080;
        outline: 2px solid #3a6d8f;
        outline-offset: 2px;
    }
    #oc-donation-banner .oc-banner__close {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        padding: 4px 6px;
    }
    #oc-donation-banner .oc-banner__close:hover,
    #oc-donation-banner .oc-banner__close:focus {
        color: #333;
        background: rgba(0, 0, 0, 0.08);
        outline: 2px solid #3a6d8f;
        outline-offset: 1px;
    }

    @media (max-width: 599px) {
        #oc-donation-banner .oc-banner__icon { display: none; }
        #oc-donation-banner .oc-banner__inner {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 44px 12px 12px;
        }
        #oc-donation-banner .oc-banner__cta { align-self: center; }
    }
</style>
{/literal}

<div id="oc-donation-banner" role="banner" style="display:none" aria-label="{if $opt.template.locale=='DE'}Spendenaufruf opencaching.de{else}Donation appeal opencaching.de{/if}">
    <div class="oc-banner__inner">
        <img class="oc-banner__icon"
             src="/resource2/misc/donation/globi_box_smal.png"
             alt="" aria-hidden="true" />
        <div class="oc-banner__copy">
            {if $opt.template.locale=='DE'}
                <p class="oc-banner__headline">opencaching.de &ndash; von der Community, f&uuml;r die Community.</p>
                <p class="oc-banner__subtext">Unsere Plattform geh&ouml;rt keinem Konzern. Sie geh&ouml;rt euch. Bitte unterst&uuml;tzt sie.</p>
            {else}
                <p class="oc-banner__headline">opencaching.de &ndash; by the community, for the community.</p>
                <p class="oc-banner__subtext">Our platform belongs to no corporation. It belongs to you. Please support it.</p>
            {/if}
        </div>
        <a class="oc-banner__cta" href="/articles.php?page=donations"
           aria-label="{if $opt.template.locale=='DE'}Jetzt spenden &mdash; opencaching.de unterst&uuml;tzen{else}Donate now &mdash; support opencaching.de{/if}">{if $opt.template.locale=='DE'}Jetzt spenden{else}Donate now{/if}</a>
    </div>
    <button class="oc-banner__close" id="oc-donation-banner__close" type="button"
            aria-label="{if $opt.template.locale=='DE'}Banner schlie&szlig;en{else}Close banner{/if}">&#x2715;</button>
</div>

{literal}
<script>
(function () {
    var DISMISS_DAYS = 5;
    var STORAGE_KEY = 'oc_donation_banner_dismissed';
    var banner = document.getElementById('oc-donation-banner');

    if (!banner) {
        return;
    }

    function isDismissed() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return false;
            var parts = raw.split('-');
            var expires = new Date(parts[0], parts[1] - 1, parseInt(parts[2]) + DISMISS_DAYS);
            return new Date().toLocaleDateString('sv') < expires.toLocaleDateString('sv');
        } catch (e) {
            return false;
        }
    }

    function dismiss() {
        try {
            localStorage.setItem(STORAGE_KEY, new Date().toLocaleDateString('sv'));
        } catch (e) {}

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            banner.classList.add('oc-banner--hidden');
            document.body.classList.remove('oc-has-banner');
            return;
        }

        banner.classList.add('oc-banner--dismissing');
        setTimeout(function () {
            banner.classList.add('oc-banner--hidden');
            document.body.classList.remove('oc-has-banner');
        }, 250);
    }

    if (isDismissed()) {
        banner.classList.add('oc-banner--hidden');
    } else {
        banner.style.display = '';
        document.body.classList.add('oc-has-banner');
        document.getElementById('oc-donation-banner__close').addEventListener('click', dismiss);
    }
}());
</script>
{/literal}
