Garmin Communicator Plug-in JavaScript API
Version 1.10

==OVERVIEW==
The Garmin Communicator Plug-in API (GCP API) is a client-side JavaScript
framework that abstracts the Garmin Communicator browser plug-in's more complex
interface. With it you can communicate with Garmin devices using JavaScript. 
The API consists of three layers, each more user-friendly than the one it 
builds upon:

Garmin.DeviceDisplay - Customizable drop-in UI widget
Garmin.DeviceControl - Device communication
Garmin.DevicePlugin  - Thin wrapper around plugin

==DOCS==
GCP API On the Web:
http://developer.garmin.com/web-device/garmin-communicator-plugin/

Full JSDoc code reference:
./documentation.index.html

==EXAMPLES==
Various examples to get more familiar with the different layers of the plugin:
./examples

==MISC==
The GCP API requires and includes the Prototype JavaScript Framework
http://prototypejs.org/

Unit tests are included in the ./jstests folder

==LICENSE==
Copyright 2007-2011 Garmin Ltd. or its subsidiaries.

Licensed under the Apache License, Version 2.0 (the 'License')
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an 'AS IS' BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.