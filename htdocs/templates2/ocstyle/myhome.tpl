{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

{if $allpics === false}
    <section>
        <div class="container-fluid">

            <div class="row oc-title">
                <div class="col-12 oc-title__title">
                    <i class="svg svg--register"></i>{t 1=$login.username}Hello %1{/t}
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">

                <div class="">

                    <div class="col-12 col-md-4 col-lg-6 oc-counter-container">
                        <div class="oc-counter-title">{t}Finds:{/t}</div>
                        <div class="p-2 oc-counter-count">
                            <div class="counter" data-count="{$found}">0</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 col-lg-6 oc-counter-container">
                        <div class="oc-counter-title">{t}Hidden:{/t}</div>
                        <div class="p-2 oc-counter-count">
                            <div class="counter" data-count="{$hidden}">0</div>
                        </div>
                    </div>


                    <div class="col-12 col-md-4 col-lg-12">

                        {if $logs|@count > 0}
                            <a name="ShowFound"
                               class="btn btn-xs btn-outline-oc-main btn-block"
                               href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bymylastlog&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=1,7&amp;calledbysearch=0">
                                <i class="svg svg--logbook"></i> {t}Geocaches found{/t}
                            </a>
                            <a name="ShowLogged"
                               class="btn btn-xs btn-outline-oc-main btn-block"
                               href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bymylastlog&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=0&amp;calledbysearch=0">
                                <i class="svg svg--logbook"></i> {t}Geocaches logged{/t}
                            </a>
                        {/if}

                        {if $caches|@count > 0}
                            <a name="ShowDetails"
                               class="btn btn-xs btn-outline-oc-main btn-block"
                               href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=0&calledbysearch=0">
                                <i class="svg svg--logbook"></i> {t}Show details{/t}
                            </a>
                            <a name="ShowLogged"
                               class="btn btn-xs btn-outline-oc-main btn-block"
                               href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=1&f_unpublished=1&calledbysearch=0">
                                <i class="svg svg--logbook"></i> {t}only active caches{/t}
                            </a>
                        {/if}

                    </div>

                </div>

            </div>
        </div>
    </section>
    <section>
        <div class="container-fluid">
            <div class="row oc-title">
                <div class="col-12 oc-title__title">
                    <i class="svg svg--logbook-entry">{t}Your latest log entries{/t}</i>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-12 p-3">

                    <table class="table table-sm table-hover table-striped">

                        {foreach from=$logs item=logItem}
                            <tr>
                                <td>{$logItem.date|date_format:$opt.format.dateshort}</td>
                                <td>
                                    <a href="viewcache.php?wp={$logItem.wp_oc}">{$logItem.name|escape}</a>
                                    {include file="res_oconly.tpl" oconly=$logItem.oconly}<br>
                                    {t}by{/t}
                                    <a href="viewprofile.php?userid={$logItem.userid}">{$logItem.username|escape}</a>
                                    {include file="res_logflags.tpl" logItem=$logItem withRecommendation=true}
                                </td>

                                <td>{include file="res_logtype.tpl" type=$logItem.type}
                                    {if $logItem.oc_team_comment}<img
                                        src="resource2/{$opt.template.style}/images/oclogo/oc-team-comment.png"
                                        alt="OC-Team"
                                        title="{t}OC team comment{/t}" />{/if}</td>

                            </tr>
                            {foreachelse}
                            <tr>
                                <td>
                                    <div class="alert-oc-secondary">{t}No entries found{/t}</div>
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>
        </div>
    </section>
{/if}

{* Log pictures *}
<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row p-2 oc-title justify-content-between">
            <div class="d-flex oc-title__title">
                <i class="svg svg--photo">{t 1=$total_pictures}Log pictures: %1{/t}</i>
            </div>

            {if $pictures|@count > 0 && $allpics === false}
                <div class="d-flex oc-title__link">
                    [
                    <a href="myhome.php?allpics=ownlogs">{t}Show all{/t}</a>
                    ]
                </div>
            {/if}
        </div>

        <div class="row no-gutters">
            <div class="col-md-12 col-12 pr-0 pr-md-2 pr-lg-4 mt-2">
                {if $pictures|@count == 0}
                    {if $allpics == 'owncaches'}
                        <p>{t}There are no log pictures yet for your caches.{/t}</p>
                    {else}
                        <p>{t}You did not upload any log pictures yet.{/t}</p>
                    {/if}
                    <br/>
                {else}

                    {if $allpics == 'ownlogs'}
                        {assign var=subtitle value="{t}Your log pictures:{/t}"}
                        {assign var=maxlines value=0}
                    {elseif $allpics == 'owncaches'}
                        {assign var=subtitle value="{t}Log pictures for your caches:{/t}"}
                        {assign var=maxlines value=0}
                    {else}
                        <b>{t}Your latest log pictures:{/t}</b>
                        {assign var=maxlines value=1}
                    {/if}

                    {if $allpics == 'owncaches'}
                        {include file="res_logpictures.tpl" logdate=true loguser=true maxlines=$maxlines shortyear=true}
                    {else}
                        {include file="res_logpictures.tpl" logdate=true loguser=false maxlines=$maxlines fullyear=true}
                    {/if}

                    {if $allpics == 'ownlogs'}
                        <p>{t}In your
                                <a href="mydetails.php">profile settings</a>
                                you can choose if your log pictures stat and gallery is visible for other users.{/t}</p>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</section>


{* Geocaches hidden *}
<section class="mt-2 mb-2">
    <div class="container-fluid">
        <div class="row p-2 oc-title justify-content-between">
            <div class="d-flex oc-title__title">
                <i class="svg svg--listing-hidden">{t}Your geocaches hidden{/t}</i>
            </div>

            <div class="d-flex d-inline-block">
                <div class="mr-1">{t}Archivert{/t}</div>
                <div class="switch"></div>

            </div>

            {if $pictures|@count > 0 && $allpics === false}
                <div class="d-flex oc-title__link">
                    [
                    <a href="myhome.php?allpics=ownlogs">{t}Show all{/t}</a>
                    ]
                </div>
            {/if}
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-12 p-3">

                <table class="table table-sm table-hover table-striped">

                    <tbody>
                    {foreach from=$caches item=cacheItem}
                        <tr class="{if $cacheItem.status>2}is--archived d-none{/if}">
                            <td>{include file="res_cacheicon_22.tpl" cachetype=$cacheItem.type} {include file="res_oconly.tpl" oconly=$cacheItem.oconly size="15x21"}</td>
                            <td>
                                {$cacheItem.date_hidden|date_format:$opt.format.datelong}
                            </td>
                            <td>{include file="res_cachestatus.tpl" status=$cacheItem.status}</td>
                            <td>
                                <a href="viewcache.php?wp={$cacheItem.wp_oc}">{$cacheItem.name|escape}</a>
                                {if strlen($cacheItem.name) < 45}
                                    {include file="res_logflags.tpl" logItem=$cacheItem lfSpace=true}{/if}
                            </td>
                            <td>
                                {if $cacheItem.toprating>0}{$cacheItem.toprating}{/if}
                            </td>
                            <td>
                                {if $cacheItem.found>0}{$cacheItem.found}{/if}
                            </td>
                            <td>
                                <a href="viewcache.php?cacheid={$cacheItem.cache_id}#logentries">{$cacheItem.lastlog|date_format:$opt.format.date}</a>
                                {include file="res_logtype.tpl" type=$cacheItem.lastlog_type}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>

                <div>
                    <a class="systemlink" href="ownerlogs.php">{t}Log history{/t}</a>
                    ,
                    <a class="systemlink" href="myhome.php?allpics=owncaches">{t}Log pictures gallery{/t}</a>
                </div>
            </div>
        </div>
    </div>
</section>


{* ... unpublished caches *}
{if $notpublished|@count}
    <section class="mt-2 mb-2">
        <div class="container-fluid">
            <div class="row p-2 oc-title">
                <i class="svg svg--logbook-edit"></i>{t}Unpublished Geocaches{/t}
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">

                {foreach from=$notpublished item=notpublishedItem}
                    <div class="col-12 col-md-4 col-lg-3 p-0 unpublished-container">
                        <div class="oc-waypoint-code">
                            {$notpublishedItem.wp_oc}
                        </div>
                        <div class="p-2 oc-waypoint-name">
                            {$notpublishedItem.name|escape}
                        </div>
                        <div class="p-2">
                            {include file="res_cacheicon_22.tpl" cachetype=$notpublishedItem.type}
                            {include file="res_oconly.tpl" oconly=$notpublishedItem.oconly size="15x21"}
                        </div>
                        <div class="p-2">
                            {$notpublishedItem.date_activate|date_format:$opt.format.datelong}
                        </div>
                        <div class="p-2">
                            {include file="res_cachestatus.tpl" status=$notpublishedItem.status}
                        </div>
                        <div class="p-2 oc-waypoint-edit">
                            <button name="ShowLog"
                                    class="btn btn-xs btn-outline-oc-secondary btn-block"
                                    type="submit"
                                    onclick="window.location.href ='viewcache.php?wp={$notpublishedItem.wp_oc}'">
                                <i class="svg svg--logbook-edit--dark"></i> {t}Edit Listing{/t}
                            </button>
                        </div>
                    </div>
                {/foreach}

            </div>
        </div>
    </section>
{/if}
