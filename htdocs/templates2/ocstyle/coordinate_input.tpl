{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<table class="table">
  <tr>
    <td>
      <select name="lat_hem" {if $disabled}disabled=""{/if}>
        <option value="N" {if $lat_hem == 'N'} selected {/if}>{t}N{/t}</option>
        <option value="S" {if $lat_hem == 'S'} selected {/if}>{t}S{/t}</option>
      </select>
    </td>
    <td>
      <nobr><input type="text" name="lat_deg" maxlength="2" value="{$lat_deg}" class="input30" {if $disabled}disabled=""{/if} /> &deg;</nobr>
    </td>
    <td>
      <nobr><input type="text" name="lat_min" maxlength="6" value="{$lat_min}" class="input50" {if $disabled}disabled=""{/if} /> '</nobr>
    </td>
  </tr>
  <tr>
    <td>
      <select name="lon_hem" {if $disabled}disabled=""{/if}>
        <option value="E" {if $lat_hem == 'E'} selected {/if}>{t}E{/t}</option>
        <option value="W" {if $lat_hem == 'W'} selected {/if}>{t}W{/t}</option>
      </select>
    </td>
    <td>
      <nobr><input type="text" name="lon_deg" maxlength="3" value="{$lon_deg}" class="input30" {if $disabled}disabled=""{/if} /> &deg;</nobr>
    </td>
    <td>
      <nobr><input type="text" name="lon_min" maxlength="6" value="{$lon_min}" class="input50" {if $disabled}disabled=""{/if} /> '</nobr>
    </td>
  </tr>

  {if isset($coord_error)}
  <tr>
    <td colspan="3" class="errormsg">
      {$coord_error}
    </td>
  </tr>
  {/if}

</table>
