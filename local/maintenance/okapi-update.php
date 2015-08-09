<?php
 /***************************************************************************
 *  For license information see doc/license.txt
 *
 *  This script replicates the current OKAPI revision into the current
 *  OC code branch (Git -> Git). Please do not confuse with OKAPI's internal 
 *  update mechanism, which is called via bin/dbupdate.php.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../htdocs';
require_once(__DIR__ . '/okapi-update-settings.inc.php');

function git($args)
{
  return exec('git ' . $args);
}


// validate settings, Git health and OKAPI repo state

echo "[okapi-update] validating settings and local OKAPI repo\n"; 

if (!@chdir(OKAPI_SOURCE_PATH))
  die("[okapi-update] bad OKAPI_SOURCE_PATH setting");
if (git('rev-list HEAD --count') < 700)
  die("[okapi-update] bad git configuration");
if (git("diff HEAD"))
  die("[okapi-update] OKAPI working branch is dirty");

$current_okapi_branch = git('rev-parse --abbrev-ref HEAD');
if ($current_okapi_branch != 'master')
{
  echo "[okapi-update] checking out OKAPI master branch\n";
  echo okapi_git('checkout master');
}
$changes = git("log upstream/master..master");
if ($changes)
  die("[okapi-update] there are unpushed local commits:\n" . $changes);

echo "[okapi-update] ok.\n";


// fetch current OKAPI revision

echo "[okapi-update] pulling and replicating OKAPI master\n";
echo git('pull ' . OKAPI_REMOTE . ' master') . "\n";
$okapi_version_number = git('rev-list HEAD --count') + 318;
$okapi_git_revision = git('rev-parse HEAD');
echo "OKAPI ver. $okapi_version_number\n";
echo "OKAPI rev. $okapi_git_revision\n";

// replicate OKAPI source code into OC code and inject version numbers

passthru(str_replace('%source', OKAPI_SOURCE_PATH . '/okapi',
         str_replace('%dest', $opt['rootpath'] . '/okapi', DIRECTORY_TREE_REPLICATOR)));
$core = file_get_contents($opt['rootpath'] . '/okapi/core.php');
$core = str_replace("\$version_number = null", "\$version_number = " . $okapi_version_number,  
        str_replace("\$git_revision = null", "\$git_revision = '" . $okapi_git_revision . "'",
				$core));
file_put_contents($opt['rootpath'] . '/okapi/core.php', $core);
chdir($opt['rootpath']);
passthru("git status");

// commit changes to OC code

echo "[okapi-update] committing to local OC Git repo\n";
echo git('add .') . "\n";
echo git('commit -m "OKAPI r' . $okapi_version_number . '"') . "\n";

?>

