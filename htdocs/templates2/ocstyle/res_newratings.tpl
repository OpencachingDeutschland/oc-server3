{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<ul class="nodot">
    {foreach name=topratings from=$topratings item=cacheitem}
        <li class="newcache_list_multi" style="margin-bottom: 8px;">
            <table class="null" cellspacing="0" cellpadding="0">
                <tr>
                <td style="vertical-align:top; padding-right:2px; padding-top:2px" rowspan="2">{include file="res_cacheicon_22.tpl" cachetype=$cacheitem.type}</td>
                <td style="vertical-align:top; ">
                    {if $cacheitem.cRatings<4}
                        {if $cacheitem.cRatings>0}<img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}" style="margin-top: -2px;"/>{/if}
                        {if $cacheitem.cRatings>1}<img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}" style="margin-top: -2px;"/>{/if}
                        {if $cacheitem.cRatings>2}<img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}" style="margin-top: -2px;"/>{/if}
                    {else}
                        <b><span style="color:#02c602; font-size: 14px;" >{$cacheitem.cRatings}x</span></b><img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}" style="margin-top: -2px;" />
                    {/if}
                    <b><a class="links" href="viewcache.php?cacheid={$cacheitem.cache_id}">{$cacheitem.name|escape}</a></b> {include file="res_oconly.tpl" oconly=$cacheitem.oconly}
                    <text class="links">{t}by{/t}</text>
                    <b><a class="links" href="viewprofile.php?userid={$cacheitem.user_id}">{$cacheitem.username|escape}</a></b>
                </td>
            </tr>
            <tr>
                <td colspan="2">
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
<p class="systemlink" style="line-height: 1.6em;">{t}You can find more recommendations &gt;<a href="tops.php">here</a>&lt;.{/t}</p>
