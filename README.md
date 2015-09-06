Opencaching.de Code Repository
==============================

[Opencaching.de](http://www.opencaching.de) is a major Geocaching website in Germany.
This repository contains the website's code, including all third-party libraries
needed to run it. It is one of two major
[Opencaching code forks](http://wiki.opencaching.de/index.php/Datei:Codegenerationen.png); 
the other one is [Opencaching.pl](http://code.google.com/p/opencaching-pl/). Feel free to use it under the provided
[license terms](https://github.com/OpencachingDeutschland/oc-server3/blob/master/doc/license.txt)
for setting up your own open and free Geocaching listing service! The code can easily be
translated to other languages.

Your contributions to this project are welcome - you may contact the team in the
[Opencaching network forum](http://forum.opencaching-network.org/) if you like to
join us. Development is usually done on a VirtualBox Linux system that you can run on your
Linux, Windows or Mac workstation, but you may also try to set up the code directly
on your Linux machine.

This repo contains three branches:
* the *stable* branch with current Opencaching.de production code
* the *master* branch, basis of all development, which contains code that will be released with the next site update
* the *next* branch with experimental features to be tested.

The *next* branch is now and then resetted to master state and rebuilt from there,
so do not derive any working-branches from it. Use master instead.

Major OC.de site updates are version-tagged. See the [changelog](http://www.opencaching.de/articles.php?page=changelog&locale=EN)
for a detailed list.

Contributions
-------------
Contributing code to Opencaching.de is easy:
* Sign up to Github and [install Git](https://help.github.com/articles/set-up-git),
* create a personal fork of this repository using the Fork button above,
* clone the fork to your development machine,
* create a feature branch based on master,
* edit and commit code,
* push your feature branch to your fork and issue a pull request.

Your code will be reviewed, eventually merged to master and put online with the next site update.
Small changes may be directly released via stable branch.

To update your working copy, add this repo as upstream ...
* git remote add upstream https://github.com/OpencachingDeutschland/oc-server3.git

... and regularly update your clone:
* git checkout master
* git pull upstream

NEVER use pull on a feature branch, but pull to master and then rebase the feature branch
on master:
* git checkout feature-branch
* git rebase master

Related Websites
----------------
* [Opencaching.de development todo list](http://redmine.opencaching.de/projects/oc-dev)
* [Opencaching.de Git tutorial](http://wiki.opencaching.de/index.php/Entwicklung/Git) (German)
* [Opencaching.de Wiki](http://wiki.opencaching.de/index.php/Hauptseite) (German) -> [Development](http://wiki.opencaching.de/index.php/Entwicklung)
* [Opencaching.de Team Blog](http://blog.opencaching.de/) (German)
* [Opencaching Network Forum](http://forum.opencaching-network.org/) (German and English) -> [Development](http://forum.opencaching-network.org/index.php?board=43.0)
* [Opencaching API project](https://github.com/opencaching/okapi)
* [Opencaching.pl project](https://github.com/opencaching/opencaching-pl)
* [Git explained](http://gitref.org/index.html)
* [old Opencaching.de/se Git Repository](https://github.com/OpencachingTeam/opencaching/)
