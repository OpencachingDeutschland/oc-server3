{**************************************************************************
* You can find the license in the docs directory
***************************************************************************}

<div class="container-fluid">

    {foreach name=events from=$events item=eventitem}
        <div class="event__block d-flex">

            <div class="event__date">
                <div class="event__date--day">{$eventitem.date_hidden|date_format:"%d"}</div>
                <div class="event__date--month">{$eventitem.date_hidden|date_format:"%b"}</div>
                {if $eventitem.date_hidden|date_format:"%Y" != date('Y')}
                    <div class="event__date--year">{$eventitem.date_hidden|date_format:"%Y"}</div>
                {/if}
            </div>

            <div class="event__content">
                <div class="event__name">
                    <a href="viewcache.php?cacheid={$eventitem.cache_id}" rel="nofollow">{$eventitem.name|escape}</a>
                </div>
                <div class="event__location">
                    <i class="svg svg--map-marker--dark"></i>
                    {$eventitem.adm1|escape}
                        {if $eventitem.adm1!=null & $eventitem.adm2!=null} &gt; {/if}
                    {$eventitem.adm2|escape}
                        {if ($eventitem.adm2!=null & $eventitem.adm4!=null) | ($eventitem.adm1!=null & $eventitem.adm4!=null)} &gt; {/if}
                    {$eventitem.adm4|escape}
                </div>
                <div class="event__owner">
                    <i class="svg svg--owner--dark"></i>{$eventitem.username|escape}
                </div>
            </div>

        </div>
    {/foreach}

</div>
