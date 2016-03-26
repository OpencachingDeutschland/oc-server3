function load(lat, lon, id, url, garminKey, language)
{
	var sSuccessMessage = "Geocache saved successfully";

	var _findDevicesButtonText = "Search device";
	var _cancelFindDevicesButtonText = "Cancel Find Devices";
	var _pluginUnlocked = "Plug-in initialized.  Find some devices to get started.";
	var _pluginNotUnlocked = "The plug-in was not unlocked successfully";
	var _usingDevice = "Using #{deviceName}";
	var _trackListing = "#{date}  (Duration: #{duration} )";
	var _writingToDevice = "Writing data to the device";
	var _writtenToDevice = "Data written to the device";
	var _writingCancelled = "Writing cancelled";
	var _lookingForDevices = "Looking for connected devices...";
	var _foundDevice = "Found #{deviceName} ";
	var _foundDevices = "Found #{deviceCount}  devices";
	var _findCancelled = "Find cancelled";
	var _dataReadProcessing = "Data read from device. Processing...";
	var _dataDownloadProcessing = "Processing data to write... ";
	var _uploadsFinished = "Transfer Complete!";
	var _installNow = "Install now?";
	var _downloadAndInstall = "Download and install now";
	var _unsupportedDevice =	"Your device is not supported by this application.";
	var _unsupportedReadDataType = "Your device does not support reading of the type #{dataType}.";
	var _unsupportedWriteDataType = "Your device does not support writing of the type #{dataType}.";
	var _uploadingActivities = "Uploading activities...";
	var _sendingDataToServer = "Sending data from #{deviceName} to server...";

	var nLat = lat;
	var nLon = lon;

	if (language == "DE")
	{
		sSuccessMessage = "Geocache wurde übertragen.";

		_findDevicesButtonText = "Gerät suchen";
		_cancelFindDevicesButtonText = "Gerätesuche abbrechen";
		_pluginUnlocked = "Plugin initialiseirt; suche nach Geräten";
		_pluginNotUnlocked = "Das Plugin wurde nicht erfolgreich aktiviert.";
		_usingDevice = "verwende #{deviceName}";
		_writingToDevice = "sende Daten an das Gerät ...";
		_writtenToDevice = "Daten wurden an das Gerät gesendet.";
		_writingCancelled = "Übertragung abgebrochen";
		_lookingForDevices = "Suche nach angeschlossenen Geräten ...";
		_foundDevice = "#{deviceName} gefunden ";
		_foundDevices = "#{deviceCount} Geräte gefunden";
		_findCancelled = "Suche abgebrochen";
		_dataReadProcessing = "Daten wurden empfangen und werden verarbeitet ...";
		_dataDownloadProcessing = "Verarbeite zu sendende Daten ... ";
		_uploadsFinished = "Übertragung vollständig.";
		_installNow = "Jetzt installieren?";
		_downloadAndInstall = "Jetzt herunterladen und installieren";
		_unsupportedDevice =	"Dein Gerät wird von dieser Anwendung nicht unterstützt.";
		_unsupportedReadDataType = "Dein Gerät kann keine Daten des Typs #{dataType} ausgeben.";
		_unsupportedWriteDataType = "Dein Gerät kann keine Daten des Typs #{dataType} einlesen.";
		_uploadingActivities = "Hochladen ...";
		_sendingDataToServer = "Sende Daten von #{deviceName} an den Server ...";
	}

	var display = new Garmin.DeviceDisplay("garminDisplay",
	{
		pathKeyPairsArray: [url, garminKey],
		showStatusElement: true,                        //basic feedback provided
		unlockOnPageLoad: false,                        //delays unlocking to avoid authorization prompt until action
		hideIfBrowserNotSupported: false,
		showStatusElement: true,                        //provide minimal feedback
		autoFindDevices: true,                          //it will search for devices upon action
		showCancelFindDevicesButton: true,              //no need to cancel small data transfers
		autoSelectFirstDevice: true,                    //pick the first device if several are found
		autoReadData: false,                            //don't automatically read the tracks/etc
		autoWriteData: true,                            //automatically write the data once devices found
		showReadDataElement: false,

		findDevicesButtonText: _findDevicesButtonText,
		cancelFindDevicesButtonText: _cancelFindDevicesButtonText,
		pluginUnlocked: _pluginUnlocked,
		pluginNotUnlocked: _pluginNotUnlocked,
		usingDevice: _usingDevice,
		writingToDevice: _writingToDevice,
		writtenToDevice: _writtenToDevice,
		writingCancelled: _writingCancelled,
		lookingForDevices: _lookingForDevices,
		foundDevice: _foundDevice,
		foundDevices: _foundDevices,
		findCancelled: _findCancelled,
		dataReadProcessing: _dataReadProcessing,
		dataDownloadProcessing: _dataDownloadProcessing,
		uploadsFinished: _uploadsFinished,
		installNow: _installNow,
		downloadAndInstall: _downloadAndInstall,
		unsupportedDevice: _unsupportedDevice,
		unsupportedReadDataType: _unsupportedReadDataType,
		unsupportedWriteDataType: _unsupportedWriteDataType,
		uploadingActivities: _uploadingActivities,
		sendingDataToServer: _sendingDataToServer,

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
			window.close();
		}
	});
}
