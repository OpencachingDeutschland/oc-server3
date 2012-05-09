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