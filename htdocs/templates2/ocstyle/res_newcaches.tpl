{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{if $minimap_enabled}
<div>
<img class="img-minimap" style="margin:16px 20px 16px 16px; width: 220px; height: 220px; float: right;" src="{$minimap_url}{foreach name=newcaches from=$newcaches item=cacheitem}|{$cacheitem.latitude},{$cacheitem.longitude}{/foreach}" />
{/if}
<ul class="nodot">
    {foreach name=newcaches from=$newcaches item=cacheitem}
        <li class="newcache_list_multi" style="margin-bottom: 8px;">
            <table class="null" cellspacing="0" cellpadding="0"><tr>
            <td style="vertical-align:top; padding-right:2px; padding-top:2px" rowspan="2">{include file="res_cacheicon_22.tpl" cachetype=$cacheitem.type}</td>
            <td style="vertical-align:top; ">{$cacheitem.date_created|date_format:$opt.format.date}&nbsp;&nbsp;</td>
            <td style="text-align:left; width:100%"><b><a class="links" href="viewcache.php?cacheid={$cacheitem.cache_id}">{$cacheitem.name|escape}</a></b> {include file="res_oconly.tpl" oconly=$cacheitem.oconly}
            {t}by{/t}
            <b><a class="links" href="viewprofile.php?userid={$cacheitem.user_id}">{$cacheitem.username|escape}</a></b></td>
            </tr>
            <tr><td colspan="2">
            <strong>
                <p class="content-title-noshade">
                    {$cacheitem.adm1|escape} {if $cacheitem.adm1!=null & $cacheitem.adm2!=null} &gt; {/if}
                    {$cacheitem.adm2|escape} {if ($cacheitem.adm2!=null & $cacheitem.adm4!=null) | ($cacheitem.adm1!=null & $cacheitem.adm4!=null)} &gt; {/if}
                    {$cacheitem.adm4|escape}
                </p>
            </strong>
            </td>
            </tr></table>
        </li>
    {/foreach}
</ul>
{if $minimap_enabled}
</div>
{/if}
