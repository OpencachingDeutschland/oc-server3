{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row no-gutters">
            <div class="col-md-6 col-12 pr-0 pr-md-2 pr-lg-4 mt-2">
                {include file="start_events.tpl"}
            </div>
            <div class="col-md-6 col-12 mt-2">
                {include file="start_newcaches.tpl"}
            </div>
        </div>
    </div>
</section>


<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row p-2 oc-title justify-content-between">
            <div class="d-flex oc-title__title">
                <i class="svg svg--pictures"></i>{t}New log pictures{/t}
            </div>

            <div class="d-flex oc-title__link">
                [
                <a href="newlogpics.php">{t}more{/t}...</a>
                ]
            </div>
        </div>
        <div class="row no-gutters">
            <div class="col-md-12 col-12 pr-0 pr-md-2 pr-lg-4 mt-2">
                {include file="start_logpictures.tpl"}
            </div>
        </div>
    </div>
</section>


<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row">
            <div class="oc-title oc-title__title col-12">
                <i class="svg svg--news"></i> News
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
