/**
 * Copyright © 2007 Garmin Ltd. or its subsidiaries.
 *
 * Licensed under the Apache License, Version 2.0 (the 'License')
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @fileoverview GarminDeviceControlDemo Demonstrates Garmin.DevicePlugin with a collection of basic tests.
 * 
 * Most tests either retrun true of false otherwise an reference to GarminDevicePlugin or null. This way
 * we can run Unit tests fairly easily. 
 * 
 * @author Michael Bina michael.bina.at.garmin.com
 * @version 1.10
 */
/*
var garminGpsPlugin;	
	// Instantiate the GarminGpsPlugin object.

/*
 * Creates an instance of the plugin and and instance of the GarminGpsPlugin.js object. Then we attempt 
 * to get the Version and by examining that we can determine if the plugin exists.
 */	
function findPlugin() {

	var MyControl;

	if( window.ActiveXObject ){
		MyControl =  document.getElementById('GarminActiveXControl');
	}else {
		MyControl =  document.getElementById('GarminNetscapePlugin');
	}
		    
	garminGpsPlugin = new Garmin.DevicePlugin(MyControl);
	
	// Test the plugin to make sure that we can script it.
	var thePluginVersionString;
	try {
		thePluginVersionString = garminGpsPlugin.getVersionXml();
	} catch(e) {
		alert(e);
	}
	
	if( typeof thePluginVersionString == "undefined" ) {
		return false;
	} else {
		return true;
	}		
}

/*
 * Unlocks the plugin with the passed url and code. If that is successful then we initate the startFindDevices().
 *  If there are no devices found then we return null otherwise we return a reference to this Javascript.
 */	
function unlockPluginAndStartFindingDevices(siteKeyCodeArray) {
	if(garminGpsPlugin.unlock(siteKeyCodeArray) != true) {
		return null;
	}

	try {
		garminGpsPlugin.startFindDevices();
	} catch(e) {
		alert(e);
		return null;
	}

	return this;
}

/*
 * Because the plugin operates in an asynchronous manner, we must poll devices to see if the 
 * initating command has completed. This returns true if the find is complete otherwise false.
 */
function finishFindingDevices(){
	return garminGpsPlugin.finishFindDevices();	
}

/*
 * Displays the xml description of all devices connected to this machine. If a device is actually 
 * connected, there will be a node in the xml with the value : 'Number' for each device enumerated
 * from 0 to the total number of devices. If the xml string contains the 'Number' string then return 
 * the whole xml chunk otherwise return null.
 * 
 */
function getDescriptionOfDevices(){
	var deviceDescription =  garminGpsPlugin.getDevicesXml();
		
	//Possibly replace this with some kind of DOM node search...	
	if(deviceDescription.indexOf("Number", 0) > -1){
		return deviceDescription;
	}else{
		return null;
	}
}

/*
 * Initates a read from the device. Poll using the function 'finishReadAndGetData()' method below to 
 * extract the xml data from the device. The device will continue to complete the task and indicate 
 * it's status with the 'completionState'. See the  'finishReadAndGetData()' function.
 */
function startReadGpsFromFirstDevice(){	
	 garminGpsPlugin.startReadFromGps(0);
}

/* Initiates a history read from the fitness device. Poll using the function 'finishReadAndGetDataForFitness()' method below to 
 * extract the xml data from the device. The device will continue to complete the task and indicate 
 * it's status with the 'completionState'. See the  'finishReadAndGetDataForFitness()' function.
 */
function startReadHistoryFromFirstDevice() {
	garminGpsPlugin.startReadFitnessData(0, "FitnessHistory");
}

/*
 * Initiates a course read from the fitness device. Poll using the function 'finishReadAndGetDataForFitness()' method below to 
 * extract the xml data from the device. The device will continue to complete the task and indicate 
 * it's status with the 'completionState'. See the  'finishReadAndGetDataForFitness()' function.
 */
function startReadCourseFromFirstDevice(){	
	 garminGpsPlugin.startReadFitnessData(0, "FitnessCourses");
}

function startReadHistoryDirectoryFromFirstDevice(){	
	 garminGpsPlugin.startReadFitnessDirectory(0, "FitnessHistory");
}

function startReadCourseDirectoryFromFirstDevice(){
	garminGpsPlugin.startReadFitnessDirectory(0, "FitnessCourses");
}

function finishReadFitnessDirectory() {
	var theCompletionState = garminGpsPlugin.finishReadFitnessDirectory();
		
	if(theCompletionState  != null){
		alert("Completion state is: " + theCompletionState);
			
		//If the completion state is 2 or 'waiting' then get the progress message...
		if(theCompletionState == "2"){
			alert(garminGpsPlugin.getProgressXml());
			return;
		}
	}
		
	return garminGpsPlugin.getTcdXml();
}

function cancelReadFitnessDirectory() {
	garminGpsPlugin.cancelReadFitnessDirectory();
}

/*
 * Poll with this method waiting for theCompletionState to show a value of 3. Here are all of the 
 * possible completion states:
 * 
 * 	0: idle
 * 	1: working
 * 	2: waiting
 * 	3: finished
 * 
 */
function finishReadAndGetData(){

	var theCompletionState = garminGpsPlugin.finishReadFromGps();
		
	if(theCompletionState  != null){
		alert("Completion state is: " + theCompletionState);
			
		//If the completion state is 2 or 'waiting' then get the progress message...
		if(theCompletionState == "2"){
			alert(garminGpsPlugin.getProgressXml());
			return;
		}
	}
		
	return garminGpsPlugin.getGpsXml();
}

/*
 * Poll with this method waiting for theCompletionState to show a value of 3. Here are all of the 
 * possible completion states:
 * 
 * 	0: idle
 * 	1: working
 * 	2: waiting
 * 	3: finished
 * 
 */
function finishReadAndGetDataForFitness(){

	var theCompletionState = garminGpsPlugin.finishReadFitnessData();
		
	if(theCompletionState != null){
		alert("Completion state is: " + theCompletionState);
			
		//If the completion state is 2 or 'waiting' then get the progress message...
		if(theCompletionState == "2"){
			alert(garminGpsPlugin.getProgressXml());
			return;
		}
	}
		
	return garminGpsPlugin.getTcdXml();
}

function cancelReadingTcdXml() {
	garminGpsPlugin.cancelReadFitnessData();
}

/*
 * Initiates a write to the first device found (fitness). Poll using the function 
 * 'finishWriteToDeviceForFitness()' method below to extract the xml data from the device. 
 * The device will continue to complete the task and indicate it's status with the 
 * 'completionState'. See the 'finishWriteToDeviceForFitness()' function.
 */
function startWriteToFirstDeviceForFitness(tcdXml, filename) {
	var xmlDevicesString = garminGpsPlugin.getDevicesXml();
	var xmlDevicesDoc = createDocumentFromString( xmlDevicesString );
	var deviceElements = xmlDevicesDoc.getElementsByTagName("Device");
	var deviceNumber = parseInt( deviceElements[0].getAttribute("Number") );

	garminGpsPlugin.startWriteFitnessData(tcdXml, deviceNumber, filename, "FitnessCourses" );
	//garminGpsPlugin.startWriteFitnessData(tcdXml, deviceNumber, filename, "FitnessCourses" );
}

/*
 * Initiates a write to the first device found. Poll using the function 
 * 'finishReadAndGetData()' method below to extract the xml data from the device. 
 * The device will continue to complete the task and indicate it's status with the 
 * 'completionState'. See the 'finishReadAndGetData()' function.
 */
function startWriteToFirstDevice(gpsXml, filename){	
	 garminGpsPlugin.startWriteToGps(gpsXml, filename,0);
}

/*
 * Determine if the startWriteToFirstDevice is complete.
 * 
 * Poll with this method waiting for theCompletionState to show a value of 3. Here are all of the 
 * possible completion states:
 * 
 * 	0: idle
 * 	1: working
 * 	2: waiting
 * 	3: finished
 * 
 */
function finishWriteToDevice(){

	var theCompletionState  = garminGpsPlugin.finishWriteToGps();
		
	if(theCompletionState  != null){
		alert("Completion state is: " + theCompletionState);
		
		//If the completion state is 2 or 'waiting' then get the progress message...
		if(theCompletionState == "2"){
			alert(garminGpsPlugin.getProgressXml());
			return;
		}
	}
	
	return;
}

/*
 * Determine if the startWriteToFirstDeviceForFitness is complete.
 * 
 * Poll with this method waiting for theCompletionState to show a value of 3. Here are all of the 
 * possible completion states:
 * 
 * 	0: idle
 * 	1: working
 * 	2: waiting
 * 	3: finished
 * 
 */
function finishWriteToDeviceForFitness(){

	var theCompletionState  = garminGpsPlugin.finishWriteFitnessData();
		
	if(theCompletionState  != null){
		alert("Completion state is: " + theCompletionState);
		
		//If the completion state is 2 or 'waiting' then get the progress message...
		if(theCompletionState == "2"){
			alert(garminGpsPlugin.getProgressXml());
			return;
		}
	}
	
	return;
}

/*
 * Start downloading the gpsDataString passed to Device number 0. 
 * Currently the file formats supported are only .gpi or .rgn. The gpsDataString
 * only contains the Url of where to download the data from and the filename
 * that you want the file to have on the device once the transfer is complete.
 */	
function startDownloadToFirstDevice(gpsDataString){
	 garminGpsPlugin.startDownloadData(gpsDataString, 0);
}

/*
 * Determine if the startDownloadToFirstDevice is complete.
 * 
 * Poll with this method waiting for theCompletionState to show a value of 3. Here are all of the 
 * possible completion states:
 * 
 * 	0: idle
 * 	1: working
 * 	2: waiting
 * 	3: finished
 */
function finishDownloadAndGetData(){
		var theCompletionState  = garminGpsPlugin.finishDownloadData();
		
		if(theCompletionState  != null){
			alert("Completion state is: " + theCompletionState);
			
			//If the completion state is 2 or 'waiting' then get the progress message...
			if(theCompletionState == "2"){
				alert(garminGpsPlugin.getProgressXml());
				return;
			}
		}
		
		return garminGpsPlugin.getGpsXml();
}

function getUnitSoftwareUpdateRequests(){
    var responsesXml = garminGpsPlugin.getUnitSoftwareUpdateRequests(0);
    //alert(responsesXml);
    return responsesXml;
}
function getAdditionalSoftwareRequests(){
    var responsesXml = garminGpsPlugin.getAdditionalSoftwareRequests(0);
    //alert(responsesXml);
    return responsesXml;
}

function startUnitSoftwareUpdate(responsesXml) {
    garminGpsPlugin.startUnitSoftwareUpdate(responsesXml, 0);
}
function finishUnitSoftwareUpdate() {
    var theCompletionState  = garminGpsPlugin.finishUnitSoftwareUpdate();
		
	if(theCompletionState  != null){
		alert("Completion state is: " + theCompletionState);
		
		//If the completion state is 2 or 'waiting' then get the progress message...
		if(theCompletionState == "2"){
			alert(garminGpsPlugin.getProgressXml());
			return;
		}
	}
	
	return;
}
function cancelUnitSoftwareUpdate() {
    garminGpsPlugin.cancelUnitSoftwareUpdate();
}


function createDocumentFromString( aXmlString )
{
	var theDocument;
	
	if( window.ActiveXObject )
	    {
	    theDocument = new ActiveXObject("Microsoft.XMLDOM");
	    theDocument.async="false";
	    theDocument.loadXML( aXmlString );
	    }
	else
	    {
	    var theDOMParser = new DOMParser();
	    theDocument = theDOMParser.parseFromString(aXmlString, "text/xml");
	    }
	
	return theDocument;
}

function createStringFromDocument( aXmlDocument )
{
	var theXmlString
	
	if( window.ActiveXObject )
		{
		theXmlString = aXmlDocument.xml
		}
	else
		{
		var theXmlSerializer = new XMLSerializer();
		theXmlString = theXmlSerializer.serializeToString( aXmlDocument );
		}
	return theXmlString
}
