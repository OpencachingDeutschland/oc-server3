{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
 {* OCSTYLE *}

	{if $wp!=''}
		<div style="margin-top:4px;">
			<p style="color: 5890a8"><b><a href="viewcache.php?wp={$wp}" target="_blank">{$cachename}</a></b><br />
			<p style="color: 5890a8">{t}by{/t} {$owner}</p>
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
		<p style="color: 5890a8"><b>Gau&szlig;-Kr&uuml;ger</b> <small>(Potsdam-Datum)</small><br />
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
