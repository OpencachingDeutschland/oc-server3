[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/OpencachingDeutschland/oc-server3/badges/quality-score.png?b=development)](https://scrutinizer-ci.com/g/OpencachingDeutschland/oc-server3/?branch=development)  [![Crowdin](https://d322cqt584bo4o.cloudfront.net/opencaching/localized.svg)](https://crowdin.com/project/opencaching) [![Travis CI Build](https://api.travis-ci.org/OpencachingDeutschland/oc-server3.svg?branch=development)](https://travis-ci.org/OpencachingDeutschland/oc-server3) [![codecov](https://codecov.io/gh/OpencachingDeutschland/oc-server3/branch/development/graph/badge.svg)](https://codecov.io/gh/OpencachingDeutschland/oc-server3) [![Slack](https://img.shields.io/badge/chat-on%20slack-green.svg?logo=slack)](https://join.slack.com/t/opencaching-de/shared_invite/zt-6blnetpu-UqrvSQr~8r0o3SNmhkmnGQ)

Opencaching.de Code Repository
==============================

[Opencaching.de](https://www.opencaching.de) is a major Geocaching website in Germany. This repository contains the
website's code, including all third-party libraries needed to run it. It is one of two major
[Opencaching code forks](https://wiki.opencaching.de/index.php/Datei:Codegenerationen.png); the other one
is [Opencaching.pl](https://github.com/opencaching/opencaching-pl). Feel free to use it under the provided
[license terms](https://github.com/OpencachingDeutschland/oc-server3/blob/development/LICENSE.md)
for setting up your own open and free Geocaching listing service! The code can easily be translated to other languages.

Your contributions to this project are welcome - you may contact the team in the
[Slack Channel](https://join.slack.com/t/opencaching-de/shared_invite/zt-6blnetpu-UqrvSQr~8r0o3SNmhkmnGQ)
or [Opencaching.de forum](https://forum.opencaching.de/) if you like to join us. Development is usually done on a
VirtualBox Linux system that you can run on your Linux, Windows or Mac workstation, but you may also try to set up the
code directly on your Linux machine. Otherwise you can use our new vagrant box. You get the needed link from a Team
Member in
[Opencaching.de forum](https://forum.opencaching.de/) or you start with our new minimal dump.

In the Vagrant System our example login for the opencaching platform ist root with the password developer.

This repo contains three branches:

* the *stable* branch with current opencaching.de production code
* the *development* branch, basis of all development, which contains code that will be released with the next site
  update
* the *next* branch with experimental features to be tested.

The *next* branch is now and then resetted to development state and rebuilt from there, so do not derive any
working-branches from it. Use development instead.

Major OC.de site updates are version-tagged. See
the [changelog](https://www.opencaching.de/articles.php?page=changelog&locale=EN)
for a detailed list.

Translation [![Crowdin](https://d322cqt584bo4o.cloudfront.net/opencaching/localized.svg)](https://crowdin.com/project/opencaching)
-----------
This Project uses crowdin to translate all words and strings in the code. It starts with Version 3.0.19 of this code. We
are looking for native speakers who will help to translate. There is a review process wich needs at least two
translators to validate the translation of each other. To join the translation team - use this invitation
url: (https://crowdin.com/project/opencaching/invite)

Sponsoring Technology Partner
-------------

* [Atlassian](https://www.atlassian.com/)
* [BrowserStack](https://www.browserstack.com/) ![BrowserStack](https://raw.githubusercontent.com/OpencachingDeutschland/oc-server3/development/doc/browser-stack.png)
* [CrowdIn](https://crowdin.com/)
* [HostEurope](https://www.hosteurope.de/)
* [Jetbrains](https://www.jetbrains.com/)
* [Sentry](https://sentry.io/) ![Sentry](https://raw.githubusercontent.com/OpencachingDeutschland/oc-server3/development/doc/sentry.png)
* [Scrutinizer](https://scrutinizer-ci.com)
* [Travis-Ci](https://travis-ci.org/)
* [Codecov](https://codecov.io/)

We thank our technology partners for their support of our open source project!

Starting the docker development environment
-------------

1. Start a terminal and run `./psh.phar docker:start` in the project root
2. Start a new terminal
    - Run `./psh.phar docker:ssh` and `./psh.phar docker:init`
3. Open your browser and visit the following URL: [http://docker.team-opencaching.de](http://docker.team-opencaching.de)

NOTE: New Version of Opencaching will be called "OC4". We decide to setup a new temporary
URL: [http://try.docker.team-opencaching.de](http://try.docker.team-opencaching.de) and we generate a new administration section
at [http://try.docker.team-opencaching.de/backend](http://try.docker.team-opencaching.de/backend). Use root:developer to login.

Contributions
-------------
Contributing code to Opencaching.de is easy:

* Sign up to Github and [install Git](https://help.github.com/articles/set-up-git),
* create a personal fork of this repository using the Fork button above,
* clone the fork to your development machine,
* create a feature branch based on development,
* edit and commit code,
* push your feature branch to your fork and issue a pull request.

Your code will be reviewed, eventually merged to development and put online with the next site update. Small changes may
be directly released via stable branch.

To update your working copy, add this repo as upstream ...

```bash
git remote add upstream https://github.com/OpencachingDeutschland/oc-server3.git
```

... and regularly update your clone:

```bash
git checkout development
git pull upstream
```

NEVER use pull on a feature branch, but pull to development and then rebase the feature branch on development:

```bash
git checkout feature-branch
git pull --rebase upstream/development
```

Related Websites
----------------

* [Opencaching.de Issue Tracker](https://opencaching.atlassian.net/jira/core/projects/RED/issues)
* [Opencaching.de Git tutorial](https://wiki.opencaching.de/index.php/Entwicklung/Git) (German)
* [Opencaching.de Wiki](https://wiki.opencaching.de/index.php/Hauptseite) (German)
  -> [Development](https://wiki.opencaching.de/index.php/Entwicklung)
* [Opencaching.de Team Blog](https://blog.opencaching.de/) (German)
* [Opencaching.de Forum](https://forum.opencaching.de/) (German and English)
  -> [Development](https://forum.opencaching-network.org/index.php?board=43.0)
* [Opencaching API project](https://github.com/opencaching/okapi)
* [Opencaching.pl project](https://github.com/opencaching/opencaching-pl)
* [Git explained](http://gitref.org/index.html)
* [old Opencaching.de/se Git Repository](https://github.com/OpencachingTeam/opencaching/)
