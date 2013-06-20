<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
	
	// prevent old OCProp versions
	if ((isset($_POST['submit']) || isset($_POST['submitform'])) && !isset($_POST['version3']))
		die('Your client may be outdated!');
	
	// use purifier in lib2
	define('PURIFIERLIB2', true);
	
	// include librarys
	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/user.class.php');
	require_once('./lib2/logic/cachelog.class.php');
	require_once('./lib2/OcHTMLPurifier.class.php');
	
	// prepare template and menue
	$tpl->name = 'log_cache';
	$tpl->menuitem = MNU_CACHES_SEARCH_VIEWCACHE;
	$tpl->caching = false;
	
	// check login
	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();
	
	// get cache_id if not given
	$cacheId = 0;
	if (isset($_REQUEST['wp']))
		$cacheId = cache::cacheIdFromWP($_REQUEST['wp']);
	else if (isset($_REQUEST['cacheid']))
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
		$logText        = (isset($_POST['logtext']))        ? ($_POST['logtext'])           : '';
		$logType        = (isset($_POST['logtype']))        ? ($_POST['logtype']+0)         : null;
		$logDateDay     = (isset($_POST['logday']))         ? trim($_POST['logday'])        : date('d');
		$logDateMonth   = (isset($_POST['logmonth']))       ? trim($_POST['logmonth'])      : date('m');
		$logDateYear    = (isset($_POST['logyear']))        ? trim($_POST['logyear'])       : date('Y');
		$logTimeHour    = (isset($_POST['loghour']))        ? trim($_POST['loghour'])       : "";
		$logTimeMinute  = (isset($_POST['logminute']))      ? trim($_POST['logminute'])     : "";
		$rateOption     = (isset($_POST['ratingoption']))   ? $_POST['ratingoption']+0      : 0;
		$rateCache      = (isset($_POST['rating']))         ? $_POST['rating']+0            : 0;
		$ocTeamComment  = (isset($_REQUEST['teamcomment'])) ? $_REQUEST['teamcomment'] != 0 : 0;
		
		// if not a found log, ignore the rating
		$rateOption = ($logType == 1 || $logType == 7) + 0;
		
		// get logtext editormode (from form or from userprofile)
		// 1 = text; 2 = HTML; 3 = tinyMCE 
		if (isset($_POST['descMode']))
			$descMode = $_POST['descMode']+0;
		else
		{
			if ($user->getNoHTMLEditor() == 1)
				$descMode = 1;
			else
				$descMode = 3;
		}
		if (($descMode < 1) || ($descMode > 3))
			$descMode = 3;
		// add javascript-header if editor
		if ($descMode == 3)
		{
			$tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
			$tpl->add_header_javascript('resource2/tinymce/config/user.js.php?lang='.strtolower($opt['template']['locale']));
		}
		
		// check and prepare log text
		if ($descMode != 1)
		{
			$ocPurifier = new OcHTMLPurifier();
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
		if (isset($_POST['submitform']) && $loggable)
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
				$cacheLog->setTextHtml(($descMode != 1) ? 1 : 0);
				$cacheLog->setTextHtmlEdit(($descMode == 3) ? 1 : 0);
				
				// save log values
				$cacheLog->save();
				
				// update cache status
				$cache->updateCacheStatus($logType);
				
				// update rating (if correct logtype, user has ratings to give and is not owner)
				if ($rateOption && $user->allowRatings() && !$isOwner)
					if ($rateCache)
						$cache->addRecommendation($user->getUserId());
					else
						$cache->removeRecommendation($user->getUserId());
				
				// save cache
				$cache->save();
				
				// clear statpic
				$statPic = $user->getStatpic();
				$statPic->deleteFile();
				
				// finished, redirect to listing
				$tpl->redirect('viewcache.php?cacheid=' . $cache->getCacheId());
			}
			else
			{
				$validate['duplicateLog'] = false;
			}
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
		$tpl->assign('octeamcomment', ($ocTeamComment || (!$cache->allowLog() && $useradmin)) ? true : false);
		$tpl->assign('octeamcommentclass', (!$cache->allowLog() && $useradmin) ? 'redtext' : '');
		// masslogs
		$tpl->assign('masslog', cachelog::isMasslogging($user->getUserId()));
		
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
