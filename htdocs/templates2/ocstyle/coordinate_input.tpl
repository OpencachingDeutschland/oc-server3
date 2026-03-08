{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
<div id="coord_single" style="display:none">
  <input type="text" id="coord_unified" size="35" placeholder="N00 00.000 E000 00.000" {if $disabled}disabled=""{/if} />
  <br />
  <span id="coord_feedback"></span>
</div>
<div id="coord_detail">
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
        <option value="E" {if $lon_hem == 'E'} selected {/if}>{t}E{/t}</option>
        <option value="W" {if $lon_hem == 'W'} selected {/if}>{t}W{/t}</option>
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
</div>
<input type="hidden" name="latitude" id="coord_lat" value="{$coord_latitude|default:''}" />
<input type="hidden" name="longitude" id="coord_lon" value="{$coord_longitude|default:''}" />
<a href="#" id="coord_toggle" style="display:none; font-size:11px"
   data-label-6="{t escape=js}6 fields{/t}"
   data-label-1="{t escape=js}1 field{/t}"
   data-label-parse-error="{t escape=js}Could not parse coordinates{/t}"></a>
<script type="module" src="resource2/ocstyle/js/coord_input.js"></script>
