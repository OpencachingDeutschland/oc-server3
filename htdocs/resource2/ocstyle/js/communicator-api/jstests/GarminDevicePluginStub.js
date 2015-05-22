if (Garmin == undefined) var Garmin = {};
/**
 * @fileoverview GarminDevicePlugin wraps the Garmin ActiveX/Netscape plugin that should be installed on your machine inorder to talk to a Garmin Gps Device.
 * The plugin is available for download from http://www.garmin.com/plugin/installation/page/update/here
 * More information is available about this plugin from http://
 * 
 * @author Carlo Latasa carlo.latasa@garmin.com
 * @version 1.10
 */

/**
 * THIS IS A STUB FOR UNIT TESTING
 * 
 * This api provides a set of functions to accomplish the following tasks with a Gps Device:
 * <br>
 * <br>  1) Unlocking devices allowing them to be found and accessed.
 * <br>  2) Finding avaliable devices plugged into this machine.
 * <br>  3) Reading from the device.
 * <br>  4) Writing gpx files to the device.
 * <br>  5) Downloading data to the device.
 * <br>	 6) Getting messages, getting transfer status/progress and version information from the device.
 * <br><br>
 * Note that the GarminPluginAPIV1.xsd is referenced throughout this API. Please find more information about the GarminPluginAPIV1.xsd from http://
 *  
 * @param {Element} gpsPluginElement element that references the  Garmin GPS Control Web Plugin that should be installed.
 * 
 * @constructor 
 * @return a new GarminDevicePlugin
 **/
Garmin.DevicePlugin = function(pluginElement){
	this.GpsXml = null;
	this.FileName = null;
	this.TcdXml = null;
	this.TcdXmlz = null;
	this.DataTypeName = null;
	this.DirectoryListingXml = "";
}
Garmin.DevicePlugin.prototype = {

	
    getDevicesXml: function() {
    	return "";
    	
    },
     
	/**
     * Unlocks the GpsControl object to be used at the given web adress.  Also takes a unlock_code
     * that was created at the Site Key page. See documentation site for more info
     * on getting a unlock_code.
     * 
     * @param {String} web_path 
     * @param {String} unlock_code
     * 
     */
	unlock: function(pathKeyPairsArray) {
		var correctKey = "0000";
		var correctPath = "http://www.fakeURLStoredInGarminDevicePluginStub.com/";

		return (pathKeyPairsArray[0] == correctPath && pathKeyPairsArray[1] == correctKey);
	},
	
	/** 
	 * Returns true if the plug-in is unlocked.
	 */
	isUnlocked: function() {
		return this.unlocked;
	},

	/** 
	 * Lazy-logic accessor to fitness write support var.
	 * This should NOT be called until the plug-in has been unlocked.
	 */
	getSupportsFitnessWrite: function() {
		return true;
	},
	
	/**
	 * Lazy-logic accessor to fitness write support var.
	 * This should NOT be called until the plug-in has been unlocked.
	 */
	getSupportsFitnessRead: function() {
		return true;
	},
	
	/** 
	 * Lazy-logic accessor to fitness read compressed support var.
	 * This should NOT be called until the plug-in has been unlocked.
	 */
	getSupportsFitnessReadCompressed: function() {
		return true;
	},
		
	/**
	 * Initiates a find Gps devices action on the plugin. 
	 * Poll with finishFindDevices to determine when the plugin has completed this action.
	 * Use getDeviceXmlString to inspect xml contents for and array of Device nodes.
	 *   
	 */
	startFindDevices: function() {
        //this.plugin.StartFindDevices();
        //alert("Plugin.startFindDevices (stub)")
	},
	
	/**
	 * Poll - with this function to determine completion of 
	 * startFindGpsDevices. 
	 * 
	 * @return {Boolean} Returns true if completed finding devices otherwise false.
	 * Used after the call to startFindGpsDevices().
	 */
	finishFindDevices: function() {
        //alert("Plugin.finishFindDevices (stub)")
    	return true;
	},
	
	/**
	 * Returns information about the number of devices connected to this machine as 
	 * well as the names of those devices.
	 * See the GarminPluginAPIV1.xsd/ Devices/ Devices_t
	 * The xml returned should contain a 'Device' element with 'DisplayName' and 'Number'
	 * if there is a device actually conneted. 
	 */
	getDevicesXml: function(){
		return "<?xml version='1.0' encoding='UTF-8' standalone='no' ?><Devices xmlns='http://www.garmin.com/xmlschemas/PluginAPI/v1'><Device DisplayName='mbina' Number='0'/><Device DisplayName='dchow' Number='1'/></Devices>";
	},

	/**
	 * Returns information about the specified Device indicated by the device Number. 
	 * See getDevicesXmlString to get the actual deviceNumber assigned.
	 * See the GarminPluginAPIV1.xsd/ Devices/ Devices_t
	 * 
	 * @param {Number} - deviceNumber assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 */
	getDeviceDescriptionXml: function(deviceNumber){
		return "<?xml version='1.0' encoding='UTF-8' standalone='no' ?><Device xmlns='http://www.garmin.com/xmlschemas/GarminDevice/v2'><Model><PartNumber>006-B0484-00</PartNumber><SoftwareVersion>240</SoftwareVersion><Description>Forerunner305 Software Version 2.40</Description></Model><Id>3308802814</Id><DisplayName>mbina</DisplayName><MassStorageMode><DataType><Name>GPSData</Name><File><Specification><Identifier>http://www.topografix.com/GPX/1/1</Identifier><Documentation>http://www.topografix.com/GPX/1/1/gpx.xsd</Documentation></Specification><Location><FileExtension>GPX</FileExtension></Location><TransferDirection>InputOutput</TransferDirection></File></DataType></MassStorageMode></Device>";
	},
	
	/** 
	 * Start the asynchronous ReadFitnessDirectory operation.
	 */
	startReadFitnessDirectory: function(deviceNumber, dataTypeName) {
		this.DataTypeName = dataTypeName;
		
		if( this.DataTypeName == "FitnessHistory") {
			this.TcdXml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><TrainingCenterDatabase xmlns="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd"><Activities><Activity Sport="Running"><Id>2007-08-07T02:42:41Z</Id><Lap StartTime="2007-08-07T02:42:41Z"><TotalTimeSeconds>2325.0200000</TotalTimeSeconds><DistanceMeters>8348.5039063</DistanceMeters><Calories>285</Calories><Intensity>Active</Intensity><TriggerMethod>Manual</TriggerMethod></Lap><Creator xsi:type="Device_t"><Name>Forerunner305</Name><UnitId>3332601296</UnitId><ProductID>484</ProductID><Version><VersionMajor>2</VersionMajor><VersionMinor>60</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>0</BuildMinor></Version></Creator></Activity><Activity Sport="Running"><Id>2007-07-26T03:16:42Z</Id><Lap StartTime="2007-07-26T03:16:42Z"><TotalTimeSeconds>1945.2700000</TotalTimeSeconds><DistanceMeters>2052.4462891</DistanceMeters><Calories>98</Calories><Intensity>Active</Intensity><TriggerMethod>Manual</TriggerMethod></Lap><Creator xsi:type="Device_t"><Name>Forerunner305</Name><UnitId>3332601296</UnitId><ProductID>484</ProductID><Version><VersionMajor>2</VersionMajor><VersionMinor>60</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>0</BuildMinor></Version></Creator></Activity></Activities><Author xsi:type="Application_t"><Name>Garmin Communicator Plug-In</Name><Build><Version><VersionMajor>2</VersionMajor><VersionMinor>2</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>0</BuildMinor></Version><Type>Alpha</Type><Time>Aug 6 2007,20:16:30</Time><Builder>halesadmin</Builder></Build><LangID>EN</LangID><PartNumber>006-A0161-00</PartNumber></Author></TrainingCenterDatabase>';
			this.TcdXmlz = "begin-base64 644 data.xml.gz";
		}
		else if( this.DataTypeName == "FitnessCourses") {
			this.TcdXml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><TrainingCenterDatabase xmlns="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd"><Folders/><Courses><Course><Name>Biking Course</Name></Course><Course><Name>Biking Course 2</Name></Course><Course><Name>Hike</Name></Course></Courses><Author xsi:type="Application_t"><Name>Garmin Communicator Plug-In</Name><Build><Version><VersionMajor>2</VersionMajor><VersionMinor>2</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>0</BuildMinor></Version><Type>Alpha</Type><Time>Aug 6 2007,20:16:30</Time><Builder>halesadmin</Builder></Build><LangID>EN</LangID><PartNumber>006-A0161-00</PartNumber></Author></TrainingCenterDatabase>';
			this.TcdXmlz = "begin-base64 644 data.xml.gz";
		}
	},
	
	/** 
	 * Poll for completion of the asynchronous ReadFitnessDirectory operation.
	 */
	finishReadFitnessDirectory: function() {
		return 3;
	},
	
	/** 
	 * Cancel the asynchronous ReadFitnessDirectory operation
	 */
	cancelReadFitnessDirectory: function() {
	},
	
	/** 
	 * Start the asynchronous ReadFitnessDetail operation.
	 */
	startReadFitnessDetail: function(deviceNumber, dataTypeName, dataId) {
		
		this.DataTypeName = dataTypeName;
		
		if( this.DataTypeName == "FitnessHistory") {
			this.TcdXml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><TrainingCenterDatabase xmlns="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd"><Activities><Activity Sport="Running"><Id>2007-07-26T03:16:42Z</Id><Lap StartTime="2007-07-26T03:16:42Z"><TotalTimeSeconds>1945.2700000</TotalTimeSeconds><DistanceMeters>2052.4462891</DistanceMeters><MaximumSpeed>2.9856725</MaximumSpeed><Calories>98</Calories><Intensity>Active</Intensity><TriggerMethod>Manual</TriggerMethod><Track><Trackpoint><Time>2007-07-26T03:16:42Z</Time><Position><LatitudeDegrees>37.9005473</LatitudeDegrees><LongitudeDegrees>-122.4979397</LongitudeDegrees></Position><AltitudeMeters>-24.8569336</AltitudeMeters><DistanceMeters>0.0000000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-07-26T03:16:45Z</Time><Position><LatitudeDegrees>37.9005554</LatitudeDegrees><LongitudeDegrees>-122.4979423</LongitudeDegrees></Position><AltitudeMeters>-29.1828613</AltitudeMeters><DistanceMeters>1.2155340</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-07-26T03:49:07Z</Time><Position><LatitudeDegrees>37.8995472</LatitudeDegrees><LongitudeDegrees>-122.4985481</LongitudeDegrees></Position><AltitudeMeters>0.6180420</AltitudeMeters><DistanceMeters>2052.4462891</DistanceMeters><SensorState>Absent</SensorState></Trackpoint></Track></Lap><Creator xsi:type="Device_t"><Name>Forerunner305</Name><UnitId>3332601296</UnitId><ProductID>484</ProductID><Version><VersionMajor>2</VersionMajor><VersionMinor>60</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>0</BuildMinor></Version></Creator></Activity></Activities><Author xsi:type="Application_t"><Name>Garmin Communicator Plug-In</Name><Build><Version><VersionMajor>2</VersionMajor><VersionMinor>2</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>2</BuildMinor></Version><Type>Beta</Type><Time>Aug 29 2007,09:42:31</Time><Builder>wissenba</Builder></Build><LangID>EN</LangID><PartNumber>006-A0161-00</PartNumber></Author></TrainingCenterDatabase>';
			this.TcdXmlz = 'begin-base64 644 data.xml.gz';
		}
		else if ( this.DataTypeName == "FitnessCourses") {
			this.TcdXml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><TrainingCenterDatabase xmlns="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd"><Folders/><Courses><Course><Name>LARC</Name><Lap><TotalTimeSeconds>251808.0000000</TotalTimeSeconds><DistanceMeters>699468.3750000</DistanceMeters><BeginPosition><LatitudeDegrees>45.9969616</LatitudeDegrees><LongitudeDegrees>-122.8051750</LongitudeDegrees></BeginPosition><EndPosition><LatitudeDegrees>46.0156292</LatitudeDegrees><LongitudeDegrees>-122.7865076</LongitudeDegrees></EndPosition><Intensity>Active</Intensity></Lap><Track><Trackpoint><Time>2007-08-27T21:59:16Z</Time><Position><LatitudeDegrees>45.9969616</LatitudeDegrees><LongitudeDegrees>-122.8051750</LongitudeDegrees></Position><AltitudeMeters>199.1301270</AltitudeMeters><DistanceMeters>0.0000000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-08-28T18:53:58Z</Time><Position><LatitudeDegrees>44.1349134</LatitudeDegrees><LongitudeDegrees>-123.1787100</LongitudeDegrees></Position><AltitudeMeters>111.1695557</AltitudeMeters><DistanceMeters>209017.0000000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-08-28T22:16:24Z</Time><Position><LatitudeDegrees>43.9453723</LatitudeDegrees><LongitudeDegrees>-122.8491210</LongitudeDegrees></Position><AltitudeMeters>197.2075195</AltitudeMeters><DistanceMeters>242801.8437500</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-08-29T07:43:16Z</Time><Position><LatitudeDegrees>44.2609372</LatitudeDegrees><LongitudeDegrees>-121.7504880</LongitudeDegrees></Position><AltitudeMeters>2002.0827637</AltitudeMeters><DistanceMeters>337496.0625000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-08-29T16:27:52Z</Time><Position><LatitudeDegrees>44.8247082</LatitudeDegrees><LongitudeDegrees>-120.9814450</LongitudeDegrees></Position><AltitudeMeters>801.8769531</AltitudeMeters><DistanceMeters>425017.0625000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-08-30T06:52:47Z</Time><Position><LatitudeDegrees>44.3866915</LatitudeDegrees><LongitudeDegrees>-119.2675780</LongitudeDegrees></Position><AltitudeMeters>1346.9443359</AltitudeMeters><DistanceMeters>569514.5000000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint><Trackpoint><Time>2007-08-30T19:56:04Z</Time><Position><LatitudeDegrees>45.0735206</LatitudeDegrees><LongitudeDegrees>-117.9272460</LongitudeDegrees></Position><AltitudeMeters>1040.7644043</AltitudeMeters><DistanceMeters>700274.0625000</DistanceMeters><SensorState>Absent</SensorState></Trackpoint></Track></Course></Courses><Author xsi:type="Application_t"><Name>Garmin Communicator Plug-In</Name><Build><Version><VersionMajor>2</VersionMajor><VersionMinor>2</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>2</BuildMinor></Version><Type>Beta</Type><Time>Aug 29 2007,09:42:31</Time><Builder>wissenba</Builder></Build><LangID>EN</LangID><PartNumber>006-A0161-00</PartNumber></Author></TrainingCenterDatabase>';
			this.TcdXmlz = "begin-base64 644 data.xml.gz";
		} 
	},
	
	/** 
	 * Poll for completion of the asynchronous ReadFitnessDetail operation.
	 */
	finishReadFitnessDetail: function() {
		return 3;
	},
	
	/** 
	 * Cancel the asynchronous ReadFitnessDirectory operation
     */	
	cancelReadFitnessDetail: function() {
	},

	/**Returns the number, assigned by plugin, of the parent device.<br/> 
     * If the device has no parent, -1 is returned.<br/>
	 * Minimum plugin version 2.9.2.5
	 * 
	 * @param deviceNumber {Number} Assigned by the plugin
     * @returns {Number} Parent device's assigned number. -1 if the device has no parent.
	 * @see #getDevicesXml for device number assignment
	 */
	getParentDevice: function(deviceNumber) {
        return -1;
	},
	
	/**
	 * Initiates the read from the gps device connected. Use finishReadFromGps and getGpsProgressXml to 
	 * determine when the plugin is done with this operation. Also, use getGpsXml to extract the
	 * actual data from the device.
	 * 
	 * @param {Number} - deviceNumber assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 */
	startReadFromGps: function(deviceNumber) {
		//alert("stub.startReadFromGps")
		var s = '<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1.0" creator="Garmin Communicator Plugin" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd" xmlns="http://www.topografix.com/GPX/1/0">';
		s    += '<time>2007-05-09T20:53:37.7627544-07:00</time>';
		s    += '<wpt lat="37.828567" lon="-122.49875"><ele>100</ele><time>2002-11-12T00:00:00.0000000-08:00</time><name>MM5MM</name><desc>SpiderTracks</desc></wpt>';
		s    += '</gpx>';
		this._setWriteGpsXml(s);
	},

	/**
	 * This is used to indicate the status of the read process. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request. Used after startReadFromGps().
	 * 
	 *  @return {Number} completion state -  The completion state can be 
	 *  one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
 	 * 
	 */
	finishReadFromGps: function() {
	 	 return 3;
	},
	
	/**
     * Cancels the current read from the device.
     * 
     */	
	cancelReadFromGps: function() {
	},

    /**
     * @type Boolean
     * @return True if the last ReadFitnessData or WriteFitnessData operation succeeded
     */
    fitnessTransferSucceeded: function() {
		return true;
    },

    /**
     * This is the fitness data Xml information from the device. Typically called after a ReadFitnessData operation.
	 *
     * Schemas for the TrainingCenterDatabase format are available at
     * http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd
     */
	getTcdXml: function(){
		return this.TcdXml;
	},

	/**
     * This is the fitness data Xml information from the device. Typically called after a ReadFitnessData operation.
	 *
     * Schemas for the TrainingCenterDatabase format are available at
     * http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd
     */
	getTcdXmlz: function(){
		return this.TcdXmlz;
	},
	
	 /** Returns last read directory xml data.<br/>
	  * <br/>
	  * 
	  * @return The directory xml data
	  * @see #finishReadFitDirectory
	  */
	getDirectoryXml: function() {
		return this.DirectoryListingXml;
	},
	
	/**
	 * Initates writing the gpsXml to the device specified by deviceNumber with a filename set by filename.
	 * The gpsXml is typically in GPX fomat and the filename is only the name without the extension. The 
	 * plugin will append the .gpx extension automatically.
	 * 
	 * Use finishWriteToGps to poll when the write operation/plugin is complete.
	 * 
	 * Uses the helper functions to set the xml info and the filename.  
	 * 
	 * @param gpsXml - the gps/gpx information that should be transferred to the device.
	 * @param filename - the desired filename for the gpsXml that shall end up on the device.
	 * @param deviceNumber - the device number assigned by the plugin.  
	 */
	startWriteToGps: function(gpsXml, filename, deviceNumber) {
		this._setWriteGpsXml(gpsXml);
		this._setWriteFilename(filename);
	},

    startUnitSoftwareUpdate: function(updateResponsesXml, deviceNumber) {
    },
    
    finishUnitSoftwareUpdate: function() {
        return 3;
    },
    
    cancelUnitSoftwareUpdate: function() {
    },
    
	/**
	 * Sets the gps xml content that will end up on the device once the transfer is complete.
	 * Use in conjunction with startWriteGpsXml to initiate the actual write.
	 *
	 * @private 
	 * @param gpsXml - xml data that is to be written to the device. Must be in GPX format.
	 */
	_setWriteGpsXml: function(gpsXml) {
    	this.GpsXml = gpsXml;
	},

	/**
	 * This the filename that wil contain the gps xml once the transfer is complete. Use with 
	 * setWriteGpsXml to set what the file contents will be. Also, use startWriteToGps to 
	 * actually make the write happen.
	 * 
	 * @private
	 * @param filename - the actual filename that will end up on the device. Should only be the
	 * name and not the extension. The plugin will append the extension portion to the file name
	 * - typically .GPX.
	 */
	_setWriteFilename: function(filename) {
    	this.FileName = filename;
	},

	/**
 	 * This is used to indicate the status of the write process. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request. 
 	 * 
 	 *  @return {Number} completion state -  The completion state can be 
	 *  one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
 	 */
	finishWriteToGps: function() {
	   	return 3;
	},
    
	/**
     * Cancels the current write operation to the gps device.
     * 
     */	
	cancelWriteToGps: function() {
	},

    
    /**
	 * Determine the amount of space available on a Mass Storage Mode Device Volume.
	 * 
	 * @param {Number} deviceNumber - the device number assigned by the plugin. See {@link getDevicesXmlString} for 
	 * assignment of that number.
	 * @param {String} relativeFilePath - if a file is being replaced, set to relative path on device, otherwise set to empty string.
	 * @return number of bytes available on the device 
	 */
	bytesAvailable: function(deviceNumber, relativeFilePath) {
	    var returnVal;
	    if( relativeFilePath == '') {
            returnVal = 22222;
	    } else {
	        returnVal = 40000;
	    }
	    return returnVal;
	},
    
	/**
	 * Determine if device is file-based. <br/>
	 * File-based devices include Mass Storage Mode Devices such as Nuvi, Oregon, Edge 705,
	 * as well as ANT Proxy Devices.<br/>
	 * 
	 * Minimum plugin version 2.8.1.0 <br/>
	 * @param {Number} deviceNumber the device number assigned by the plugin. See {@link getDevicesXmlString} for 
	 * assignment of that number.
	 * @returns {Boolean} true for file based devices, false otherwise
	 */
	isDeviceFileBased: function(deviceNumber) {
        return false;
	},

	/**
	 * Start the asynchronous ReadFitnessData operation.
	 * 
	 * @param {Number} - deviceNumber assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 * @param {String} - a Fitness DataType from the GarminDevice.xml retrieved with DeviceDescription
	 */
	startReadFitnessData: function(deviceNumber, dataTypeName) {
	},

	/**
	 * Poll for completion of the asynchronous ReadFitnessData operation.
     *
     * If the CompletionState is eMessageWaiting, call MessageBoxXml
     * to get a description of the message box to be displayed to
     * the user, and then call RespondToMessageBox with the value of the
     * selected button to resume operation.
	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 */
	finishReadFitnessData: function() {
	 	 return  3;
	},
	
	/**
     * Cancel the asynchronous ReadFitnessData operation
     * 
     */	
	cancelReadFitnessData: function() {
	},
	
	/**
	 * Start the asynchronous WriteFitnessData operation.
	 * 
	 * @param {Number} - deviceNumber assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 * @param {String} - a Fitness DataType from the GarminDevice.xml retrieved with DeviceDescription
	 */
	startWriteFitnessData: function(tcdXml, deviceNumber, filename, dataTypeName) {
	},
	
	/**
	 * This is used to indicate the status of the write process for fitness data. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request. 
 	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 */
	finishWriteFitnessData: function() {
	 	return  3;
	},
	
	/**
     * Cancel the asynchronous ReadFitnessData operation
     * 
     */	
	cancelWriteFitnessData: function() {
	},
	
    /**
     * Responds to a message box on the device.  
     * @param response should be an int which corresponds to a button value from this.plugin.MessageBoxXml
     */
    respondToWriteMessage: function(response) {
        //this.plugin.RespondToMessageBox(response ? 1 : 2);
    },

	/**
	 * Initates downloading the gpsDataString to the device specified by deviceNumber.
	 * The gpsDataString is typically in GPI fomat and the filename is only the name without the extension. The 
	 * plugin will append the .gpx extension automatically.
	 * 
	 * Use finishWriteToGps to poll when the write operation/plugin is complete.
	 * 
	 * Uses the helper functions to set the xml info and the filename.  
	 * 
	 * @param gpsDataString - the gpi information that should be transferred to the device.
	 * @param deviceNumber - the device number assigned by the plugin. 
	 * 
	 * 	This is necessary for the download of this .GPI files according to the  GarminAxControl.idl.
	 */
	startDownloadData: function(gpsDataString, filename, deviceNumber) {
	},

	/**
	 * This is used to indicate the status of the download process. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request.
	 * 
	 *  @return {Number} completion state -  The completion state can be 
	 *  one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 */
	finishDownloadData: function() {
		 return 3;
	},

	/**
     * Cancels the current write operation to the gps device.
	 */
	cancelDownloadData: function() {
		 //this.plugin.CancelWriteToGps();
	},

    /**
     * @type Boolean
     * @return True if the last StartDownloadData operation was successful
     */
    downloadDataSucceeded: function() {
    	return true;
		//return this.plugin.DownloadDataSucceeded;
    },


    /**
     * @param type determines if we are ReadFrom or WriteTo the device
     */
    gpsTransferSucceeded: function() {
		//return this.plugin.GpsTransferSucceeded;
		return true;
    },

	fitnessTransferSucceeded: function() {
		return true;
    },
    
    /**
     * This is the GpsXml information from the device. Typically called after a read operation.
     */
	getGpsXml: function(){
		return this.GpsXml;
	},

	/**
     * This is the TcdXml information from the device. Typically called after a read operation.
     */
	getTcdXml: function(){
		return this.TcdXml;
	},
	
    /**
     * @return The xml describing the message??? please clarify.
     * @type String
     */
	getMessageBoxXml: function(){
		this.MessageBoxXml;
	},
    
	/**
     * Get the status/progress of the current state or transfer
     *
     */	
	getProgressXml: function() {
		return "<?xml version='1.0' encoding='UTF-8' standalone='no' ?><ProgressWidget xmlns='http://www.garmin.com/xmlschemas/PluginAPI/v1'><Title>Receiving From mbina</Title><Text>93% complete, 1 second(s) remaining</Text><Text>Receiving tracks...</Text><Text></Text><ProgressBar Type='Percentage' Value='93'/></ProgressWidget>";
	},

	/**
	 * Returns metadata information about the plugin version. 
	 */
	getVersionXml: function() {
		return "<?xml version='1.0' encoding='UTF-8' standalone='no' ?><Requests xmlns='http://www.garmin.com/xmlschemas/PcSoftwareUpdate/v2'><Request><PartNumber>006-A0161-00</PartNumber><Version><VersionMajor>2</VersionMajor><VersionMinor>1</VersionMinor><BuildMajor>0</BuildMajor><BuildMinor>3</BuildMinor><BuildType>Internal</BuildType></Version><LanguageID>1033</LanguageID></Request></Requests>";
	},
	
	/** Gets the version number for the plugin the user has currently installed
     */	
	getPluginVersion: function() {
    	var versionMajor = parseInt(this._getElementValue(this.getVersionXml(), "VersionMajor"));
    	var versionMinor = parseInt(this._getElementValue(this.getVersionXml(), "VersionMinor"));
    	var buildMajor = parseInt(this._getElementValue(this.getVersionXml(), "BuildMajor"));
    	var buildMinor = parseInt(this._getElementValue(this.getVersionXml(), "BuildMinor"));

	    var versionArray = [versionMajor, versionMinor, buildMajor, buildMinor];
	    return versionArray;
	},
	
	/** Gets a string of the version number for the plugin the user has currently installed
     */	
	getPluginVersionString: function() {
		var versionArray = this.getPluginVersion();
	
		var versionString = versionArray[0] + "." + versionArray[1] + "." + versionArray[2] + "." + versionArray[3];
	    return versionString;
	},
	
	getSupportsFitDirectoryRead: function() {
        return false;
	},
	
	/** Sets the required plugin version number for the application.
	 * @param reqVersionArray {Array} The required version to set to.  In the format [versionMajor, versionMinor, buildMajor, buildMinor]
	 * 			i.e. [2,2,0,1]
	 */
	setPluginRequiredVersion: function(reqVersionArray) {
	},
	
	/** Sets the latest plugin version number.  This represents the latest version available for download at Garmin.
	 * We will attempt to keep the default value of this up to date with each API release, but this is not guaranteed,
	 * so set this to be safe or if you don't want to upgrade to the latest API.
	 * 
	 * @param reqVersionArray {Array} The latest version to set to.  In the format [versionMajor, versionMinor, buildMajor, buildMinor]
	 * 			i.e. [2,2,0,1]
	 */
	setPluginLatestVersion: function(reqVersionArray) {
	},
	
	checkPluginVersionSupport: function() {
        return true;	    
	},
	
	/** Determines if the Garmin plugin is the required version or newer.
     * @type Boolean 
	 */
	isPluginOutOfDate: function() {
        return false;
	},

	/** Determines if the Garmin plugin is the latest version 
     * @type Boolean 
	 */
	isUpdateAvailable: function() {
        return false;
	},
	
	/** Pulls value from xml given an element name or null if no tag exists with that name.
	 * @private
	 */
	_getElementValue: function(xml, tagName) {
		var start = xml.indexOf("<"+tagName+">");
		if (start == -1)
			return null;
		start += tagName.length+2;
		var end = xml.indexOf("</"+tagName+">");
		var result = xml.substring(start, end);
		return result;
	}
}