{* Blognews *}
<div class="container-fluid">
    <div class="row oc-title">
        <div class="col-12 oc-title__title">
            {t}Blog-News{/t}
        </div>
    </div>

    <div class="row">
        {if $news|@count}
            {include file="res_rssparser.tpl" rss=$news}
        {else}
            <p><em>{t}currently not available{/t}</em></p>
        {/if}
    </div>
</div>


