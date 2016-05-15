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

    <p>This page lists all changes since version 3.0. Some minor changes have been omitted here. New Features may be relased before the final relase date.</p>
    <br />

    <div class="changelog-changes">

    <p id="v3.0.18"><strong>OC 3.0 Release 18</strong> &ndash; 31 March 2016</p>
    <p>New:</p>
    <ul>
        <li>Show markers when searching for coordinates on the map</li>
        <li>Pictures in cache descriptions and logs can be reordered.</li>
        <li>Convert coordinates of additional waypoints in cache listings</li>
        <li>OKAPI: Upload log images</li>
        <li>OKAPI: &bdquo;Geocache needs maintenance&ldquo; can be queried and logged.</li>
        <li>OKAPI: Consumer keys can be revoked (by sysadmins)</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>Better ordering of logs with and without time on the same day.</li>
        <li>When the owner changes the type of the latest log, the cache status will be adjusted (depending on the old/new log type). The type of older status logs can no longer be changed.</li>
        <li>Smileys in notification emails (shown as text smilies)</li>
        <li>Improved cache report handling by OC admins</li>
        <li>Detail improvements and adjustments for maintenance state logs</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>Repaired coordinate searching on the map</li>
        <li><a href="doc/xml">XML interface fixes</a>. If you were using the replication feature of version 1.4, you should import a complete dump.</li>
    </ul>
    <br />

    <p id="v3.0.17"><strong>OC 3.0 Release 17</strong> &ndash; 7 March 2016</p>
    <p>New:</p>
    <ul>
        <li>With every log, the maintenance state of the cache and the cache description can be supplied. If "geocache needs maintenance" or "description is outdated" is chosen, this is indicated by corresponding symbols in the upper right area of the listing. The owner or any other user may reset these marks by another log, if he thinks that the geocache / the description is ok (now). The marks should be set only if there are substancial problems with the cache or the description.</li>
        <li>Coordinate changes are displayed inbetween logs. The cache owner may protect old coordinates by a listing setting; then the last three digits will be replaced by ***.</li>
        <li>If logs entries are edited on the day after or later, the last modification date is displayed at the bottom of the log. This works only for changes since release 17.</li>
        <li>If caches are watched via lists, the names of the lists are given in the notification emails.</li>
        <li>When reporting caches, the status of the reporting system is shown.</li>
        <li>OKAPI: The own home coordinates from the user profile can be retrieved.</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>Parts of cache list descriptions can no longer overrun the frame. [bug of release 13]</li>
        <li>small fixes for search result sorting [bugs of release 16]</li>
        <li>fixed some page display glitches</li>
    </ul>
    <br />

    <p id="v3.0.16"><strong>OC 3.0 Release 16</strong> &ndash; 1 February 2016</p>
    <p>New:</p>
    <ul>
        <li>The <a href="search.php">cache search</a> results can be sorted by clicking on the table headers.</li>
        <li>When chosing search result order by hide date, this date is displayed in the results list.</li>
        <li>Added statistics by log type to the <a href="ownerlogs.php">log statistics for own caches</a>.</li>
        <li>Old late logs can be hidden in the <a href="newlogs.php">list of new logs</a>; list now is sortable by either log date or log submit date.</li>
        <li>OConly marks (<img src="resource2/ocstyle/images/misc/15x15-oc.png">) are shown in the lists of new logs and of own logs.</li>
        <li>Special Unicode symbols (character codes &gt; 65535, e.g. smileys) can be entered in cache descriptions, logs, user profile texts and cache list descriptions.</li>
        <li>OKAPI: own list of ignored caches can be retrieved</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>The list of new logs now shows the log dates instead of log submit dates.</li>
        <li>Removed the links to navicache.com from cache descriptions; Navicache waypoints can no longer be entered.</li>
        <li>Return to the cache listing edit page instead to the listing page after adding/modifying pictures.</li>
        <li>Statistics picture selection now distinguishes between current OC logos and other logos.</li>
        <li>Disabled listings ("temporarily not available") now are archived automatically after 6 instead of 12 months, if they have been disabled by OC admins or if the owner has not been logged in since disabling the cache. Caches with attribute "Only available during specified seasons" are excempt from this rule, as well as caches with new logs after disabling; the 12 months period applies here. Any listing modifications by the owner reset the archiving "countdown". The owner may arbitrarily change the state back to "temporarily not available".</li>
        <li>Prevented upload of (most) duplicate log pictures by Ocprop.</li>
        <li>OKAPI: greatly improved proformance of cache search by radius for small radiuses (&lt;&lt; 100 km)</li>
        <li>Ported SQL code from MySQL 5.1 to MariaDB 5.5</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>Show the magnifyer symbol when hovering over the own log pictures gallery [bug of release 5]</li>
        <li>Show the correct symbol for cache listings which have been locked/hidden by the Opencaching team (<img src="resource2/ocstyle/images/cachestatus/16x16-locked-invisible.png">) [bug of release 6]</li>
        <li>fixed handling of HTML code in cache short-descriptions</li>
        <li>When chosing "search geocaches nearby: searchable" in a cache listing, disabled caches will be hidden. [bug of release 9]</li>
        <li>fixed english date layout in the list of new caches</li>
        <li>prevented resetting of the chache types <i>Drive-in</i> and <i>Maths/Physics</i> as well as the sizes <i>nano</i> and <i>extremely large</i> by Ocprop to GC values. [bug of release 6]</li>
        <li>final adjustments for the start of HTTPS &rarr; <a href="https://www.opencaching.de">https://www.opencaching.de</a>; not yet stable implemented for OKAPI (&rarr; <a href="https://github.com/opencaching/okapi/issues/343">more info</a>)</li>
        <li>OKAPI: corrected error message when accessing locked caches; some other small fixes</li>
    </ul>
    <br />

    <p id="v3.0.15"><strong>OC 3.0 Release 15</strong> &ndash; 19 September 2015</p>
    <p>New:</p>
    <ul>
        <li>finished French translation; relaunch of <a href="http://www.opencaching.fr">Opencaching.fr</a></li>
        <li>full HTTPS support (not enabled yet at Opencaching.de)</li>
        <li>added <a href="articles.php?page=htmltags#cssstyles">Documentation</a> on allowed CSS styles</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>better presentation of <a href="http://www.opencaching.it" target="_blank">www.opencaching.it</a> and <a href="http://www.opencachingspain.es" target="_blank">www.opencachingspain.es</a> (<a href="http://redmine.opencaching.de/issues/816">#816</a>, <a href="http://redmine.opencaching.de/issues/818">#818</a>)</li>
        <li>improved translation system</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>reasonable text conversions when switching between text and HTML editor (<a href="http://redmine.opencaching.de/issues/236">#236</a>)</li>
        <li>GPX and TXT download will return cache descriptions preferably in the selected language (<a href="http://redmine.opencaching.de/issues/852">#852</a>)</li>
        <li>removed HTML code from log notifications</li>
        <li>Fixed &bdquo;Send to GPS devices&ldquo; at www.opencaching.it, www.opencachingspain.es and www.opencaching.fr.</li>
    </ul>
    <br />

    <p id="v3.0.14"><strong>OC 3.0 Release 14</strong> &ndash; 5 September 2015</p>
    <p>New:</p>
    <ul>
    <li>Sharing of personal cachelists using passwords</li>
    <li>Bookmarks for public and private cachelists</li>
    <li>Show the number of recommendations in the list of own caches, changed
    display of recommendations in several list views
    </li>
    <li>Prepared French translation</li>
    <li>Automatically rotate uploaded pictures
    (<a href="http://redmine.opencaching.de/issues/732">#732</a>)
    </li>
    <li>
    Selection criterea for cache search now allow searching for listings in
    a specified language.
    (<a href="http://redmine.opencaching.de/issues/48">#48</a>)
    </li>
    <li>
    Display the newest logs for the selected country
    (<a href="http://redmine.opencaching.de/issues/183">#183</a>)
    </li>
    <li>History of coordinate changes in the admin view (internal)</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
    <li>Links to Wiki and Geokrety.org now call the localized target</li>
    <li>Notification emails in the language of the user
    (<a href="http://redmine.opencaching.de/issues/813">#813</a>)
    </li>
    <li>Automatically load log entries for logged-off users
    (<a href="http://redmine.opencaching.de/issues/785">#785</a>)
    </li>
    <li>Avoid creation of Danish cache descriptions by mistake</li>
    <li>Add smileys in the HTML editor mode</li>
    <li>Allow more European special characters in the user name
    (<a href="http://redmine.opencaching.de/issues/212">#212</a>)
    </li>
    <li>
    Improved the English translation (
    <a href="http://redmine.opencaching.de/issues/163">#163</a>,
    <a href="http://redmine.opencaching.de/issues/184">#184</a>,
    <a href="http://redmine.opencaching.de/issues/817">#817</a>)
    </li>
    </ul>

    <p>Fixed:</p>
    <ul>
    <li>Corrected typos in several city names
    (<a href="http://redmine.opencaching.de/issues/175">#175</a>)</li>
    <li>Language names instead of flags in the cache listing
    </li>
    <li>Corrected the (in rare cases) wrong date for recommendations
    (<a href="http://redmine.opencaching.de/issues/713">#713</a>) [bug of release 9]</li>
    <li>JavaScript improvement: Icons in popups on the map are now displayed in
    broken browsers. (<a href="http://redmine.opencaching.de/issues/11">#11</a>)
    </li>
    <li>Improved HTML filters for logs and cache descriptions
    (<a href="http://redmine.opencaching.de/issues/79">#79</a>),
    a list of allowed HTML elements can be found
    <a href="http://www.opencaching.de/articles.php?page=htmltags&locale=EN">here</a>.
    </li>
    <li>
    Popups of the HTML-editor are not cropped any more when using large fonts.
    (<a href="http://redmine.opencaching.de/issues/300">#300</a>)
    </li>
    <li>
    After removing an entry of the list of ignored caches, the correct page is displayed now
    (<a href="http://forum.opencaching.de/index.php?topic=4214">see forum</a>).
    </li>
    <li>
    Corrected error when adding hints in english or spanish language
    (<a href="http://redmine.opencaching.de/issues/746">#746</a>, Ticket)
    </li>
    <li>
    OKAPI now updates the login date
    (<a href="http://forum.opencaching.de/index.php?topic=4260.0">see forum</a>)
    </li>
    <li>
    OKAPI now handles pipe ('|') in user names correctly
    (<a href="https://github.com/opencaching/okapi/issues/339">see github-issue</a>)
    </li>
    <li>
    Dispaying the search result in Google Maps has been removed
    (<a href="http://redmine.opencaching.de/issues/31">#31</a>)
    </li>
    <li>
    Corrected notifications for newly set OConly attributes
    (<a href="http://redmine.opencaching.de/issues/810">#810</a>) [bug of release 9]
    </li>
    <li>Furthermore, many smaller bugs were fixed, see <a href="http://redmine.opencaching.de/versions/10">here</a>
    for a complete list.</li>
    </ul>
    <br />

    <p id="v3.0.13"><strong>OC 3.0 Release 13</strong> &ndash; 4 July 2015</p>
    <p>New:</p>
    <ul>
        <li><a href="cachelists.php">Cache lists</a></li>
        <li>Filtering option for Caches with Geokrets in cache search and on the map</i></li>
        <li>Link to Safari caches list on the map</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>Pictures up to 250 KB size stay unchanged; only pictures &gt; 250 KB will be resized.</li>
        <li>To publish an unplished cache, only the "publish now" option at the bottom of the listing needs to be changed.</li>
        <li>Watch and ignore lists of disabled user accounts are deleted.</li>
        <li>some design improvements</li>
        <li>Update OC countries list: The Rumanian site is new, Sweden/Norway was shut down.</li>
        <li>internal improvements for the data maintenance team</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>more reliable hiding of GC crosslistings in the cache search and on the map [bug of release 9]</li>
        <li>fixed new cache marks in the search results lists [bug of release 9]</li>
        <li>fixed attributes display in Internet Explorer [bug of release 11]</li>
        <li>fixed event icons in log statistics</li>
        <li>fixed GC and NC waypoint input [bug of release 12]</li>
    </ul>
    <br />

    <p id="v3.0.12"><strong>OC 3.0 Release 12</strong> &ndash; 31 May 2015</p>
    <p>New:</p>
    <ul>
        <li>"more..." link in the start page event list, if there are more than ten events or events in other countries</li>
        <li>The "Convert coordinates" link in cache listings shows <i>what3words</i> coordinates.</i></li>
        <li>search for <i>what3words</i> coordinates on the map</li>
        <li>new <a href="myprofile.php">user profile setting</a> to include the own e-mail address by default when writing to other users</li>
        <li><a href="okapi">OKAPI</a>: added experimental support for the new Garmin GGZ data format</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>protection against accidential duplicate cache listings</li>
        <li>GC waypoint input in cache listings checks for valid waypoint format</li>
        <li>Notification e-mails for deleted logs now include the log date and type.</li>
        <li>The last entered log date will be used as default for only 12 hours; then the current date is proposed for new logs.</li>
        <li>Personal cache notes are saved even if the entered coordinate is invalid.</li>
        <li>updated information on Opencaching Deutschland e.V.</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>fixed "Send to GPS device" button in cache listings</li>
        <li>Disabled events are automatically archived after one year like all caches. [bug of release 9]</li>
        <li>added missing e-mail address in e-mails to other users</li>
        <li>fixed language switch on www.opencaching.it and www.opencachingspain.es [problem since release 10]</li>
        <li>some OKAPI fixes (&rarr; <a href="https://code.google.com/p/opencaching-api/source/list">changelog</a>)</li>
    </ul>
    <br />

    <p id="v3.0.11"><strong>OC 3.0 Release 11</strong> &ndash; 21 June 2014</p>
    <p>New:</p>
    <ul>
        <li>get direct link to a specific log entry by right-clicking on its symbol and choosing "copy hyperlink"</li>
        <li>automatically shrink images on upload</li>
        <li>confirmation before deleting pictures</li>
        <li>show link "Nearby search at geocaching.com" in admin area for reported caches</li>
        <li>user option for receiving the OC newsletter (or not)</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>improved button design in IE</li>
        <li>link to listing on listing page with http:// for better use on webpages</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>fixed positioining of help button in IE</li>
        <li>fixed generation of direct link to log entry after loading all logs</li>
        <li>fixed a few typos in german translation and teamlist</li>
    </ul>
    <br />

    <p id="v3.0.10"><strong>OC 3.0 Release 10</strong> &ndash; 24 August 2013</p>
    <p>New:</p>
    <ul>
        <li>automatically load rest of log entries, when scrolling to the end of a cache page and more than 5 logs are present</li>
        <li>regional finds statistics in user profile</li>
        <li>OConly statistics in user profile</li>
        <li><a href="oconly81.php">OConly-81</a></li>
        <li>"all" function in the list of own logs</li>
        <li>image browsing for pictures of one log entry</li>
        <li>added Social Media links to "sidebar" menu (left/bottom, German media only)</i>
        <li>explained cache sizes on the <a href="articles.php?page=cacheinfo">description</a> page</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>redesigned hyperlinks</li>
        <li>improved cache listing print view</li>
        <li>many minor improvements of page display and layout</li>
        <li>reworked CSS style sheets</li>
        <li>redesigned www.opencaching.it and www.opencachingspain.es start pages</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>fixed several display glitches in Microsoft Internet Explorer</li>
        <li>fixed line spacing for large fonts in cache listings</li>
        <li>always show log picture titles [bug of release 5]</li>
        <li>fixed lots of HTML syntax errors</li>
        <li>fixed recommendation star display in log lists [bug of release 9]</li>
        <li>fixed several problems when downloading search results [bugs of release 9]</li>
    </ul>
    <br />

    <p id="v3.0.9"><strong>OC 3.0 Release 9</strong> &ndash; 25 July 2013</p>
    <p>Reworked <a href="search.php">searching for geocaches</a>:</p>
    <ul>
        <li>new, clearer design</li>
        <li>show search results on map</li>
        <li>temporarily unavailable and archived geocaches can be separately hidden (also on the map)</li>
        <li>changed hide-crosslistings option in hide GC listings; does use additional waypoints maintained by the Opencaching team (also on the map)</li>
        <li>single cache sizes and types can be easier selected via "all" and "none" buttons</li>
        <li>search for all caches logged by a user (alternatively to searching by log type)</li>
    </ul>

    <p>Improved <a href="myhome.php#mycaches">lists of own geocaches</a>:</p>
    <ul>
        <li>show cache type, number of found logs and type/date of last log</li>
        <li>archived and locked caches can be hidden</li>
        <li>shows all caches instead of only the last 20</li>
        <li><a href="ownerlogs.php">New logs list</a> for all own caches</li>
    </ul>

    <p>More new features:</p>
    <ul>
        <li>recommendations are marked with <img src="images/rating-star.gif" /> in log lists</li>
        <li>OConly caches are marked with <img src="resource2/ocstyle/images/misc/15x15-oc.png" /> in cache lists</li>
        <li>OConly tag in new cache notifications; notifications about newly tagged OConlies can be enabled in user profile</li>
        <li>information on protection areas in GPX, OKAPI and <a href="doc/xml">XML Interface</a> downloads</li>
        <li>new menu option &bdquo;New Features&ldquo; on the start page</li>
        <li>new menu option &bdquo;New Wiki articles&ldquo; (currently German only) on the start page</li>
        <li>"Temporarily unavailable" geocaches are automatically archived after one year, event caches after five weeks.</li>
        <li>ongoing evaluation and avoiding of undeliverable e-mails; see also release 5 / undeliverable emails</li>
        <li>OKAPI: query geocache attributes via "OKAPI attributes" which are the same on all OC sites</li>
        <li>OKAPI: GC and OC.de compatible geocache attributes in GPX files</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>When entering multiple logs, the last log date is proposed.</li>
        <li>clearer start page menu, splitted into &bdquo;News&ldquo; and &bdquo;Opencaching&ldquo;</li>
        <li>show own unpublished caches on the map (can take up to one hour until they appear)</li>
        <li>bolder tagging of new caches in search result lists; extended new-timespan from 7 to 14 days</li>
        <li>worked around special character display problems on some Garmin devices</li>
        <li>Garmin download window now can be closed with just one click onto OK.</li>
        <li>simpler confirmation of user registration with just one click</li>
        <li>hide invalid Dutch Grid coordinates in cache listings' "convert coordinates" list</li>
        <li>sort <a href="mytop5.php">own recommendations</a> by date</li>
        <li>use new OC.de logo at some more places</li>
        <li>status logs (see release 8) will also be generated by Ocprop and when disabling user accounts</li>
        <li>some improvements on the geocache adoption page</li>
        <li>extensive internal restructuring and cleanup</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>fixed Geokret data inconsistencies (problem with Geokrets which are reported as lost still exists)</li>
        <li>fixed page layout when displaying pictures in new logs list [bug of release 5]</li>
        <li>fixed another problem with ' in picture titles [bug of release 5]</li>
        <li>fixed transfering OC user names into <a href="webchat.php">chat</a> window</li>
        <li>fixed GPX file version inconsistency by completely migrating to Groundspeak GPX version 1.0.1</li>
        <li>fixed date display in e-mail address reminder e-mails</li>
        <li>fixed geocache recommendation data in XML interface</li>
        <li>fixed some OKAPI bugs</li>
  </ul>
    <br />

    <p id="v3.0.8"><strong>OC 3.0 Release 8</strong> &ndash; 1 June 2013</p>
    <p>New:</p>
    <ul>
        <li>The geocache listing status can now (only) be changed by logging the new state. New log types <em>temporarily unavailable</em>, <em>archived</em>, <em>locked </em> und <em>ready for search</em> have been added. Unchanged states may be logged, too, e.g. to confirm that the cache is still searchable. The type of old logs can be changed afterwards.</li>
        <li>geocache status change notifications via state logging</li>
        <li>HTML description in user profile, with comfortable editor</li>
        <li>new attribute <em>Safari Cache</em> vor reverse ("locationless") caches</li>
        <li>map of new caches at the bottom of the start page</li>
        <li>additional waypoints and personal note are included in listing printouts</li>
        <li>link "logged caches" in user profile; own logs are listed in the order of log dates</li>
        <li>number of active caches in user profile + link to show them</li>
        <li>search results are sortable by date of own log; only the own logs are shown then in the right column of the search results list</li>
        <li>map filter settings now can be saved permanently</li>
        <li>OC support team members can mark thir logs as "OC team log" (<img src="resource2/ocstyle/images/oclogo/oc-team-comment.png" />)</li>
        <li>added additional waypoints, log time, OC team log flag and preview picture flag to the "XML interface"</li>
        <li>additional user profile menu option <a href="okapi/apps/">API applications</a>, to control <a href="okapi">OKAPI</a> app access rights</li>
        <li>OKAPI: GC codes of caches and OC team log flag can be queried</li>
        <li>OKAPI: pictures in GPX files can be embedded as thumbnail links</li>
        <li>new <a href="404.php">error page</a> für for invalid page requests</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>improved / simplified user profile settings</li>
        <li>redesign of cache lists layout in user profile and search results</li>
        <li>default coordinates for new additional waypoints are the geocache's coordinates</li>
        <li>When deleting logs, the original log text and the listing address are added to the notification email.</li>
        <li>strike-trough of inactive caches in search result lists</li>
        <li>redesign of listing header, e.g. display of short URLs, dropdown list for print view options and neater route length display</li>
        <li>Past, but still active event listings are greyed on the map, like inactive caches.</li>
        <li>removed reset button from all dialogs; renamed "change" buttons to "save"</li>
        <li>Unpublished and locked/hidden caches no longer count in the user's hidden statistics.</li>
        <li>Log, watch and report buttons in cache listings are visible if not logged in.</li>
        <li>intergrated <a href="webchat.php">chat</a> directly into the OC website</li>
        <li>increased maximum picture size from 150 to 250 KB</li>
        <li>ignored geocaches will immediately disappear from the map; same for un-ignoring</li>
        <li>wider editor window for cache descriptions</li>
        <li>added recommendation date to <a href="mytop5.php">personal recommendation list</a></li>
        <li>distinction between "will attend" and "attended" in event attendees lists</li>
        <li>Cache owners and OC support team members can see locked/hidden caches in search lists.</li>
        <li>improved cache report handling by the support team</li>
        <li>updated <a href="articles.php?page=verein">Opencaching Deutschland e.V.</a> page and membership application form (German)</li>
    </ul>

    <p>Fixed:</p>
    <ul>
        <li>event log icons (<img src="resource2/ocstyle/images/log/16x16-will_attend.png" /> <img src="resource2/ocstyle/images/log/16x16-attended.png" />) in search result lists</li>
        <li>show <em>all</em> matching unlogged caches when sorting search results by last log date</li>
        <li>solved picture display problem when the title contains ' [bug of release 5]</li>
        <li>removed dysfunctional "in GM" link (show in Google Maps) when executing stored queries</li>
        <li>fixed rare error message after retracting recommendations</li>
        <li>fixed date/time in email address reminder emails</li>
        <li>layout fix in hint decoder table</li>
        <li>fixed start page cache lists layout in Internet Explorer</li>
        <li>personal notes will not change the listing modification date; fixed date of the affected listings [bug of release 6]</li>
        <li>"hide cache" will redirect not-logged-in users to the login page [bug of release 5]</li>
        <li>fixed "XML interface" DTDs</li>
        <li>several OKAPI fixes</li>
  </ul>
    <br />

    <p id="v3.0.7"><strong>OC 3.0 Release 7</strong> &ndash; 19 April 2013</p>
    <ul>
        <li>New: <a href="okapi">OKAPI</a></li>
        <li>"Apple Touch Icons" for Smartphones</li>
        <li>When logging own caches, the log type "Note" is preselected instead of "Found it".</li>
    </ul>
    <br />

    <p id="v3.0.6"><strong>OC 3.0 Release 6</strong> &ndash; 12 April 2013</p>
    <p>New:</p>
    <ul>
        <li>logging with time</li>
        <li>new menu option "About Opencaching" on the start page</li>
        <li>new menu option "New Logs / without Germany" on the start page</li>
        <li>new menu option "Public profile" on the profile page</li>
        <li>logout button in full screen map</li>
        <li>Data license is displayed on the left hand of all pages and is deliverd through GPX and TXT download and "XML interface".</li>
        <li>symbols for locked and unpublished caches on profile pages</li>
        <li>RSS news feed link on the start page</li>
        <li>"Listing vandalism" can be reverted by the Opencaching.de support team.</li>
    </ul>

    <p>Changed / improved:</p>
    <ul>
        <li>design improvements in the new caches and logs lists</li>
        <li>OC-Code short-links (see r3.0.1) are included in notification emails.</li>
        <li>publishing of duplicate logs is prevented</li>
        <li>better assignment of attributes to GC attributes in GPX files</li>
        <li>changed tides attribute in "not at high water level"</li>
        <li>new Opencaching.de logo in listing printouts</li>
        <li>reduced news and forum post display on the start page to headlines-only; double number of display posts</li>
        <li>updated team list</li>
        <li>The sent-email-counter in the user profile has been discarded for technical reasons.</li>
  </ul>

    <p>Fixed:</p>
    <ul>
        <li>Enabling ignored caches on the map works (for the first time).</li>
        <li>Log passwords now also work with event caches.</li>
        <li>Changes of additional waypoints and pictures update the listing last-modification date.</li>
        <li>Cache search sometimes did not work after logout.</li>
        <li>Additional waypoint type (e.g. "Parking area") is delivered in GPX files. [bug of release 4]</li>
        <li>When deleting logs, pictures will be deleted, too, instead of remaining somwhere in the system.</li>
        <li>fixed Ocprop duplicate logs problem</li>
        <li>fixed log editor language on the English, Italian and Spanish site</li>
        <li>translated cache-location country names on the English, Italian and Spanish site</li>
  </ul>
    <br />

    <p id="v3.0.5"><strong>OC 3.0 Release 5</strong> &ndash; 16 March 2013</p>
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
        <li>fixed handling of nano caches with saved searches [bug of release 4]</li>
        <li>removed JavaScript warning when logging on the Italien page</li>
        <li>show Danish flag with Danish cache descriptions</li>
        <li>fixed nano size selection in search form</li>
    </ul>

     <p id="v3.0.4"><strong>OC 3.0 Release 4</strong> &ndash; 17 February 2013
   <p>New:</p>
     <ul>
     <li>new cache size "nano"</li>
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

     <p id="v3.0.3"><strong>OC 3.0 Release 3</strong> &ndash; 18 November 2012</p>
   <p>New:</p>
     <ul>
     <li>attribute "only at certain seasons"</li>
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

     <p id="v3.0.2"><strong>OC 3.0 Release 2</strong> &ndash; 26 August 2012</p>
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

     <p id="v3.0.1"><strong>OC 3.0 Release 1</strong> &ndash; 8 August 2012</p>
   <p>New:</p>
     <ul>
       <li>URL shortener for direct cache listing access, e.g. <a href="http://www.opencaching.de/OCD93B">http://www.opencaching.de/OCD93B</a></li>
       <li>englisch translation of <a href="./articles.php?page=geocaching">About Geocaching</a>, <a href="./articles.php?page=cacheinfo">Cache descriptions</a>, <a href="./articles.php?page=impressum">Legal information</a>, <a href="./articles.php?page=dsb">Privacy statement</a>, <a href="./articles.php?page=donations">Donations</a>, <a href="./articles.php?page=contact">Contact</a> and <a href="./articles.php?page=team">Team members' list</a> pages; team list, legal information and donations pages have been updated</li>
       <li>display of "You have attended this event" in map popups</li>
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
       <li>correct default country for new caches, fixed "Belgium/Afghanistan problem"</li>
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
</div>
