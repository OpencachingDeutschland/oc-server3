Changes in oc-server 3.1

All notable changes of the oc-server 3.1 release series are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## unreleased [3.1.3...development](https://github.com/OpencachingDeutschland/oc-server3/compare/3.1.3...development)

### Added
- notice for unpublished cashes [#1063](https://redmine.opencaching.de/issues/1063)
- notice for reported cashes [#1100](https://redmine.opencaching.de/issues/1100)
- recommendation notice in log messages [#1090](https://redmine.opencaching.de/issues/1090)

### Changed
- activated error_reporting in vagrant dev environment [#1082](https://redmine.opencaching.de/issues/1082)
- allow rel attribute in a tags [#1077](https://redmine.opencaching.de/issues/1077)
- admin report warning [#1047](https://redmine.opencaching.de/issues/1047)
- increase text line height [#1091](https://redmine.opencaching.de/issues/1091)
- cache image alignment [#1092](https://redmine.opencaching.de/issues/1092)[#955](https://redmine.opencaching.de/issues/955)
- ask before delete an image [#1028](https://redmine.opencaching.de/issues/1028)
- better description for the hint field [#889](https://redmine.opencaching.de/issues/889)
- add event date to popup [#944](https://redmine.opencaching.de/issues/944)
- save logtyp during edit process [#1099](https://redmine.opencaching.de/issues/1099)
- show team-comment log flags [#1101](https://redmine.opencaching.de/issues/1101)
- hide 'original coordinates' if there is an older log [#1102](https://redmine.opencaching.de/issues/1102)
- change logout time text [#1095](https://redmine.opencaching.de/issues/1095)

### Removed
- removed ocm map [#1068](https://redmine.opencaching.de/issues/1068)

### Fixed
- cookie notice [#1052](https://redmine.opencaching.de/issues/1052)
- sorting issue with guest users [#1049](https://redmine.opencaching.de/issues/1049)
- cache status [#1073](https://redmine.opencaching.de/issues/1073)
- undefined variable notice [#1080](https://redmine.opencaching.de/issues/1080)
- wrong directions [#1078](https://redmine.opencaching.de/issues/1078)
- wrong oc only flags [#1081](https://redmine.opencaching.de/issues/1081)
- listing outdated link [#1065](https://redmine.opencaching.de/issues/1065)
- empty map on zoom level 3 [#1084](https://redmine.opencaching.de/issues/1084)
- error page [#1088](https://redmine.opencaching.de/issues/1088)
- language switch on lib pages [#1087](https://redmine.opencaching.de/issues/1087)
- okapi changelog [#1085](https://redmine.opencaching.de/issues/1085)
- lib2 SQL debugger [#1093](https://redmine.opencaching.de/issues/1093)
- some missing translations [#1035](https://redmine.opencaching.de/issues/1035)
- some whitespace issue [#1020](https://redmine.opencaching.de/issues/1020)


## [3.1.3] - 2017-05-17 [3.1.2...3.1.3](https://github.com/OpencachingDeutschland/oc-server3/compare/3.1.2...3.1.3)

### Added
- handicap cache attribute  [#1031](https://redmine.opencaching.de/issues/1031)
- maintenance mode [#841](https://redmine.opencaching.de/issues/841)
- new theme structure and bootstrap 4
- google analytics tracking [#1038](https://redmine.opencaching.de/issues/1038)
- cookie notice [#768](https://redmine.opencaching.de/issues/768)
- image gallery for own caches [#39](https://redmine.opencaching.de/issues/39)
- new restore functionality for oc support [#1094](https://redmine.opencaching.de/issues/1094)

### Changed
- change default geocache publish type [#1040](https://redmine.opencaching.de/issues/1040),[#745](https://redmine.opencaching.de/issues/745)
- increased max image size for log pictures [#1037](https://redmine.opencaching.de/issues/1037)
- cleaned up old directories [#1033](https://redmine.opencaching.de/issues/1033)
- support line breaks in filed notes file [#1042](https://redmine.opencaching.de/issues/1042)
- show only log password if it is needed [#1021](https://redmine.opencaching.de/issues/1021)
- trim input values like listing name [#221](https://redmine.opencaching.de/issues/221)
- optimize admin view [#1002](https://redmine.opencaching.de/issues/1002)
- support subtitle search of caches [#986](https://redmine.opencaching.de/issues/986)


### Removed

### Fixed
- support c:geo field notes file [#1042](https://redmine.opencaching.de/issues/1042)
- broken image links
- fixed display issue in cache recommendation [#1036](https://redmine.opencaching.de/issues/1036)

## [3.1.2] - 2017-02-06 [3.1.1...3.1.2](https://github.com/OpencachingDeutschland/oc-server3/compare/3.1.1...3.1.2)

### Added
* user search [#1015](https://redmine.opencaching.de/issues/1015)
* adding psh.phar
* adding more UnitTests
* travis ci integration completed 

### Changed
* fix login issue with lib and lib2 components
* code style improvements based on scrutinizer patches
* recommendation star [#1013](https://redmine.opencaching.de/issues/1013)
* hidden caches [#1001](https://redmine.opencaching.de/issues/1001)
* fixing language links [#1022](https://redmine.opencaching.de/issues/1022)
* fixing language switch [#995](https://redmine.opencaching.de/issues/995)

### Removed
* not needed sql debug functions
* [htdocs/lib/logic.inc.php](https://github.com/OpencachingDeutschland/oc-server3/commit/6d369d3ab15140fbf5cb70177716877d8621931f#diff-0724e744015c5d5065054a2b46e8ae67)
* [sql/tests/*](https://github.com/OpencachingDeutschland/oc-server3/commit/fb0222644d263c4428aa0c22b6fc72694bd066e8)
* [htdocs/lib/eventhandler.inc.php](https://github.com/OpencachingDeutschland/oc-server3/commit/2c9e596615cecec6071b3cd7361fbc46a419ade7)
* [htdocs/lib/tinymce/](https://github.com/OpencachingDeutschland/oc-server3/commit/c90261baee46e7c594fe546dfa74f49a7ccd6d93)
* [removed not longer supported garmin integration](https://github.com/OpencachingDeutschland/oc-server3/commit/5495d17f2e2d848b299d416c62a1a058dd176074)

## [3.1.1] - 2016-10-26 [v3.0.19...3.1.1](https://github.com/OpencachingDeutschland/oc-server3/compare/v3.0.19...3.1.1)

### Added

* FieldNotes Upload
* implementation of [crowdin](https://crowdin.com/project/opencaching)
* Implementation of travis for continues integration
* minimal sqldump for developers
* integration of symfony

### Changed
* SessionCookie only over https
* update whats3words api
* automatically update of translations

### Fixed
* map display bugs
* some small improvements

### Deprecated
* all usage of lib and lib2 Libraries Components
