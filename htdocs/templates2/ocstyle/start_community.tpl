{* forum news *}
<div class="container-fluid">
    <div class="row oc-title">
        <div class="col-11 oc-title__title">
            {t}Community news{/t}
        </div>
    </div>

    <div class="row">
        {if $forum|@count}
            {include file="res_rssparser.tpl" rss=$forum}
        {else}
            <p><em>{t}currently not available{/t}</em></p>
        {/if}
    </div>
</div>
