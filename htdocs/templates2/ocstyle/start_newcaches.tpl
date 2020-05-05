{* new caches *}
<div class="container-fluid">
    <div class="row oc-title">
        <div class="col-1">
            <img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif">
        </div>
        <div class="col-9 oc-title__title">
            <a href="newcaches.php?country={$usercountryCode}">
                {t 1=$usercountry|escape}Newest caches in %1{/t}
            </a>
        </div>
        <div class="col-2 oc-title__link">
            [
            <a href="newcaches.php">{t}more{/t}...</a>
            ]
        </div>
    </div>
    <div class="row">
        {include file="res_newcaches.tpl" newcaches=$newcaches}
    </div>
    <div class="row float-right">
        ({t 1=$count_hiddens 2=$count_founds 3=$count_users}Total of %1 active Caches and %2 founds by %3 users{/t}
        )
    </div>
</div>
