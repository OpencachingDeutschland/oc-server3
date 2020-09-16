<div class="col-11 col-md-4 col-lg-2 p-0 unpublished-container">
    <div class="oc-waypoint-code">
        {$picture.picdate|date_format:'%d.%m.%y'}
    </div>
    <div>
        <a class="gallery"
           href="{$picture.pic_url}"
           data-featherlight="image"
        >
            <img src="{$picture.pic_url}"
                 class="mx-auto img-fluid"
                 alt="{$picture.title|replace:"'":"´"|replace:'"':'´´'}"
            ></a>
    </div>
    <div class="">
        {$picture.title|replace:"'":"´"|replace:'"':'´´'}
    </div>
    <div class="">
        <button name="ShowLog"
                class="btn btn-xs btn-outline-oc-secondary btn-block rounded-0"
                type="submit"
                id="pl{$picture.pic_uuid}"
                onclick="window.location.href ='viewcache.php?cacheid={$picture.cache_id}&log=A#log{$picture.logid}'">
            {t}Show Log{/t}
        </button>
    </div>
</div>


