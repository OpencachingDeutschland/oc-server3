{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

    <table class="table table-sm table-hover table-striped">

        <tbody>
        {foreach name=newcaches from=$newcaches item=cacheitem}
            <tr>
                <td>{include file="res_cacheicon_22.tpl" cachetype=$cacheitem.type}</td>

                <td>
                    <a href="viewcache.php?cacheid={$cacheitem.cache_id}">{$cacheitem.name|escape}</a> {include file="res_oconly.tpl" oconly=$cacheitem.oconly}
                    <br>
                    {$cacheitem.adm1|escape}
                    {if $cacheitem.adm1!=null & $cacheitem.adm2!=null} &gt; {/if}
                    {$cacheitem.adm2|escape}
                    {if ($cacheitem.adm2!=null & $cacheitem.adm4!=null) | ($cacheitem.adm1!=null & $cacheitem.adm4!=null)} &gt; {/if}
                    {$cacheitem.adm4|escape}
                </td>

                <td class="align-right">
                    <a href="viewprofile.php?userid={$cacheitem.user_id}">{$cacheitem.username|escape}</a>
                    <br>
                    {$cacheitem.date_created|date_format:"%d.%m."}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

