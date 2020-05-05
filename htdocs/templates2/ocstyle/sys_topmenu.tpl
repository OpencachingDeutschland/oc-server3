{***************************************************************************
 * You can find the license in the docs directory
 ***************************************************************************}
{* OCSTYLE *}
{strip}
    {foreach name=topmenu from=$items item=menuitem}

        <li class="nav-item">
            <a class="nav-link {if $menuitem.selected}active{/if}"
               href="{$menuitem.href}"
                    {$menuitem.target}
                    >{$menuitem.menustring|escape}</a>
        </li>

    {/foreach}
{/strip}
