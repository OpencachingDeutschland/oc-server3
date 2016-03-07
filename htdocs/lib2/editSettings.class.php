<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Set template variables for displaying a page browser control.
 *  Output is formatted by templates2/<style>/res_pager.tpl.
 ***************************************************************************/

require_once('./lib2/logic/user.class.php');
require_once('./lib2/logic/coordinate.class.php');
require_once('./lib2/logic/countriesList.class.php');
require_once('lib2/logic/useroptions.class.php');


    class editSettings
    {
        //edit from old mydetails:
        function change($display_settings,$tplname)
        {
            global $tpl, $login, $opt, $redirect;
            $tplname;

            /* start old change*/
            if($tplname=="myprofile"||$tplname=="mydetails"||$tplname=="ocsettings"||$tplname == "emailsettings") {

                if (isset($_REQUEST['cancel']))
                    $tpl->redirect('myprofile.php');

                $user = new user($login->userid);

                $this->assignFromUser($user);

                $bError = false;

                // set user properties
                if (isset($_REQUEST['username'])) {
                    $tpl->assign('username', trim($_REQUEST['username']));
                    if (!$user->setUsername(trim($_REQUEST['username']))) {
                        $tpl->assign('usernameErrorInvalidChars', true);
                        $bError = true;
                    }
                }

                if (isset($_REQUEST['firstName'])) {
                    $tpl->assign('firstName', trim($_REQUEST['firstName']));
                    if (!$user->setFirstName(trim($_REQUEST['firstName']))) {
                        $tpl->assign('firstNameError', true);
                        $bError = true;
                    }
                }

                if (isset($_REQUEST['lastName'])) {
                    $tpl->assign('lastName', trim($_REQUEST['lastName']));
                    if (!$user->setLastName(trim($_REQUEST['lastName']))) {
                        $tpl->assign('lastNameError', true);
                        $bError = true;
                    }
                }

                if (isset($_REQUEST['country'])) {
                    $tpl->assign('countryCode', $_REQUEST['country']);
                    if (!$user->setCountryCode(($_REQUEST['country'] == 'XX') ? null : $_REQUEST['country'])) {
                        $tpl->assign('countryError', true);
                        $bError = true;
                    }
                }

                if (isset($_REQUEST['notifyRadius'])) {
                    $tpl->assign('notifyRadius', $_REQUEST['notifyRadius'] + 0);
                    if (!$user->setNotifyRadius($_REQUEST['notifyRadius'] + 0)) {
                        $tpl->assign('notifyRadiusError', true);
                        $bError = true;
                    }
                }

                if($tplname=="emailsettings"){
                    if (isset($_REQUEST['notifyOconly'])) {
                        $tpl->assign('notifyOconly', $_REQUEST['notifyOconly'] + 0);
                        $user->setNotifyOconly($_REQUEST['notifyOconly'] != 0);
                    }else if (isset($_REQUEST['save']))
                        $user->setNotifyOconly(false);
                }

                $oconly_helplink = helppagelink('oconly');
                $tpl->assign('oconly_helpstart', $oconly_helplink);
                $tpl->assign('oconly_helpend', $oconly_helplink != '' ? '</a>' : '');

                $coord['lat'] = coordinate::parseRequestLat('coord');
                $coord['lon'] = coordinate::parseRequestLon('coord');
                if (($coord['lat'] !== false) && ($coord['lon'] !== false)) {
                    $tpl->assign('coordsDecimal', $coord);
                    if (!$user->setLatitude($coord['lat'])) {
                        $tpl->assign('latitudeError', true);
                        $bError = true;
                    }
                    if (!$user->setLongitude($coord['lon'])) {
                        $tpl->assign('longitudeError', true);
                        $bError = true;
                    }
                }

                $bAccMailing = $user->getAccMailing();
                $tpl->assign('accMailing', $bAccMailing);
                $user->setAccMailing($bAccMailing);

                $bUsePMR = isset($_REQUEST['save'])&&$tplname=='mydetails' ? isset($_REQUEST['usePMR']) : $user->getUsePMR();
                $tpl->assign('usePMR', $bUsePMR);
                $user->setUsePMR($bUsePMR);

                $bPermanentLogin = isset($_REQUEST['save'])&&$tplname=='ocsettings' ? isset($_REQUEST['permanentLogin']) : $user->getPermanentLogin();
                $tpl->assign('permanentLogin', $bPermanentLogin);
                $user->setPermanentLogin($bPermanentLogin);

                $bNoHTMLEditor = isset($_REQUEST['save'])&&$tplname=='ocsettings' ? isset($_REQUEST['noHTMLEditor']) : $user->getNoHTMLEditor();
                $tpl->assign('noHTMLEditor', $bNoHTMLEditor);
                $user->setNoHTMLEditor($bNoHTMLEditor);

                $bUsermailSendAddress = $user->getUsermailSendAddress();
                $tpl->assign('sendUsermailAddress', $bUsermailSendAddress);
                $user->setUsermailSendAddress($bUsermailSendAddress);


                if (!$bError && isset($_REQUEST['save'])) {
                    if ($user->getAnyChanged()) {
                        if (!$user->save()) {
                            $bError = true;

                            // check for duplicate username
                            if ($user->getUsernameChanged() && user::existUsername($_REQUEST['username']))
                                $tpl->assign('errorUsernameExist', true);
                            else
                                $tpl->assign('errorUnknown', true);
                        } else
                            $redirect = true;

                    } else
                        $redirect = true;
                }

                $showAllCountries = isset($_REQUEST['showAllCountries']) ? $_REQUEST['showAllCountries'] + 0 : 0;
                if (isset($_REQUEST['showAllCountriesSubmit']))
                    $showAllCountries = 1;

                $countriesList = new countriesList();
                $rs = $countriesList->getRS($user->getCountryCode(), $showAllCountries != 0);
                $tpl->assign_rs('countries', $rs);
                sql_free_result($rs);
                if ($countriesList->defaultUsed() == true)
                    $showAllCountries = 0;
                else
                    $showAllCountries = 1;
                $tpl->assign('showAllCountries', $showAllCountries);
            }

            /* end old change*/

            if($tplname=="myprofile"||$tplname=="mydetails"||$tplname=="ocsettings") {

                foreach($display_settings as $options_type) {

                    $useroptions = new useroptions($login->userid);

                    if (isset($_REQUEST['save'])) {
                        $rs = sql('SELECT `id` FROM `profile_options` WHERE `optionset`=' . $options_type . ' ORDER BY `id`');
                        $bError = false;
                        $error = ': ';
                        $errorlen = ': ';
                        $bErrorlen = false;

                        while ($record = sql_fetch_array($rs)) {
                            $id = $record['id'];
                            $vis = isset($_REQUEST['chk' . $id]) ? $_REQUEST['chk' . $id] + 0 : 0;
                            $value = isset($_REQUEST['inp' . $id]) ? $_REQUEST['inp' . $id] : '';
                            if ($vis != 1) $vis = 0;

                            $useroptions->setOptVisible($id, $vis);
                            if (strlen($value) > 2000) {
                                $errorlen .= $useroptions->getOptName($id);
                                $bErrorlen = true;
                            } else {
                                if (!$useroptions->setOptValue($id, $value)) {
                                    $error .= $useroptions->getOptName($id) . ', ';
                                    $bError = true;
                                }
                            }
                        }

                        sql_free_result($rs);

                        $error = substr($error, 0, -2);

                        $tpl->assign('error', $bError);
                        $tpl->assign('errormsg', $error);
                        $tpl->assign('errorlen', $bErrorlen);
                        $tpl->assign('errormsglen', $errorlen);

                        if (!$useroptions->save()) {
                            $bError = true;
                            $tpl->assign('errorUnknown', true);
                        } else if (!$bError)
                            $redirect = true;
                    }
                }
            }

            if($redirect==true)
                $tpl->redirect($tplname.'.php');
            foreach($display_settings as $options_type) {
                $this->assignFromDB($login->userid,false,$options_type);
            }
            $tpl->assign('edit', true);
            $tpl->display();
        }


        function changetext($options_type)
        {
            global $tpl, $login, $opt;

            if (isset($_REQUEST['save']))
            {
                $purifier = new OcHTMLPurifier($opt);
                $desctext = isset($_REQUEST['desctext']) ? $purifier->purify($_REQUEST['desctext']) : "";
                $desc_htmledit = isset($_REQUEST['descMode']) && $_REQUEST['descMode'] == '2' ? '0' : '1';
                sql("
			UPDATE `user`
			SET `description`='&2', `desc_htmledit`='&3'
			WHERE `user_id`='&1'",
                    $login->userid, $desctext, $desc_htmledit);
                $tpl->redirect('mydetails.php');
            }
            else
            {
                $tpl->name = 'mydescription';
                $this->assignFromDB($login->userid,true,$options_type);
                $tpl->assign('scrollposx', isset($_REQUEST['scrollposx']) ? $_REQUEST['scrollposx'] + 0 : 0);
                $tpl->assign('scrollposy', isset($_REQUEST['scrollposy']) ? $_REQUEST['scrollposy'] + 0 : 0);
                $tpl->display();
            }
        }


        function display($display_settings)
        {
            global $tpl, $login;
            foreach($display_settings as $options_type){
                $this->assignFromDB($login->userid,false,$options_type);
                $user = new user($login->userid);
                $this->assignFromUser($user);
            }
            $tpl->display();
            unset($display_settings);
        }


        function assignFromDB($userid,$include_editor,$options_type)
        {
            global $tpl, $opt, $smilies, $_REQUEST;

            $rs = sql("SELECT `p`.`id`, IFNULL(`tt`.`text`, `p`.`name`) AS `name`, `p`.`default_value`,`p`.`optionset`, `p`.`check_regex`, `p`.`option_order`, `u`.`option_visible`, `p`.`internal_use`, `p`.`option_input`, IFNULL(`u`.`option_value`, `p`.`default_value`) AS `option_value`
		           FROM `profile_options` AS `p`
		      LEFT JOIN `user_options` AS `u` ON `p`.`id`=`u`.`option_id` AND (`u`.`user_id` IS NULL OR `u`.`user_id`='&1')
		      LEFT JOIN `sys_trans` AS `st` ON `p`.`trans_id`=`st`.`id` AND `p`.`name`=`st`.`text`
		      LEFT JOIN `sys_trans_text` AS `tt` ON `st`.`id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
		          WHERE `p`.`optionset`=".$options_type."
		       ORDER BY `p`.`internal_use` DESC, `p`.`option_order`",
                $userid+0,
                $opt['template']['locale']);
            $tpl->assign_rs('useroptions'.$options_type, $rs);
            sql_free_result($rs);

            if (isset($_REQUEST['desctext']))
                $tpl->assign('desctext', $_REQUEST['desctext']);
            else
                $tpl->assign('desctext',
                    sql_value("SELECT `description` FROM `user` WHERE `user_id`='&1'", '', $userid+0));

            // Use the same descmode values here like in log and cachedesc editor:
            if ($include_editor)
            {
                if (isset($_REQUEST['descMode']))
                    $descMode = min(3,max(2,$_REQUEST['descMode']+0));
                else
                {
                    if (sql_value("SELECT `desc_htmledit` FROM `user` WHERE `user_id`='&1'", 0, $userid+0))
                        $descMode = 3;
                    else
                        $descMode = 2;
                }
                if ($descMode == 3)
                {
                    $tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
                    $tpl->add_header_javascript('resource2/tinymce/config/user.js.php?lang='.strtolower($opt['template']['locale']));
                }
                $tpl->add_header_javascript(editorJsPath());
                $tpl->assign('descMode',$descMode);
            }
        }

        function assignFromUser($user)
        {
            global $tpl;

            $tpl->assign('username', $user->getUsername());
            $tpl->assign('email', $user->getEMail());
            $tpl->assign('firstName', $user->getFirstName());
            $tpl->assign('lastName', $user->getLastName());
            $tpl->assign('country', $user->getCountry());
            $tpl->assign('countryCode', $user->getCountryCode());

            $coords = new coordinate($user->getLatitude(), $user->getLongitude());
            $tpl->assign('coords', $coords->getDecimalMinutes());
            $tpl->assign('coordsDecimal', $coords->getFloat());

            $tpl->assign('notifyRadius', $user->getNotifyRadius());

            $tpl->assign('notifyOconly', $user->getNotifyOconly());
            $oconly_helplink = helppagelink('oconly');
            $tpl->assign('oconly_helpstart', $oconly_helplink);
            $tpl->assign('oconly_helpend', $oconly_helplink != '' ? '</a>' : '');

            $tpl->assign('registeredSince', $user->getDateRegistered());

            $tpl->assign('accMailing', $user->getAccMailing());

            $tpl->assign('usePMR', $user->getUsePMR());
            $tpl->assign('permanentLogin', $user->getPermanentLogin());
            $tpl->assign('noHTMLEditor', $user->getNoHTMLEditor());
            $tpl->assign('sendUsermailAddress', $user->getUsermailSendAddress());
        }
    }