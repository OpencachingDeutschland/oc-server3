if (Garmin == undefined) var Garmin = {};
/** Copyright &copy; 2007-2010 Garmin Ltd. or its subsidiaries.
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

 * @fileoverview PluginDetect. Not API.
 * @version 1.10
 */
/** A library for detecting the browser's plugins.
 * @class PluginDetect
 */
var PluginDetect = {
	
	hasActiveX: (window.ActiveXObject !== undefined),
	
	canDetectPlugins: function() {
	    return ( PluginDetect.hasActiveX || !!navigator.mimeTypes );
	},
	
	detectFlash: function() {
	    var pluginFound = PluginDetect.detectPluginByMIME('application/x-shockwave-flash');
	    // if not found, try to initialize with ActiveX
	    if(!pluginFound && PluginDetect.hasActiveX) {
			pluginFound = PluginDetect.detectActiveXControl('ShockwaveFlash.ShockwaveFlash.1');
	    }
	    return pluginFound;
	},
	
	detectGarminCommunicatorPlugin: function() {
	    var pluginFound = PluginDetect.detectPluginByMIME('application/vnd-garmin.mygarmin');
	    // if not found, try to initialize with ActiveX
	    if(!pluginFound && PluginDetect.hasActiveX) {
			pluginFound = PluginDetect.detectActiveXControl('GARMINAXCONTROL.GarminAxControl_t.1');
	    }
	    return pluginFound;		
	},
	
	detectPluginByMIME: function(mimeTypeName) {
		if (navigator.mimeTypes) {
			var mimeType = navigator.mimeTypes[mimeTypeName];
			return !!(mimeType && mimeType.enabledPlugin);
		}
		else {
			return false;
		}
	},

	detectActiveXControl: function(activeXControlName) {
		try {
			var control = new ActiveXObject(activeXControlName);
			return true;
		}
		catch (e) {
			return false;
		}
	}
}