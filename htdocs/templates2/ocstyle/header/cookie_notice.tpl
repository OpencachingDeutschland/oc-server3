<style>
    {literal}
    .cookie-notice--container {
        display: none;
        position: relative;
        top: 0;
        left: 0;
        width: 100%;
        height: 30px;
        border-bottom: 1px solid grey;
        background: white;
    }

    .cookie-notice--container .cookie-notice--text {
        width: 100%;
        display: inline-block;
        text-align: center;

        line-height: 30px;
        font-size: 14px;
    }

    .cookie-notice--container .cookie-notice--information-button,
    .cookie-notice--container .cookie-notice--close-button {
        padding: 2px 0;
        margin-left: 10px;
        display: inline-block;
    }

    .cookie-notice--container .cookie-notice--information-button input[type=button],
    .cookie-notice--container .cookie-notice--close-button input[type=button] {
        height: 24px;
    }

    body.cookie-notice--body .cookie-notice--container {
        display: block;
    }

    body.cookie-notice--body #overall {
    }

    body.cookie-notice--body #overall #langstripe {
        position: relative;
    }

    body.cookie-notice--body #overall .page-container-1 {
        position: relative;
        margin-top: 0;
    }
    {/literal}
</style>
<div class="cookie-notice--container">
    <div class="cookie-notice--text">
        {t}This website uses cookies. If you stay on this site you accept the usage of cookies.{/t}
        <div class="cookie-notice--information-button">
            <a href="/articles.php?page=dsb">
                <input type="button" class="formbutton" value="{t}More information{/t}" />
            </a>
        </div>
        <div class="cookie-notice--close-button">
            <input type="button" class="formbutton" id="js--cookie-notice--close-button" value="{t}Close{/t}" />
        </div>
    </div>
</div>

<script>
    {literal}
    var ocCookieNoticeName = 'occookienotice';

    function checkCookieExists(name) {
        return document.cookie.indexOf(name) !== -1;
    }

    document.addEventListener("DOMContentLoaded", function() {
        var bodyElement = document.getElementsByTagName('body')[0];
        if (!checkCookieExists(ocCookieNoticeName)) {
            bodyElement.className += " cookie-notice--body";
        }

        var cookieNoticeCloseButton = document.getElementById('js--cookie-notice--close-button');
        cookieNoticeCloseButton.onclick = function () {
            document.cookie = ocCookieNoticeName+"=1; path=/";
            bodyElement.classList.remove("cookie-notice--body");
            return false;
        };
    });

    {/literal}
</script>
