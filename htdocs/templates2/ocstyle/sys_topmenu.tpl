{***************************************************************************
 * You can find the license in the docs directory
 ***************************************************************************}
 {* OCSTYLE *}
{strip}
    {foreach name=topmenu from=$items item=menuitem}
        <li><a href="{$menuitem.href}" {if isset($menuitem.target)}{$menuitem.target|default}{/if} {if $menuitem.selected} class="selected bg-green06"{/if}>{$menuitem.menustring|escape}</a></li>
    {/foreach}
    {* New UI Early Access Toggle *}
    <li>
        <label for="useNewUIToggle" style="display: block; float: left; margin: 0; padding: 5px 10px; color: rgb(255,255,255); font-weight: bold; font-size: 100%; cursor: pointer; border-left: solid 1px rgb(54,83,151); white-space: nowrap;">
            <input type="checkbox" id="useNewUIToggle" style="margin: 0 4px 0 0; cursor: pointer; vertical-align: middle;">Try new UI
        </label>
    </li>
{/strip}