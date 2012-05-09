function load(lat, lon, id, url, garminKey, language)
{
	var sFindDevicesButtonText = "Search device";
	var sSuccessMessage = "Geocache saved successfully";
	var nLat = lat;
	var nLon = lon;

	if (language == "DE")
	{
		sFindDevicesButtonText = "Gerät suchen";
		sSuccessMessage = "Geocache wurde übertragen";
	}

	var display = new Garmin.DeviceDisplay("garminDisplay",
	{
		pathKeyPairsArray: [url, garminKey],
		showStatusElement: true,                        //basic feedback provided
		unlockOnPageLoad: false,                        //delays unlocking to avoid authorization prompt until action
		hideIfBrowserNotSupported: true,
		showStatusElement: true,                        //provide minimal feedback
		autoFindDevices: true,                          //it will search for devices upon action
		findDevicesButtonText: sFindDevicesButtonText,  //allows you to customize the action text
		showCancelFindDevicesButton: true,              //no need to cancel small data transfers
		autoSelectFirstDevice: true,                    //pick the first device if several are found
		autoReadData: false,                            //don't automatically read the tracks/etc
		autoWriteData: true,                            //automatically write the data once devices found
		showReadDataElement: false,

		/*This is where the waypoint object is created and the necessary GPX is created.
		* The plugin speaks GPX, but you (the developer) may not so simply use the 
		* Waypoint data structure to produce the GPX.
		*/
		getWriteData: function()
		{
			var xmlReq = createXMLHttp();
			var params = 'searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid=' + id + '&output=gpx';
			if (!xmlReq) return;

			xmlReq.open('POST', 'search.php', false);
			xmlReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlReq.setRequestHeader("Content-length", params.length);
			xmlReq.setRequestHeader("Connection", "close");
			xmlReq.send(params);

			if (xmlReq.status == 200)
				return xmlReq.responseText;
			else
				return '';
		},
		getWriteDataFileName: function() { return garmin_gpx_filename; },
		afterFinishWriteToDevice: function()
		{
			alert(sSuccessMessage);
		}
	});
}

function createXMLHttp()
{
	if (typeof XMLHttpRequest != 'undefined')
		return new XMLHttpRequest();
	else if (window.ActiveXObject)
	{
		var avers = ["Microsoft.XmlHttp", "MSXML2.XmlHttp","MSXML2.XmlHttp.3.0", "MSXML2.XmlHttp.4.0","MSXML2.XmlHttp.5.0"];
		for (var i = avers.length -1; i >= 0; i--)
		{
			try
			{
				httpObj = new ActiveXObject(avers[i]);
				return httpObj;
			}
			catch(e)
			{
			}
		}
	}
	return null;
}
