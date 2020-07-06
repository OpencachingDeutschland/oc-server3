{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row no-gutters">
            <div class="oc-section col-12">
                <h2><i class="svg svg--shield-star-ouline"></i>{t 1=$usercountry|escape}Newest caches in %1{/t}</h2>
            </div>
            <div class="col-12 mt-2">
                {include file="start_newcaches.tpl"}
            </div>
        </div>
    </div>
</section>

<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row no-gutters">
            {include file="start_events.tpl"}
        </div>
    </div>
</section>

<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row no-gutters">
            <div class="oc-section col-12">
                <h2>News</h2>
            </div>
            <div class="col-md-6 col-12 pr-0 pr-md-2 pr-lg-4 mt-2">
                {include file="start_blog.tpl"}
            </div>
            <div class="col-md-6 col-12 mt-2">
                {include file="start_community.tpl"}
            </div>
        </div>
    </div>
</section>
