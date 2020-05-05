{* recommendations *}
<div class="container-fluid">
    <div class="row oc-title">
        <div class="col-1">
            <img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png">
        </div>
        <div class="col-9 oc-title__title">
            <a href="tops.php">
                {t}Current top ratings{/t}
            </a>
        </div>
        <div class="col-2 oc-title__link">
            [
            <a href="tops.php">{t}more{/t}...</a>
            ]
        </div>
    </div>

    <div class="row">
        {include file="res_newratings.tpl" topratings=$topratings}
    </div>
</div>
