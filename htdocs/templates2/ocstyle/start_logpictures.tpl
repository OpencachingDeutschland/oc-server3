{* Logpictures *}
<div class="container-fluid">
    <div class="row oc-title">
        <div class="col-1">
            <img src="resource2/{$opt.template.style}/images/misc/32x32-pictures.gif">
        </div>
        <div class="col-9 oc-title__title">
            <a href="newlogpics.php">
                {t}New log pictures{/t}
            </a>
        </div>
        <div class="col-2 oc-title__link">
            [
            <a href="newlogpics.php">{t}more{/t}...</a>
            ]
        </div>
    </div>

    <div class="row">
        {include file="res_logpictures.tpl" logdate=true loguser=true}
    </div>
</div>
