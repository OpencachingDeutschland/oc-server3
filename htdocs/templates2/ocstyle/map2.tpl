{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
*
*  TODO: 
*  - implement all GM-controls as GControl (tmd_*, rqc_*)
*  - show wait dialog while requesting data from OC
*  - save and load all search options 
*  - html-popup inactive
*  - ignore/owned/found in post filter?
*  - download as GPX etc.pp.
*  - smaller icons with any caches shown
*  - implement fullscreen
*  - monitor and improve DB cleanup
*  - review/rewrite mapsubmit_click
*  - rewrite function names
*  - use resultid in each map.php query
*  - check if slave db connection works as expected (especially sys_repl_exclude / master connection)
*  - save and load search config from different profiles that the user can modify
*  - show labels inside the map
*  - GPX download of current view
*  - save and load current view
*  - switch current options/view to search result page
***************************************************************************}
{* OCSTYLE *}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_balloon.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_centerwindow.js"></script>

{if $opt.template.popup==false}
	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="" />
		{t}Map{/t}
	</div>

	<div class="mapform">
		<form onsubmit="javascript:mapsubmit_click(); return false;" id="cachemap">
			<table class="mapsearch">
				<tr>
					<td class="mapsearch-a">
						<input type="text" id="mapsearch" value="" onfocus="javascript:mapsearch_onfocus()" onblur="javascript:mapsearch_onblur()" class="searchfield" size="50" />
					</td>
					<td class="mapsearch-b">
						<input type="button" id="mapsubmit" value="{t}Search{/t}" onclick="javascript:mapsubmit_click()" class="searchbutton" />
					</td>
					<td class="mapsearch-c">
						<a href="#" onclick="javascript:showPermlinkBox_click()"><img src="resource2/{$opt.template.style}/images/map/35x35-star.png" align="right" style="margin-left:15px; margin-right: 0px;" height="35" width="35" alt="{t}Show link to this map{/t}" /></a>
						{if !$bDisableFullscreen}
							<a href="#" onclick="javascript:fullscreen_click()"><img src="resource2/{$opt.template.style}/images/map/35x35-fullscreen.png" align="right" style="margin-left:15px; margin-right: 0px;" height="35" width="35" alt="{t}Switch to full screen{/t}" /></a>
						{/if}
						<a href="#" onclick="javascript:download_gpx()"><img id="download_gpx_img" src="resource2/{$opt.template.style}/images/map/35x35-gpx-download.png" align="right" style="margin-left:15px; margin-right: 0px;" height="35" width="35" alt="{t}Download GPX file (max. 500){/t}" /></a>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<div class="mapselectgeocode">
		<select id="mapselectlist" name="mapselectlist" size="6" class="mapselectlist" onblur="mapselectlist_onblur()" onchange="mapselectlist_onchange()">
		</select>
	</div>

	<div id="permalink_box" class="mappermalink" style="display:none;">
		<table>
			<tr><td><img src="resource2/ocstyle/images/viewcache/link.png" alt="" height="16" width="16" /> {t}Link{/t} ( <a href="#" onclick="javascript:openPermalink_click()">{t}Open{/t}</a> )</td></tr>
			<tr><td><input id="permalink_text" type="text" value="" size="50"/></td></tr>
			<tr id="permalink_addFavorites"><td align="right"><input type="button" value="{t}Add to favorites...{/t}" onclick="javascript:addFavorites_click()" /></td></tr>
		</table>
	</div>
{/if}

{if $opt.template.popup==false}
	<p>&nbsp;</p>
{/if}

{if $opt.template.popup==false}
	<div id="map" style="width:770px;height:600px;"></div>
	<div style="width:770px;text-align:right;">{t}Caches displayed{/t} <span id="statCachesCount">0</span>, {t}Time to load{/t} <span id="statLoadTime">0</span> {t}Sec.{/t}</div>
{else}
	<div id="map" style="width:100%;height:100%;"></div>
{/if}

{if $opt.template.popup==false}

<div class="buffer" style="width: 500px; height: 5px;">&nbsp;</div>
	<p class="content-title-noshade-size2">{t}Only show Geocaches with the following properties:{/t}</p>
<div class="buffer" style="width: 500px; height: 5px;">&nbsp;</div>
	<table>
		<tr>
			<td class="mapfilter pad10" width="752"><strong>{t}Name:{/t}</strong> <input type="text" id="cachename" name="cachename" value="" onkeypress="filter_changed()" onchange="filter_changed()" class="input200" /></td>
		</tr>
	</table>
	<table>
		<tr>
			<td valign="top" class="mapfilter pad10" width="140">
				<table>
					<tr><td class="mapfiltertopic">{t}Cachetype{/t}</td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					{foreach from=$aCacheType item=cacheTypeItem}
						<tr>
							<td>
								<input type="checkbox" id="cachetype{$cacheTypeItem.id}" name="cachetype{$cacheTypeItem.id}" value="1" checked="checked" onclick="filter_changed()" class="checkbox" />
								<label for="cachetype{$cacheTypeItem.id}">{$cacheTypeItem.text|escape}</label>
							</td>
						</tr>
					{/foreach}
				</table>
			</td>
			<td valign="top" class="mapfilter pad10" width="150">
				<table>
					<tr><td class="mapfiltertopic">{t}Cache container{/t}</td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					{foreach from=$aCacheSize item=cacheSizeItem}
						<tr>
							<td>
								<input type="checkbox" id="cachesize{$cacheSizeItem.id}" name="cachesize{$cacheSizeItem.id}" value="1" checked="checked" onclick="filter_changed()" class="checkbox" />
								<label for="cachesize{$cacheSizeItem.id}">{$cacheSizeItem.text|escape}</label>
							</td>
						</tr>
					{/foreach}
				</table>
			</td>
			<td valign="top" class="mapfilter pad10" width="110">
				<table>
					<tr><td class="mapfiltertopic">{t}Hide{/t}</td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					<tr>
						<td>
							<input type="checkbox" id="f_userowner" name="f_userowner" value="1" onclick="filter_changed()" class="checkbox" />
							<label for="f_userowner">{t}My owns{/t}</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" id="f_userfound" name="f_userfound" value="1" onclick="filter_changed()" class="checkbox" />
							<label for="f_userfound">{t}My finds{/t}</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" id="f_ignored" name="f_ignored" value="1" onclick="filter_changed()" class="checkbox" />
							<label for="f_ignored">{t}My ignored{/t}</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" id="f_inactive" name="f_inactive" value="1" checked="checked" onclick="filter_changed()" class="checkbox" />
							<label for="f_inactive">{t}Not active{/t}</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" id="f_otherPlatforms" name="f_otherPlatforms" value="1" onclick="filter_changed()" class="checkbox" />
							<label for="f_otherPlatforms">{t}Double listings{/t}</label>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" class="mapfilter pad10" width="280"> 
				<table>
					<tr>
						<td colspan="2" class="mapfiltertopic">{t}Rating{/t}</td>
					<tr><td colspan="2"><span style="line-height: 5px;">&nbsp;</span></td></tr>
					</tr>
					<tr>
						<td>{t}Difficulty{/t}</td>
						<td>
							<select id="difficultymin" name="difficultymin" onchange="filter_changed()">
								<option value="0" selected="selected">-</option>
								<option value="2">1.0</option>
								<option value="3">1.5</option>
								<option value="4">2.0</option>
								<option value="5">2.5</option>
								<option value="6">3.0</option>
								<option value="7">3.5</option>
								<option value="8">4.0</option>
								<option value="9">4.5</option>
								<option value="10">5.0</option>
							</select>
							&nbsp;&nbsp;&nbsp;{t}to{/t}&nbsp;&nbsp;&nbsp;
							<select id="difficultymax" name="difficultymax" onchange="filter_changed()">
								<option value="0" selected="selected">-</option>
								<option value="2">1.0</option>
								<option value="3">1.5</option>
								<option value="4">2.0</option>
								<option value="5">2.5</option>
								<option value="6">3.0</option>
								<option value="7">3.5</option>
								<option value="8">4.0</option>
								<option value="9">4.5</option>
								<option value="10">5.0</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>{t}Terrain{/t}</td>
						<td>
							<select id="terrainmin" name="terrainmin" onchange="filter_changed()">
								<option value="0" selected="selected">-</option>
								<option value="2">1.0</option>
								<option value="3">1.5</option>
								<option value="4">2.0</option>
								<option value="5">2.5</option>
								<option value="6">3.0</option>
								<option value="7">3.5</option>
								<option value="8">4.0</option>
								<option value="9">4.5</option>
								<option value="10">5.0</option>
							</select>
							&nbsp;&nbsp;&nbsp;{t}to{/t}&nbsp;&nbsp;&nbsp;
							<select id="terrainmax" name="terrainmax" onchange="filter_changed()">
								<option value="0" selected="selected">-</option>
								<option value="2">1.0</option>
								<option value="3">1.5</option>
								<option value="4">2.0</option>
								<option value="5">2.5</option>
								<option value="6">3.0</option>
								<option value="7">3.5</option>
								<option value="8">4.0</option>
								<option value="9">4.5</option>
								<option value="10">5.0</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>{t}Min. recommendations{/t}</td>
						<td>
							<select id="recommendationmin" name="recommendationmin" onchange="filter_changed()">
								<option value="0" selected="selected">-</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	{* attributes *}
	<table>
		<tr>
			<td  valign="top" class="mapfilter pad10" width="752">
				<table>
					<tr><td class="mapfiltertopic">Attribute</td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					<tr>
						<td>
							{include file="res_attribgroup.tpl" attriblist=$aAttributes onmousedown="attribute_onmousedown" inputprefix="attribute" stateDisable=$aAttributesDisabled}
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<div class="buffer" style="width: 500px; height: 5px;">&nbsp;</div>

{/if}

{literal}
  <script type="text/javascript">
  <!--
		var mnInitLat = {/literal}{$nGMInitLat}{literal};
		var mnInitLon = {/literal}{$nGMInitLon}{literal};
		var mnInitZoom = {/literal}{$nGMInitZoom}{literal};
		var mbInitCookiePos = {/literal}{$bGMInitCookiePos}{literal};
		var msInitWaypoint = "{/literal}{$sGMInitWaypoint}{literal}";
		var msInitType = '';

		var msSearchHint = "{/literal}{t escape=js}Search for city, cache or waypoint{/t}{literal}";
		var mbResetSearchTextOnFocus = false;
		var msPermalink = 'map2.php';

		var msInitCookieLastPosName = 'ocgmlastpos';
		var msInitCookieConfigName = 'ocgmconfig';
		if (!navigator.cookieEnabled)
		{
			msInitCookieLastPosName = '';
			msInitCookieConfigName = '';
		}

		var moGeocoder = new GClientGeocoder();
		var moSearchList = document.getElementById('mapselectlist');
		var maSearchListCoords = new Array();
		var moMapSearch = document.getElementById('mapsearch');
		var moMap = null;

		var moMarkerList = new Array();
		var msPopupMarkerWP = '';

		var mnMapWidth = 770;
		var mnMapHeight = 600;

		// id of search.php result
		var msURLSearchPHP = 'search.php';
		var msURLMapPHP = 'map2.php';
		var mnResultId = 0;
		var mbDataDownloadHaveSecondChance = false;
		var mbDataDownloadStartTime;
		var moDataLoadTimer = null;

		var mnMaxDownloadCount = 500;
		var mbDownloadEnabled = false;

		function cookieLoad()
		{
			if (msInitCookieConfigName == '')
				return;

			var sCookieContent = document.cookie.split(";");
			for (var nIndex = 0; nIndex < sCookieContent.length; nIndex++)
			{
				var sCookieValue = trim(sCookieContent[nIndex]).split("=");
				if (sCookieValue[0] == msInitCookieLastPosName)
				{
					var sSavedLastPos = sCookieValue[1];
					if (mbInitCookiePos == 1)
					{
						var sValues = sSavedLastPos.split(":");
						mnInitZoom = parseInt(sValues[0]);
						mnInitLon = parseFloat(sValues[1]);
						mnInitLat = parseFloat(sValues[2]);
					}
				}
				else if (sCookieValue[0] == msInitCookieConfigName)
				{
					msInitType = sCookieValue[1];
				}
			}
		}

		function cookieSave()
		{
			if (msInitCookieConfigName == '')
				return;

			var oCenterPos = moMap.getCenter();
			var nZoomLevel = moMap.getZoom();
			var dCookieExp = new Date(2049, 12, 31);

			document.cookie = msInitCookieConfigName + "=" + moMap.getCurrentMapType().getName(false) + ";expires=" + dCookieExp.toUTCString();
			document.cookie = msInitCookieLastPosName + "=" + nZoomLevel + ":" + oCenterPos.lng() + ":" + oCenterPos.lat() + ";expires=" + dCookieExp.toUTCString();

			msPermalink = msURLMapPHP + "?lat=" + oCenterPos.lat() + "&lon=" + oCenterPos.lng() + "&zoom=" + nZoomLevel + "&map=" + encodeURI(moMap.getCurrentMapType().getName(false));
			
			var oPermalinkTextBox = document.getElementById('permalink_text');
			if (oPermalinkTextBox)
				oPermalinkTextBox.value = "{/literal}{$opt.page.absolute_url}{literal}" + msPermalink;
		}

		function mapLoad()
		{
			if (GBrowserIsCompatible())
			{
        var aDragZoomOptions = { 
          buttonStartingStyle: {display:'block',color:'black',background:'white',width:'7em',textAlign:'center',fontFamily:'Verdana',fontSize:'8px',fontWeight:'bold',border:'1px solid gray',paddingBottom:'1px',cursor:'pointer'},
          buttonHTML: '{t escape=js}Drag Zoom{/t}',
          buttonZoomingHTML: '{t escape=js}Drag a region on the map (click here to reset){/t}',
          buttonZoomingStyle: {background:'yellow'},
          backButtonHTML: '{t escape=js}Drag Zoom Back{/t}',  
          backButtonStyle: {display:'none',marginTop:'3px',background:'#FFFFC8'},
          backButtonEnabled: false
        } 
				cookieLoad();

        moMap = new GMap2(document.getElementById("map"));
        moMap.addMapType(G_PHYSICAL_MAP);
				OSM_addMapType();
        moMap.addControl(new GSmallZoomControl());
        moMap.addControl(new GHierarchicalMapTypeControl());
        moMap.addControl(new GOverviewMapControl());
        moMap.addControl(new GScaleControl());
        moMap.addControl(new DragZoomControl({}, aDragZoomOptions, {}), new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(28,8)));
        moMap.addControl(new LatLonDisplayControl());
        //moMap.addControl(new MessageControl(250, 125));
        rqc_initialize(moMap);
        tmd_initialize(moMap);
        moMap.enableScrollWheelZoom();
        moMap.setCenter(new GLatLng(mnInitLat, mnInitLon), mnInitZoom, map_GetMapTypeByName(msInitType));

        GEvent.addListener(moMap, "movestart", function(){map_movestart()});
        GEvent.addListener(moMap, "moveend", function(){map_moveend()});
        GEvent.addListener(moMap, "maptypechanged", function(){map_maptypechanged()});

        if (msInitWaypoint != "")
					show_cachepopup_wp(msInitWaypoint, true);

				if (moMapSearch)
	        moMapSearch.value = msSearchHint;

				// built initial msPermalink
        cookieSave();

        moDataLoadTimer = window.setTimeout('data_load()', 2000);
			}
		}

		function map_GetMapTypeByName(sName)
		{
			var oMapTypeFound = null;
			var aMapTypes = moMap.getMapTypes();

			for (var nIndex=0; nIndex<aMapTypes.length; nIndex++)
			{
				if (aMapTypes[nIndex].getName(false) == sName)
				{
					oMapTypeFound = aMapTypes[nIndex];
					break;
				}
			}

			if (oMapTypeFound==null)
				oMapTypeFound = G_HYBRID_MAP;

			return oMapTypeFound;
		}

		function map_movestart()
		{
			window.clearTimeout(moDataLoadTimer);
		}

		function map_moveend()
		{
			cookieSave();
			
			if (!rqc_visible())
				moDataLoadTimer = window.setTimeout('data_load()', 500);
		}

		function map_maptypechanged()
		{
			cookieSave();
		}

		function fullscreen_click()
		{
			// TODO
			window.open(msPermalink.replace(/map2\.php/, 'map.php') + '&mode=fullscreen','ocgm','width=' + screen.width + ',height=' + screen.height + ',resizable=yes,scrollbars=1');
		}

		function showPermlinkBox_click()
		{
			var oBox = document.getElementById('permalink_box');

			if (window.opera)
				document.getElementById('permalink_addFavorites').style.display = 'none';
			else
			{
				if ((typeof window.external.AddFavorite == 'undefined') && 
						(typeof window.external.addPanel == 'undefined'))
				{
					document.getElementById('permalink_addFavorites').style.display = 'none';
				}
			}

			if (oBox.style.display == 'none')
				oBox.style.display = 'block';
			else
				oBox.style.display = 'none';
		}

		function addFavorites_click()
		{
			var sLink = document.getElementById('permalink_text').value;
			var sTitle = '{/literal}{$opt.page.title} - {t escape=js}Map{/t}{literal}';

			if (typeof window.external.AddFavorite != 'undefined')
				window.external.AddFavorite(sLink, sTitle);

			if (typeof window.external.addPanel != 'undefined')
				window.external.addPanel(sTitle, sLink, '');
		}

		function openPermalink_click()
		{
			window.location = document.getElementById('permalink_text').value;
		}

		function mapselectlist_onblur()
		{
			mapselectlist_hide();
		}

		function mapselectlist_clear()
		{
			while (moSearchList.length>0)
				moSearchList.options[0] = null;
			maSearchListCoords = new Array();
		}

		function add_searchlist_itemcoords(nLat, nLon, sWaypoint, sText)
		{
			var aItem = new Array();
			aItem['lat'] = nLat;
			aItem['lon'] = nLon;
			aItem['wp'] = sWaypoint;
			aItem['text'] = sText;

			maSearchListCoords[maSearchListCoords.length] = aItem;
			return maSearchListCoords.length-1;
		}

		function mapselectlist_show()
		{
			moSearchList.style.display = "block";
		}

		function mapselectlist_hide()
		{
			moSearchList.style.display = "none";
		}

		function mapselectlist_onchange()
		{
			for (var nIndex=0; nIndex<moSearchList.length; nIndex++)
			{
				if (moSearchList.options[nIndex].selected)
				{
					if (moSearchList.options[nIndex].value == -1)
						return;

					var sText = moSearchList.options[nIndex].text;
					var nCoordsIndex = moSearchList.options[nIndex].value;
					document.getElementById('mapsearch').value = trim(sText);
					mbResetSearchTextOnFocus = true;
					mapselectlist_hide();

					// go to the location
					searchlist_openitem(nCoordsIndex);

					return;
				}
			}
		}

		function searchlist_openitem(nIndex)
		{
			var nLat = maSearchListCoords[nIndex]['lat'];
			var nLon = maSearchListCoords[nIndex]['lon'];
			var sWaypoint = maSearchListCoords[nIndex]['wp'];
			var sText = maSearchListCoords[nIndex]['text'];

			if (sWaypoint != '')
				show_cachepopup_wp(sWaypoint, true);
			else
			{
				var oCoords = new GLatLng(nLat, nLon);
				moMap.setCenter(oCoords, 13);
				moMap.openInfoWindowHtml(oCoords, xmlentities(sText));
			}
		}

		function mapsubmit_click()
		{
			var sSearchText = moMapSearch.value;
			var oTempOption;

			if (!moMap) return;

			if (msSearchHint==sSearchText || sSearchText=='')
			{
				alert('{t escape=js}Enter a search text, please!{/t}');
				return;
			}

			// check for geocaching waypoint
			if (sSearchText.match(/^OC[\S]{1,}$/i) || 
			    sSearchText.match(/^GC[\S]{1,}$/i) || 
			    sSearchText.match(/^N[0-9]{1,5}$/i))
			{
				show_cachepopup_wp(sSearchText, true);
				return;
			}

			// do search on opencaching.de
			// TODO: ensure mnResultId is set
			var oCenterPos = moMap.getCenter();
			GDownloadUrl(msURLMapPHP + "?mode=namesearch&name=" + encodeURI(sSearchText) + "&lat=" + oCenterPos.lat() + "&lon=" + oCenterPos.lng() + "&resultid=" + mnResultId, 
			  function(data, responseCode)
			  {
          var xml = GXml.parse(data);
    			var caches = xml.documentElement.getElementsByTagName("cache");

			    // clear the result list
			    mapselectlist_clear();

          if (caches.length>0)
          {
  			    oTempOption = new Option("Gefundene Geocaches", -1);
	  		    oTempOption.style.color = "gray";
		  	    moSearchList.options[moSearchList.length] = oTempOption;

		  	    for (var nCacheIndex=0; nCacheIndex<caches.length; nCacheIndex++)
		  	    {
		  	      var name = caches[nCacheIndex].getAttribute("name");
		  	      var wpoc = caches[nCacheIndex].getAttribute("wpoc");
		  	      var text = name + " (" + wpoc + ")";
							var value = add_searchlist_itemcoords(0, 0, wpoc, text);
							var item = new Option("     " + text, value);
							item.style.marginLeft = "20px";
							moSearchList.options[moSearchList.length] = item;
		  	    }
		  	    
		  	    if (caches.length >= 30)
		  	    {
							var item = new Option("     {/literal}{t escape=js}Some more items found...{/t}{literal}", -1);
							item.style.marginLeft = "20px";
							item.style.color = "gray";
							moSearchList.options[moSearchList.length] = item;
		  	    }
		  	  }

          // do search on google
			    moGeocoder.setViewport(new GLatLngBounds(new GLatLng(56, 5), new GLatLng(47, 16)));
			    moGeocoder.getLocations(sSearchText, 
				    function(retval)
				    {
					    if (retval["Status"]["code"] == 602)
					    {
						    // no result
					    }
					    else if (retval["Status"]["code"] != 200)
					    {
						    alert("Fehler während der Suche!");
						    return;
					    }

					    var nPlacemarksCount = 0;
					    var nPlacemarkIndex;

					    if (retval["Status"]["code"] == 200)
						    nPlacemarksCount = retval["Placemark"].length;

					    if (nPlacemarksCount>0)
					    {
						    oTempOption = new Option("Gefundene Orte (Google)", -1);
						    oTempOption.style.color = "gray";
						    moSearchList.options[moSearchList.length] = oTempOption;

						    for (nPlacemarkIndex=0; nPlacemarkIndex<nPlacemarksCount; nPlacemarkIndex++)
						    {
							    var coord = retval["Placemark"][nPlacemarkIndex]["Point"]["coordinates"];
							    var text = retval["Placemark"][nPlacemarkIndex]["address"];
							    var value = add_searchlist_itemcoords(coord[1], coord[0], "", text);
							    var item = new Option("     " + text, value);
									item.style.marginLeft = "20px";
							    moSearchList.options[moSearchList.length] = item;
						    }
					    }

					    if (moSearchList.length==0)
					    {
						    mapselectlist_hide();
						    alert("'" + sSearchText + "' mit den unten ausgewählten Suchkriterien nicht gefunden!");
						    return;
					    }
					    else if (maSearchListCoords.length==1)
					    {
								mapselectlist_hide();
								searchlist_openitem(0);
								return;
					    }

					    // adjust size
					    if (moSearchList.length>15)
						    moSearchList.size = 15;
					    else
						    moSearchList.size = moSearchList.length;

					    mapselectlist_show();
				    });
        });
		}

		function show_cachepopup_wp(sWaypoint, bAllowZoomChange)
		{
			show_cachepopup_url(msURLMapPHP + "?mode=wpsearch&wp=" + sWaypoint, sWaypoint, bAllowZoomChange);
		}

		function show_cachepopup_latlon(nLat, nLon, bAllowZoomChange)
		{
			show_cachepopup_url(msURLMapPHP + "?mode=locate&lat=" + nLat + "&lon=" + nLon, "", bAllowZoomChange);
		}

		function show_cachepopup_url(sURL, sWaypoint, bAllowZoomChange)
		{
			moMap.closeInfoWindow();

			GDownloadUrl(sURL, function(data, responseCode)
			{
        var oXML = GXml.parse(data);
        var oCoords = parseXML_GetPoint(oXML);
        if (!oCoords)
        {
          if (sWaypoint != '')
          {
						var sMessage = '{t escape=js}Waypoint %1 not found!{/t}';
						sMessage = sMessage.replace(/%1/, sWaypoint);
  					alert(sMessage);
  				}
					return;
				}

				msPopupMarkerWP = parseXML_GetWaypoint(oXML);

				if (bAllowZoomChange==true)
					moMap.setCenter(oCoords, 13);

        var oPopupHTML = parseXML_GetHTML(oXML);
				moMap.openInfoWindowHtml(oCoords, oPopupHTML);
			});
		}

		function download_gpx()
		{
			var oBounds = moMap.getBounds();
			var params = get_searchfilter_params('gpx', false, true);

			if (mbDownloadEnabled == false)
				return;

			params += '&bbox=' + oBounds.getSouthWest().lng() + ',' + oBounds.getSouthWest().lat() + ',' + oBounds.getNorthEast().lng() + ',' + oBounds.getNorthEast().lat();
			params += '&count=max';

			location.href = msURLSearchPHP + '?' + params;
		}

		function parseXML_GetWaypoint(xmlobject)
		{
			var aCaches = xmlobject.documentElement.getElementsByTagName("cache");
			if (aCaches.length<1)
				return false;
			return aCaches[0].getAttribute("wpoc");
		}

		function parseXML_GetHTML(xmlobject)
		{
			var aCaches = xmlobject.documentElement.getElementsByTagName("cache");
			if (aCaches.length<1)
				return false;

			var sName = aCaches[0].getAttribute("name");
			var sWPOC = aCaches[0].getAttribute("wpoc");
			var sCoords = aCaches[0].getAttribute("coords");
			var bStatusTNA = aCaches[0].getAttribute("status_tna");
			var sStatusText = aCaches[0].getAttribute("status_text");
			var nTypeId = aCaches[0].getAttribute("type_id");
			var sTypeText = aCaches[0].getAttribute("type_text");
			var sSizeText = aCaches[0].getAttribute("size");
			var nDifficulty = aCaches[0].getAttribute("difficulty");
			var nTerrain = aCaches[0].getAttribute("terrain");
			var sListedSince = aCaches[0].getAttribute("listed_since");
			var nTopRating = aCaches[0].getAttribute("toprating");
			var nGeoKreties = aCaches[0].getAttribute("geokreties");
			var bFound = aCaches[0].getAttribute("found");
			var bNotFound = aCaches[0].getAttribute("notfound");
			var bOwner = aCaches[0].getAttribute("owner");
			var sUsername = aCaches[0].getAttribute("username");
			var nUserId = aCaches[0].getAttribute("userid");

			var sHtml = "<table>";
			if (bStatusTNA == 1)
			{
				sHtml += "<tr><td colspan='2'><font size='2' color='red'><b>" + xmlentities(sStatusText) + "</b></font></td></tr>";
				sHtml += "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
			}

			sHtml += "<tr><td><img src='resource2/ocstyle/images/cacheicon/16x16-" + nTypeId + ".gif' alt='" + xmlentities(sTypeText) + "' title='" + xmlentities(sTypeText) + "' /> <a href='viewcache.php?wp=" + encodeURI(sWPOC) + "' target='_blank'><font size='2'>" + xmlentities(sName) + "</font></a></td><td align='right' width='60px'><font size='2'><b>" + xmlentities(sWPOC) + "</b></font></td></tr>";
			sHtml += "<tr><td colspan='2'>{/literal}{t escape=js}by{/t}{literal} <a href='viewprofile.php?userid=" + encodeURI(nUserId) + "' target='_blank'>" + xmlentities(sUsername) + "</a></td></tr>";
			sHtml += "<tr><td colspan='2'>" + xmlentities(sTypeText) + " (" + xmlentities(sSizeText) + ")&nbsp;&nbsp;&nbsp;{t escape=js}D/T:{/t} " + parseFloat(nDifficulty).toFixed(1) + "/" + parseFloat(nTerrain).toFixed(1) + "</td></tr>";
			sHtml += "<tr><td colspan='2'>{/literal}{t escape=js}Listed since:{/t}{literal} " + xmlentities(sListedSince) + "</td></tr>";

			if (bOwner==1)
				sHtml += "<tr><td colspan='2'><img src='resource2/ocstyle/images/misc/16x16-home.png' alt='' /> {/literal}{t escape=js}This cache is yours{/t}{literal}</td></tr>";

			if (bFound==1)
				sHtml += "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/16x16-found.png' alt='' /> {/literal}{t escape=js}You found this cache{/t}{literal}</td></tr>";

			if (bNotFound==1)
				sHtml += "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/16x16-dnf.png' alt='' /> {/literal}{t escape=js}You havn't found this cache, yet{/t}{literal}</td></tr>";

			if (nGeoKreties>0)
				sHtml += "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/gk.png' alt='' /> {/literal}{t escape=js}This cache stores a GeoKrety{/t}{literal}</td></tr>";

			if (nTopRating>0)
				sHtml += "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/rating-star.gif' alt='' /> {/literal}{t escape=js}This cache has %1 recommandations{/t}{literal}</td></tr>".replace(/%1/, nTopRating);

			sHtml += "</table>";

			return sHtml;
		}

		function parseXML_GetPoint(oXMLObject)
		{
			var oCaches = oXMLObject.documentElement.getElementsByTagName("cache");

			if (oCaches.length<1)
				return false;

			var oCoords = oCaches[0].getAttribute("coords").split(",");
			var oCoordsYX = new GLatLng(oCoords[1],oCoords[0]);

			return oCoordsYX;
		}

		function OSM_addMapType()
		{
      var oOSMCopyright = new GCopyright(1, new GLatLngBounds(new GLatLng(-90,-180), new GLatLng(90,180)), 0, '(<a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>)');
      var oCopyrightCollection = new GCopyrightCollection('{t escape=js}Maps &copy; 2009 <a href="http://www.openstreetmap.org/">OpenStreetMap</a> Contributors{/t}');
      oCopyrightCollection.addCopyright(oOSMCopyright);
      var aTileLayers = new Array();
      aTileLayers[0] = new GTileLayer(oCopyrightCollection, 0, 18);
      aTileLayers[0].getTileUrl = function(a,z) { return "http://tile.openstreetmap.org/" + z + "/" + a.x + "/" + a.y + ".png"; };
      aTileLayers[0].isPng = function () { return true; };
      aTileLayers[0].getOpacity = function () { return 1.0; };
      var oOSMMapType = new GMapType(aTileLayers, new GMercatorProjection(19), "OSM", { urlArg: 'mapnik', linkColor: '#000000' });
      moMap.addMapType(oOSMMapType);
		}

		function mapsearch_onfocus()
		{
			if (moMapSearch.value==msSearchHint || mbResetSearchTextOnFocus==true)
				moMapSearch.value = '';
			mbResetSearchTextOnFocus = false;
		}

		function mapsearch_onblur()
		{
			if (moMapSearch.value=='')
				moMapSearch.value = msSearchHint;
		}

		function trim(s)
		{
			while (s.substring(0, 1) == ' ')
			{
				s = s.substring(1, s.length);
			}
			while (s.substring(s.length-1, s.length) == ' ')
			{
				s = s.substring(0, s.length-1);
			}
			return s;
		}

		function xmlentities(str)
		{
			str = str.replace(/&/, '&amp;');
			str = str.replace(/</, '&lt;');
			str = str.replace(/>/, '&gt;');
			str = str.replace(/"/, '&quot;');
			return str;
		}

		function attribute_onmousedown(nId, sIcon)
		{
			var oInputElement = document.getElementById('attribute' + nId);
			var oImageElement = document.getElementById('imgattribute' + nId);
			var nValue = oInputElement.value;

			if (nValue == 1)
				nValue = 2;
			else if (nValue == 2)
				nValue = 3;
			else if (nValue == 3)
				nValue = 1;

			{/literal}
				if (nValue == 1)
					oImageElement.src = 'resource2/{$opt.template.style}/images/attributes/' + sIcon + '.png';
				else if (nValue == 2)
					oImageElement.src = 'resource2/{$opt.template.style}/images/attributes/' + sIcon + '-no.png';
				else if (nValue == 3)
					oImageElement.src = 'resource2/{$opt.template.style}/images/attributes/' + sIcon + '-disabled.png';
			{literal}

			oInputElement.value = nValue;

			filter_changed();
		}

		function filter_changed()
		{
			// we need a new mnResultId!
			mnResultId = 0;
			msPopupMarkerWP = '';

			tmd_hide();
			rqc_show();
			data_clear();
			download_enabled(false);
		}

		// built query string for search.php
		function get_searchfilter_params(output, skipqueryid, zip)
		{
			var sPostBody = 'showresult=1&expert=0&output=' + output + '&utf8=1';
			var sCacheName = document.getElementById('cachename').value;

			if (skipqueryid)
				sPostBody += '&skipqueryid=1';

			if (zip)
				sPostBody += '&zip=1';

			if (sCacheName!='')
				sPostBody += '&searchto=searchbyname&cachename=' + encodeURIComponent(sCacheName);
			else
				sPostBody += '&searchto=searchbynofilter';

			/* cachetype
			 */
			var nCacheTypeCount = {/literal}{count array=$aCacheType}{literal};
			var sCacheTypes = '';
			for (var i=1; i<=nCacheTypeCount; i++)
			{
				if (document.getElementById('cachetype' + i).checked)
				{
					if (sCacheTypes != '') sCacheTypes += ';';
					sCacheTypes += i;
				}
			}
			sPostBody += '&cachetype=' + sCacheTypes;

			/* cachesize
			 */
			var nCacheSizeCount = {/literal}{count array=$aCacheSize}{literal};
			var sCacheSizes = '';
			for (var i=1; i<=nCacheSizeCount; i++)
			{
				if (document.getElementById('cachesize' + i).checked)
				{
					if (sCacheSizes != '') sCacheSizes += ';';
					sCacheSizes += i;
				}
			}
			sPostBody += '&cachesize=' + sCacheSizes;

			/* hide options
			 */
			sPostBody += document.getElementById('f_userowner').checked ? '&f_userowner=1' : '';
			sPostBody += document.getElementById('f_userfound').checked ? '&f_userfound=1' : '';
			sPostBody += document.getElementById('f_ignored').checked ? '&f_ignored=1' : '';
			sPostBody += document.getElementById('f_inactive').checked ? '&f_inactive=1' : '&f_inactive=0';
			sPostBody += document.getElementById('f_otherPlatforms').checked ? '&f_otherPlatforms=1' : '';

			/* rating options
			 */
			nSelectValue = document.getElementById('terrainmin').value;
			if (nSelectValue != 0) sPostBody += '&terrainmin=' + nSelectValue;
			nSelectValue = document.getElementById('terrainmax').value;
			if (nSelectValue != 0) sPostBody += '&terrainmax=' + nSelectValue;
			nSelectValue = document.getElementById('difficultymin').value;
			if (nSelectValue != 0) sPostBody += '&difficultymin=' + nSelectValue;
			nSelectValue = document.getElementById('difficultymax').value;
			if (nSelectValue != 0) sPostBody += '&difficultymax=' + nSelectValue;
			nSelectValue = document.getElementById('recommendationmin').value;
			if (nSelectValue != 0) sPostBody += '&recommendationmin=' + nSelectValue;

			/* attributes
			 */
			var sCacheAttribs = '';
			var sCacheAttribsNot = '';
			for (var nCacheAttribIndex=1; nCacheAttribIndex<100; nCacheAttribIndex++)
			{
				if (document.getElementById('attribute' + nCacheAttribIndex))
				{
					var nValue = document.getElementById('attribute' + nCacheAttribIndex).value;
					if (nValue == '1')
					{
						if (sCacheAttribs != '') sCacheAttribs += ';';
						sCacheAttribs += nCacheAttribIndex;
					}
					else if (nValue == '2')
					{
						if (sCacheAttribsNot != '') sCacheAttribsNot += ';';
						sCacheAttribsNot += nCacheAttribIndex;
					}
				}
			}
			sPostBody += '&cache_attribs=' + sCacheAttribs;
			sPostBody += '&cache_attribs_not=' + sCacheAttribsNot;

			return sPostBody;
		}

		// built query string for map2.php
		function get_mapfilter_params()
		{
			var oBounds = moMap.getBounds();
			var sPostBody = 'mode=searchresult&resultid=' + mnResultId;
			sPostBody += '&lat1=' + oBounds.getSouthWest().lat();
			sPostBody += '&lat2=' + oBounds.getNorthEast().lat();
			sPostBody += '&lon1=' + oBounds.getSouthWest().lng();
			sPostBody += '&lon2=' + oBounds.getNorthEast().lng();
			return sPostBody;
		}

		function data_clear()
		{
			document.getElementById('statCachesCount').firstChild.nodeValue = 0;

			if (msPopupMarkerWP == '')
			{
				// remove every waypoint and popups etc.
				moMarkerList = new Array();
				moMap.clearOverlays();
			}
			else
			{
				for (var nIndex=0; nIndex<moMarkerList.length; nIndex++)
				{
					var oMarker = moMarkerList[nIndex];
					if (oMarker.getWaypoint() != msPopupMarkerWP)
						moMap.removeOverlay(oMarker);
				}
			}
		}

		function data_load()
		{
			window.clearTimeout(moDataLoadTimer);
			mbDataDownloadStartTime = new Date();

			if (mnResultId != 0)
			{
				tmd_hide();
				mbDataDownloadHaveSecondChance = true;
				GDownloadUrl(msURLMapPHP, data_mapreceive, get_mapfilter_params());
			}
			else
			{
				GDownloadUrl(msURLSearchPHP, data_searchreceive, get_searchfilter_params('map2', true, false));
			}
		}

		function data_mapreceive(data, responseCode)
		{
			if (responseCode != 200)
			{
				var sMessage = '{t escape=js}Error: Unable to download the search result (HTTP error code %1){/t}';
				sMessage = sMessage.replace(/%1/, responseCode);
				alert(sMessage);
				return;
			}
			var oXML = GXml.parse(data);

			if (oXML.documentElement.getAttribute("available") == 0)
			{
				if (mbDataDownloadHaveSecondChance == true)
					GDownloadUrl(msURLSearchPHP, data_searchreceive, get_searchfilter_params('map2', true, false));
				else
				{
					alert('{t escape=js}Error: Unable to download the search result. The data was not available on the server.{/t}');
				}
				return;
			}

			data_clear();

			var record_count = oXML.documentElement.getAttribute("count");

			download_enabled((record_count<=mnMaxDownloadCount) && (record_count>0));

			if (oXML.documentElement.getAttribute("maxrecordreached") == 1)
			{
				tmd_show(record_count);
				return;
			}

			var oCachesList = oXML.documentElement.getElementsByTagName("cache");
			for (var nIndex=0; nIndex<oCachesList.length; nIndex++)
			{
				var sWaypoint = oCachesList[nIndex].getAttribute("wp");
				var nLon = oCachesList[nIndex].getAttribute("lon");
				var nLat = oCachesList[nIndex].getAttribute("lat");
				var nType = oCachesList[nIndex].getAttribute("type");

				addCacheToMap(sWaypoint, nLon, nLat, nType);
			}

			document.getElementById('statCachesCount').firstChild.nodeValue = oCachesList.length;
			document.getElementById('statLoadTime').firstChild.nodeValue = getTimeDiff(mbDataDownloadStartTime, new Date());
		}

		function download_enabled(enabled)
		{
			mbDownloadEnabled = enabled;
			if (enabled)
				document.getElementById('download_gpx_img').src = 'resource2/ocstyle/images/map/35x35-gpx-download.png';
			else
				document.getElementById('download_gpx_img').src = 'resource2/ocstyle/images/map/35x35-no-gpx-download.png';
		}

		function getTimeDiff(dTime1, dTime2)
		{
			return ((dTime2-dTime1)/1000).toFixed(1);
		}

		function addCacheToMap(sWaypoint, nLon, nLat, nType)
		{
			if (sWaypoint == msPopupMarkerWP)
			{
				for (var nIndex=0; nIndex<moMarkerList.length; nIndex++)
				{
					var oMarker = moMarkerList[nIndex];
					if (oMarker.getWaypoint() == msPopupMarkerWP)
						return;
				}
			}

			var oMarker = new CacheMarker(new GLatLng(nLat, nLon), sWaypoint, nType);
			GEvent.addListener(oMarker, "click", function(wp){CacheMarker_click(wp);});
			moMap.addOverlay(oMarker);

			moMarkerList[moMarkerList.length] = oMarker;
		}

		function CacheMarker_click(sWaypoint)
		{
			show_cachepopup_wp(sWaypoint, false);
		}

		// receives the result of map2.php (if result is not available, search.php has to be invoked)
		function data_searchreceive(data, responseCode)
		{
			// TODO: check
			if (responseCode == 200)
				mnResultId = data;

			// continue with data download
			tmd_hide();
			mbDataDownloadHaveSecondChance = false;
			GDownloadUrl(msURLMapPHP, data_mapreceive, get_mapfilter_params());
		}

		/* GM custom controls */

		/* TooManyDataControl
		 * Implement as GControl
		 */
		var tmd_width = 250;
		var tmd_height = 85;
		var tmd_container = null;
		var tmd_text = null;

		function tmd_initialize(oMap)
		{
      tmd_container = document.createElement("div");

      var boxDiv = document.createElement("div");
      var textDiv = document.createElement("div");
      tmd_text = document.createTextNode(" ");

      boxDiv.style.position = 'absolute';
      boxDiv.style.height = tmd_height + 'px';
      boxDiv.style.width = tmd_width + 'px';
      boxDiv.style.backgroundColor = '#0035FF';
      boxDiv.style.borderStyle = 'solid';
      boxDiv.style.borderWidth = 2 + 'px';
      boxDiv.style.borderColor = 'black';
      boxDiv.style.filter = 'Alpha(opacity=70,finishopacity=70)';
      boxDiv.style.MozOpacity = 0.7;
      boxDiv.style.textAlign = 'center';
      boxDiv.style.verticalAlign = 'middle';
      tmd_centerBox(oMap, boxDiv);
      
      textDiv.style.fontSize = 'small';
      textDiv.style.position = 'absolute';
      textDiv.style.paddingTop = 10 + 'px';
      textDiv.style.paddingLeft = 5 + 'px';
      textDiv.style.height = tmd_height + 'px';
      textDiv.style.width = tmd_width + 'px';
      textDiv.style.color = 'white';
      textDiv.style.textAlign = 'center';
      textDiv.style.verticalAlign = 'middle';
      tmd_centerBox(oMap, textDiv);

			tmd_container.appendChild(boxDiv);
			tmd_container.appendChild(textDiv);
			textDiv.appendChild(tmd_text);

      oMap.getContainer().appendChild(tmd_container);
      tmd_hide();

      return tmd_container;
		}

		function tmd_centerBox(oMap, boxDiv)
		{
			boxDiv.style.position = 'absolute';
      boxDiv.style.left = (mnMapWidth - tmd_width)/2 + 'px';
      boxDiv.style.top = (mnMapHeight - tmd_height)/2 + 'px';
		}

		function tmd_show(number)
		{
			var sMessage = '{t escape=js}There are %1 Geocaches in the selected area, matching the filter options. Please zoom in to display the caches.{/t}';
			sMessage = sMessage.replace(/%1/, number);
			tmd_text.nodeValue = sMessage;
			tmd_container.style.visibility = 'visible';
		}

		function tmd_hide()
		{
			tmd_container.style.visibility = 'hidden';
		}

		/* ReloadQuestionControl
		 * Implement as GControl
		 */
		var rqc_width = 250;
		var rqc_height = 125;
		var rqc_container = null;

		function rqc_initialize(oMap)
		{
      rqc_container = document.createElement("div");
 
      var boxDiv = document.createElement("div");
      var textDiv = document.createElement("div");
      var textText = document.createTextNode('{t escape=js}The filter options have been changed. Click OK to reload the map content.{/t}');
      var okButton = document.createElement("button");
      var okText = document.createTextNode("OK");
      
      boxDiv.style.position = 'absolute';
      boxDiv.style.height = rqc_height + 'px';
      boxDiv.style.width = rqc_width + 'px';
      boxDiv.style.backgroundColor = '#0035FF';
      boxDiv.style.borderStyle = 'solid';
      boxDiv.style.borderWidth = 2 + 'px';
      boxDiv.style.borderColor = 'black';
      boxDiv.style.filter = 'Alpha(opacity=70,finishopacity=70)';
      boxDiv.style.MozOpacity = 0.7;
      boxDiv.style.textAlign = 'center';
      boxDiv.style.verticalAlign = 'middle';
      rqc_centerBox(oMap, boxDiv);
      
      textDiv.style.fontSize = 'small';
      textDiv.style.position = 'absolute';
      textDiv.style.paddingTop = 10 + 'px';
      textDiv.style.paddingLeft = 5 + 'px';
      textDiv.style.height = rqc_height + 'px';
      textDiv.style.width = rqc_width + 'px';
      textDiv.style.color = 'white';
      textDiv.style.textAlign = 'center';
      textDiv.style.verticalAlign = 'middle';
      rqc_centerBox(oMap, textDiv);
      
      okButton.style.color = 'black';
      okButton.style.position = 'absolute';
      okButton.style.top = rqc_height - 40 + 'px';
      okButton.style.left = (rqc_width - 35) / 2 + 'px';
      okButton.style.width = 35 + 'px';
      okButton.style.backgroundColor = 'white';
      okButton.style.borderColor = 'darkgray';
      okButton.style.borderWidth = 2 + 'px';
      okButton.style.borerStyle = 'solid';
      okButton.onclick = rqc_click;
      
      textDiv.appendChild(textText);
      okButton.appendChild(okText);
      textDiv.appendChild(okButton);

			rqc_container.appendChild(boxDiv);
			rqc_container.appendChild(textDiv);

      oMap.getContainer().appendChild(rqc_container);

      rqc_hide();

      return rqc_container;
		}

		function rqc_centerBox(oMap, boxDiv)
		{
			boxDiv.style.position = 'absolute';
      boxDiv.style.left = (mnMapWidth - rqc_width)/2 + 'px';
      boxDiv.style.top = (mnMapHeight - rqc_height)/2 + 'px';
		}

		function rqc_click()
		{
			rqc_hide();
			data_load();
		}

		function rqc_show()
		{
			rqc_container.style.visibility = 'visible';
		}
		
		function rqc_hide()
		{
			rqc_container.style.visibility = 'hidden';
		}

		function rqc_visible()
		{
			return (rqc_container.style.visibility == 'visible');
		}
	//-->
	</script>
{/literal}
