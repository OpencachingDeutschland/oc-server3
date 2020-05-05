{* Events *}
<div class="container-fluid">
    <div class="row oc-title">
        <div class="col-1">
            <img src="resource2/{$opt.template.style}/images/cacheicon/event.gif">
        </div>
        <div class="col-9 oc-title__title">
            {t 1=$usercountry|escape}The next events in %1{/t}
        </div>
        <div class="col-2 oc-title__link">
            {if $total_events > $events|@count}
                [
                <a href="newcaches.php?cachetype=6">{t}more{/t}...</a>
                ]
            {/if}
        </div>
    </div>

    <div class="row">
        {include file="res_newevents.tpl" events=$events}
    </div>
</div>
