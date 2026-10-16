{***************************************************************************
* Shared donation progress bar snippet.
* Required Smarty vars: $donation_year, $donation_target, $donation_current,
*                       $donation_last_updated
* You can find the license in the docs directory.
***************************************************************************}
{if $donation_target > 0}
    {assign var="donation_percent" value=$donation_current/$donation_target*100}
    {if $donation_percent > 100}{assign var="donation_percent" value=100}{/if}

    <div style="width: 100%; max-width: 565px; background-color: #eee; border-radius: 4px; margin: 20px 0 10px 0; height: 30px; position: relative; border: 1px solid #ccc;">
        <div style="width: {$donation_percent}%; background-color: #5890a8; height: 100%; border-radius: 3px 0 0 3px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
            {$donation_current|string_format:"%.2f"} &euro;
        </div>
    </div>
    <div style="width: 100%; max-width: 565px; display: flex; justify-content: space-between; font-size: 0.9em; margin-bottom: 20px;">
        <span>0 &euro;</span>
        <span>{$donation_target} &euro;</span>
    </div>
{/if}




