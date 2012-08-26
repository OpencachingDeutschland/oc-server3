{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
	<div class="content2-pagetitle">
		Changelog
	</div>
	<div class="content-txtbox-noshade" style="padding-right: 25px;">

	<p>Opencaching.de Version 1.0 went online in August 2005 und was steadily enhanced in the following years, switching to Version 2. In the beginning of 2011, development was halted, until the new Opencaching Deutschland association restarted it in July 2012 at Version&nbsp;3.0. The new developement team has started with simple tasks for learning the ropes.</p>

	<p>This page lists all changes since version 3.0.</p>
	<br />

	 <p><strong>Release 3.0.2</strong> &ndash; (scheduled for September)</p>
   <p>New:</p>
	 <ul class="changelog">
	   <li class="changelogitem"><a href="./articles.php?page=cacheinfo#difficulty">Difficulty ratings</a> explained, including tooltips and links within the cache listings</li>
	   <li class="changelogitem"><a href="./articles.php?page=verein">Vereinsseite</a> (currently German only)
   </ul>
	 <p>Changed / improved:</p>
	 <ul class="changelog">
     <li class="changelogitem">improved <a href="./index.php">Homepag</a> performance</li>
     <li class="changelogitem">rewritten <a href="./articles.php?page=cacheinfo">cache description</a> info</li>
	   <li class="changelogitem">recommendations stars are display with Found and Attended logs only.</li>
	   <li class="changelogitem">reversed log type order for event caches</li>
	   <li class="changelogitem">updated <a href="./doc/xml/">XML Interface Documentation</a> (German) and <a href="https://github.com/OpencachingDeutschland/oc-server3/blob/master/doc/license.txt">Source code license</a></li>
	   <li class="changelogitem">updated <a href="./articles.php?page=team">Team members list</a></li>
	   <li class="changelogitem">announced new <a href="./articles.php?page=donations">bank account</a> for donations</li>
	   <li class="changelogitem">better display of cache reports for the support team</li>
	   <li class="changelogitem">removed 65565-cache-listings-limit (OCFFFF, including archived caches)</li>
	   <li class="changelogitem">Completed Spanish and Italian translations</li>
	   <li class="changelogitem">hidden inaktive new caches on <a href="./newcachesrest.php">All-but-Germany</a> page</li>
	   <li class="changelogitem">hidden log button for locked caches, instead of linking it to an emty page</li>
   </ul>
	 <p>Fixed:</p>
	 <ul class="changelog">
	   <li class="changelogitem">added missing event attendees count in log summary line</li>
	   <li class="changelogitem">fixed OC waypoint creation error</li>
	   <li class="changelogitem">Recommendations are no longer lost when logging a cache again, e.g. a note after a found log.</li>
	   <li class="changelogitem">Recommendations are no longer lost when deleting one of multiple logs of the same user, or when editing one of them.</li>
	   <li class="changelogitem">Multiple logs by the same used only count once at the homepage top ratings list.</li>
	   <li class="changelogitem"><a href="doc/xml/">XML-Interface</a> will not truncate default charset data at unknown characters.</li>
	   <li class="changelogitem">fixed error message for invalid log date</li>
	   <li class="changelogitem">fixed case insensitivity of log passwords</li>
	   <li class="changelogitem">decrypting hints when JavaScript is disabled</li>
	   <li class="changelogitem">removed non-workink log entry deletion link for cache owners</li>
	   <li class="changelogitem">fixed log edit permissions for locked caches</li>
   </ul>
	<br />

	 <p><strong>Release 3.0.1</strong> &ndash; August 8, 2012</p>
   <p>New:</p>
	 <ul class="changelog">
	   <li class="changelogitem">URL shortener for direct cache listing access, e.g. <a href="http://www.opencaching.de/OCD93B">http://www.opencaching.de/OCD93B</a></li>
	   <li class="changelogitem">englisch translation of <a href="./articles.php?page=geocaching">About Geocaching</a>, <a href="./articles.php?page=cacheinfo">Cache descriptions</a>, <a href="./articles.php?page=impressum">Legal information</a>, <a href="./articles.php?page=dsb">Privacy statement</a>, <a href="./articles.php?page=donations">Donations</a>, <a href="./articles.php?page=contact">Contact</a> and <a href="./articles.php?page=team">Team members' list</a> pages; team list, legal information and donations pages have been updated</li>
	   <li class="changelogitem">display of &bdquo;You have attended this event&ldquo; in map popups</li>
	   <li class="changelogitem">changelog</li>
	 </ul>
	 <p>Changed / improved:</p>
	 <ul class="changelog">
     <li class="changelogitem">separation of opencaching.de/geocaching.de</li>
	   <li class="changelogitem">new <a href="./articles.php?page=team">list of team members</a></li>
	   <li class="changelogitem">display of new caches at <a href="./index.php">start page</a> by publishing instead of hiding date, and at <a href="./newcaches.php">New caches</a> page by publishing instead of listing creation date</li>
	   <li class="changelogitem">inactive caches are hidden in new caches lists</li>
	   <li class="changelogitem">GC waypoints will be no longer truncated when copy-and-pasted with leading spaces (frequent problem)</li>
	   <li class="changelogitem">layout adjustment of <a href="./search.php">search page</a> and <a href="http://www.blog.opencaching.de">blog/info page</a></li>
	   <li class="changelogitem">removed "0.0 km" distance in search lists when logged off (no home coordinates available)</li>
	 </ul>
	 <p>Fixed:</p>
	 <ul class="changelog">
	   <li class="changelogitem">cache icon scaling in exported KML files</li>
	   <li class="changelogitem">correct default country for new caches, fixed &bdquo;Belgium/Afghanistan problem&ldquo;</li>
	   <li class="changelogitem">first log was missing in print view</li>
	   <li class="changelogitem">display of event attenders' count</li>
	   <li class="changelogitem">display of cache type icon for unpublished caches at <a href="./myhome.php">user profile</a> &rarr; Show all</li>
	   <li class="changelogitem">link "Geokrety history" and recommendation count in cache listings will not be cropped when using big fonts</li>
	   <li class="changelogitem">complete, clickable opencaching.de links in log notification emails</li>
	   <li class="changelogitem">added missing links to <a href="http://www.opencaching.nl/">opencaching.nl</a></li>
	   <li class="changelogitem">correct error message when entering a wrong email-address-change confirmation code</li>
 	 </ul>
	<br />

	</div>