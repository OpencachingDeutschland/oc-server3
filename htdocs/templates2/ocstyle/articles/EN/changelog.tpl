{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
	<div class="content2-pagetitle">
		Changelog
	</div>
	<div class="content-txtbox-noshade changelog" style="padding-right: 25px;">

	<p>Opencaching.de Version 1.0 went online in August 2005 und was steadily enhanced in the following years, switching to Version 2. In the beginning of 2011, development was halted, until the new Opencaching Deutschland association restarted it in July 2012 at Version&nbsp;3.0.</p>

	<p>This page lists all changes since version 3.0.</p>
	<br />

	<p><strong>Releaas 3.0.5</strong> &ndash; March 16, 2013</p>
	<p>Completely reworked map:</p>
	<ul>
		<li>Own, found, not found and <a href="http://wiki.opencaching.de/index.php/OConly" target="_blank">OConly</a> caches are marked</li>
		<li><a href="map2.php?mode=fullscreen">Full screen map</a> with swing-out filter options</li> 
		<li>additional waypoints of the selected cache are shown</li>
		<li>up to 4000 caches on one map view</li>
		<li>optional you can use Opencaching.pl-style cache icons</li>
		<li>home button jumps to the home coordinates given in your <a href="myprofile.php">user profile</a></li>
		<li>Cache selection / filtering improvements:
			<ul>
				<li>easier selection of single cache types and sizes</li>
				<li>preselection of the most relevant attributes like at the <a href="search.php">search pache</a></li>
				<li>changes are automatically activated in the map without having to click "ok"</li>
				<li>settings will be retained until you close your web browser</li>
			</ul>
		</li>
		<li>nicer popup windows with OConly, difficulty and terrain symbols and larger cache type symbols</li>
		<li>OConly are shown in top, inactive and (not) found caches below</li>
		<li>better readable coordinate display</li>
		<li>improved search function handling</li>
		<li>faster retreival of cache data</li>
		<li>additional maps from <a href="http://www.thunderforest.com/opencyclemap/" target="_blank">OpenCycleMap</a> and <a href="http://www.mapquest.com/" target="_blank">MapQuest</a></li>
		<li>internal change from Google Maps Version 2 to Version 3; <span class="redtext">somewhat sluggish in Microsoft Internet Explorer, we recommend to use another browser</span></li>
	</ul>

	<p>Other new features and changes:</p>
	<ul>
		<li>Log picture galleries on the start page, the new <a href="newlogpics.php">gallery page</a>, in cache listings (via link "log pictures") and in user profiles. The profile galleries can be disabled via <a href="mydetails.php">profile settings.</a></li>
		<li>re-added spoiler option for log pics (see rev. 3.0.2)
		<li>Title and spoiler settings of log pictures can be changed.</li>
		<li>Pictures are displayed within a nice popup window instead of a separate page.</li>
		<li>new waypoint types <em>path</em>, <em>final</em> and <em>point of interest</em></li>
		<li>After emails could not be delivered, users may be prompted to confirm or change their email address.</li>
		<li>Inactive caches are <s>striked out</s> in the <a href="mywatches.php">watch list</a> like in the ignore list.</li>
		<li>improved picture embedding in GPX files, should be identical now with listing display</li>
		<li>many detail improvements of form layouts</li>
		<li>admin list (internal)</li>
		<li>discarded old HTML preview function</li>
	</ul>

	<p>Fixed:</p>
	<ul>
		<li>fixed handling of nano caches with saved searches</li>
		<li>removed JavaScript warning when logging on the Italien page</li>
		<li>show Danish flag with Danish cache descriptions</li>
		<li>fixed nano size selection in search form</li>
	</ul>

	 <p><strong>Release  3.0.4</strong> &ndash; February 17, 2013
   <p>New:</p>
	 <ul>
     <li>new cache size &bdquo;nano&ldquo;</li>
     <li>GPX files downloaded from Opencaching.de or sent to GPS devices contain additional waypoints</li>
     <li>Picturs (including spoilers) from cache listings are embedded in GPX files. You will need internet connection to view them in field.</li>
     <li><a href="articles.php?page=impressum#datalicense">data license CC-BY-NC-ND</a></li>
     <li>statistic pictures with new Opencaching.de logo</a></li>
   </ul>

	 <p>Changed / improved:</p>
	 <ul>
     <li>new page head design with new logo</a></li>
     <li>The <a href="map2.php">map</a> will now show up to 600 instead of 180 cache symbols (MS Internet Explorer: up to 200).</a></li>
     <li>consistend display of <a href="newlogs.php">new logs list</a></li>
     <li>improved user interface feedback when changing profile details, email address or password</li>
     <li>improved display of cache reports for OC admins</a></li>
     <li>optimized search engine access</a></li>
     <li>updated <a href="articles.php?page=team">team</a> and <a href="articles.php?page=donations">donations</a> page</a></li>
     <li>updated <a href="articles.php?page=dsb">privacy statement</a> and improved data privacy</li>
     <li>internal modifications for migration from PHP 5.2 to 5.3</li>
   </ul>

	 <p>Fixed:</p>
	 <ul>
     <li>explicit country setting of cache listings is preferred to automatical detection from coordinates</li>
     <li>icons for event logs in <a href="newlogs.php">new logs list</a></li>
   </ul>
	<br />

	 <p><strong>Release  3.0.3</strong> &ndash; November 18, 2012</p>
   <p>New:</p>
	 <ul>
     <li>attribute &bdquo;only at certain seasons&ldquo;</li>
     <li>new forum posts are listed on start page</li>
   </ul>

	 <p>Changed / improved:</p>
	 <ul>
     <li>moved help pages to the new <a href="http://wiki.opencaching.de/">Wiki</a> (German)</li>
     <li>updated <a href="./articles.php?page=team">team members list</a></li>
     <li>optimized search engine access</li>
     <li>simplified internal software configuration</li>
   </ul>

	 <p>Fixed:</p>
	 <ul>
     <li>fixed waypoint generation error</li>
     <li>fixed error message when saving unchanged user profile</li>
     <li>fixed main menu when logged off</li>
     <li>GC waypoing search will work with duplicates</li>
     <li>user profile and search page layout fixes</li>
   </ul>
	<br />

	 <p><strong>Release 3.0.2</strong> &ndash; August 26, 2012</p>
   <p>New:</p>
	 <ul>
	   <li><a href="./articles.php?page=cacheinfo#difficulty">Difficulty ratings</a> explained, including tooltips and links within the cache listings</li>
	   <li><a href="./articles.php?page=verein">Vereinsseite</a> (currently German only)
   </ul>
	 <p>Changed / improved:</p>
	 <ul>
     <li>improved <a href="./index.php">Homepag</a> performance</li>
     <li>rewritten <a href="./articles.php?page=cacheinfo">cache description</a> info</li>
	   <li>updated <a href="./doc/xml/">XML Interface Documentation</a> (German) and <a href="https://github.com/OpencachingDeutschland/oc-server3/blob/master/doc/license.txt">Source code license</a></li>
	   <li>updated <a href="./articles.php?page=team">Team members list</a></li>
	   <li>announced new <a href="./articles.php?page=donations">bank account</a> for donations</li>
	   <li>better display of cache reports for the support team</li>
	   <li>removed 65565-cache-listings-limit (OCFFFF, including archived caches)</li>
	   <li>Completed Spanish and Italian translations</li>
	   <li>hidden inaktive new caches on <a href="./newcachesrest.php">All-but-Germany</a> page</li>
	   <li>hidden log button for locked caches, instead of linking it to an emty page</li>
	   <li>recommendations stars are display with Found and Attended logs only.</li>
	   <li>reversed log type order for event caches</li>
   </ul>
	 <p>Fixed:</p>
	 <ul>
	   <li>added missing event attendees count in log summary line</li>
	   <li>fixed overwriting saved queries</li>
	   <li>fixed OC waypoint creation error</li>
	   <li>Recommendations are no longer lost when logging a cache again, e.g. a note after a found log.</li>
	   <li>Recommendations are no longer lost when deleting one of multiple logs of the same user, or when editing one of them.</li>
	   <li>Multiple logs by the same used only count once at the homepage top ratings list.</li>
	   <li><a href="doc/xml/">XML-Interface</a> will not truncate default charset data at unknown characters.</li>
	   <li>fixed error message for invalid log date</li>
	   <li>fixed case insensitivity of log passwords</li>
	   <li>decrypting hints when JavaScript is disabled</li>
	   <li>removed non-workink log entry deletion link for cache owners</li>
	   <li>fixed log edit permissions for locked caches</li>
	   <li>removed dummy spoiler option when uploading log pictures</li>
   </ul>
	<br />

	 <p><strong>Release 3.0.1</strong> &ndash; August 8, 2012</p>
   <p>New:</p>
	 <ul>
	   <li>URL shortener for direct cache listing access, e.g. <a href="http://www.opencaching.de/OCD93B">http://www.opencaching.de/OCD93B</a></li>
	   <li>englisch translation of <a href="./articles.php?page=geocaching">About Geocaching</a>, <a href="./articles.php?page=cacheinfo">Cache descriptions</a>, <a href="./articles.php?page=impressum">Legal information</a>, <a href="./articles.php?page=dsb">Privacy statement</a>, <a href="./articles.php?page=donations">Donations</a>, <a href="./articles.php?page=contact">Contact</a> and <a href="./articles.php?page=team">Team members' list</a> pages; team list, legal information and donations pages have been updated</li>
	   <li>display of &bdquo;You have attended this event&ldquo; in map popups</li>
	   <li>changelog</li>
	 </ul>
	 <p>Changed / improved:</p>
	 <ul>
     <li>separation of opencaching.de/geocaching.de</li>
	   <li>new <a href="./articles.php?page=team">list of team members</a></li>
	   <li>display of new caches at <a href="./index.php">start page</a> by publishing instead of hiding date, and at <a href="./newcaches.php">New caches</a> page by publishing instead of listing creation date</li>
	   <li>inactive caches are hidden in new caches lists</li>
	   <li>GC waypoints will be no longer truncated when copy-and-pasted with leading spaces (frequent problem)</li>
	   <li>layout adjustment of <a href="./search.php">search page</a> and <a href="http://www.blog.opencaching.de">blog/info page</a></li>
	   <li>removed "0.0 km" distance in search lists when logged off (no home coordinates available)</li>
	 </ul>
	 <p>Fixed:</p>
	 <ul>
	   <li>cache icon scaling in exported KML files</li>
	   <li>correct default country for new caches, fixed &bdquo;Belgium/Afghanistan problem&ldquo;</li>
	   <li>first log was missing in print view</li>
	   <li>display of event attenders' count</li>
	   <li>display of cache type icon for unpublished caches at <a href="./myhome.php">user profile</a> &rarr; Show all</li>
	   <li>link "Geokrety history" and recommendation count in cache listings will not be cropped when using big fonts</li>
	   <li>complete, clickable opencaching.de links in log notification emails</li>
	   <li>added missing links to <a href="http://www.opencaching.nl/">opencaching.nl</a></li>
	   <li>correct error message when entering a wrong email-address-change confirmation code</li>
 	 </ul>
	<br />

	</div>