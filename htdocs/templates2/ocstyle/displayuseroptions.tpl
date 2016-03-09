{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{foreach from=$dUseroptions item=useropt}
    <tr>
        <td style="vertical-align:top; width:10px">
            {if $useropt.option_visible==1 || ($useropt.optionset=="1" && $useropt.option_value=="1")}
                <span class="public-setting"><nobr>{$useropt.name|escape}{t}#colonspace#{/t}:</nobr></span>
            {else}
                <nobr>{$useropt.name|escape}{t}#colonspace#{/t}:</nobr>
            {/if}
        </td>
        <td>
            {if $edit==true}
                {if $useropt.option_input=="text"}
                    <input type="text" name="inp{$useropt.id}" value="{$useropt.option_value|escape}" class="input200" />
                {/if}
                {if $useropt.option_input=="textarea"}
                    <textarea class="logs" cols="68" rows="6" name="inp{$useropt.id}" style="max-width:500px; max-height:250px">{$useropt.option_value|escape}</textarea>
                {/if}
                {if $useropt.option_input=="checkbox"}
                    <input type="checkbox" class="checkbox" name="inp{$useropt.id}" value="1" {if $useropt.option_value=="1"}checked="checked"{/if} />
                {/if}
            {else}
                {if $useropt.option_input=="checkbox"}
                    {if $useropt.option_value=="1"}
                        {if $useropt.optionset=="1"}
                            <span class="public-setting">{t}Yes{/t}</span>
                        {else}
                            {t}Yes{/t}
                        {/if}
                    {else}
                        {t}No{/t}
                    {/if}
                {else}
                    {if $useropt.option_visible==1}
                        <span class="public-setting">{$useropt.option_value|escape}</span>
                    {else}
                        {$useropt.option_value|escape}
                    {/if}
                {/if}
            {/if}
        </td>
        <td style="vertical-align:top;">
            {if $edit==true}
                {if $useropt.internal_use!=1}
                    <input type="checkbox" name="chk{$useropt.id}" value="1"{if $useropt.option_visible==1} checked="checked"{/if} class="checkbox" /> {t}show{/t}
                {/if}
            {else}
                {if $useropt.internal_use!=1}
                    {if $useropt.option_visible==1}
                        <span class="public-setting">{t}visible{/t}</span>
                    {else}
                        <span style="color:#666666;">{t}invisible{/t}</span>
                    {/if}
                {else}
                    <!-- <span style="color:#666666;">{t}internal{/t}</span> -->
                {/if}
            {/if}
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="3">{t}No information on user details found.{/t}</td>
    </tr>
{/foreach}