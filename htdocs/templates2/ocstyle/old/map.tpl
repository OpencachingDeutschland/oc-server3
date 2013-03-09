{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

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
						<input type="button" id="mapsubmit" value="Suchen" onclick="javascript:mapsubmit_click()" class="searchbutton" />
					</td>
					<td class="mapsearch-c">
						<a href="#" onclick="javascript:showPermlinkBox_click()">
							<img src="resource2/{$opt.template.style}/images/map/35x35-star.png" align="right" style="margin-left:15px; margin-right: 0px;" height="35" width="35" alt="{t}Show link to this map{/t}" />
						</a>
						<a href="#" onclick="javascript:fullscreen_click()">
							<img src="resource2/{$opt.template.style}/images/map/35x35-fullscreen.png" align="right" style="margin-left:15px; margin-right: 0px;" height="35" width="35" alt="{t}Switch to full screen{/t}" />
						</a>
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
			<tr><td><img src="resource2/ocstyle/images/viewcache/link.png" alt="" height="16" width="16" /> Link ( <a href="#" onclick="javascript:openPermalink_onclick()">Öffnen</a> )</td></tr>
			<tr><td><input id="permalink_text" type="text" value="" size="50"/></td></tr>
			<tr id="permalink_addFavorites"><td align="right"><input type="button" value="Zu den Favoriten hinzufügen" onclick="javascript:addFavorites_onclick()" /></td></tr>
		</table>
	</div>
{/if}

{if $opt.template.popup==false}
	<p>&nbsp;</p>
{/if}

{if $opt.template.popup==false}
	<div id="map" style="width:770px;height:600px;"></div>
{else}
	<div id="map" style="width:100%;height:100%;"></div>
{/if}

{if $opt.template.popup==false}
	<div style="font-size:x-small;">Quelle Naturschutzgebiete: Bundesamt für Naturschutz (BfN). Entwicklung durch das Team Opencaching.de (Mapserver Tiger, OC-Umsetzung Oliver, Design Schrottie &amp; Feedback vom Team)</div>
	<p>&nbsp;</p>
{/if}

{literal}
  <script type="text/javascript">
  <!--
		var initlat = {/literal}{$gm_initlat}{literal};
		var initlon = {/literal}{$gm_initlon}{literal};
		var initzoom = {/literal}{$gm_initzoom}{literal};
		var initcookiepos = {/literal}{$gm_initcookiepos}{literal};
		var initwp = "{/literal}{$gm_initwp}{literal}";
		var inittype = '';

		var searchhint = "{/literal}{t escape=js}Search for city, cache or waypoint{/t}{literal}";
		var resetsearchtextonfocus = false;
		var fullscreen = {/literal}{$fullscreen}{literal};
		var permalink = 'map.php';

		var initcookielastpos = 'ocgmlastpos';
		var initcookieconfig = 'ocgmconfig';
		if (!navigator.cookieEnabled)
		{
			initcookielastpos = '';
			initcookieconfig = '';
		}

    var urlTemplate = 'http://maps.geocaching.de/tilecache/tilecache.py/1.0.0/occaches/{Z}/{X}/{Y}.png?type=google';
    var cacheLayer = new GTileLayer(null,0,18,{tileUrlTemplate:urlTemplate, isPng:true, opacity:1.0});
		var geocoder = new GClientGeocoder();
		var searchlist = document.getElementById('mapselectlist');
		var searchlist_coords = new Array();
		var mapsearch = document.getElementById('mapsearch');
		var map = null;

		function loadCookie()
		{
			if (initcookieconfig == '')
				return;

			var cookiecontent = document.cookie.split(";");
			for (var index = 0; index < cookiecontent.length; index++)
			{
				var cookievalue = trim(cookiecontent[index]).split("=");
				if (cookievalue[0] == initcookielastpos)
				{
					var savedlastpos = cookievalue[1];
					if (initcookiepos == 1)
					{
						var values = savedlastpos.split(":");
						initzoom = parseInt(values[0]);
						initlon = parseFloat(values[1]);
						initlat = parseFloat(values[2]);
					}
				}
				else if (cookievalue[0] == initcookieconfig)
				{
					var savedconfig = cookievalue[1];
					inittype = savedconfig;
				}
			}
		}

		function saveCookie()
		{
			if (initcookieconfig == '')
				return;

			var centerpos = map.getCenter();
			var zoomlevel = map.getZoom();
			var dtExp = new Date(2049, 12, 31);

			document.cookie = initcookieconfig + "=" + map.getCurrentMapType().getName(false) + ";expires=" + dtExp.toUTCString();
			document.cookie = initcookielastpos + "=" + zoomlevel + ":" + centerpos.lng() + ":" + centerpos.lat() + ";expires=" + dtExp.toUTCString();

			permalink = "map.php?lat=" + centerpos.lat() + "&lon=" + centerpos.lng() + "&zoom=" + zoomlevel + "&map=" + encodeURI(map.getCurrentMapType().getName(false));
			
			var permalink_textbox = document.getElementById('permalink_text');
			if (permalink_textbox)
				permalink_textbox.value = "{/literal}{$opt.page.absolute_url}{literal}" + permalink;
		}

		function loadMap()
		{
			if (GBrowserIsCompatible())
			{
        var dragZoomOptions = { 
          buttonStartingStyle: {display:'block',color:'black',background:'white',width:'7em',textAlign:'center',
            fontFamily:'Verdana',fontSize:'8px',fontWeight:'bold',border:'1px solid gray',paddingBottom:'1px',cursor:'pointer'},
          buttonHTML: 'Drag Zoom',
          buttonZoomingHTML: 'Drag a region on the map (click here to reset)',
          buttonZoomingStyle: {background:'yellow'},
          backButtonHTML: 'Drag Zoom Back',  
          backButtonStyle: {display:'none',marginTop:'3px',background:'#FFFFC8'},
          backButtonEnabled: false
        } 
				loadCookie();

				var map_tag = document.getElementById("map");
        map = new GMap2(map_tag, {draggableCursor: 'crosshair', draggingCursor: 'pointer'});
        map.addMapType(G_PHYSICAL_MAP);
				OSM_addMapType();
        map.addControl(new GSmallZoomControl());
        map.addControl(new GHierarchicalMapTypeControl());
        map.addControl(new GOverviewMapControl());
        map.addControl(new GScaleControl());
        map.addControl(new DragZoomControl({}, dragZoomOptions, {}), new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(28,8)));
        map.addControl(new LatLonDisplayControl());
        map.enableScrollWheelZoom();
        map.setCenter(new GLatLng(initlat, initlon), initzoom, map_GetMapTypeByName(inittype));
        var cacheOverlay = new GTileLayerOverlay(cacheLayer);
        map.addOverlay(cacheOverlay);

        GEvent.addListener(map, "click", function(overlay,point){map_click(overlay,point)});
        GEvent.addListener(map, "moveend", function(){map_moveend()});
        GEvent.addListener(map, "maptypechanged", function(){map_maptypechanged()});

        if (initwp != "")
					show_cachepopup_wp(initwp, true);

				if (mapsearch)
	        mapsearch.value = searchhint;

				// built initial permalink
        saveCookie();
			}
		}

		function map_GetMapTypeByName(name)
		{
			var mapFound = null;
			var mapTypes = map.getMapTypes();

			for (var index = 0; index < mapTypes.length; index++)
			{
				if (mapTypes[index].getName(false) == name)
				{
					mapFound = mapTypes[index];
					break;
				}
			}

			if (mapFound == null)
				mapFound = G_HYBRID_MAP;

			return mapFound;
		}

		function map_click(overlay,point)
		{
			if (point == null)
				return;

			show_cachepopup_latlon(point.lat(), point.lng(), false);
		}

		function map_moveend()
		{
			saveCookie();
		}

		function map_maptypechanged()
		{
			saveCookie();
		}

		function fullscreen_click()
		{
			window.open(permalink + '&mode=fullscreen','ocgm','width=' + screen.width + ',height=' + screen.height + ',resizable=yes,scrollbars=1');
		}

		function showPermlinkBox_click()
		{
			var box = document.getElementById('permalink_box');

			if(window.opera)
				document.getElementById('permalink_addFavorites').style.display = 'none';
			else
			{
				if ((typeof window.external.AddFavorite == 'undefined') && 
						(typeof window.external.addPanel == 'undefined'))
				{
					document.getElementById('permalink_addFavorites').style.display = 'none';
				}
			}

			if (box.style.display == 'none')
				box.style.display = 'block';
			else
				box.style.display = 'none';
		}

		function addFavorites_onclick()
		{
			var link = document.getElementById('permalink_text').value;
			var title = '{/literal}{$opt.page.title} - {t escape=js}Map{/t}{literal}';

			if (typeof window.external.AddFavorite != 'undefined')
				window.external.AddFavorite(link, title);

			if (typeof window.external.addPanel != 'undefined')
				window.external.addPanel(title, link, '')
		}

		function openPermalink_onclick()
		{
			var link = document.getElementById('permalink_text').value;
			window.location = link;
		}

		function mapselectlist_onblur()
		{
			mapselectlist_hide();
		}

		function mapselectlist_clear()
		{
			while (searchlist.length>0)
				searchlist.options[0] = null;
			searchlist_coords = new Array();
		}

		function add_searchlist_itemcoords(lat, lon, wp, text)
		{
			var item = new Array();
			item['lat'] = lat;
			item['lon'] = lon;
			item['wp'] = wp;
			item['text'] = text;

			searchlist_coords[searchlist_coords.length] = item;
			return searchlist_coords.length-1;
		}

		function mapselectlist_show()
		{
			searchlist.style.display = "block";
		}

		function mapselectlist_hide()
		{
			searchlist.style.display = "none";
		}

		function mapselectlist_onchange()
		{
			var index;

			for (index=0; index<searchlist.length; index++)
			{
				if (searchlist.options[index].selected)
				{
					if (searchlist.options[index].value == -1)
						return;

					var text = searchlist.options[index].text;
					var coords_index = searchlist.options[index].value;
					document.getElementById('mapsearch').value = trim(text);
					resetsearchtextonfocus = true;
					mapselectlist_hide();

					// go to the location
					searchlist_openitem(searchlist.options[index].value);

					return;
				}
			}
		}

		function searchlist_openitem(index)
		{
			var lat = searchlist_coords[index]['lat'];
			var lon = searchlist_coords[index]['lon'];
			var wp = searchlist_coords[index]['wp'];
			var text = searchlist_coords[index]['text'];

			if (wp != '')
				show_cachepopup_wp(wp, true);
			else
			{
				var coords = new GLatLng(lat, lon);
				map.setCenter(coords, 13);
				map.openInfoWindowHtml(coords, xmlentities(text));
			}
		}

		function mapsubmit_click()
		{
			var searchtext = mapsearch.value;
			var tempOption;

			if (!map) return;

			if (searchhint == searchtext || searchtext == '')
			{
				alert('Bitte einen Suchbegriff eingeben!');
				return;
			}

			// check for geocaching waypoint
			if (searchtext.match(/^OC[\S]{1,}$/i) || 
			    searchtext.match(/^GC[\S]{1,}$/i) || 
			    searchtext.match(/^N[0-9]{1,5}$/i))
			{
				show_cachepopup_wp(searchtext, true);
				return;
			}

			// do search on opencaching.de			
			var centerpos = map.getCenter();
			GDownloadUrl("map.php?mode=namesearch&name=" + encodeURI(searchtext) + "&lat=" + centerpos.lat() + "&lon=" + centerpos.lng(), 
			  function(data, responseCode)
			  {
          var xml = GXml.parse(data);
    			var caches = xml.documentElement.getElementsByTagName("cache");

			    // clear the result list
			    mapselectlist_clear();

          if (caches.length>0)
          {
  			    tempOption = new Option("Gefundene Geocaches", -1);
	  		    tempOption.style.color = "gray";
		  	    searchlist.options[searchlist.length] = tempOption;

		  	    for (var nCacheIndex=0; nCacheIndex<caches.length; nCacheIndex++)
		  	    {
		  	      var name = caches[nCacheIndex].getAttribute("name");
		  	      var wpoc = caches[nCacheIndex].getAttribute("wpoc");
		  	      var text = name + " (" + wpoc + ")";
							var value = add_searchlist_itemcoords(0, 0, wpoc, text);
							var item = new Option("     " + text, value);
							item.style.marginLeft = "20px";
							searchlist.options[searchlist.length] = item;
		  	    }
		  	    
		  	    if (caches.length >= 30)
		  	    {
							var item = new Option("     {/literal}{t escape=js}Some more items found...{/t}{literal}", -1);
							item.style.marginLeft = "20px";
							item.style.color = "gray";
							searchlist.options[searchlist.length] = item;
		  	    }
		  	  }

          // do search on google
			    geocoder.setViewport(new GLatLngBounds(new GLatLng(56, 5), new GLatLng(47, 16)));
			    geocoder.getLocations(searchtext, 
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
						    tempOption = new Option("Gefundene Orte (Google)", -1);
						    tempOption.style.color = "gray";
						    searchlist.options[searchlist.length] = tempOption;

						    for (nPlacemarkIndex=0; nPlacemarkIndex<nPlacemarksCount; nPlacemarkIndex++)
						    {
							    var coord = retval["Placemark"][nPlacemarkIndex]["Point"]["coordinates"];
							    var text = retval["Placemark"][nPlacemarkIndex]["address"];
							    var value = add_searchlist_itemcoords(coord[1], coord[0], "", text);
							    var item = new Option("     " + text, value);
									item.style.marginLeft = "20px";
							    searchlist.options[searchlist.length] = item;
						    }
					    }

					    if (searchlist.length==0)
					    {
						    mapselectlist_hide();
						    alert("'" + searchtext + "' nicht gefunden!");
						    return;
					    }
					    else if (searchlist_coords.length==1)
					    {
								mapselectlist_hide();
								searchlist_openitem(0);
								return;
					    }

					    // adjust size
					    if (searchlist.length>15)
						    searchlist.size = 15;
					    else
						    searchlist.size = searchlist.length;

					    mapselectlist_show();
				    });
        });
		}

		function show_cachepopup_wp(wp, allowZoomChange)
		{
			show_cachepopup_url("map.php?mode=wpsearch&wp=" + wp, wp, allowZoomChange);
		}

		function show_cachepopup_latlon(lat, lon, allowZoomChange)
		{
			show_cachepopup_url("map.php?mode=locate&lat=" + lat + "&lon=" + lon, "", allowZoomChange);
		}

		function show_cachepopup_url(url, wp, allowZoomChange)
		{
			GDownloadUrl(url, function(data, responseCode)
			{
        var xml = GXml.parse(data);
        var coords = parseXML_GetPoint(xml);
        if (!coords)
        {
          if (wp != '')
  					alert('Wegpunkt ' + wp + ' nicht gefunden!');
					return;
				}

				if (allowZoomChange==true)
					map.setCenter(coords, 13);

        var popupHTML = parseXML_GetHTML(xml);
				map.openInfoWindowHtml(coords, popupHTML);
			});
		}

		function parseXML_GetHTML(xmlobject)
		{
			var caches = xmlobject.documentElement.getElementsByTagName("cache");
			if (caches.length<1)
				return false;

			var val_name = caches[0].getAttribute("name");
			var val_wpoc = caches[0].getAttribute("wpoc");
			var val_coords = caches[0].getAttribute("coords");
			var val_status_tna = caches[0].getAttribute("status_tna");
			var val_status_text = caches[0].getAttribute("status_text");
			var val_type_id = caches[0].getAttribute("type_id");
			var val_type_text = caches[0].getAttribute("type_text");
			var val_size = caches[0].getAttribute("size");
			var val_difficulty = caches[0].getAttribute("difficulty");
			var val_terrain = caches[0].getAttribute("terrain");
			var val_listed_since = caches[0].getAttribute("listed_since");
			var val_toprating = caches[0].getAttribute("toprating");
			var val_geokreties = caches[0].getAttribute("geokreties");
			var val_found = caches[0].getAttribute("found");
			var val_notfound = caches[0].getAttribute("notfound");
			var val_owner = caches[0].getAttribute("owner");
			var val_username = caches[0].getAttribute("username");
			var val_userid = caches[0].getAttribute("userid");

			var myHtml = "<table>";
			if (val_status_tna == 1)
			{
				myHtml = myHtml + "<tr><td colspan='2'><font size='2' color='red'><b>" + xmlentities(val_status_text) + "</b></font></td></tr>";
				myHtml = myHtml + "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
			}

			myHtml = myHtml + "<tr><td><img src='resource2/ocstyle/images/cacheicon/16x16-" + val_type_id + ".gif' alt='" + xmlentities(val_type_text) + "' title='" + xmlentities(val_type_text) + "' /> <a href='viewcache.php?wp=" + encodeURI(val_wpoc) + "' target='_blank'><font size='2'>" + xmlentities(val_name) + "</font></a></td><td align='right' width='60px'><font size='2'><b>" + xmlentities(val_wpoc) + "</b></font></td></tr>";
			myHtml = myHtml + "<tr><td colspan='2'>{/literal}{t escape=js}by{/t}{literal} <a href='viewprofile.php?userid=" + encodeURI(val_userid) + "' target='_blank'>" + xmlentities(val_username) + "</a></td></tr>";
			myHtml = myHtml + "<tr><td colspan='2'>" + xmlentities(val_type_text) + " (" + xmlentities(val_size) + ")&nbsp;&nbsp;&nbsp;D/T: " + parseFloat(val_difficulty).toFixed(1) + "/" + parseFloat(val_terrain).toFixed(1) + "</td></tr>";
			myHtml = myHtml + "<tr><td colspan='2'>{/literal}{t escape=js}Listed since:{/t}{literal} " + xmlentities(val_listed_since) + "</td></tr>";

			if (val_owner==1)
				myHtml = myHtml + "<tr><td colspan='2'><img src='resource2/ocstyle/images/misc/16x16-home.png' alt='' /> {/literal}{t escape=js}This cache is yours{/t}{literal}</td></tr>";

			if (val_found==1)
				myHtml = myHtml + "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/16x16-found.png' alt='' /> {/literal}{t escape=js}You found this cache{/t}{literal}</td></tr>";

			if (val_notfound==1)
				myHtml = myHtml + "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/16x16-dnf.png' alt='' /> {/literal}{t escape=js}You havn't found this cache, yet{/t}{literal}</td></tr>";

			if (val_geokreties>0)
				myHtml = myHtml + "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/gk.png' alt='' /> {/literal}{t escape=js}This cache stores a GeoKrety{/t}{literal}</td></tr>";

			if (val_toprating>0)
				myHtml = myHtml + "<tr><td colspan='2'><img src='resource2/ocstyle/images/viewcache/rating-star.gif' alt='' /> {/literal}{t escape=js}This cache has %1 recommandations{/t}{literal}</td></tr>".replace(/%1/, val_toprating);

			myHtml = myHtml + "</table>";

			return myHtml;
		}

		function parseXML_GetPoint(xmlobject)
		{
			var caches = xmlobject.documentElement.getElementsByTagName("cache");

			if (caches.length<1)
				return false;

			var coords = caches[0].getAttribute("coords").split(",");
			var coordsyx = new GLatLng(coords[1],coords[0]);

			return coordsyx;
		}

		function OSM_addMapType()
		{
      var osmcopyright = new GCopyright(1, new GLatLngBounds(new GLatLng(-90,-180), new GLatLng(90,180)), 0, '(<a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>)');
      var copyrightCollection = new GCopyrightCollection('Kartendaten &copy; 2009 <a href="http://www.openstreetmap.org/">OpenStreetMap</a> Contributors');
      copyrightCollection.addCopyright(osmcopyright);
      var tilelayers_mapnik = new Array();
      tilelayers_mapnik[0] = new GTileLayer(copyrightCollection, 0, 18);
      tilelayers_mapnik[0].getTileUrl = function(a,z) { return "http://tile.openstreetmap.org/" + z + "/" + a.x + "/" + a.y + ".png"; };
      tilelayers_mapnik[0].isPng = function () { return true; };
      tilelayers_mapnik[0].getOpacity = function () { return 1.0; };
      var mapnik_map = new GMapType(tilelayers_mapnik, new GMercatorProjection(19), "OSM", { urlArg: 'mapnik', linkColor: '#000000' });
      map.addMapType(mapnik_map);
		}

		function mapsearch_onfocus()
		{
			if (mapsearch.value==searchhint || resetsearchtextonfocus==true)
				mapsearch.value = '';
			resetsearchtextonfocus = false;
		}

		function mapsearch_onblur()
		{
			if (mapsearch.value=='')
				mapsearch.value = searchhint;
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
		
		/* GM custom controls */
		
		/* LatLonDisplayControl
		 */
    function LatLonDisplayControl() {}
    LatLonDisplayControl.prototype = new GControl();
    LatLonDisplayControl.prototype.initialize = function(map)
    {
			var control = this;
      var container = document.createElement("div");
 
      var latDiv = document.createElement("div");
      var latText = document.createTextNode(" ");
      this.setLabelStyle_(map, latDiv);
      container.appendChild(latDiv);
      latDiv.appendChild(latText);

      var lonDiv = document.createElement("div");
      var lonText = document.createTextNode(" ");
      this.setLabelStyle_(map, lonDiv);
      container.appendChild(lonDiv);
      lonDiv.appendChild(lonText);

			GEvent.addListener(map, "maptypechanged", function(latLng)
			{
				if (map.getCurrentMapType().getName(false) == "Hybrid")
				{
					latDiv.style.color = "white";
					lonDiv.style.color = "white";
				}
				else
				{
					latDiv.style.color = "black";
					lonDiv.style.color = "black";
				}
			});
			GEvent.addListener(map, "mouseout", function(latLng)
			{
				var newLonText = document.createTextNode(" ");
				lonDiv.replaceChild(newLonText, lonText);
				lonText = newLonText;
				
				var newLatText = document.createTextNode(" ");
				latDiv.replaceChild(newLatText, latText);
				latText = newLatText;
			});
			GEvent.addListener(map, "mousemove", function(latLng)
			{
				var newLonText = document.createTextNode(control.formatLon_(latLng.lng()));
				lonDiv.replaceChild(newLonText, lonText);
				lonText = newLonText;
				
				var newLatText = document.createTextNode(control.formatLat_(latLng.lat()));
				latDiv.replaceChild(newLatText, latText);
				latText = newLatText;
			});

      map.getContainer().appendChild(container);
      return container;
    }
 
    LatLonDisplayControl.prototype.getDefaultPosition = function()
    {
      return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(7, 32));
    }
 
    LatLonDisplayControl.prototype.setLabelStyle_ = function(map, button)
    {
			if (map.getCurrentMapType().getName(false) == "Hybrid")
	      button.style.color = "white";
	    else
				button.style.color = "black";

      button.style.font = "small Arial";
      button.style.padding = "0px";
      button.style.marginBottom = "1px";
      button.style.textAlign = "right";
      button.style.width = "10em";
    }

		LatLonDisplayControl.prototype.formatLat_ = function(lat)
		{
			var sPrefix = "";
			if (lat<0)
				sPrefix = "S ";
			else
				sPrefix = "N ";
			
			return sPrefix + this.formatLatLon_(lat, 2);
		}

		LatLonDisplayControl.prototype.formatLon_ = function(lon)
		{
			var sPrefix = "";
			if (lon<0)
				sPrefix = "W ";
			else
				sPrefix = "E ";
			
			return sPrefix + this.formatLatLon_(lon, 3);
		}

		LatLonDisplayControl.prototype.formatLatLon_ = function(value, nDegLength)
		{
			value = Math.abs(value);
			var nDeg = Math.floor(value);
			var nDecimal = Math.round((value - nDeg) * 60 * 1000) / 1000;
			if (nDecimal>=60)
			{
				nDecimal = nDecimal - 60
				nDeg = nDeg + 1;
			}
			var sDeg = nDeg.toString();
			var sDecimal = nDecimal.toFixed(3);
			while (sDeg.length<nDegLength) sDeg = "0" + sDeg;
			while (sDecimal.length<6) sDecimal = "0" + sDecimal;
			return sDeg + "° " + sDecimal + "'";
		}
	//-->
	</script>
{/literal}
