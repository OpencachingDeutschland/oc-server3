<div class="card" style="width: 15rem;">

    <a href="{$picture.pic_url}"
       data-toggle="lightbox"
       data-title="{$picture.title|replace:"'":"´"|replace:'"':'´´'}"
       data-footer="&copy; by {$picture.username|escape}"
    >
        <img src="thumbs.php?type=1&uuid={$picture.pic_uuid}"
             class="card-img-top"
             alt="{$picture.title|replace:"'":"´"|replace:'"':'´´'}"
        ></a>

    <div class="card-body">

        <h5 class="card-title">{$picture.title|replace:"'":"´"|replace:'"':'´´'}</h5>

        <p class="card-text">
            {if $logdate || $loguser}
                {if $logdate}
                    {if  $fullyear}
                        {assign var=dateformat value=$opt.format.date}
                    {elseif $picture.oldyear == "1" || $shortyear}
                        {assign var=dateformat value=$opt.format.dateshort}
                    {else}
                        {assign var=dateformat value=$opt.format.dm}
                    {/if}

                    {if !$loguser}<a href="viewcache.php?cacheid={$picture.cache_id}&log=A#log{$picture.logid}">{/if}{$picture.picdate|date_format:$dateformat}{if !$loguser}</a>{/if}{/if}
                &nbsp;
                {if $loguser}
                <a href="{if $profilelink}
                            viewprofile.php?userid={$picture.user_id}
                            {else}
                            viewcache.php?cacheid={$picture.cache_id}&log=A#log{$picture.logid}
                            {/if}">{$picture.username|escape}</a>
                {if $picture.cachename}
                    <br/>
                    <span title="{$picture.cachename|escape}">{$picture.cachename|escape}</span>{/if}
            {/if}
            {/if}
        </p>

        <button name="ShowLog"
                class="btn btn-xs btn-outline-oc-main btn-block"
                type="submit"
                id="pl{$picture.pic_uuid}"
                onclick="window.location.href ='viewcache.php?cacheid={$picture.cache_id}&log=A#log{$picture.logid}'">
            <i class="svg svg--logbook-entry--dark"></i> {t}Show Log{/t}
        </button>

    </div>
</div>

