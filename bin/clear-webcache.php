#!/usr/bin/php
<?php
	/*  refresh cached files and translations
	 *
	 *  stop/start apache
	 *  delete/recreate cached files 
	 *
	 *  TODO: quick&dirty to test functionality
	 */

	$opt['rootpath'] = '../htdocs/';
	require($opt['rootpath'] . 'lib2/web.inc.php');

	// stop apache
	system($opt['httpd']['stop']);

	echo "Delete cached files\n";
	clearCache();

	echo "Create translation files for gettext()\n";
	$translationHandler->createMessageFiles();

	echo "Create menu cache file\n";
	createMenuCache();

	echo "Create label cache file\n";
	createLabelCache();

	echo "Precompiling template files\n";
	precompileAllTemplates();

	// start apache
	system($opt['httpd']['start']);

function precompileAllTemplates()
{
	global $opt;

	if ($hDir = opendir($opt['stylepath']))
	{
		while (($sFilename = readdir($hDir)) !== false)
		{
			if (substr($sFilename, -4) == '.tpl')
			{
				//echo substr($sFilename, 0, strlen($sFilename) - 4) . "\n";
				precompileTemplate(substr($sFilename, 0, strlen($sFilename) - 4));
			}
		}
		closedir($hDir);
	}

	// fix file ownership
	$sCompileDir = $opt['rootpath'] . 'cache2/smarty/compiled/';
	if ($hDir = opendir($sCompileDir))
	{
		while (($sFilename = readdir($hDir)) !== false)
		{
			if (filetype($sCompileDir . $sFilename) == 'file')
			{
				chown($sCompileDir . $sFilename, $opt['httpd']['user']);
				chgrp($sCompileDir . $sFilename, $opt['httpd']['group']);
			}
		}
		closedir($hDir);
	}
}

function precompileTemplate($sTemplate)
{
	global $opt;

	foreach ($opt['locale'] AS $sLanguage => $v)
	{
		precompileTemplateWithLanguage($sTemplate, $sLanguage);
	}
}

function precompileTemplateWithLanguage($sTemplate, $sLanguage)
{
	global $opt, $translate;

	// cheating a little bit
	$opt['template']['locale'] = $sLanguage;
	setlocale(LC_MONETARY, $opt['locale'][$opt['template']['locale']]['locales']);
	setlocale(LC_TIME, $opt['locale'][$opt['template']['locale']]['locales']);
	if (defined('LC_MESSAGES'))
		setlocale(LC_MESSAGES, $opt['locale'][$opt['template']['locale']]['locales']);

	if ($translate->t('INTERNAL_LANG', 'all', 'OcSmarty.class.php', '') != $sLanguage)
	{
		die("setlocale() failed to set language to " . $sLanguage . ". Is the translation of INTERNAL_LANG correct?\n");
	}

	$preTemplate = new OcSmarty();
	$preTemplate->name = $sTemplate;
	$preTemplate->compile($sTemplate . '.tpl', $preTemplate->get_compile_id());
}

function createMenuCache()
{
	global $opt, $translate;

	foreach ($opt['locale'] AS $sLanguage => $v)
	{
		// cheating a little bit
		$opt['template']['locale'] = $sLanguage;
		setlocale(LC_MONETARY, $opt['locale'][$opt['template']['locale']]['locales']);
		setlocale(LC_TIME, $opt['locale'][$opt['template']['locale']]['locales']);
		if (defined('LC_MESSAGES'))
			setlocale(LC_MESSAGES, $opt['locale'][$opt['template']['locale']]['locales']);

		if ($translate->t('INTERNAL_LANG', 'all', 'OcSmarty.class.php', '') != $sLanguage)
		{
			die("setlocale() failed to set language to " . $sLanguage . ". Is the translation of INTERNAL_LANG correct?\n");
		}

		// this will create the cache file
		$menu = new Menu();

		// change to file owner
		chown($menu->sMenuFilename, $opt['httpd']['user']);
		chgrp($menu->sMenuFilename, $opt['httpd']['group']);
	}
}

function createLabelCache()
{
	global $opt;

	foreach ($opt['locale'] AS $sLanguage => $v)
	{
                // cheating a little bit
                $opt['template']['locale'] = $sLanguage;

		labels::CreateCacheFile();

		// change to file owner
		$sFilename = $opt['rootpath'] . 'cache2/labels-' . $opt['template']['locale'] . '.inc.php';
		chown($sFilename, $opt['httpd']['user']);
		chgrp($sFilename, $opt['httpd']['group']);
	}
}

function clearCache()
{
	global $tpl, $translang, $translate;

	unlinkFiles('cache2', 'php');

	unlinkFiles('cache2/smarty/cache', 'tpl');
	unlinkFiles('cache2/smarty/compiled', 'inc');
	unlinkFiles('cache2/smarty/compiled', 'php');
	unlinkFiles('cache2/captcha', 'jpg');
	unlinkFiles('cache2/captcha', 'txt');

}

function unlinkFiles($relbasedir, $ext)
{
	global $opt;

	if (substr($relbasedir, -1, 1) != '/')
		$relbasedir .= '/';

	if ($opt['rootpath'] . $relbasedir)
	{
		if ($dh = opendir($opt['rootpath'] . $relbasedir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if ($file != '.' && $file != '..' && is_file($opt['rootpath'] . $relbasedir . $file))
				{
					if (substr($file, -(strlen($ext)+1), strlen($ext)+1) == '.' . $ext)
					{
						unlink($opt['rootpath'] . $relbasedir . $file);
					}
				}
			}
		}
		closedir($dh);
	}
}
?>