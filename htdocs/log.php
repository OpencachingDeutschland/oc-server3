<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
	
	// prevent old OCProp versions
	if ((isset($_POST['submit']) || isset($_POST['submitform'])) && !isset($_POST['version3']))
		die('Your client may be outdated!');
	
	// include librarys
	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/user.class.php');
	require_once('./lib2/logic/cachelog.class.php');
	require_once('./lib2/OcHTMLPurifier.class.php');
	
	// prepare template and menue
	$tpl->name = 'log_cache';
	$tpl->menuitem = MNU_CACHES_LOG;
	$tpl->caching = false;
	
	// check login
	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();
	
	// get cache_id if not given
	$cacheId = 0;
	if (isset($_REQUEST['wp']))
		$cacheId = cache::cacheIdFromWP($_REQUEST['wp']);
	else if (isset($_REQUEST['cacheid']))  // Ocprop
		$cacheId = $_REQUEST['cacheid'];
	
	// check adminstatus of user
	$useradmin = ($login->hasAdminPriv()) ? 1 : 0;
	
	// prepare array to indicate errors in template
	$validate = array();
	
	// proceed loggable, if valid cache_id
	$validate['logAllowed'] = true;
	if ($cacheId != 0)
	{
		// get cache object
		$cache = new cache($cacheId);
		
		// check log allowed, depending on cache state and logged in user
		$validate['logAllowed'] = $cache->allowLog();
				
		// get user object
		$user = new user($login->userid);
		// is user cache owner
		$isOwner = ($user->getUserId() == $cache->getUserId());
		
		// assing ratings to template
		$tpl->assign('ratingallowed', $user->allowRatings());
		$tpl->assign('givenratings', $user->getGivenRatings());
		$tpl->assign('maxratings', $user->getMaxRatings());
		$tpl->assign('israted', $cache->isRecommendedByUser($user->getUserId()));
		$tpl->assign('foundsuntilnextrating', $user->foundsUntilNextRating());
		$tpl->assign('isowner', $isOwner);
		
		// check and prepare form values
		$datesaved = isset($_COOKIE['oclogdate']);
		if ($datesaved)
		{
			$defaultLogYear  = substr($_COOKIE['oclogdate'],0,4);
			$defaultLogMonth = substr($_COOKIE['oclogdate'],4,2);
			$defaultLogDay   = substr($_COOKIE['oclogdate'],6,2);
		}
		
		// check if masslog warning is accepted (in cookie)
		$masslogCookieSet = isset($_COOKIE['ocsuppressmasslogwarn']);
		if ($masslogCookieSet)
			$masslogCookieContent = $_COOKIE['ocsuppressmasslogwarn'] + 0;
		else
		{
			// save masslog acception in cookie that expires on midnight if clicked
			if (isset($_REQUEST['suppressMasslogWarning']) && $_REQUEST['suppressMasslogWarning'] == 1)
				setcookie('ocsuppressmasslogwarn', '1', strtotime('tomorrow'));
		}

		// Ocprop:
		//   logtext, logtype, logday, logmonth, logyear

		$logText                = (isset($_POST['logtext']))                   ? ($_POST['logtext'])                 : '';
		$logType                = (isset($_REQUEST['logtype']))                ? ($_REQUEST['logtype']+0)            : null;
		$logDateDay             = (isset($_POST['logday']))                    ? trim($_POST['logday'])              : ($datesaved ? $defaultLogDay   : date('d'));
		$logDateMonth           = (isset($_POST['logmonth']))                  ? trim($_POST['logmonth'])            : ($datesaved ? $defaultLogMonth : date('m'));
		$logDateYear            = (isset($_POST['logyear']))                   ? trim($_POST['logyear'])             : ($datesaved ? $defaultLogYear  : date('Y'));
		$logTimeHour            = (isset($_POST['loghour']))                   ? trim($_POST['loghour'])             : "";
		$logTimeMinute          = (isset($_POST['logminute']))                 ? trim($_POST['logminute'])           : "";
		$rateOption             = (isset($_POST['ratingoption']))              ? $_POST['ratingoption']+0            : 0;
		$rateCache              = (isset($_POST['rating']))                    ? $_POST['rating']+0                  : 0;
		$ocTeamComment          = (isset($_REQUEST['teamcomment']))            ? $_REQUEST['teamcomment'] != 0       : 0;
		$suppressMasslogWarning = (isset($_REQUEST['suppressMasslogWarning'])) ? $_REQUEST['suppressMasslogWarning'] : ($masslogCookieSet ? $masslogCookieContent : 0);
		
		// if not a found log, ignore the rating
		$rateOption = ($logType == 1 || $logType == 7) + 0;
		
		// get logtext editormode (from form or from userprofile)
		// 2 = HTML; 3 = tinyMCE 
		if (isset($_POST['descMode']))
			$descMode = $_POST['descMode']+0;  // Ocprop: 2
		else
			if ($user->getNoWysiwygEditor() == 1)
				$descMode = 2;
			else
				$descMode = 3;
		if (($descMode < 1) || ($descMode > 3))
			$descMode = 3;
		// add javascript-header if editor
		if ($descMode == 3)
		{
			$tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
			$tpl->add_header_javascript('resource2/tinymce/config/log.js.php?lang='.strtolower($opt['template']['locale']));
		}
		$tpl->add_header_javascript('templates2/' . $opt['template']['style'] . '/js/editor.js');
		
		// check and prepare log text
		if ($descMode != 1)
		{
			$ocPurifier = new OcHTMLPurifier($opt);
			$logText = $ocPurifier->purify($logText);
		}
		else
			$logText = nl2br(htmlspecialchars($logText, ENT_COMPAT, 'UTF-8'));
		
		// validate date
		if (is_numeric($logDateMonth)
			&& is_numeric($logDateDay)
			&& is_numeric($logDateYear)
			&& ($logTimeHour . $logTimeMinute == "" || is_numeric($logTimeHour))
			&& ($logTimeMinute == "" || is_numeric($logTimeMinute)))
		{
			$validate['dateOk'] = checkdate(	$logDateMonth, $logDateDay, $logDateYear)
											&& ($logDateYear >= 2000) 
											&& ($logTimeHour>=0)
											&& ($logTimeHour<=23)
											&& ($logTimeMinute>=0)
											&& ($logTimeMinute<=59);
			if ($validate['dateOk'] && isset($_POST['submitform']))
				$validate['dateOk'] = (mktime(	$logTimeHour+0,
											$logTimeMinute+0,
											0,
											$logDateMonth,
											$logDateDay,
											$logDateYear) < time());
		}
		else
			$validate['dateOk'] = false;

		// store valid date in temporary cookie; it will be the default for the next log
		if ($validate['dateOk'])
			setcookie('oclogdate',
			          sprintf('%04d%02d%02d',$logDateYear, $logDateMonth, $logDateDay),
			          time() + 12*60*60);   // expiration time
		
		// check log type
		$validate['logType'] = $cache->logTypeAllowed($logType);
		
		// check log password
		$validate['logPw'] = true;
		if (isset($_POST['submitform']) && $cache->requireLogPW())
			$validate['logPw'] = $cache->validateLogPW($logType, $_POST['log_pw']);
		
		// check for errors
		$loggable = array_product($validate);
		
		// prepare duplicate log error
		$validate['duplicateLog'] = true;
		
		// all checks done, no error => log
		if (isset($_POST['submitform']) && $loggable)  // Ocprop
		{
			/*
			 * check if time is logged
			 * set seconds 00:00:01, means "00:00 was logged"
			 * set seconds 00:00:00, means "no time was logged"
			 */
			$logTimeSecond = ($logTimeHour . $logTimeMinute != ""
								&& $logTimeHour == 0
								&& $logTimeMinute == 0) + 0;
			
			// make time values database ready
			$logDate = date($opt['db']['dateformat'],
							mktime(	$logTimeHour+0,
									$logTimeMinute+0,
									$logTimeSecond,
									$logDateMonth,
									$logDateDay,
									$logDateYear));
			
			// check if duplicate entry already exists (sending form multiple times, or OCProp error)
			if (!cachelog::isDuplicate($cache->getCacheId(), $user->getUserId(), $logType, $logDate, $logText))
			{
				// get new cachelog object
				$cacheLog = cachelog::createNewFromCache($cache, $user->getUserId());
				
				// set values
				$cacheLog->setType($logType);
				$cacheLog->setDate($logDate);
				$cacheLog->setText($logText);
				$cacheLog->setTextHtmlEdit(($descMode == 3) ? 1 : 0);
				$cacheLog->setOcTeamComment($ocTeamComment);
				
				// save log values
				$cacheLog->save();
				
				// update cache status
				$cache->updateCacheStatus($logType);
				
				// update rating (if correct logtype, user has ratings to give and is not owner (exept owner adopted cache))
				if ($rateOption && $user->allowRatings() && (!$isOwner || ($isOwner && $cache->hasAdopted($user->getUserId()))))
					if ($rateCache)
						$cache->addRecommendation($user->getUserId(), $logDate);
					else
						$cache->removeRecommendation($user->getUserId());
				
				// save cache
				$cache->save();
				
				// clear statpic
				$statPic = $user->getStatpic();
				$statPic->deleteFile();
			}

			// finished, redirect to listing
			$tpl->redirect('viewcache.php?cacheid=' . $cache->getCacheId());
		}
		
		// assign values to template
		// error
		$tpl->assign('validate', $validate);
		// user info
		$tpl->assign('userFound', $user->getStatFound());
		// cache infos
		$tpl->assign('cachename', $cache->getName());
		$tpl->assign('cacheid', $cache->getCacheId());
		$tpl->assign('cachetype', $cache->getType());
		// date/time
		$tpl->assign('logday', $logDateDay);
		$tpl->assign('logmonth', $logDateMonth);
		$tpl->assign('logyear', $logDateYear);
		$tpl->assign('loghour', $logTimeHour);
		$tpl->assign('logminute', $logTimeMinute);
		// log text
		$tpl->assign('logtext', $logText);
		// text, <html> or editor
		$tpl->assign('descMode', $descMode);
		// logtypes
		$tpl->assign('logtypes', $cache->getUserLogTypes($logType));
		// teamcomment
		$tpl->assign('octeamcommentallowed', $cache->teamcommentAllowed(3));
		$tpl->assign('octeamcomment', ($ocTeamComment || (!$cache->statusUserLogAllowed() && $useradmin)) ? true : false);
		$tpl->assign('octeamcommentclass', (!$cache->statusUserLogAllowed() && $useradmin) ? 'redtext' : '');
		// masslogs
		$tpl->assign('masslogCount', $opt['logic']['masslog']['count']);
		$tpl->assign('masslog', cachelog::isMasslogging($user->getUserId()) && $suppressMasslogWarning == 0);
		// show number of found on log page
		$tpl->assign('showstatfounds', $user->showStatFounds());
		$tpl->assign('logpw', $cache->requireLogPW());
	}
	else
	{
		// not loggable
		$validate['logAllowed'] = false;
	}
	
	// prepare template and display
	$tpl->assign('validate', $validate);
	$tpl->display();
?>
