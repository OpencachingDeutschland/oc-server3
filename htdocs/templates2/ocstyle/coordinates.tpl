{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
 {* OCSTYLE *}

    {if $wp!=''}
        <div style="margin-top:4px;">
            <p style="color: 5890a8"><b>{$cachename}</b>{if $childwp}, {t}waypoint{/t}&nbsp;{$childwp}<br />
            <p style="color: 5890a8">{t}by{/t} {$owner}{/if}
            </p>
        </div>
    {/if}
    <div style="margin-top:16px;">
        <p style="color: 5890a8"><b>DD,dddd&deg;</b> <small>(WGS84)</small><br />
        {$coordDeg.lat|escape} {$coordDeg.lon|escape}</p>
    </div>
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>DD&deg; MM,mmm&#39;</b> <small>(WGS84)</small><br />
        {$coordDegMin.lat|escape} {$coordDegMin.lon|escape}</p>
    </div>
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>DD&deg; MM&#39; SS&#39;&#39;</b> <small>(WGS84)</small><br />
        {$coordDegMinSec.lat|escape} {$coordDegMinSec.lon|escape}</p>
    </div>
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>UTM</b> <small>(WGS84)</small><br />
        {$coordUTM.zone|escape}{$coordUTM.letter|escape} {$coordUTM.north|escape} {$coordUTM.east|escape} </p>
    </div>
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>{t}Gauss-Krüger{/t}</b>{if $opt.template.locale=='DE'} <small>(Potsdam-Datum)</small>{/if}<br />
        {$coordGK|escape}</p>
    </div>
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>QTH Locator</b><br />
        {$coordQTH|escape}</p>
    </div>
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>Swissgrid</b> <small>(CH1903)</small><br />
        {$coordSwissGrid.coord|escape}</p>
    </div>
    {if $showRD}
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>RD</b> <small>(Dutch Grid)</small><br />
        {$coordRD|escape}</p>
    </div>
    {/if}
    {if $coordW3W1}
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>what3words</b> <small>({$W3Wlang1})</small><br />
        <a href="http://what3words.com/{$coordW3W1|escape}" target="w3w">{$coordW3W1|escape}</a></p>
    </div>
    {/if}
    {if $coordW3W2}
    <div style="margin-top:4px;">
        <p style="color: 5890a8"><b>what3words</b> <small>({$W3Wlang2})</small><br />
        <a href="http://what3words.com/{$coordW3W2|escape}" target="w3w">{$coordW3W2|escape}</a></p>
    </div>
    {/if}
