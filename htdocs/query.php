<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'query';
$tpl->menuitem = MNU_MYPROFILE_QUERIES;

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect('login.php?target=query.php');
}

if ($action == 'save') {
    $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] + 0 : 0;
    $sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : false;
    $sortorder = isset($_REQUEST['sortorder']) ? $_REQUEST['sortorder'] : false;
    $creationdate = isset($_REQUEST['creationdate']) ? $_REQUEST['creationdate'] : false;
    $queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
    $submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;

    savequery($queryid, $queryname, false, $submit, 0, $sortby, $sortorder, $creationdate);
} elseif ($action == 'saveas') {
    $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] + 0 : 0;
    $sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : false;
    $sortorder = isset($_REQUEST['sortorder']) ? $_REQUEST['sortorder'] : false;
    $creationdate = isset($_REQUEST['creationdate']) ? $_REQUEST['creationdate'] : false;
    $queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
    $submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;
    $oldqueryid = isset($_REQUEST['oldqueryid']) ? $_REQUEST['oldqueryid'] + 0 : 0;

    savequery($queryid, $queryname, true, $submit, $oldqueryid, $sortby, $sortorder, $creationdate);
} elseif ($action == 'delete') {
    $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] + 0 : 0;
    deletequery($queryid);
} else { // default: view
    viewqueries();
}

function deletequery($queryid)
{
    global $tpl, $login;

    sql("DELETE FROM `queries` WHERE `id`='&1' AND `user_id`='&2' LIMIT 1", $queryid, $login->userid);

    $tpl->redirect('query.php?action=view');
}

function viewqueries()
{
    global $tpl, $login;

    $tpl->assign('action', 'view');

    $rs = sql("SELECT `id`, `name` FROM `queries` WHERE `user_id`='&1' ORDER BY `name` ASC", $login->userid);
    $tpl->assign_rs('queries', $rs);
    sql_free_result($rs);

    $tpl->display();
}

function savequery($queryid, $queryname, $saveas, $submit, $saveas_queryid, $sortby, $sortorder, $creationdate)
{
    global $login, $tpl;

    if ($submit == true) {
        $options = sql_value("SELECT `options` FROM `queries` WHERE `id`='&1'", false, $queryid);
        if (!$options) {
            $tpl->error(ERROR_UNKNOWN);   // query does not exist
        } elseif ($sortby || $sortorder || $creationdate) {
            $oa = unserialize($options);
            if ($sortby) {
                $oa['sort'] = $sortby;
            }
            if ($sortorder) {
                $oa['sortorder'] = $sortorder;
            }
            if ($creationdate) {
                $oa['creationdate'] = $creationdate;
            }
            $options = serialize($oa);
        }

        if ($saveas == false) {
            $bError = false;
            if ($queryname == '') {
                $tpl->assign('errorEmptyName', true);
                $bError = true;
            }

            if (sql_value(
                "SELECT COUNT(*)
                 FROM `queries`
                 WHERE `name`='&1' AND `user_id`='&2'",
                0,
                $queryname,
                $login->userid
            ) > 0) {
                $tpl->assign('errorNameExists', true);
                $bError = true;
            }

            if ($bError == false) {
                // save
                sql(
                    "UPDATE `queries`
                     SET `user_id`='&1', `name`='&2', `options`='&4'
                     WHERE `id`='&3'",
                    $login->userid,
                    $queryname,
                    $queryid,
                    $options
                );
                $tpl->redirect('query.php?action=view');
            }
        } else {
            // save as
            if (sql_value(
                "SELECT COUNT(*)
                 FROM `queries`
                 WHERE `id`='&1' AND `user_id`='&2'",
                0,
                $saveas_queryid,
                $login->userid
            ) == 0) {
                $tpl->assign('errorMustSelectQuery', true);
            } else {
                sql("UPDATE `queries` SET `options`='&1' WHERE `id`='&2'", $options, $saveas_queryid);
                $tpl->redirect('query.php?action=view');
            }
        }
    }

    $rs = sql(
        "SELECT `id`, `name`
         FROM `queries`
         WHERE `user_id`='&1'
         ORDER BY `name` ASC",
        $login->userid
    );
    $tpl->assign_rs('queries', $rs);
    sql_free_result($rs);

    $tpl->assign('queryid', $queryid);
    $tpl->assign('queryname', $queryname);
    $tpl->assign('sortby', $sortby);
    $tpl->assign('sortorder', $sortorder);
    $tpl->assign('creationdate', $creationdate);

    $tpl->assign('action', 'save');
    $tpl->display();
}
