{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<table class="stattable">
    <tr>
        <th class="h1 nodatacell" style="text-align:right">{if $userid>0}<nobr>{if @$oco81_helplink}{$oco81_helplink}{else}<a class="systemlink" href="oconly81.php">{/if}OConly-81</a>&nbsp;&nbsp;&nbsp;</nobr>{/if}</th>
        <th class="h1 nodatacell" colspan="11" style="line-height:1.8em">{t}Terrain{/t}</th>
    </tr>
    <tr>
        <td></td>
        <td>&nbsp;<img src="resource2/ocstyle/images/log/16x16-found.png" /></td>
        {foreach from=$stat81 key=step item=dummy}
            <th style="text-align:center">{$step/2}</th>
        {/foreach}
        <th class="h1">Σ</th>
        <td>&nbsp;</td>
    </tr>

    {assign var=matrixfound value=0}
    {assign var=totalsum value=0}
    {foreach from=$stat81 key=difficulty item=terrains name=difficulty}
        <tr>
            {if $smarty.foreach.difficulty.first}
                <th class="h1" rowspan="9">{t}Difficulty{/t}&nbsp;&nbsp;&nbsp;</th>
            {/if}
            <th>&nbsp;&nbsp;{$difficulty/2}</th>
            {assign var=dsum value=0}
            {foreach from=$terrains key=terrain item=count}
                <td style="text-align:center; background-color:{if $count}rgb({$count/$stat81_maxcount*-242+242.5|floor},{$count/$stat81_maxcount*-242+242.5|floor},242){else}#f2f2f2{/if}" {if $count}onclick='location.href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;{if $userid>0}f_inactive=0&amp;f_disabled=0&amp;finderid={$userid}&amp;searchbyfinder={else}f_inactive=1&amp;f_disabled=1&amp;searchall={/if}&amp;logtype=1,7&amp;calledbysearch=0&amp;cache_attribs=6&amp;terrainmin={$terrain}&amp;terrainmax={$terrain}&amp;difficultymin={$difficulty}&amp;difficultymax={$difficulty}"'{/if}>
                    {if $count}
                        <span style="cursor:pointer; color:{if $count > $stat81_maxcount/3}#fff{else}#000{/if}">{$count}</span>
                        {assign var=dsum value=$dsum+$count}
                        {assign var=matrixfound value=$matrixfound+1}
                    {else}&nbsp;{/if}
                </td>
            {/foreach}
            <th class="h0" style="text-align:center">{if $dsum}{$dsum}{assign var=totalsum value=$totalsum+$dsum}{/if}</th>
        </tr>
    {/foreach}

    <tr>
        <td rowspan="2"></td>
        <th class="h1">Σ</th>
        {foreach from=$stat81_tsum item=count}
            <th class="h0">{if $count}{$count}{/if}</th>
        {/foreach}
        <td style="text-align:center"><b>{$totalsum}</b></td>
    </tr>
    {if $userid > 0}
        <tr>
            <td colspan="12" style="padding-top:0.5em"><p>{t 1=$matrixfound}The user has found <b>%1</b> of <b>81</b> theoretically possible terrain/difficulty combinations.{/t}</p></td>
        </tr>
    {/if}
</table>
