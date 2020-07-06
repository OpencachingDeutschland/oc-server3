{* Events *}

<div class="oc-section col-12">
    <h2><i class="svg svg--party-propper"></i>{t 1=$usercountry|escape}The next events in %1{/t}</h2>
</div>
<div class="col-12 mt-2">
    <div class="row">
        {if $events|@count}
            {include file="res_newevents.tpl" events=$events}
        {else}
            <p><em>{t}currently not available{/t}</em></p>
        {/if}
    </div>
</div>


