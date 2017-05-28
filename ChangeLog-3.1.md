Changes in oc-server 3.1

All notable changes of the oc-server 3.1 release series are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## unreleased [3.1.3...development](https://github.com/OpencachingDeutschland/oc-server3/compare/3.1.3...development)

### Added

### Changed

### Removed

### Fixed
- fixed cookie notice [#1052](https://redmine.opencaching.de/issues/1052)
- fixed sorting issue with guest users #1049](https://redmine.opencaching.de/issues/1049)

## [3.1.3] - 2017-05-17 [3.1.2...3.1.3](https://github.com/OpencachingDeutschland/oc-server3/compare/3.1.2...3.1.3)

### Added
- added handicap cache attribute  [#1031](https://redmine.opencaching.de/issues/1031)
- added maintenance mode [#841](https://redmine.opencaching.de/issues/841)
- added new theme structure and bootstrap 4
- adding google analytics tracking [#1038](https://redmine.opencaching.de/issues/1038)
- adding cookie notice [#768](https://redmine.opencaching.de/issues/768)

### Changed
- change default geocache publish type [#1040](https://redmine.opencaching.de/issues/1040),[#745](https://redmine.opencaching.de/issues/745)
- increased max image size for log pictures [#1037](https://redmine.opencaching.de/issues/1037)
- cleaned up old directories [#1033](https://redmine.opencaching.de/issues/1033)
- support line breaks in filed notes file [#1042](https://redmine.opencaching.de/issues/1042)
- show only log password if it is needed [#1021](https://redmine.opencaching.de/issues/1021)
- trim input values like listing name [#221](https://redmine.opencaching.de/issues/221)

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
