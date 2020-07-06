{* Events *}
<div class="container-fluid">

    <div class="row oc-title">
        <div class="col-12 oc-title__title">
            <i class="svg svg--party-propper"></i>{t 1=$usercountry|escape}The next events in %1{/t}
        </div>
    </div>

    <div class="row">
        {if $events|@count}
            {include file="res_newevents.tpl" events=$events}
        {else}
            <p><em>{t}currently not available{/t}</em></p>
        {/if}
    </div>

</div>
