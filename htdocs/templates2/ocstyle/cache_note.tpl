{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
  <input type="hidden" name="cacheid" value="{$cacheid|escape}" />

  <table class="table">
    <tr valign="top">
      <td>{t}Note:{/t}</td>
      <td>
        <textarea name="note" rows="4" cols="60" >{$note}</textarea>
      </td>
      <td>
        <input type="checkbox" class="checkbox" name="incl_coord" value="true" {if $inclCoord}checked="checked"{/if}/> {t}Include a coordinate in the note{/t}<br />
        {include file='coordinate_input.tpl'}
      </td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">
        <input type="submit" name="submit_cache_note" value="{t}Save{/t}" class="formbutton" onclick="submitbutton('submit_cache_note')" />
      </td>
    </tr>
    <tr>
      <td colspan="3">
        <img src="resource2/{$opt.template.style}/images/viewcache/16x16-info.png" class="icon16" alt="Info" />
        <small style="line-height:1em">
          {t}The note is not visible to other users. The note and the optional coordinate will be included in GPX-file downloads and sent to the GPS device.{/t}
        </small>
      </td>
    </tr>
  </table>
