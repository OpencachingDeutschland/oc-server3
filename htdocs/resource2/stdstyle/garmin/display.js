function load(lat, lon, id, url, garminKey, language)
{
	var sFindDevicesButtonText = "Search device";
	var sSuccessMessage = "Geocache saved successfully";
	var nLat = lat;
	var nLon = lon;

	if (language == "DE")
	{
		sFindDevicesButtonText = "Geraet suchen";
		sSuccessMessage = "Geocache wurde uebertragen";
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
			var waypoint = new Garmin.WayPoint(nLat, nLon, null, id);
			var factory = new Garmin.GpsDataFactory();
			var gpx = factory.produceGpxString(null, [waypoint]);
			return gpx;
		},
		afterFinishWriteToDevice: function()
		{
			alert(sSuccessMessage);
		}
	});
}
