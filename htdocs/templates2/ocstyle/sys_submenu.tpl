{***************************************************************************
 * You can find the license in the docs directory
 ***************************************************************************}
 {* OCSTYLE *}
{strip}
    {foreach name=submenu from=$items item=menuitem}
        {if $menuitem.href == ''}
            {* separator headline *}
            <li style="background-color:white; line-height:1.1em">&nbsp;</li>
            <li class="title secondtitle">{$menuitem.menustring|escape}</li>
        {else}
            {* selectable menu option *}
            <li class="group{$menuitem.sublevel}{if $menuitem.selected} group_active{/if}">
                <a href="{$menuitem.href}" {$menuitem.target|default}>
                    {$menuitem.menustring|escape}
                    {if strpos($menuitem.href, 'page=donations') !== false}
                        <img src="resource2/misc/donation/globi_box_smal.png" style="height:16px; width:auto; border:none; vertical-align:middle; margin-left:5px;" />
                    {/if}
                </a>
            </li>
        {/if}
    {/foreach}
{/strip}
