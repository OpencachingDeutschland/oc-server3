{* new caches *}
<div class="container-fluid">

    <div class="row oc-title">
        <div class="col-12 oc-title__title">
            <i class="svg svg--shield-star-ouline"></i>{t 1=$usercountry|escape}Newest caches in %1{/t}
        </div>
    </div>

    <div class="row">
        <div class="d-flex">
            {include file="res_newcaches.tpl" newcaches=$newcaches}
        </div>
    </div>

    <div class="row d-flex">
            <p class="text-right">
                ({t 1=$count_hiddens 2=$count_founds 3=$count_users}Total of %1 active Caches and %2 founds by %3 users{/t}
                )</p>
    </div>

</div>
