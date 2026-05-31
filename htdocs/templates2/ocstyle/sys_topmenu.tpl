{***************************************************************************
 * You can find the license in the docs directory
 ***************************************************************************}
 {* OCSTYLE *}
{strip}
    {foreach name=topmenu from=$items item=menuitem}
        <li><a href="{$menuitem.href}" {if isset($menuitem.target)}{$menuitem.target|default}{/if} {if $menuitem.selected} class="selected bg-green06"{/if}>{$menuitem.menustring|escape}</a></li>
    {/foreach}
    {* New UI Early Access Toggle *}
    <li style="border-left: 1px solid #999; padding-left: 10px; margin-left: 10px;">
        <label for="useNewUIToggle" style="display: flex; align-items: center; gap: 6px; margin: 0; padding: 4px 0;">
            <input type="checkbox" id="useNewUIToggle" style="margin: 0; cursor: pointer;">
            <span style="white-space: nowrap; font-size: 0.95em; cursor: pointer;">Try new UI</span>
        </label>
    </li>
{/strip}