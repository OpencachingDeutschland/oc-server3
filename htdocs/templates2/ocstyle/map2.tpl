{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
*
*  TODO (old, may be outdated):
*  - monitor and improve DB cleanup
*  - review/rewrite mapsubmit_click
*  - rewrite function names
*  - use resultid in each map.php query
*  - check if slave db connection works as expected (especially sys_repl_exclude / master connection)
*  - save and load search config from different profiles that the user can modify
*  - show labels inside the map
*  - switch current options/view to search result page
*
*  KNOWN PROBLEMS:
*  - InfoWindow sizing problems, (preliminarily?) solved by workaround, see adjust_infowindow()
*  - not all marker tootltips are displayed in Firefox, therefore disabled
*  - strange GM zoom control jumping while panning or resizing the map
*  - memory leak on MSIE, closing the map page then awfully slow due to garbage collection?
*
***************************************************************************}
{* OCSTYLE *}

{if $old_msie}
	<script type="text/javascript">
	{literal}<!--
	function mapLoad()
	{
		document.getElementById('map').innerHTML =
			"<p>{t}Your Internet Explorer is too old to display the map. Please upgrade at least to version 7, better 8.{/t}</p>";
	}
	-->{/literal}
	</script>
{else}

<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_balloon.js"></script>
<script type="text/javascript" src="resource2/{$opt.template.style}/js/tip_centerwindow.js"></script>
{* <script type="text/javascript" src="resource2/{$opt.template.style}/js/debug.js"></script> *}

{literal}
<script type="text/javascript">
<!--

function trim(s)
{
	while (s.substring(0, 1) == ' ')
		s = s.substring(1, s.length);

	while (s.substring(s.length-1, s.length) == ' ')
		s = s.substring(0, s.length-1);
		
	return s;
}

function getTimeDiff(dTime1, dTime2)
{
	return ((dTime2-dTime1)/1000).toFixed(1);
}


/*========================================================================= 
    Initialization and Configuration
 ==========================================================================*/

var bFullscreen = {/literal}{$bFullscreen}{literal};
var nDefaultZoom = 13;

var mnInitLat = {/literal}{$nGMInitLat}{literal};
var mnInitLon = {/literal}{$nGMInitLon}{literal};
var mnInitZoom = {/literal}({$nGMInitZoom} < 0 ? nDefaultZoom : {$nGMInitZoom}){literal};
var mbInitCookiePos = {/literal}{$bGMInitCookiePos}{literal};
var msInitWaypoint = "{/literal}{$sGMInitWaypoint}{literal}";
var msInitType = 'roadmap';
var msInitSiderbarDisplay = 'none';
var msInitAttribSelection = false;

/* home coordinates */
var mnUserLat = {/literal}{$nUserLat}{literal};
var mnUserLon = {/literal}{$nUserLon}{literal};

var msSearchHint = "{/literal}{t escape=js}city, cachename or waypoint{/t}{literal}";
var mbResetSearchTextOnFocus = false;

var msInitCookieLastPosName = 'ocgmlastpos';
var msInitCookieConfigName = 'ocgmconfig';
var msInitCookieFilterName = 'ocgmfilter';
if (!navigator.cookieEnabled)
{
	msInitCookieLastPosName = '';
	msInitCookieConfigName = '';
	msInitCookieFilterName = '';
}

var moGeocoder = new google.maps.Geocoder();
var moSearchList = null;
var maSearchListCoords = new Array();
var moPermalinkBox = null;
var moMapSearch = null;
var moMap = null;
var moInfoWindow = null;
var moMarkerList = new Array();
var msPopupMarkerWP = '';
var mhInfoWindowHackTimer = null;
var mhInfoWindowHackTries = 0;

var mnMapWidth = 770;
var mnMapHeight = 600;

// id of search.php result
var msURLSearchPHP = 'search.php';
var msURLMapPHP = 'map2.php';
var mnResultId = 0;
var mbDataDownloadHaveSecondChance = false;
var mbDataDownloadStartTime;
var moDataLoadTimer = null;
var copyrightDiv = null;
var mnMaxDownloadCount = 500;
var mbDownloadEnabled = false;

var bFilterChanged = false;
var sFilterSaveText;
var oFilterSaveColor;
var bAllAttribs = true;

{/literal}
var nCacheTypeCount = {count array=$aCacheType};
var nCacheSizeCount = {count array=$aCacheSize};
var nMaxAttributeId = {$maxAttributeId};
{literal}


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
			if (mbInitCookiePos == 1)
			{
				var sValues = sCookieValue[1].split(":");
				mnInitZoom = parseInt(sValues[0]);
				mnInitLon = parseFloat(sValues[1]);
				mnInitLat = parseFloat(sValues[2]);
			}
		}
		else if (sCookieValue[0] == msInitCookieConfigName)
		{
			var sValues = sCookieValue[1].split(":");
			msInitType = sValues[0];
			if (sValues.length > 1)
				msInitSiderbarDisplay = sValues[1];
			if (sValues.length > 2)
				msInitAttribSelection = (sValues[2] > 0);
		}
		else if (sCookieValue[0] == msInitCookieFilterName)
			eval_filtercookies(sCookieValue[1].split('/'));
	}
}

function eval_filtercookies(aValues)
{
	for (var iValue=0; iValue<aValues.length; iValue++)
	{
		fs = aValues[iValue].split(':');

		if (fs[0] == 'types')
		{
			ftypes = ',' + fs[1] + ',';
			{/literal}
			{foreach from=$aCacheType item=cachetype}
				document.getElementById('cachetype' + {$cachetype.id}).checked = ftypes.indexOf(',' + {$cachetype.id} + ',') >= 0;
			{/foreach}
			{literal}
			cachetype_all_set();
		}
		else if (fs[0] == 'sizes')
		{
			fsizes = ',' + fs[1] + ',';
			{/literal}
			{foreach from=$aCacheSize item=cachesize}
				document.getElementById('cachesize' + {$cachesize.id}).checked = fsizes.indexOf(',' + {$cachesize.id} + ',') >= 0;
			{/foreach}
			{literal}
			cachesize_all_set();
		}
		else if (fs[0] == 'hide')
		{
			document.getElementById('f_userowner').checked = fs[1].indexOf('0') >= 0;
			document.getElementById('f_userfound').checked = fs[1].indexOf('F') >= 0;
			document.getElementById('f_ignored').checked   = fs[1].indexOf('I') >= 0;
			document.getElementById('f_inactive').checked  = fs[1].indexOf('D') >= 0;
			document.getElementById('f_otherPlatforms').checked = fs[1].indexOf('M') >= 0;
		}
		else if (fs[0] == 'rated')
		{
			fr = fs[1].split(',');
			document.getElementById('terrainmin').value = fr[0];
			document.getElementById('terrainmax').value = fr[1];
			document.getElementById('difficultymin').value = fr[2];
			document.getElementById('difficultymax').value = fr[3];
			document.getElementById('recommendationmin').value = fr[4];
		}
		else if (fs[0] == 'attr')
		{
			fa = ',' + fs[1] + ',';

			{/literal}{strip}
			{foreach from=$aAttributes item=attribGroupItem}
				{foreach from=$attribGroupItem.attr item=attribItem}
					eva(fa,{$attribItem.id},'{$attribItem.icon}');
				{/foreach}
			{/foreach}
			{/strip}{literal}
		}
	}
}

function eva(fa,id,icon)
{
	if (fa.indexOf(',' + id + '-1,') >= 0)
	{
		document.getElementById('attribute' + id).value = 1;
		document.getElementById('imgattribute' + id).src = 'resource2/{/literal}{$opt.template.style}{literal}/images/attributes/' + icon + '.png';
	}
	else if (fa.indexOf(',' + id + '-2,') >= 0)
	{
		document.getElementById('attribute' + id).value = 2;
		document.getElementById('imgattribute' + id).src = 'resource2/{/literal}{$opt.template.style}{literal}/images/attributes/' + icon + '-no.png';
	}
}

function cookieSave()
{
	if (msInitCookieConfigName == '')
		return;

	var dCookieExp = new Date(2049, 12, 31);

	// map type and sidebar state
	document.cookie = msInitCookieConfigName + "=" + moMap.getMapTypeId() + ":" + msInitSiderbarDisplay + ":" + (bAllAttribs ? "1" : "0") + ";expires=" + dCookieExp.toUTCString();

	// map position and zoom level
	var oCenterPos = moMap.getCenter();
	document.cookie = msInitCookieLastPosName + "=" + moMap.getZoom() + ":" + oCenterPos.lng() + ":" + oCenterPos.lat() + ";expires=" + dCookieExp.toUTCString();

	// filter settings
	var sFilter = '';

	if (!document.getElementById('all_cachetypes').checked)
	{
		var sCtFilter = '';
		{/literal}
		{foreach from=$aCacheType item=cachetype}
			if (document.getElementById('cachetype' + {$cachetype.id}).checked) sCtFilter += ',' + {$cachetype.id};
		{/foreach}
		{literal}
		sFilter += '/types:' + sCtFilter.substring(1);
	}

	if (!document.getElementById('all_cachesizes').checked)
	{
		var sCsFilter = '';
		{/literal}
		{foreach from=$aCacheSize item=cachesize}
			if (document.getElementById('cachesize' + {$cachesize.id}).checked) sCsFilter += ',' + {$cachesize.id};
		{/foreach}
		{literal}
		sFilter += '/sizes:' + sCsFilter.substring(1);
	}

	sFilter += '/hide:';
	if (document.getElementById('f_userowner').checked) sFilter += 'O';
	if (document.getElementById('f_userfound').checked) sFilter += 'F';
	if (document.getElementById('f_ignored').checked)   sFilter += 'I';
	if (document.getElementById('f_inactive').checked)  sFilter += 'D';
	if (document.getElementById('f_otherPlatforms').checked) sFilter += 'M';

	sFilter += '/rated:' +
		document.getElementById('terrainmin').value + ',' +  
		document.getElementById('terrainmax').value + ',' +
		document.getElementById('difficultymin').value + ',' +
		document.getElementById('difficultymax').value + ',' +
		document.getElementById('recommendationmin').value;

	var sAttrFilter = '';
	for (nAttribId=1; nAttribId<=nMaxAttributeId; nAttribId++)
	{
		var ao = document.getElementById('attribute' + nAttribId);
		if (ao)
			if (ao.value != 3)
				sAttrFilter += ',' + nAttribId + '-' + ao.value;
	}
  if (sAttrFilter != "")
		sFilter += '/attr:' + sAttrFilter.substring(1);

	document.cookie = msInitCookieFilterName + "=" + sFilter.substring(1); 
		// "expires" not set, so that the cookie will expire when browser is closed
 		// ;because the user can easily forget or overlook that filtering is acive. 
}


function mapLoad()
{
	cookieLoad();
	showcoords(mnInitLat, mnInitLon);

	if (!msInitAttribSelection)
		toggle_attribselection(false);
		
	if (bFullscreen && msInitSiderbarDisplay == "block")
    toggle_sidebar(false);

	var maptypes = ['OSM', 'MQ', 'OCM',
	                google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE, 
	                google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.TERRAIN ];
  var initType = google.maps.MapTypeId.ROADMAP;
  for (var i=0; i<maptypes.length; i++)
    if (msInitType == maptypes[i])
			initType = msInitType;
	
	var myOptions = {
		zoom: mnInitZoom,
		center: new google.maps.LatLng(mnInitLat, mnInitLon),
		mapTypeId: initType,
		backgroundColor: "#d0dccc",

		mapTypeControl: true,
		mapTypeControlOptions: { mapTypeIds: maptypes },
			
		panControl: false,
		zoomControl: true,
		scaleControl: true,
		streetViewControl: false,

		overviewMapControl: true,  {/literal}
		{if $opt_overview==1}
		overviewMapControlOptions: {literal}{ opened: true }{/literal},
		{/if}  {literal}

		styles:
			[ { featureType:"poi.business", elementType:"labels", stylers: [{ visibility:"off" }] },
			  { featureType:"poi.government", elementType:"labels", stylers: [{ visibility:"off" }] } ]
			// poi types: https://developers.google.com/maps/documentation/javascript/reference#MapTypeStyleFeatureType
	};

	moMap = new google.maps.Map(document.getElementById("googlemap"), myOptions);

	setMapType("OSM", "OpenStreetMap", "http://tile.openstreetmap.org/", 18);
	setMapType("MQ"," MapQuest", "http://otile1.mqcdn.com/tiles/1.0.0/osm/", 19);
	setMapType("OCM", "OpenCycleMap", "http://tile.opencyclemap.org/cycle/", 18);		

	moInfoWindow = new google.maps.InfoWindow();

	moSearchList = document.getElementById('mapselectlist');
	moMapSearch = document.getElementById('mapsearch');
	moPermalinkBox = document.getElementById('permalink_box');

	// Create div for showing copyrights.
	copyrightDiv = document.createElement("div");
	copyrightDiv.id = "map-copyright";
	copyrightDiv.style.fontSize = "11px";
	copyrightDiv.style.fontFamily = "Arial, sans-serif";
	copyrightDiv.style.padding = "0 2px 0 2px";
	copyrightDiv.style.whiteSpace = "nowrap";
	copyrightDiv.style.background = "#FFFFFF";
	copyrightDiv.style.opacity = "0.7";

	//copyrightDiv.class = "mapattribution";
	moMap.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(copyrightDiv);
	
	google.maps.event.addListener(moMap, "dragstart", function(){map_movestart()});
	google.maps.event.addListener(moMap, "dragend", function(){map_moveend()});
	google.maps.event.addListener(moMap, "bounds_changed", function(){map_moveend()});
	// google.maps.event.addListener(moMap, "zoom_changed", function(){map_moveend()});
	// is included in bounds_changed
	google.maps.event.addListener(moMap, "maptypeid_changed", function(){map_maptypechanged()});
	google.maps.event.addListener(moMap, "mousemove", function(event){map_mousemove(event)});
	google.maps.event.addListener(moMap, "click", function(event){map_clicked()});

	if (msInitWaypoint != "")
		show_cachepopup_wp(msInitWaypoint, true);

	if (moMapSearch)
		moMapSearch.value = msSearchHint;
    
	updateCopyrights();
	cookieSave();
	queue_dataload(500);
}

function setMapType(shortname,longname,url,max_zoom)
{
	moMap.mapTypes.set(shortname, new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return url + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: shortname,
		alt: longname,
		maxZoom: max_zoom
	}));
}

function mapUnload()
{
	cookieSave();
}


/*========================================================================= 
    GM hooks and custom controls
 =========================================================================*/

function map_movestart()
{
	window.clearTimeout(moDataLoadTimer);
	moDataLoadTimer = null;
}

function map_moveend()
{
	cookieSave();
	queue_dataload(400);
}

function map_maptypechanged()
{
	updateCopyrights();
	cookieSave();
}

function map_mousemove(event)
{
	showcoords(event.latLng.lat(), event.latLng.lng());
}

function map_clicked()
{
	if (bFullscreen)
		if (document.getElementById("sidebar").style.display != 'none')
			toggle_sidebar();
	mapselectlist_hide();		// firefox needs this
	moInfoWindow.close();
	permalinkbox_hide();
}

function updateCopyrights() 
{
	if (copyrightDiv == null )
		return;
	
	var newMapType = moMap.getMapTypeId();
	
	if (newMapType == "OSM" || newMapType == "MQ" || newMapType == "OCM")
	{
		{/literal}
		if (newMapType == "OCM")
			copyrightDiv.innerHTML = '{t escape=js}Map data &copy; <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> and <a href="http://www.thunderforest.com/opencyclemap/" target="_blank">OpenCycleMap</a> contributors{/t}';
		else
			copyrightDiv.innerHTML = '{t escape=js}Map data &copy; <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors{/t}';
		if (newMapType == "MQ")
			copyrightDiv.innerHTML += ', ' + '{t escape=js}tiles provided by <a href="http://www.mapquest.com/" target="_blank">MapQuest</a>{/t}' + ' <img src="http://developer.mapquest.com/content/osm/mq_logo.png">';
		{literal}
	}
	else
		copyrightDiv.innerHTML = "";
}


/*========================================================================= 
    XML cache data download
 =========================================================================*/

function ajaxLoad( url, callback, postData ) 
{
	var http_request = false;
	
	if (window.XMLHttpRequest) 
	{ // Mozilla, Safari, ...
		http_request = new XMLHttpRequest();
	} 
	else if (window.ActiveXObject) 
	{ // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) 
	{
		alert('Giving up: Cannot create an XMLHTTP instance');
		return false;
	}
	
	http_request.onreadystatechange =  function() {
		if (http_request.readyState == 4) 
		{
			if (http_request.status == 200) 
			{
				callback(http_request.responseText, http_request.status);
			}
			else if (http_request.status != 0)     // avoid dummy messages when aborting transfer 
			{
				alert('Request Failed: ' + http_request.status);
			}
		}
	};

	if (postData) 
	{ // POST
		http_request.open('POST', url, true);
		http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');  
		http_request.send(postData);
	}
	else 
	{
		http_request.open('GET', url, true);
		http_request.send(null);
	}
}

/**
* This functions wraps XMLHttpRequest open/send function.
* It lets you specify a URL and will call the callback if
* it gets a status code of 200.
* @param {String} url The URL to retrieve
* @param {Function} callback The function to call once retrieved.
*/
function downloadUrl( url, callback, postbody ) 
{
    ajaxLoad( url, callback, postbody );
    //GDownloadUrl( url, callback );
}

function downloadUrl2(url, callback, postbody) {
    ajaxLoad( url, callback, postbody );
    //GDownloadUrl( url, callback, postbody );
}

/**
 * Parses the given XML string and returns the parsed document in a
 * DOM data structure. This function will return an empty DOM node if
 * XML parsing is not supported in this browser.
 * @param {string} str XML string.
 * @return {Element|Document} DOM.
 */
function xmlParse( str ) 
{
	if (typeof ActiveXObject != 'undefined' && typeof GetObject != 'undefined') 
	{
		var doc = new ActiveXObject('Microsoft.XMLDOM');
		doc.loadXML(str);
		return doc;
	}

	if (typeof DOMParser != 'undefined') 
	{
		return (new DOMParser()).parseFromString(str, 'text/xml');
	}

	return createElement('div', null);
}

function queue_dataload(nMs)
{
	if (moDataLoadTimer != null)
		clearTimeout(moDataLoadTimer);
	moDataLoadTimer = window.setTimeout('data_load()', nMs);
}

function data_load()
{
	if (bFilterChanged)
		moInfoWindow.close();
	bResetFilterHeading = bFilterChanged;
	bFilterChanged = false;

	window.clearTimeout(moDataLoadTimer);
	moDataLoadTimer = null;
	mbDataDownloadStartTime = new Date();

	if (mnResultId != 0)
	{
		tmd_hide();
		mbDataDownloadHaveSecondChance = true;
		ajaxLoad(msURLMapPHP, data_mapreceive, get_mapfilter_params());
	}
	else
	{
		gpx_download_enabled(false);
		ajaxLoad(msURLSearchPHP, data_searchreceive, get_searchfilter_params('map2', true, false));
	}
}

function data_mapreceive(data, responseCode)
{
	if (bResetFilterHeading)
		reset_filter_heading();

	if (responseCode != 200)
	{
		var sMessage = '{t escape=js}Error: Unable to download the search result (HTTP error code %1){/t}';
		sMessage = sMessage.replace(/%1/, responseCode);
		alert(sMessage);
		return;
	}

	var oXML = xmlParse(data);

	if (oXML.documentElement.getAttribute("available") +0 == 0)
	{
		if (mbDataDownloadHaveSecondChance == true)
		{
			ajaxLoad(msURLSearchPHP, data_searchreceive, get_searchfilter_params('map2', true, false));
		}
		else
		{
			// alert('{t escape=js}Error: Unable to download the search result. The data was not available on the server.{/t}');
			error_no_data(true);
		}
		return;
	}
	else
		error_no_data(false);
	
	var record_count = oXML.documentElement.getAttribute("count");

	gpx_download_enabled((record_count<=mnMaxDownloadCount) && (record_count>0));

	/* nee too many markers -> clear all and display message */
	if (oXML.documentElement.getAttribute("maxrecordreached") == 1)
	{
		data_clear();
		tmd_show(record_count);
		return;
	}
    
	/* compute set of markers to keep */
	var wpset = {};
	var oCachesList = oXML.documentElement.getElementsByTagName("c");
	for (var nIndex=0; nIndex<oCachesList.length; nIndex++)
	{
		var sCacheData = oCachesList[nIndex].getAttribute("d");
		var sWaypoint = sCacheData.substring(0,sCacheData.indexOf('/')); 
		wpset[sWaypoint] = true;
	}
	/* delete unneeded markers */
	var alreadythere = data_clear_except( wpset );
	
	/* add new markers (skip existing) */
	for (var nIndex=0; nIndex<oCachesList.length; nIndex++)
	{
		var aCacheData = oCachesList[nIndex].getAttribute("d").split('/');
		var sWaypoint = aCacheData[0];
		if (!(sWaypoint in alreadythere))
		{
			var nLon = aCacheData[1];
			var nLat = aCacheData[2];
			var nType = aCacheData[3];
			var nFlags = aCacheData[4];

			addCacheToMap(sWaypoint, nLon, nLat, nType, nFlags,
			              oCachesList[nIndex].getAttribute("n"), nIndex);
		}
	}

	document.getElementById('statCachesCount').innerHTML = oCachesList.length;
	document.getElementById('statLoadTime').innerHTML = getTimeDiff(mbDataDownloadStartTime, new Date());
}

// receives the result of map2.php (if result is not available, search.php has to be invoked)
function data_searchreceive(data, responseCode)
{
	// TODO: check
	if (responseCode == 200)
	{
		// TODO: make sure that data must is a numeric ID
		mnResultId = data;
	}
	
	// continue with data download
	tmd_hide();
	mbDataDownloadHaveSecondChance = false;
	ajaxLoad(msURLMapPHP, data_mapreceive, get_mapfilter_params());
}

function error_no_data(errorstate)
{
	var msgo = document.getElementById('mapstat_caches');
	msgo.style.color = (errorstate ? 'red' : 'black');
	msgo.style.fontWeight = (errorstate ? 'bold' : 'normal');
}

function xmlentities(str)
{
	str = str.replace(/&/, '&amp;');
	str = str.replace(/</, '&lt;');
	str = str.replace(/>/, '&gt;');
	str = str.replace(/"/, '&quot;');
	// hack for phpDesigner syntax HL problem: "

	return str;
}


/*========================================================================= 
    Markers
 =========================================================================*/

function NewCacheMarker(nLat, nLon, sWaypoint, nType, nFlags, sName, nZindex)
{
	var image, anchor;

	{/literal}
	{if $opt_cacheicons == 1}
		image = 'resource2/ocstyle/images/map/caches1/24x24-';
		anchor = new google.maps.Point(12,12);
	{else}
		image = 'resource2/ocstyle/images/map/caches2/';
		anchor = new google.maps.Point(13,24);
	{/if}
	{literal}

  if (nFlags & 1)				image += 'owned';
  else if (nFlags & 2)	image += 'found';
  else									image += 'cachetype-' + nType;

  if (nFlags & 4) 			image += '-inactive';
  else if (nFlags & 8)	image += '-oconly';

	var mi = new google.maps.MarkerImage(image + '.png');
	mi.anchor = anchor;

	marker = new google.maps.Marker(
		{	position: new google.maps.LatLng(nLat, nLon),
			map: moMap,
			title: sName,
			icon: mi,
			zIndex: nZindex });
	marker.waypoint = sWaypoint;
	return marker;
}			

function addCacheToMap(sWaypoint, nLon, nLat, nType, nFlags, sName, nZindex)
{
	var oMarker = NewCacheMarker(nLat, nLon, sWaypoint, nType, nFlags, sName, nZindex);
	google.maps.event.addListener(oMarker, "click", function(){CacheMarker_click(sWaypoint);});

	moMarkerList[moMarkerList.length] = oMarker;
}

function data_clear()
{
	document.getElementById('statCachesCount').innerHTML = '0';
	for (var nIndex=0; nIndex<moMarkerList.length; nIndex++)
		moMarkerList[nIndex].setMap(null);
	moMarkerList = new Array();
}


/* delete all markers, except those specified in "wpset". return the set of remaining markers */
function data_clear_except(wpset)
{
	var existing = {};
	
	document.getElementById('statCachesCount').innerHTML = '0';
	
	var newList = new Array();
	
	for (var nIndex=0; nIndex<moMarkerList.length; nIndex++)
	{
		var oMarker = moMarkerList[nIndex];
		var wp = oMarker.waypoint;
		
		if (wp != msPopupMarkerWP && !(wp in wpset))
		{
			oMarker.setMap(null);
		}
		else
		{
			existing[wp] = true;
			newList[newList.length] = oMarker;
		}
	}
	
	moMarkerList = newList;
	
	return existing;
}

function CacheMarker_click(sWaypoint)
{
	show_cachepopup_wp(sWaypoint, false);
}


/*========================================================================= 
    Cache-InfoWindow popup
 =========================================================================*/

function show_cachepopup_wp(sWaypoint, bAllowZoomChange)
{
	show_cachepopup_url(msURLMapPHP + "?mode=wpsearch&wp=" + sWaypoint, sWaypoint, bAllowZoomChange);
}

function show_cachepopup_latlon(nLat, nLon, bAllowZoomChange)
{
	show_cachepopup_url(msURLMapPHP + "?mode=locate&lat=" + nLat + "&lon=" + nLon, "", bAllowZoomChange);
}

{/literal}
{* In some browers and zoom levels, a wrong size is calculated by GM for some infowindow
 * contents. If the caluclated size is too small, the containing div will show a scrollbar:
 *
 *   http://stackoverflow.com/questions/1554893/google-maps-api-v3-infowindow-not-sizing-correctly
 *
 * The only solution working under all conditions seems to be increasing the container
 * div's size (while tampering with it's overflow setting will cause other problems 
 * especiallyin Chrome, with waypoints cut off at the right). This will neither affect the 
 * size of our 'mapinfowindow' div nor of the infowindow, but only give our content more 
 * space. We allocate as much fits into the InfoWindow borders.
 *
 * Also, there is another MSIE issue regading a second inner pair of scrollbars. We get 
 * rid of them by settings overflow:hidden on mapinfowindow (see style_screen.css).
 *}
{literal}

function adjust_infowindow()
{
	if (mhInfoWindowHackTries > 0 &&
			document.getElementById('mapinfowindow') != null)
		if (typeof document.getElementById('mapinfowindow').parentNode != null)
			if (typeof document.getElementById('mapinfowindow').parentNode.parentNode != null)
				if (typeof document.getElementById('mapinfowindow').parentNode.parentNode.style != null)
	{
		var iw_frame = document.getElementById('mapinfowindow').parentNode.parentNode;
		var iw_width = parseInt(iw_frame.style.width.substr(0, iw_frame.style.width.indexOf('px')));
		var iw_height = parseInt(iw_frame.style.height.substr(0, iw_frame.style.height.indexOf('px')));

		if (iw_width != "" && iw_height != "")
		{
			mhInfoWindowHackTries = 0;
			// alert("Before: " + iw_frame.style.width +  " / " + iw_frame.style.height);  
			iw_frame.style.width = String(iw_width + 25) + "px";
			iw_frame.style.height = String(iw_height + 25) + "px";
			// alert("After: " + iw_frame.style.width +  " / " + iw_frame.style.height);  
		}
		else
			mhInfoWindowHackTries -= 1;
	}

	if (mhInfoWindowHackTries <= 0)
		clearInterval(mhInfoWindowHackTimer);
}

function reopen_infowindow(oCoords, sText)
{
	moInfoWindow.close();
	moInfoWindow = new google.maps.InfoWindow({ position: oCoords, content: sText });
	moInfoWindow.open( moMap, null, true );
	wid = document.getElementById('mapinfowindow');
	mhInfoWindowHackTries = 150;
	mhInfoWindowHackTimer = window.setInterval("adjust_infowindow()",10);
}

function show_cachepopup_url(sURL, sWaypoint, bAllowZoomChange)
{
	moInfoWindow.close();

	ajaxLoad(sURL, function(data, responseCode) {
		var oXML = xmlParse(data);
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
		{
			moMap.setCenter(oCoords);
			if (Math.abs(moMap.getZoom() - nDefaultZoom) > 1)
			  moMap.setZoom(nDefaultZoom);
		}

		reopen_infowindow(oCoords, parseXML_GetHTML(oXML));
	});

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
	var bIsPublishdate = aCaches[0].getAttribute("is_publishdate");
	var nTopRating = aCaches[0].getAttribute("toprating");
	var nGeoKreties = aCaches[0].getAttribute("geokreties");
	var bFound = aCaches[0].getAttribute("found");
	var bNotFound = aCaches[0].getAttribute("notfound");
	var bAttended = aCaches[0].getAttribute("attended");
	var bOconly = aCaches[0].getAttribute("oconly");
	var bOwner = aCaches[0].getAttribute("owner");
	var sUsername = aCaches[0].getAttribute("username");
	var nUserId = aCaches[0].getAttribute("userid");

	{/literal}{*
	// When changing any of the following HTML code, test the map popups carefully
	// on many different browsers and zoom levels! Even trivial changes can cause
	// inernal rendering errors which create annoying scrollbars within the info window.
	*}{literal}

	var sHtml = "<div id='mapinfowindow' class='mappopup'><table class='mappopup'>";
	if (bStatusTNA == 1)
		sHtml += "<tr><td colspan='2'><font size='2' color='red'><b>" + xmlentities(sStatusText) + "</b></font></td></tr>";

	// InfoWindows have a min width; set min width for content to avoid large right borders:
	sHtml += "<tr><td><img src='resource2/ocstyle/images/cacheicon/20x20-" + nTypeId + ".png' alt='" + xmlentities(sTypeText) + "' title='" + xmlentities(sTypeText) + "' height='20px' width='20px'/></td><td style='min-width:150px";
	if (sName.length > 60)
		sHtml += "; white-space:normal";
	sHtml += "'><a href='viewcache.php?wp=" + encodeURI(sWPOC) + "' target='_blank'><font size='2'>" + xmlentities(sName) + "</font></a></td><td align='right' vertical-align:'top'><font size='2'><b>&nbsp;" + xmlentities(sWPOC) + "</b></font></td></tr>";
	sHtml += "<tr><td colspan='2' style='vertical-align:top;'>{/literal}{t escape=js}by{/t}{literal} <a href='viewprofile.php?userid=" + encodeURI(nUserId) + "' target='_blank'>" + xmlentities(sUsername) + "</a></td><td align='right'><a class='nooutline' href='articles.php?page=cacheinfo#difficulty' target='_blank'><img src='resource2/{/literal}{$opt.template.style}/images/difficulty/diff-" + String(nDifficulty*10) + ".gif' border='0' width='19' height='16' hspace='2' alt='{t}D{/t} " + nDifficulty + "' title='{t}Difficulty{/t} " + nDifficulty + "/5'{literal} /><img src='resource2/{/literal}{$opt.template.style}/images/difficulty/terr-" + String(nTerrain*10) + ".gif' border='0' width='19' height='16' hspace='2' alt='{t}T{/t} " + nTerrain + "' title='{t}Terrain{/t} " + nTerrain + "/5'{literal} /></a></td></tr>";
	sHtml += "<tr><td colspan='3' height='3px'></td></tr>";
	
	sHtml += "<tr><td colspan='2'>" + xmlentities(sTypeText) + " (" + xmlentities(sSizeText) + ")</td><td align='right' rowspan='2'>" + (bOconly==1 ? "{/literal}{$help_oconly}{literal}<img src='resource2/ocstyle/images/misc/is_oconly_small.png' alt='OConly' title='OConly' /></a>" : "") + "</td></tr>";
	sHtml += "<tr><td colspan='2'>" + {/literal}(bIsPublishdate == true ? "{t escape=js}Published on{/t}:" : "{t escape=js}Listed since:{/t}"){literal} + " " + xmlentities(sListedSince) + "</td></tr>";

	sAddHtml = "";
	if (bOwner==1)
		sAddHtml += "<tr><td colspan='3'><img src='resource2/ocstyle/images/misc/16x16-home.png' alt='' /> {/literal}{t escape=js}This cache is yours{/t}{literal}</td></tr>";

	if (bFound==1)
		sAddHtml += "<tr><td colspan='3'><img src='resource2/ocstyle/images/viewcache/16x16-found.png' alt='' /> {/literal}{t escape=js}You found this cache{/t}{literal}</td></tr>";

	if (bNotFound==1)
		sAddHtml += "<tr><td colspan='3'><img src='resource2/ocstyle/images/viewcache/16x16-dnf.png' alt='' /> {/literal}{t escape=js}You havn't found this cache, yet{/t}{literal}</td></tr>";

	if (bAttended==1)
		sAddHtml += "<tr><td colspan='3'><img src='resource2/ocstyle/images/log/16x16-attended.png' alt='' /> {/literal}{t escape=js}You have attended this event!{/t}{literal}</td></tr>";

	if (nGeoKreties>0)
		sAddHtml += "<tr><td colspan='3'><img src='resource2/ocstyle/images/viewcache/gk.png' alt='' /> {/literal}{t escape=js}This cache stores a GeoKrety{/t}{literal}</td></tr>";

	if (nTopRating>0)
		sAddHtml += "<tr><td colspan='3'><img src='resource2/ocstyle/images/viewcache/rating-star.gif' alt='' /> {/literal}{t escape=js}This cache has %1 recommandations{/t}{literal}</td></tr>".replace(/%1/, nTopRating);

	if (sAddHtml != "")
		sHtml += 	"<tr><td colspan='3' height='3px'></td></tr>" + sAddHtml;

	sHtml += "</table></div>";

	return sHtml;
}

function parseXML_GetPoint(oXMLObject)
{
	var oCaches = oXMLObject.documentElement.getElementsByTagName("cache");

	if (oCaches.length<1)
		return false;

	var oCoords = oCaches[0].getAttribute("coords").split(",");
	var oCoordsYX = new google.maps.LatLng(oCoords[1],oCoords[0]);

	return oCoordsYX;
}


/*========================================================================= 
    Status display
 =========================================================================*/

function coordtext(coord)
{
	var deg = Math.floor(coord);
	var min = Math.floor(60*(coord-deg));
	var tmin = Math.round(1000*(60*(coord-deg)-min));

	if (tmin > 999) {
		tmin -= 1000;
		min += 1;
		if (min > 59)
		{
			min -= 60;
			deg += 1;
		}
	}

	return (deg < 10 ? "0" : "") + String(deg) + "°" + (min < 10 ? "0" : "") + String(min) + "." + 
	       (tmin < 10 ? "00" : (tmin < 100 ? "0" : "")) + String(tmin) + "'";
}

function showcoords(lat,lng)
{
	if (lat < 0) { lattext = "S " + coordtext(-lat) }
	else { lattext = "N " + coordtext(lat); }

	if (lng < 0) { longtext = "W " + coordtext(-lng) }
	else { longtext = "E " + coordtext(lng); }

	{/literal}
		document.getElementById('coordbox').innerHTML = lattext + "&nbsp;&nbsp;" + longtext;
	{literal}
}

function tmd_show(number)
{
	var tmd = document.getElementById("toomanycaches");
	var txt = document.getElementById("toomanycaches_txt");

	var sMessage = '{t escape=js}There are %1 Geocaches in the selected area, matching the filter options. Please zoom in to display the caches.{/t}';
	sMessage = sMessage.replace(/%1/, number);
	txt.innerHTML = sMessage;

	tmd.style.display = 'block';
}

function tmd_hide()
{
	document.getElementById("toomanycaches").style.display = 'none';
}


/*========================================================================= 
    Function buttons
 =========================================================================*/

// HOME
function center_home()
{
	if (mnUserLat != 0 || mnUserLon != 0)
	{
		moMap.setCenter( new google.maps.LatLng(mnUserLat, mnUserLon) );
		moMap.setZoom( nDefaultZoom );
	}
	else
	{
		alert( "no home coordinates!" );
	}
}

// GPX DOWNLOAD
function gpx_download_enabled(enabled)
{
	mbDownloadEnabled = enabled;
	if (enabled)
		document.getElementById('download_gpx_img').src = 'resource2/ocstyle/images/map/35x35-gpx-download.png';
	else
		document.getElementById('download_gpx_img').src = 'resource2/ocstyle/images/map/35x35-no-gpx-download.png';
}

function download_gpx()
{
	var oBounds = moMap.getBounds();
	var params = get_searchfilter_params('gpx', false, true);

	if (mbDownloadEnabled == false)
	{
		alert({/literal}"{t}Between 1 and 500 caches must be displayed for download.{/t}"{literal});
		return;
	}

	params += '&bbox=' + oBounds.getSouthWest().lng() + ',' + oBounds.getSouthWest().lat() + ',' + oBounds.getNorthEast().lng() + ',' + oBounds.getNorthEast().lat();
	params += '&count=max';

	location.href = msURLSearchPHP + '?' + params;
}

// PERMALINK
function showPermlinkBox_click()
{
	if (window.opera)
		document.getElementById('permalink_addFavorites').style.display = 'none';
	else
		if ((typeof window.external.AddFavorite == 'undefined') && 
			(typeof window.external.addPanel == 'undefined'))
			document.getElementById('permalink_addFavorites').style.display = 'none';

	if (moPermalinkBox.style.display == 'none')
	{
		var oCenterPos = moMap.getCenter();
		var nZoomLevel = moMap.getZoom();
		var msPermalink = msURLMapPHP + "?lat=" + Math.round(oCenterPos.lat()*1000000)/1000000 + "&lon=" + Math.round(oCenterPos.lng()*1000000)/1000000 + "&zoom=" + nZoomLevel + "&map=" + encodeURI(moMap.getMapTypeId()) {/literal}{if $bFullscreen}+ "&mode=fullscreen"{/if}{literal};

		var oPermalinkTextBox = document.getElementById('permalink_text');
		if (oPermalinkTextBox)
			oPermalinkTextBox.value = "{/literal}{$opt.page.absolute_url}{literal}" + msPermalink;

		moPermalinkBox.style.display = 'block';
		document.getElementById('permalink_text').select();
	}
	else
		moPermalinkBox.style.display = 'none';
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

function permalinkbox_hide()
{
	moPermalinkBox.style.display = 'none';
}

// SETTINGS
function toggle_settings()
{
	var so = document.getElementById('mapoptions');
	if (so.style.display == 'block')
	  so.style.display = 'none';
	else
		so.style.display = 'block';
}

// SEARCH
function mapselectlist_onblur()
{
	mapselectlist_hide();
}

function mapselectlist_clear()
{
	moSearchList.selectedIndex = 0;
	while (moSearchList.length>0)
		moSearchList.options[moSearchList.length-1] = null;
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
	moSearchList.focus();
}

function mapselectlist_hide()
{
	moSearchList.style.display = "none";
}

function mapselectlist_onclick()
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
	{
		queue_dataload(500);
		show_cachepopup_wp(sWaypoint, true);
	}
	else
	{
		var oCoords = new google.maps.LatLng(nLat, nLon);
		reopen_infowindow(oCoords, xmlentities(sText));
		moMap.setCenter(oCoords);
	  moMap.setZoom(nDefaultZoom-1);

		queue_dataload(500);
	}
}

function mapsearch_click()
{
	flashbutton('mapsubmit');

	var sSearchText = moMapSearch.value;
	var oTempOption;

	if (!moMap) return;

	if (msSearchHint==sSearchText || sSearchText=='')
	{
		alert('{t escape=js}Enter a search text, please!{/t}');
		return;
	}

	// do search on opencaching.de
	// TODO: ensure mnResultId is set
	var oCenterPos = moMap.getCenter();
	
	// check for geocaching waypoint
	if (sSearchText.match(/^OC[\S]{1,}$/i) || 
		sSearchText.match(/^GC[\S]{1,}$/i) || 
		sSearchText.match(/^N[0-9]{1,5}$/i))
	{
		searchpar = "wpsearch&wp=" + sSearchText;
	}
	else
	  searchpar = "namesearch&name=" + encodeURI(sSearchText) + "&lat=" + oCenterPos.lat() + "&lon=" + oCenterPos.lng() + "&resultid=" + mnResultId; 

	ajaxLoad(msURLMapPHP + "?mode=" + searchpar, 
		function(data, responseCode) {
			var xml = xmlParse(data);
			var caches = xml.documentElement.getElementsByTagName("cache");

			// clear the result list
			mapselectlist_clear();

			if (caches.length>0)
			{
				//TODO: translate
				var oTempOption = new Option("{t escape=js}Geocaches found, nearest first:{/t}", -1);
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
			moGeocoder.geocode( { 'address': sSearchText }, 
				function(results, status)
				{
					if (status == google.maps.GeocoderStatus.ZERO_RESULTS)
					{
						// no result
					}
					else if (status != google.maps.GeocoderStatus.OK)
					{
						//TODO: translate
						alert("Internal search error");
						return;
					}

					var nPlacemarksCount = 0;
					var nPlacemarkIndex;

					if (status == google.maps.GeocoderStatus.OK)
						nPlacemarksCount = results.length;

					if (nPlacemarksCount>0)
					{
						//TODO: translate
						var oTempOption = new Option("{t escape=js}Places found via Google:{/t}", -1);
						oTempOption.style.color = "gray";
						moSearchList.options[moSearchList.length] = oTempOption;

						for (nPlacemarkIndex=0; nPlacemarkIndex<nPlacemarksCount; nPlacemarkIndex++)
						{
							var coord = results[nPlacemarkIndex].geometry.location;
							var text = results[nPlacemarkIndex].formatted_address;
							var value = add_searchlist_itemcoords(coord.lat(), coord.lng(), "", text);
							var item = new Option("     " + text, value);
								item.style.marginLeft = "20px";
							moSearchList.options[moSearchList.length] = item;
						}
					}

					if (moSearchList.length==0)
					{
						mapselectlist_hide();
						//TODO: translate
						alert("'" + sSearchText + "' {/literal}{t escape=js}was not found (with the selected settings){/t}{literal}");
						return;
					}
					else if (maSearchListCoords.length==1)
					{
						mapselectlist_hide();
						searchlist_openitem(0);
						return;
					}

					// adjust size
					if (moSearchList.length>20)
						moSearchList.size = 20;
					else
						moSearchList.size = moSearchList.length;

					moSearchList.selectedIndex = 0;
					mapselectlist_show();
				});
		});
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


/*========================================================================= 
    Filtering
 =========================================================================*/

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
	if (!bFilterChanged)
	{
		bFilterChanged = true;
		msPopupMarkerWP = '';
		// we need a new mnResultId!
		mnResultId = 0;
		tmd_hide();
		gpx_download_enabled(false);
		
		var heading = document.getElementById("filterboxtitle");
		if (heading.style.color != "#f88c00")
		{
			sFilterSaveText = heading.innerHTML;
			bFilterSaveColor = heading.style.color;
			heading.innerHTML = "{/literal}{t escape=js}Map is being updated{/t}{literal} ...";
			heading.style.color = "#f88c00";
		}
	}
	
	queue_dataload(2000);
	cookieSave();
}

function cachetype_all_set()
{
	var bAll = true;
	for (var i=1; i<=nCacheTypeCount; i++)
		if (!document.getElementById('cachetype' + i).checked)
			bAll = false;
	document.getElementById("all_cachetypes").checked = bAll;
}

function cachetype_filter_changed()
{
	cachetype_all_set();
	filter_changed();
}

function cachesize_all_set()
{
	var bAll = true;
	for (var i=1; i<=nCacheSizeCount; i++)
		if (!document.getElementById('cachesize' + i).checked)
			bAll = false;
	document.getElementById("all_cachesizes").checked = bAll;
}	

function cachesize_filter_changed()
{
	cachesize_all_set();
	filter_changed();
}

function alltypes_changed()
{
	var bAll = document.getElementById("all_cachetypes").checked != false;
	for (var i=1; i<=nCacheTypeCount; i++)
		document.getElementById('cachetype' + i).checked = bAll;
	filter_changed();
}
	
function allsizes_changed()
{
	var bAll = document.getElementById("all_cachesizes").checked != false;
	for (var i=1; i<=nCacheSizeCount; i++)
		document.getElementById('cachesize' + i).checked = bAll;
	filter_changed();
}	

function reset_filter_heading()
{
	var heading = document.getElementById("filterboxtitle");
	heading.innerHTML = sFilterSaveText;
	heading.style.color = bFilterSaveColor;
}  

function reset_filter()
{
	flashbutton('resetfilter');
	document.getElementById('cachename').value = "";
	
	for (var i=1; i<=nCacheTypeCount; i++)
		document.getElementById('cachetype' + i).checked = "checked";
	document.getElementById('all_cachetypes').checked = "checked";

	for (var i=1; i<=nCacheSizeCount; i++)
		document.getElementById('cachesize' + i).checked = "checked";
	document.getElementById('all_cachesizes').checked = "checked";

	document.getElementById('f_userowner').checked = "";
	document.getElementById('f_userfound').checked = "";
	document.getElementById('f_ignored').checked = "checked";
	document.getElementById('f_inactive').checked = "checked";
	document.getElementById('f_otherPlatforms').checked = "";

	document.getElementById('terrainmin').value = "0";
	document.getElementById('terrainmax').value = "0";
	document.getElementById('difficultymin').value = "0";
	document.getElementById('difficultymax').value = "0";
	document.getElementById('recommendationmin').value = "0";

	{/literal}
	{foreach from=$aAttributes item=attribGroupItem}
		{foreach from=$attribGroupItem.attr item=attribItem}
			document.getElementById('attribute{$attribItem.id}').value = "3";
			document.getElementById('imgattribute{$attribItem.id}').src = 'resource2/{$opt.template.style}/images/attributes/{$attribItem.icon}-disabled.png';
		{/foreach}
	{/foreach}
	{literal}

	filter_changed();
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
	var sCacheTypes = '';
	for (var i=1; i<=nCacheTypeCount; i++)
	{
		if (document.getElementById('cachetype' + i).checked)
		{
			if (sCacheTypes != '') sCacheTypes += ';';
			sCacheTypes += i;
		}
	}
	// search.php will traditionally ignore 'cachetype' option if it is empty,
	// so we must force it to find nothing if nothing is selected here:
	if (sCacheTypes == '') sCacheTypes = 'none';
	sPostBody += '&cachetype=' + sCacheTypes;

	/* cachesize
	 */
	var sCacheSizes = '';
	for (var i=1; i<=nCacheSizeCount; i++)
	{
		if (document.getElementById('cachesize' + i).checked)
		{
			if (sCacheSizes != '') sCacheSizes += ';';
			sCacheSizes += i;
		}
	}
	if (sCacheSizes == '') sCacheSizes = 'none';
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
	sPostBody += '&cache_attribs=' + get_attrib_filter_params(false);
	sPostBody += '&cache_attribs_not=' + get_attrib_filter_params(true);

	return sPostBody;
}

function get_attrib_filter_params(no)
{
	var state = (no ? '2' : '1');
	var sAttribs = '';

	for (var nCacheAttribId=1; nCacheAttribId<nMaxAttributeId; nCacheAttribId++)
		if (document.getElementById('attribute' + nCacheAttribId))
			if (document.getElementById('imgattribute' + nCacheAttribId).style.display != 'none')
				if (document.getElementById('attribute' + nCacheAttribId).value == state)
				{
					if (sAttribs != '') sAttribs += ';';
					sAttribs += nCacheAttribId;
				}

	return sAttribs;
}

// built query string for map2.php
function get_mapfilter_params()
{
	var oBounds = moMap.getBounds();
	var sPostBody = 'mode=searchresult&compact=1&resultid=' + mnResultId;
	sPostBody += '&lat1=' + oBounds.getSouthWest().lat();
	sPostBody += '&lat2=' + oBounds.getNorthEast().lat();
	sPostBody += '&lon1=' + oBounds.getSouthWest().lng();
	sPostBody += '&lon2=' + oBounds.getNorthEast().lng();

	{/literal}
	{* - marker tooltips are unreliable in Firefox
	   - anti-flicker mechanism (clear_marker_except) is irritating here because
		 - names are not added/removed when zooming for already existing markers
		 therefore this feature is disabled
	// if (moMap.getZoom() > 12)
	//  	sPostBody += '&cachenames=1';
	*}
	{literal}
	if (!bFullscreen) sPostBody += "&smallmap=1"; 

	return sPostBody;
}

function toggle_attribselection(bSaveCookies)
{
	{/literal}
	var filterbefore = get_attrib_filter_params(false) + '/' + get_attrib_filter_params(true);
	
	var tas = document.getElementById('toggle_attribselection');
	var bShow = !bAllAttribs;
	var sShow = (bShow ? 'inline-block' : 'none');

	{foreach from=$aAttributes item=attribGroupItem}
		{if !$attribGroupItem.search_default}
			document.getElementById('attribgroup{$attribGroupItem.id}').style.display = sShow;
		{/if}

		{foreach from=$attribGroupItem.attr item=attribItem}
			{if !$attribItem.search_default}
				document.getElementById('imgattribute{$attribItem.id}').style.display = sShow;
			{/if}
		{/foreach}
	{/foreach}

	tas.innerHTML = (bShow ? "{t}Less{/t}" : "{t}Show all{/t}");
	if (bFullscreen)
		document.getElementById('attribcell').style.width = (bShow ? '675px' : '600px');
	bAllAttribs = !bAllAttribs;
	{literal}

	if (bSaveCookies)
	{
		if (get_attrib_filter_params(false) + '/' + get_attrib_filter_params(true) != filterbefore)
			filter_changed();
		cookieSave();
	}
}

/*========================================================================= 
    End of JavaScript code
 =========================================================================*/

-->
</script>
{/literal}
{/if}  {* not old MSIE *}


<div id="{if $bFullscreen}fullmap{else}smallmap{/if}" class="mapframe">

	{if $bFullscreen}
		{* fullscreen header line *}
		<div id="maplangstripe" class="maplangstripe mapboxshadow" style="position:absolute; left:0; right:0; height:41px; border-bottom:solid 1px grey; z-index:5;">
			<div id="coordbox" class="mapcoord_fullscreen" style="z-index:10"></div>
			<div id="mapstat_caches" class="mapstat_fullscreen" style="z-index:5">{t}Caches displayed{/t}: <span id="statCachesCount">0</span><span id="statLoadTime" style="display:none">0</span></div>
			<div style="position:absolute; top:0px; left:10px; right:180px; height:36px; z-index:15">
	{else}
		{* normal screen coords display *}
		<div class="buffer" style="width: 500px; height: 10px;">&nbsp;</div>
		<div id="coordbox" class="mapcoord_normalscreen" style="z-index:10">&nbsp;</div>
		{if $msie}
			<p>
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
				{t 1=$maxrecords}Map display with Microsoft Internet Explorer is slow and restricted to %1 geocaches. Use another browser for better performance.{/t}
			</p>
		{/if}
	{/if}
	
	{* search and buttons bar *}
	<div class="mapform" style="position:relative; z-index:90">
	<form onsubmit="mapsearch_click(); return false;">
		<table class="mapsearch" align="center">
			<tr>
				{if $bFullscreen}
					{* login status *}
					<td rowspan="2">&nbsp;&nbsp;</td>
					<td rowspan="2" class="maplogin">
						{if $username != ""}{t}Logged in as{/t}<br /><a href="myhome.php"><b>{$username}</b></a>{else}<a href="login.php?target=map2.php%3Fmode%3Dfullscreen">{t}Login{/t} ...</a>{/if}
					</td>
					<td rowspan="2">&nbsp;&nbsp;</td>
				{/if}

				<td rowspan="2" class="mapheader_spacer"></td>

				{* search bar and button *}
				<td rowspan="2"><input type="text" id="mapsearch" style="margin-right:5px" value="" onfocus="javascript:mapsearch_onfocus()" onblur="javascript:mapsearch_onblur()" class="searchfield{if $bFullscreen}_fullscreen{/if}" size="{if $bFullscreen}40{else}50{/if}" /></td><td rowspan="2"><input type="button" id="mapsubmit" name="mapsubmit" value="&nbsp;&nbsp;{t}Search{/t}&nbsp;&nbsp;" class="formbutton" style="width:auto; font-size:{if $bFullscreen}11px{else}12px{/if}" onclick="mapsearch_click()" /></td>
				<td rowspan="2" class="mapheader_spacer"></td>

				{* home button *}
				{if $nUserLat != 0 || $nUserLon != 0 }
					<td rowspan="2"><a class="jslink" onclick="javascript:center_home()"><img id="center_home_img" style="margin-left:5px; margin-right:5px" src="resource2/{$opt.template.style}/images/misc/32x32-home.png" alt="{t}Go to home coordinates{/t}" title="{t}Go to home coordinates{/t}" /></a></td>
				{/if}

				{* GPX download button *}
				<td rowspan="2"><a class="jslink" onclick="javascript:download_gpx()"><img id="download_gpx_img" src="resource2/{$opt.template.style}/images/map/35x35-gpx-download.png" style="margin-left:5px; margin-right:5px" height="35" width="35" alt="{t}Download GPX file (max. 500 caches){/t}" title="{t}Download GPX file (max. 500){/t}"/></a></td>

				{* permalink button *}
				<td rowspan="2"><a class="jslink" onclick="showPermlinkBox_click()"><img src="resource2/{$opt.template.style}/images/map/35x35-star.png" style="margin-left:3px; margin-right:1px" height="35" width="35" alt="{t}Show link to this map{/t}" title="{t}Show link to this map{/t}" /></a></td>

				{* configure button *}
				<td rowspan="2"><a class="jslink" onclick="toggle_settings()"><img src="resource2/{$opt.template.style}/images/openicons/35x35-configure.png" class="mapbutton" style="margin-left:0px; margin-right:0px" height="35" width="35" alt="{t}Settings{/t}" title="{t}Settings{/t}" /></a></td>

				{* help button *}
				{if $help_map != ""}
					<td rowspan="2">{$help_map}<img src="resource2/{$opt.template.style}/images/openicons/35x35-system-help.png" class="mapbutton" style="margin-left:2px; margin-right:3px" height="35" width="35" alt="{t}Instructions{/t}" title="{t}Instructions{/t}" /></a></td>
				{/if}

				{* normal / full screen button *}
				<td rowspan="2">
					{if $bFullscreen}
						<a class="nooutline" href="map2.php?mode=normal"><img src="resource2/{$opt.template.style}/images/map/35x35-normalscreen.png" align="right" style="margin-left:4px; margin-right:4px" height="35" width="35" alt="{t}Switch to small map{/t}" title="{t}Switch to small map{/t}" /></a>
					{else}
						<a class="nooutline" href="map2.php?mode=fullscreen"><img src="resource2/{$opt.template.style}/images/map/35x35-fullscreen.png" align="right" style="margin-left:4px; margin-right:4px" height="35" width="35" alt="{t}Switch to full screen{/t}" title="{t}Switch to full screen{/t}" /></a>
					{/if}
				</td>

			<td rowspan="2" class="mapheader_spacer"></td>
			{if !$bFullscreen}
				<td rowspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			{/if}
		</table>
		</form>
	</div>

	{* dropdown list for search results *}
	<div class="mapselectgeocode">
		<select id="mapselectlist" name="mapselectlist" class="mapselectlist{if $bFullscreen}_fullscreen{/if} mapboxshadow" onblur="mapselectlist_onblur()" onclick="mapselectlist_onclick()">
		</select>
	</div>

	{* popup box for permalink *}
	<div id="permalink_box" class="mappermalink mapboxshadow" style="display:none;">
		<table>
			<tr><td><img src="resource2/ocstyle/images/viewcache/link.png" alt="" height="16" width="16" /> {t}Link to this map view{/t}:</td><td align="right"><a href="javascript:permalinkbox_hide()"><img src="resource2/ocstyle/images/misc/close-medium.png"></a></td></tr>
			<tr><td><input id="permalink_text" type="text" value="" size="55"/></td></tr>
			<tr id="permalink_addFavorites"><td align="right"><input type="button" value="{t}Add to favorites...{/t}" onclick="javascript:addFavorites_click()" /></td></tr>
		</table>
	</div>
	{if $bFullscreen}
		</div></div>		{* end of 'langstripe' *}
	{/if}

	{* map and map overlays *}
	{if $bFullscreen}
		{* let fullscreen map float 1 pixel under the header bar, to compensate for 1px chrome rounding errors *}		
		<div style="position:absolute; top:41px; bottom:0; width:100%; z-index:1">
	{else}
		<div class="buffer" style="width: 500px; height: 18px;">&nbsp;</div>
		<div style="position:relative; width:770px; height:600px; z-index:1">
	{/if}

			{* here go any divs that shall float over the map *}
			<div id="toomanycaches" class="toomanycaches mapboxshadow" style="display:none; z-index:3">
				<table><tr><td id="toomanycaches_txt"></td></tr></table>
			</div>

			<div class="maploading">{t}Loading map{/t} ...</div>
			<div class="mapversion">GM Version <script type="text/javascript">document.write(google.maps.version);</script></div>

			<div id="mapoptions" class="mapoptions mapboxshadow" style="z-index:999; display:none">
				<form action="map2.php?mode={if $bFullscreen}fullscreen{else}normalscreen{/if}" method="post" style="display:inline;">
					<input type="hidden" name="submit" value="1" />
					<table>
						<tr><td><strong>{t}Settings{/t}</strong></td><td style="text-align:right"><a href="javascript:toggle_settings()"><img src="resource2/ocstyle/images/misc/close-medium.png" /></a></tr>
						<tr><td>{t}Menu option 'Map' shows{/t}:</td><td><select name="opt_menumap"><option value="0" {if $opt_menumap==0}selected="selected"{/if}>{t}small map{/t}</option><option value="1" {if $opt_menumap==1}selected="selected"{/if}>{t}fullscreen map{/t}</option></select></td></tr>
						<tr><td>{t}Show overview map{/t}:</td><td><input type="checkbox" name="opt_overview" value="1" {if $opt_overview==1}checked="checked"{/if}/></td></tr>
						<tr><td>{t 1=$min_maxrecords 2=$max_maxrecords}Maximum caches on map<br />(%1-%2, 0=automatic){/t}:</td><td><input type="text" name="opt_maxcaches" size="6" value="{$opt_maxcaches}" /></td></tr>
						<tr><td>{t}Cache icons{/t}:</td><td><select name="opt_cacheicons"><option value="1" {if $opt_cacheicons==1}selected="selected"{/if}>{t}classic OC{/t}<option value="2" {if $opt_cacheicons==2}selected="selected"{/if}>{t}OKAPI-Stil{/t}</option></select></td></tr>
						<tr><td colspan="2">{if $login.userid>0}<input type="button" class="formbutton" value="{t}Cancel{/t}" onclick="toggle_settings()"/>&nbsp; <input type="submit" name="submitsettings" class="formbutton" value="{t}Change{/t}" onclick="submitbutton('submitsettings')" />{else}<em>{t}You must be logged in to change map settings.{/t}</em>{/if}</td></tr>	
					</table>
				</form>
			</div>

			{* THE MAP *}
			<div id="googlemap" style="position:absolute; top:0; bottom:0; left:0; width:100%; z-index:2"></div>
		</div>

	{if $bFullscreen}
		{* the logo *}
		<a href="index.php"><img src="resource2/ocstyle/images/head/overlay/oc_logo_alpha3.png" style="position:absolute; left:32px; top:50px; z-index:2; border:0;"></a>
		
		{literal}
		<script language="javascript">
		function toggle_sidebar(savecookies) 
		{
			var ele = document.getElementById("sidebar");
			var img = document.getElementById("sidbar-toggle-img");
			{/literal}
			// var hideimg = "resource2/{$opt.template.style}/images/map/32x32-right.png";
			// var showimg = "resource2/{$opt.template.style}/images/map/32x32-left.png";
			{literal}
			if (ele.style.display == "block") {
				ele.style.display = "none";
				// img.src=showimg;
				img.style.display = "block";
				if (bFilterChanged)
					queue_dataload(100);
			}
			else {
				ele.style.display = "block";
				// img.src=hideimg;
				img.style.display = "none";
				if (bFilterChanged)  // for the case ...
					reset_filter_heading();
			}
			msInitSiderbarDisplay = ele.style.display; 
			if (savecookies) cookieSave();
		}
		</script>
		{/literal}		

		{* frame for all sidebare contents: *}
		<div class="mapboxshadow" style="position:absolute; top: 80px; right:0px; margin: 0px; padding: 4px; border:1px solid #000; background:#fff; opacity: .9; z-index:2">
			{* sidebar hidden: '<' icon to open *}
			<a class="jslink nofocus" onclick="javascript:toggle_sidebar(true);" id='sidebar-toggle' style="width: 32px; height: 32px"><img id="sidbar-toggle-img" src="resource2/{$opt.template.style}/images/map/32x32-left.png"></a>
			{* sidebar visible: filter options table & '>' icon to close *}
			<div id="sidebar" style="display:none; overflow:auto">

	{* filter options header *}
	{* outer table es needed to use "width=100%" for inner table (to position the close 
     icon right) without consuming whole screen width in MSIE *}
	<table cellspacing=0 cellpadding=0><tr><td>
		<table style="width:100%">
			<tr>
				<td style="width:3px"></td>
				<td id="filterboxtitle" class="content-title-noshade-size1">{t}Only show Geocaches with the following properties:{/t}</td>
				<td align="right""><a class="jslink" onclick="javascript:toggle_sidebar(true);"><img src="resource2/ocstyle/images/map/32x32-right.png"></a></td>
			</tr>
		</table>
	{else}
		<div class="buffer" style="width: 500px; height: 2px;">&nbsp;</div>
		<div style="width:770px;text-align:right;"><span id="mapstat_caches">{t}Caches displayed{/t} <span id="statCachesCount">0</span></span>, {t}Time to load{/t} <span id="statLoadTime">0</span> {t}Sec.{/t}</div>
		<p id="filterboxtitle" class="content-title-noshade-size1">{t}Only show Geocaches with the following properties:{/t}</p>
		<div class="buffer" style="width: 500px; height: 5px;">&nbsp;</div>
	{/if}

	{* filter options *}

	{* name *}
	<table>
		<tr>
			<td class="mapfilter pad10" width="{if $bFullscreen}600{else}752{/if}">
				<table style="width:100%">
					<tr>
						<td>
							<strong>{t}Name:{/t}</strong>&nbsp; <input type="text" id="cachename" name="cachename" value="" onkeyup="filter_changed()" onchange="filter_changed()" class="input200" /></td>
						</td>
						<td style="text-align:right">
							<input type="button" name="resetfilter" class="formbutton" style="width:auto" value="&nbsp;&nbsp;{t}Reset{/t}&nbsp;&nbsp;" onclick="reset_filter()" />&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			{* cache types *}
			<td valign="top" class="mapfilter pad10" width="{if $bFullscreen}140{else}150{/if}">
				<table>                                                                 
					<tr><td colspan="3" class="mapfiltertopic mft_withcheckbox"><input id="all_cachetypes" type="checkbox" checked="checked" onchange="alltypes_changed()"> <label for="all_cachetypes">{t}Cachetype{/t}</label></td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					{foreach from=$aCacheType item=cacheTypeItem}
						<tr>
							<td><input type="checkbox" id="cachetype{$cacheTypeItem.id}" name="cachetype{$cacheTypeItem.id}" value="1" checked="checked" onchange="cachetype_filter_changed()" class="checkbox" /></td>
							<td><img src="resource2/ocstyle/images/cacheicon/16x16-{$cacheTypeItem.id}.gif"></td>
							<td style="white-space:nowrap;">&nbsp;<label for="cachetype{$cacheTypeItem.id}">{$cacheTypeItem.text|escape}</label></td>
						</tr>
					{/foreach}
				</table>
			</td>

			{* cache sizes *}
			<td valign="top" class="mapfilter pad10" width="{if $bFullscreen}128{else}137{/if}">
				<table>                                                     
					<tr><td class="mapfiltertopic mft_withcheckbox"><input id="all_cachesizes" type="checkbox" checked="checked" onchange="allsizes_changed()"> <label for="all_cachesizes">{t}Container{/t}</label></td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					{foreach from=$aCacheSize item=cacheSizeItem}
						<tr>
							<td style="white-space:nowrap">
								<input type="checkbox" id="cachesize{$cacheSizeItem.id}" name="cachesize{$cacheSizeItem.id}" value="1" checked="checked" onchange="cachesize_filter_changed()" class="checkbox" />
								<label for="cachesize{$cacheSizeItem.id}">{$cacheSizeItem.text|escape}</label>
							</td>
						</tr>
					{/foreach}
				</table>
			</td>

			{* hide *}
			<td valign="top" class="mapfilter pad10" width="{if $bFullscreen}130{else}140{/if}">
				<table>
					<tr><td class="mapfiltertopic">{t}Hide{/t}</td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					<tr>
						<td style="white-space:nowrap">
							<input type="checkbox" id="f_userowner" name="f_userowner" value="1" onchange="filter_changed()" class="checkbox" {if $username==""}disabled{/if} />
							<label for="f_userowner" {if $username==""}style="color:grey"{/if}>{t}My owns{/t}</label>
						</td>
					</tr>
					<tr>
						<td style="white-space:nowrap">
							<input type="checkbox" id="f_userfound" name="f_userfound" value="1" onchange="filter_changed()" class="checkbox"  {if $username==""}disabled{/if} />
							<label for="f_userfound" {if $username==""}style="color:grey"{/if}>{t}My finds{/t}</label>
						</td>
					</tr>
					<tr>
						<td style="white-space:nowrap">
							<input type="checkbox" id="f_ignored" name="f_ignored" value="1" checked="checked" onchange="filter_changed()" class="checkbox"  {if $username==""}disabled{/if} />
							<label for="f_ignored" {if $username==""}style="color:grey"{/if}>{t}My ignored{/t}</label>
						</td>
					</tr>
					<tr>
						<td style="white-space:nowrap">
							<input type="checkbox" id="f_inactive" name="f_inactive" value="1" checked="checked" onchange="filter_changed()" class="checkbox" />
							<label for="f_inactive">{t}Not active{/t}</label>
						</td>
					</tr>
					<tr>
						<td style="white-space:nowrap">
							<input type="checkbox" id="f_otherPlatforms" name="f_otherPlatforms" value="1" onchange="filter_changed()" class="checkbox" />
							<label for="f_otherPlatforms">{t}Double listings{/t}</label>
						</td>
					</tr>
				</table>
			</td>

			{* rating *}
			<td valign="top" class="mapfilter pad10" width="{if $bFullscreen}160{else}282{/if}"> 
				<table>
					<tr>
						<td colspan="2" class="mapfiltertopic">{t}Rating{/t}</td>
					<tr><td colspan="2"><span style="line-height: 5px;">&nbsp;</span></td></tr>
					</tr>
					<tr>
						<td {if $bFullscreen}colspan="2"{/if}>{t}Difficulty{/t}:</td>
						{if $bFullscreen}</tr><tr><td colspan="2" style="white-space:nowrap; text-align:right">{else}<td>{/if}
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
							&nbsp;&nbsp;{t}to{/t}&nbsp;&nbsp;
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
					{if $bFullscreen}<tr><td style="height:4px"></td></tr>{/if}
					<tr>
						<td {if $bFullscreen}colspan="2"{/if}>{t}Terrain{/t}:</td>
							{if $bFullscreen}</tr><tr><td colspan="2" style="white-space:nowrap; text-align:right">{else}<td>{/if}
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
							&nbsp;&nbsp;{t}to{/t}&nbsp;&nbsp;
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
					{if $bFullscreen}<tr><td>&nbsp;</td></tr>{/if}
					<tr>
						<td>{t}Min. recommendations{/t}:</td>
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
			<td id="attribcell" valign="top" class="mapfilter pad10" style="padding-bottom:2px" width="{if $bFullscreen}675{else}752{/if}">
				<table>
					<tr><td class="mapfiltertopic">Attribute &nbsp; <span class="mapfiltertopic_add">[<a id="toggle_attribselection" class="jslink" onclick='toggle_attribselection(true)'>{t}Less{/t}</a>]</span></td></tr>
					<tr><td><span style="line-height: 5px;">&nbsp;</span></td></tr>
					<tr>
						<td>
							{include file="res_attribgroup.tpl" attriblist=$aAttributes onmousedown="attribute_onmousedown" inputprefix="attribute" stateDisable=$aAttributesDisabled searchsel=1}
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	{if $bFullscreen}
		</td></tr></td></table>
		</div>
		</div>
	{/if}

</div>
