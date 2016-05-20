<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  This module includes all database function necessary to do queries from
 *  the database.
 ***************************************************************************/

/***************************************************************************
 * Overview:
 *
 * sql_enable_debugger()           ... enables Sqldebug if not not already done by config
 *
 * sql($sql)                       ... Query SQL and return result
 * sql_escape($value)              ... Escape parameter for SQL-use
 * sql_escape_backtick($value)     ... escape $value for use within backticks
 * sql_value($sql, $default)       ... Query SQL and return first row of first line
 * sql_fetch_array($rs)            ... mysql_fetch_array with charset conversion
 * sql_fetch_assoc($rs)            ... mysql_fetch_assoc with charset conversion
 * sql_fetch_row($rs)              ... mysql_fetch_row with charset conversion
 * sql_fetch_column($rs)           ... fetch column with charset conversion
 * sql_fetch_assoc_table($rs)      ... fetch_assoc for all rows
 * sql_temp_table($table)          ... registers an placeholder for use as temporary
 * table and drop's temporary tables if
 * mysql_pconnect is used
 * sql_drop_temp_table($table)     ... unregisters and drops an tmp-table placeholder
 * sql_free_result($rs)            ... mysql_free_result
 * sql_affected_rows()             ... mysql_affected_rows
 * sql_insert_id()                 ... mysql_insert_id
 * sql_num_rows($rs)               ... mysql_num_rows
 * sql_export_recordset($f, $rs)   ... export recordset to file
 * sql_export_table($f, $table)    ... export table to file
 * sql_export_table_to_file($filename, $table)
 *
 * sql_table_exists                ... tests if a table exists
 * sql_field_exists                ... tests if a table and a field in this table exist
 * sql_field_type                  ... queries the type of a field (uppercase letters)
 * sql_index_exists                ... tests if a table and an index of this table exist
 *
 * // slave query functions
 * sql_slave_exclude()             ... do not use slave servers for the current user
 * until the slaves have replicated to this point
 * (e.g. after a new cache was hidden)
 * sql_slave($sql)
 * sql_value_slave($sql, $default)
 * sql_temp_table_slave($table)
 * sql_drop_temp_table_slave($table)
 * sql_affected_rows_slave()
 * sql_insert_id_slave()
 * sql_connect_anyslave()
 * sql_connect_slave($id)
 * sqlf_slave($sql)
 *
 * // for sqldebugger
 * sqlf($sql)                    ... sql for framwork functions
 * sqll($sql)                    ... sql for business layer functions
 * sqlf_value($sql, $default)    ... sql_value for framwork functions
 * sqll_value($sql, $default)    ... sql_value for business layer functions
 *
 * // only for internal use      ... invoked automatically
 * sql_connect()                 ... connect to the database
 * sql_disconnect()              ... disconnect database
 * sql_disconnect_slave()        ... disconnect slave database
 * sql_error()                   ... report an error and stop processing
 * sql_warn($warnmessage)        ... report a warning and resume processing
 *
 * // for maintenance functions
 * sql_connect_maintenance()       ... connect the database with more privileges
 * sql_dropFunction                ... drops stored function
 * sql_dropProcedure               ... drops stored procedure
 * sql_dropTrigger                 ... drops stored trigger
 ***************************************************************************/

$db['connected'] = false;
$db['debug'] = (($opt['debug'] & DEBUG_SQLDEBUGGER) == DEBUG_SQLDEBUGGER);
$db['dblink'] = false;
$db['dblink_slave'] = false;
$db['slave_id'] = - 1;
$db['slave_server'] = '';
$db['temptable_initialized'] = false;
$db['temptables'] = array();
$db['temptables_slave'] = array();
$db['mode'] = DB_MODE_USER;
$db['error'] = false;

function sql_enable_debugger()
{
    global $opt, $db;

    $opt['debug'] |= DEBUG_SQLDEBUGGER;
    $db['debug'] = true;
}

/*
    sql("SELECT id FROM &tmpdb.table WHERE a=&1 AND &tmpdb.b='&2'", 12345, 'abc');

    Important: only use ASCII charecters with ord() < 128 in your queries, otherwise
               they will fail when $opt['charset']['mysql'] is not UTF-8.
               However, parameters pased with &x or escaped with sql_escape will be
               converted from UTF-8 to $opt['charset']['iconv'].

    returns: recordset or false
*/
function sql($sql)
{
    global $db;

    // establish db connection
    if ($db['connected'] != true) {
        sql_connect();
    }

    // prepare args
    $args = func_get_args();
    unset($args[0]);

    if (isset($args[1]) && is_array($args[1])) {
        $tmp_args = $args[1];
        unset($args);

        // correct indizes
        $args = array_merge(array(0), $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    return sql_internal($db['dblink'], $sql, $args);
}

function sql_slave($sql)
{
    global $db;

    if ($db['dblink_slave'] === false) {
        sql_connect_anyslave();
    }

    // prepare args
    $args = func_get_args();
    unset($args[0]);

    if (isset($args[1]) && is_array($args[1])) {
        $tmp_args = $args[1];
        unset($args);

        // correct indizes
        $args = array_merge(array(0), $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    return sql_internal($db['dblink_slave'], $sql, $args);
}

function sql_internal($dblink, $sql)
{
    global $opt, $db, $sqldebugger;

    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);

    /* as an option, you can give as second parameter an array
     * with all values for the placeholder. The array has to be
     * with numeric indizes.
     */
    if (isset($args[2]) && is_array($args[2])) {
        $tmp_args = $args[2];
        unset($args);

        // correct indizes
        $args = array_merge(array(0), $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    $sqlpos = 0;
    $filtered_sql = '';

    // replace every &x in $sql with the placeholder or parameter
    $nextarg = strpos($sql, '&');
    while ($nextarg !== false) {
        // & escaped?
        $escapesCount = 0;
        while ((($nextarg - $escapesCount - 1) > 0) && (substr($sql, $nextarg - $escapesCount - 1, 1) == '\\')) {
            $escapesCount ++;
        }
        if (($escapesCount % 2) == 1) {
            $nextarg ++;
        } else {
            $nextchar = substr($sql, $nextarg + 1, 1);
            if (is_numeric($nextchar)) {
                $arglength = 0;
                $arg = '';

                // find next non-digit
                while (preg_match('/^[0-9]{1}/', $nextchar) == 1) {
                    $arg .= $nextchar;

                    $arglength ++;
                    $nextchar = substr($sql, $nextarg + $arglength + 1, 1);
                }

                // ok ... replace
                $filtered_sql .= substr($sql, $sqlpos, $nextarg - $sqlpos);
                $sqlpos = $nextarg + $arglength;

                if (isset($args[$arg])) {
                    if (is_numeric($args[$arg])) {
                        $filtered_sql .= $args[$arg];
                    } else {
                        if ((substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (substr($sql, $sqlpos + 1, 1) == '\'')) {
                            $filtered_sql .= sql_escape($args[$arg]);
                        } elseif ((substr($sql, $sqlpos - $arglength - 1, 1) == '`') && (substr($sql, $sqlpos + 1, 1) == '`')) {
                            $filtered_sql .= sql_escape_backtick($args[$arg]);
                        } else {
                            sql_error($sql);
                        }
                    }
                } else {
                    // NULL
                    if ((substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (substr($sql, $sqlpos + 1, 1) == '\'')) {
                        // strip apostroph and insert NULL
                        $filtered_sql = substr($filtered_sql, 0, strlen($filtered_sql) - 1);
                        $filtered_sql .= 'NULL';
                        $sqlpos ++;
                    } else {
                        $filtered_sql .= 'NULL';
                    }
                }

                $sqlpos ++;
            } else {
                $arglength = 0;
                $arg = '';

                // find next non-alphanumeric char
                // (added '_' - it is used in temptable names - following 2013/07/18)
                while (preg_match('/^[a-zA-Z0-9_]{1}/', $nextchar) == 1) {
                    $arg .= $nextchar;

                    $arglength ++;
                    $nextchar = substr($sql, $nextarg + $arglength + 1, 1);
                }

                // ok ... replace
                $filtered_sql .= substr($sql, $sqlpos, $nextarg - $sqlpos);

                if (isset($opt['db']['placeholder'][$arg])) {
                    if (substr($sql, $nextarg - 1, 1) != '`') {
                        $filtered_sql .= '`';
                    }

                    $filtered_sql .= sql_escape_backtick($opt['db']['placeholder'][$arg]);

                    if (substr($sql, $nextarg + $arglength + 1, 1) != '`') {
                        $filtered_sql .= '`';
                    }
                } elseif (isset($db['temptables'][$arg])) {
                    if (substr($sql, $nextarg - 1, 1) != '`') {
                        $filtered_sql .= '`';
                    }

                    $filtered_sql .= sql_escape_backtick($opt['db']['placeholder']['tmpdb']) . '`.`' . sql_escape_backtick($db['temptables'][$arg]);

                    if (substr($sql, $nextarg + $arglength + 1, 1) != '`') {
                        $filtered_sql .= '`';
                    }
                } else {
                    sql_error($sql);
                }

                $sqlpos = $nextarg + $arglength + 1;
            }
        }

        $nextarg = strpos($sql, '&', $nextarg + 1);
    }

    // append the rest
    $filtered_sql .= substr($sql, $sqlpos);

    // strip escapes of &
    $nextarg = strpos($filtered_sql, '\&');
    while ($nextarg !== false) {
        $escapesCount = 0;
        while ((($nextarg - $escapesCount - 1) > 0)
            && (substr($filtered_sql, $nextarg - $escapesCount - 1, 1) == '\\')) {
            $escapesCount ++;
        }
        if (($escapesCount % 2) == 0) {
            // strip escapes of &
            $filtered_sql = substr($filtered_sql, 0, $nextarg) . '&' . substr($filtered_sql, $nextarg + 2);
            $nextarg --;
        }

        $nextarg = strpos($filtered_sql, '\&', $nextarg + 2);
    }

    //
    // ok ... filtered_sql is ready for usage
    //

    /* todo:
        - errorlogging
        - LIMIT
        - block DROP/DELETE
    */

    if (isset($db['debug']) && ($db['debug'] == true)) {
        require_once $opt['rootpath'] . 'lib2/sqldebugger.class.php';
        $result = $sqldebugger->execute($filtered_sql, $dblink, ($dblink === $db['dblink_slave']), $db['slave_server']);
        if ($result === false) {
            sql_error($filtered_sql);
        }
    } else {
        // measure time
        if ($opt['db']['warn']['time'] > 0) {
            require_once $opt['rootpath'] . 'lib2/bench.inc.php';
            $cSqlExecution = new Cbench;
            $cSqlExecution->start();
        }

        $result = @mysql_query($filtered_sql, $dblink);
        if ($result === false) {
            sql_error($filtered_sql);
        }

        if ($opt['db']['warn']['time'] > 0) {
            $cSqlExecution->stop();

            if ($cSqlExecution->diff() > $opt['db']['warn']['time']) {
                $ua = isset($_SERVER['HTTP_USER_AGENT']) ? "\r\n" . $_SERVER['HTTP_USER_AGENT'] : "";
                sql_warn("execution took " . $cSqlExecution->diff() . " seconds" . $ua);
            }
        }
    }

    return $result;
}

function sqlf($sql)
{
    global $db;
    $nOldMode = $db['mode'];
    $db['mode'] = DB_MODE_FRAMEWORK;
    $args = func_get_args();
    unset($args[0]);
    $result = sql($sql, $args);
    $db['mode'] = $nOldMode;

    return $result;
}

function sqlf_slave($sql)
{
    global $db;
    $nOldMode = $db['mode'];
    $db['mode'] = DB_MODE_FRAMEWORK;
    $args = func_get_args();
    unset($args[0]);
    $result = sql_slave($sql, $args);
    $db['mode'] = $nOldMode;

    return $result;
}

function sqll($sql)
{
    global $db;
    $nOldMode = $db['mode'];
    $db['mode'] = DB_MODE_BUSINESSLAYER;
    $args = func_get_args();
    unset($args[0]);
    $result = sql($sql, $args);
    $db['mode'] = $nOldMode;

    return $result;
}

function sqlf_value($sql, $default)
{
    global $db;
    $nOldMode = $db['mode'];
    $db['mode'] = DB_MODE_FRAMEWORK;
    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);
    $result = sql_value($sql, $default, $args);
    $db['mode'] = $nOldMode;

    return $result;
}

function sqll_value($sql, $default)
{
    global $db;
    $nOldMode = $db['mode'];
    $db['mode'] = DB_MODE_BUSINESSLAYER;
    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);
    $result = sql_value($sql, $default, $args);
    $db['mode'] = $nOldMode;

    return $result;
}

function sql_escape($value)
{
    global $db, $opt;

    // convert the charset of $value
    if ($opt['charset']['iconv'] != 'UTF-8') {
        $value = iconv('UTF-8', $opt['charset']['iconv'], $value);
    }

    // establish db connection
    if ($db['connected'] != true) {
        sql_connect();
    }

    $value = mysql_real_escape_string($value, $db['dblink']);
    $value = str_replace('&', '\&', $value);

    return $value;
}

function sql_escape_backtick($value)
{
    $value = sql_escape($value);
    $value = str_replace('`', '``', $value);

    return $value;
}

function sql_value($sql, $default)
{
    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);

    if (isset($args[2]) && is_array($args[2])) {
        $tmp_args = $args[2];
        unset($args);

        // correct indizes
        $args = array_merge(array(0), $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    return sql_value_internal(false, $sql, $default, $args);
}

function sql_value_slave($sql, $default)
{
    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);

    if (isset($args[2]) && is_array($args[2])) {
        $tmp_args = $args[2];
        unset($args);

        // correct indizes
        $args = array_merge(array(0), $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    return sql_value_internal(true, $sql, $default, $args);
}

function sql_value_internal($bQuerySlave, $sql, $default)
{
    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);
    unset($args[2]);

    /* as an option, you can give as third parameter an array
     * with all values for the placeholder. The array has to be
     * with numeric indizes.
     */
    if (isset($args[3]) && is_array($args[3])) {
        $tmp_args = $args[3];
        unset($args);

        // correct indizes
        $args = array_merge(array(0), $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    if ($bQuerySlave == true) {
        $rs = sql_slave($sql, $args);
    } else {
        $rs = sql($sql, $args);
    }

    $r = sql_fetch_row($rs);
    sql_free_result($rs);

    if ($r) {
        if ($r[0] == null) {
            return $default;
        } else {
            return $r[0];
        }
    } else {
        return $default;
    }
}

/*
    Replacement for builtin MySQL functions
    (includes database charset conversion)
*/
function sql_fetch_array($rs)
{
    global $opt;
    $retval = mysql_fetch_array($rs);
    if (is_array($retval)) {
        if ($opt['charset']['iconv'] != 'UTF-8') {
            foreach ($retval as $k => $v) {
                $retval[$k] = iconv($opt['charset']['iconv'], 'UTF-8', $v);
            }
        }
    }

    return $retval;
}

function sql_fetch_assoc($rs)
{
    global $opt;
    $retval = mysql_fetch_assoc($rs);
    if (is_array($retval)) {
        if ($opt['charset']['iconv'] != 'UTF-8') {
            foreach ($retval as $k => $v) {
                $retval[$k] = iconv($opt['charset']['iconv'], 'UTF-8', $v);
            }
        }
    }

    return $retval;
}

function sql_fetch_assoc_table($rs)
{
    $result = array();
    while ($r = sql_fetch_assoc($rs)) {
        $result[] = $r;
    }
    sql_free_result($rs);

    return $result;
}

// returns false if no more matching rows exist
function sql_fetch_row($rs)
{
    global $opt;
    $retval = mysql_fetch_row($rs);
    if (is_array($retval)) {
        if ($opt['charset']['iconv'] != 'UTF-8') {
            foreach ($retval as $k => $v) {
                $retval[$k] = iconv($opt['charset']['iconv'], 'UTF-8', $v);
            }
        }
    }

    return $retval;
}

function sql_fetch_column($rs)
{
    global $opt;
    $result = array();
    while ($r = mysql_fetch_row($rs)) {
        if ($opt['charset']['iconv'] != 'UTF-8') {
            $result[] = iconv($opt['charset']['iconv'], 'UTF-8', $r[0]);
        } else {
            $result[] = $r[0];
        }
    }
    mysql_free_result($rs);

    return $result;
}

function sql_affected_rows()
{
    global $db;

    return mysql_affected_rows($db['dblink']);
}

function sql_affected_rows_slave()
{
    global $db;

    return mysql_affected_rows($db['dblink_slave']);
}

function sql_free_result($rs)
{
    return mysql_free_result($rs);
}

function sql_insert_id()
{
    global $db;

    return mysql_insert_id($db['dblink']);
}

function sql_insert_id_slave()
{
    global $db;

    return mysql_insert_id($db['dblink_slave']);
}

function sql_num_rows($rs)
{
    return mysql_num_rows($rs);
}

function sql_temp_table($table)
{
    global $db, $opt;

    if ($db['connected'] == false) {
        sql_connect();
    }

    if ($opt['db']['pconnect'] == true) {
        if ($db['temptable_initialized'] == false) {
            $rs = sqlf(
                "SELECT `threadid`, `name` FROM &db.`sys_temptables` WHERE `threadid`='&1'",
                mysql_thread_id($db['dblink'])
            );
            while ($r = sql_fetch_assoc($rs)) {
                sqlf("DROP TEMPORARY TABLE IF EXISTS &tmpdb.`&1`", $r['name']);
            }
            sql_free_result($rs);
            sqlf("DELETE FROM &db.`sys_temptables` WHERE `threadid`='&1'", mysql_thread_id($db['dblink']));

            $db['temptable_initialized'] = true;
        }

        sqlf("INSERT IGNORE INTO &db.`sys_temptables` (`threadid`, `name`) VALUES ('&1', '&2')", mysql_thread_id($db['dblink']), $table);
    }

    $db['temptables'][$table] = $table;
}

function sql_temp_table_slave($table)
{
    global $db, $opt;

    if ($db['dblink_slave'] === false) {
        sql_connect_anyslave();
    }

    if ($opt['db']['pconnect'] == true) {
        sqlf_slave(
            "INSERT IGNORE INTO &db.`sys_temptables` (`threadid`, `name`) VALUES ('&1', '&2')",
            mysql_thread_id($db['dblink_slave']),
            $table
        );
    }

    $db['temptables'][$table] = $table;
    $db['temptables_slave'][$table] = $table;
}

function sql_drop_temp_table($table)
{
    global $db, $opt;

    sqlf("DROP TEMPORARY TABLE IF EXISTS &tmpdb.`&1`", $table);

    if ($opt['db']['pconnect'] == true) {
        sqlf(
            "DELETE FROM &db.`sys_temptables` WHERE `threadid`='&1' AND `name`='&2'",
            mysql_thread_id($db['dblink']),
            $table
        );
    }

    unset($db['temptables'][$table]);
}

function sql_rename_temp_table($table, $newname)
{
    global $db, $opt;

    if ($opt['db']['pconnect'] == true) {
        sqlf(
            "UPDATE &db.`sys_temptables` SET `name`='&3' WHERE `threadid`='&1' AND `name`='&2'",
            mysql_thread_id($db['dblink']),
            $table,
            $newname
        );
    }

    sqlf('ALTER TABLE &tmpdb.`&1` RENAME &tmpdb.`&2`', $table, $newname);

    unset($db['temptables'][$table]);
    $db['temptables'][$newname] = $newname;
}

function sql_drop_temp_table_slave($table)
{
    global $db, $opt;

    sqlf_slave("DROP TEMPORARY TABLE IF EXISTS &tmpdb.`&1`", $table);

    if ($opt['db']['pconnect'] == true) {
        sqlf_slave(
            "DELETE FROM &db.`sys_temptables` WHERE `threadid`='&1' AND `name`='&2'",
            mysql_thread_id($db['dblink']),
            $table
        );
    }

    unset($db['temptables'][$table]);
    unset($db['temptables_slave'][$table]);
}

function sql_rename_temp_table_slave($table, $newname)
{
    global $db, $opt;

    if ($opt['db']['pconnect'] == true) {
        sqlf(
            "UPDATE &db.`sys_temptables` SET `name`='&3' WHERE `threadid`='&1' AND `name`='&2'",
            mysql_thread_id($db['dblink']),
            $table,
            $newname
        );
    }

    sqlf_slave('ALTER TABLE &tmpdb.`&1` RENAME &tmpdb.`&2`', $table, $newname);

    unset($db['temptables'][$table]);
    unset($db['temptables_slave'][$table]);
    $db['temptables'][$newname] = $newname;
    $db['temptables_slave'][$newname] = $newname;
}

//database handling
function sql_connect($username = null, $password = null, $raiseError = true)
{
    global $opt, $db;

    if ($username == null) {
        $username = $opt['db']['username'];
    }
    if ($password == null) {
        $password = $opt['db']['password'];
    }

    //connect to the database by the given method - no php error reporting!
    if ($opt['db']['pconnect'] == true) {
        $db['dblink'] = @mysql_pconnect($opt['db']['servername'], $username, $password);
    } else {
        $db['dblink'] = @mysql_connect($opt['db']['servername'], $username, $password);
    }

    if ($db['dblink'] !== false) {
        mysql_query("SET NAMES '" . mysql_real_escape_string($opt['charset']['mysql'], $db['dblink']) . "'", $db['dblink']);

        //database connection established ... set the used database
        if (@mysql_select_db($opt['db']['placeholder']['db'], $db['dblink']) == false) {
            //error while setting the database ... disconnect
            sql_disconnect();
            $db['dblink'] = false;
        }
    }

    // output the error form if there was an error
    if ($db['dblink'] === false) {
        if ($raiseError == true) {
            sql_error();
        }
    } else {
        $db['connected'] = true;
    }
}

function sql_slave_exclude()
{
    global $login;
    if ($login->userid == 0) {
        return;
    }

    sql(
        "INSERT INTO `sys_repl_exclude` (`user_id`, `datExclude`) VALUES ('&1', NOW())
                    ON DUPLICATE KEY UPDATE `datExclude`=NOW()",
        $login->userid
    );
}

function sql_connect_anyslave()
{
    global $db, $opt, $login;

    if ($db['dblink_slave'] !== false) {
        return;
    }

    $nMaxTimeDiff = $opt['db']['slave']['max_behind'];
    if ($login->userid != 0) {
        $nMaxTimeDiff = sql_value(
            "SELECT TIMESTAMP(NOW())-TIMESTAMP(`datExclude`)
            FROM `sys_repl_exclude`
            WHERE `user_id`='&1'",
            $opt['db']['slave']['max_behind'],
            $login->userid
        );
        if ($nMaxTimeDiff > $opt['db']['slave']['max_behind']) {
            $nMaxTimeDiff = $opt['db']['slave']['max_behind'];
        }
    }

    $id = sqlf_value(
        "SELECT `id`, `weight`*RAND() AS `w`
        FROM `sys_repl_slaves`
        WHERE `active`= 1
        AND `online`= 1
        AND (TIMESTAMP(NOW())-TIMESTAMP(`last_check`)+`time_diff`<'&1')
        ORDER BY `w` DESC LIMIT 1",
        - 1,
        $nMaxTimeDiff
    );

    sql_connect_slave($id);
}

function sql_connect_master_as_slave()
{
    global $db;

    // the right slave is connected
    if ($db['dblink_slave'] !== false) {
        sql_error();

        return;
    }

    // use existing master connection
    $db['slave_id'] = - 1;
    $db['dblink_slave'] = $db['dblink'];
    $db['slave_server'] = 'master';
}

function sql_connect_slave($id)
{
    global $opt, $db;

    if ($id == - 1) {
        sql_connect_master_as_slave();

        return;
    }

    // the right slave is connected
    if ($db['dblink_slave'] !== false) {
        // TODO: disconnect if other slave is connected
        if ($db['slave_id'] != $id) {
            sql_error();
        }

        return;
    }

    $db['slave_id'] = $id;
    $slave = $opt['db']['slaves'][$id];

    // for display in SQL debugger
    $db['slave_server'] = $slave['server'];

    if ($opt['db']['pconnect'] == true) {
        $db['dblink_slave'] = @mysql_pconnect($slave['server'], $slave['username'], $slave['password']);
    } else {
        $db['dblink_slave'] = @mysql_connect($slave['server'], $slave['username'], $slave['password']);
    }

    if ($db['dblink_slave'] !== false) {
        if (mysql_select_db($opt['db']['placeholder']['db'], $db['dblink_slave']) == false) {
            sql_error();
        }

        mysql_query("SET NAMES '" . mysql_real_escape_string($opt['charset']['mysql'], $db['dblink_slave']) . "'", $db['dblink_slave']);

        // initialize temp tables on slave server
        $rs = sqlf_slave("SELECT `threadid`, `name` FROM `sys_temptables` WHERE `threadid`='&1'", mysql_thread_id($db['dblink_slave']));
        while ($r = sql_fetch_assoc($rs)) {
            sqlf_slave("DROP TEMPORARY TABLE IF EXISTS &tmpdb.`&1`", $r['name']);
        }
        sql_free_result($rs);
        sqlf_slave("DELETE FROM &db.`sys_temptables` WHERE `threadid`='&1'", mysql_thread_id($db['dblink_slave']));
    } else {
        sql_error();
    }
}

function sql_connect_maintenance()
{
    global $tpl, $db, $opt;

    sql_connect($opt['db']['maintenance_user'], $opt['db']['maintenance_password'], false);
    if ($db['dblink'] === false) {
        sql_disconnect();
        sql_connect();
        if ($db['connected'] == false) {
            $tpl->error(ERROR_DB_COULD_NOT_RECONNECT);
        }

        return false;
    }

    return true;
}

//disconnect the databse
function sql_disconnect()
{
    global $opt, $db;
    sql_disconnect_slave();

    if (($opt['db']['pconnect'] == true) && ($db['dblink'] !== false)) {
        if (count($db['temptables']) > 0) {
            foreach ($db['temptables'] as $table) {
                sqlf("DROP TEMPORARY TABLE IF EXISTS &tmpdb.`&1`", $table);
            }

            sqlf("DELETE FROM &db.`sys_temptables` WHERE `threadid`='&1'", mysql_thread_id($db['dblink']));
            $db['temptables'] = array();
            $db['temptables_slave'] = array();
        }
    }

    if ($db['dblink'] === $db['dblink_slave']) {
        $db['dblink_slave'] = false;
    }

    //is connected and no persistent connect used?
    if (($opt['db']['pconnect'] == false) && ($db['dblink'] !== false)) {
        mysql_close($db['dblink']);
        $db['dblink'] = false;
        $db['connected'] = false;
    }
}

//disconnect the databse
function sql_disconnect_slave()
{
    global $opt, $db;

    if ($db['dblink_slave'] === false) {
        return;
    }

    if (($opt['db']['pconnect'] == true) && ($db['dblink'] !== false)) {
        if (count($db['temptables']) > 0) {
            foreach ($db['temptables'] as $k => $table) {
                if (isset($db['temptables_slave'][$table])) {
                    sqlf_slave("DROP TEMPORARY TABLE IF EXISTS &tmpdb.`&1`", $table);
                    unset($db['temptables_slave'][$table]);
                    unset($db['temptables'][$k]);
                }
            }

            if (count($db['temptables_slave']) > 0) {
                sqlf_slave(
                    "DELETE FROM &db.`sys_temptables` WHERE `threadid`='&1'",
                    mysql_thread_id($db['dblink_slave'])
                );
            }
            $db['temptables_slave'] = array();
        }
    }

    if ($db['dblink'] === $db['dblink_slave']) {
        $db['dblink_slave'] = false;

        return;
    }

    //is connected and no persistent connect used?
    if (($opt['db']['pconnect'] == false) && ($db['dblink_slave'] !== false)) {
        mysql_close($db['dblink_slave']);
    }
    $db['dblink_slave'] = false;
}

function sql_error($sqlstatement = "")
{
    global $tpl, $opt, $db;
    global $bSmartyNoTranslate;

    $errno = mysql_errno();
    $error = mysql_error();
    if ($sqlstatement != "") {
        $error .= "\n\nSQL statement: " . $sqlstatement;
    }

    if ($db['error'] == true) {
        // datbase error recursion, because another error occured while trying to
        // build the error template (e.g. because connection was lost, or an error mail
        // could not load translations from database)

        if ($opt['db']['error']['display'] == true) {
            $errmsg = 'MySQL error recursion (' . $errno . '): ' . $error;
        } else {
            $errmsg = "";
        }
        $errtitle = 'Datenbankfehler';
        require 'html/error.php';
        exit;
    }
    $db['error'] = true;

    if ($db['connected'] == false) {
        $bSmartyNoTranslate = true;
    }

    if ($opt['db']['error']['mail'] != '') {
        $subject = '[' . $opt['page']['domain'] . '] SQL error';
        if (admin_errormail($opt['db']['error']['mail'],
            $subject,
            str_replace("\n", "\r\n", $error) . "\n" . print_r(debug_backtrace(), true),
            "From: " . $opt['mail']['from']
        )) {
            require_once $opt['rootpath'] . 'lib2/mail.class.php';

            $mail = new mail();
            $mail->subject = $subject;
            $mail->to = $opt['db']['error']['mail'];

            $mail->name = 'sql_error';

            $mail->assign('errno', $errno);
            $mail->assign('error', str_replace("\n", "\r\n", $error));
            $mail->assign('trace', print_r(debug_backtrace(), true));

            $mail->send();
            $mail = null;
        }
    }

    if ($opt['gui'] == GUI_HTML) {
        if (isset($tpl)) {
            if ($opt['db']['error']['display'] == true) {
                $tpl->error('MySQL error (' . $errno . '): ' . $error);
            } else {
                $tpl->error('A database command could not be performed.');
            }
        } else {
            if ($opt['db']['error']['display'] == true) {
                die('<html><body>' . htmlspecialchars('MySQL error (' . $errno . '): ' . str_replace("\n,", "<br />", $error)) . '</body></html>');
            } else {
                die('<html><body>A database command could not be performed</body></html>');
            }
        }
    } else {
        // CLI script, simple text output
        if ($opt['db']['error']['display'] == true) {
            die('MySQL error (' . $errno . '): ' . $error . "\n");
        } else {
            die("A database command could not be performed.\n");
        }
    }
}

function sql_warn($warnmessage)
{
    global $opt;

    if ($opt['db']['error']['mail'] != '') {
        $subject = '[' . $opt['page']['domain'] . '] SQL error';
        if (admin_errormail($opt['db']['error']['mail'],
            $subject,
            $warnmessage . "\n" . print_r(debug_backtrace(), true),
            "From: " . $opt['mail']['from']
        )) {
            require_once $opt['rootpath'] . 'lib2/mail.class.php';
            $mail = new mail();
            $mail->name = 'sql_warn';
            $mail->subject = $subject;
            $mail->to = $opt['db']['warn']['mail'];

            $mail->assign('warnmessage', $warnmessage);
            $mail->assign('trace', print_r(debug_backtrace(), true));

            $mail->send();
            $mail = null;
        }
    }
}

function sql_export_recordset($f, $rs, $table, $truncate = true)
{
    fwrite($f, "SET NAMES 'utf8';\n");

    if ($truncate == true) {
        fwrite($f, "TRUNCATE TABLE `" . sql_escape($table) . "`;\n");
    }

    while ($r = sql_fetch_assoc($rs)) {
        $fields = array();
        $values = array();

        foreach ($r as $k => $v) {
            $fields[] = '`' . sql_escape($k) . '`';
            if ($v === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'" . sql_escape($v) . "'";
            }
        }
        unset($r);

        fwrite(
            $f,
            "INSERT INTO `" . sql_escape($table) . "` (" . implode(', ', $fields) . ")"
            . " VALUES (" . implode(', ', $values) . ");\n"
        );
    }
}

function sql_export_table($f, $table)
{
    $primary = array();
    $rsIndex = sql("SHOW INDEX FROM `&1`", $table);
    while ($r = sql_fetch_assoc($rsIndex)) {
        if ($r['Key_name'] == 'PRIMARY') {
            $primary[] = '`' . sql_escape($r['Column_name']) . '` ASC';
        }
    }
    sql_free_result($rsIndex);

    $sql = "SELECT * FROM `" . sql_escape($table) . "`";
    if (count($primary) > 0) {
        $sql .= ' ORDER BY ' . implode(', ', $primary);
    }

    $rs = sql($sql);
    sql_export_recordset($f, $rs, $table);
    sql_free_result($rs);
}

function sql_export_tables_to_file($filename, $tables)
{
    $f = fopen($filename, 'w');

    fwrite($f, "-- Content of tables:\n");

    foreach ($tables as $t) {
        fwrite($f, "-- $t\n");
    }
    fwrite($f, "\n");

    foreach ($tables as $t) {
        fwrite($f, "-- Table $t\n");
        sql_export_table($f, $t);
        fwrite($f, "\n");
    }

    fclose($f);
}

function sql_export_table_to_file($filename, $table)
{
    $f = fopen($filename, 'w');
    sql_export_table($f, $table);
    fclose($f);
}

function sql_export_structure($f, $table)
{
    $rs = sql("SHOW CREATE TABLE `&1`", $table);
    $r = sql_fetch_array($rs);
    sql_free_result($rs);

    $sTableSql = $r[1];
    $sTableSql = preg_replace("/ AUTO_INCREMENT=[0-9]{1,} /", ' ', $sTableSql);
    $sTableSql = preg_replace("/,\n +?(KEY )?`okapi_syncbase`.+?(,)?\n/", "\\2\n", $sTableSql);

    fwrite($f, "SET NAMES 'utf8';\n");
    fwrite($f, "DROP TABLE IF EXISTS `" . sql_escape($table) . "`;\n");
    fwrite($f, $sTableSql . " ;\n");
}

function sql_export_structure_to_file($filename, $table)
{
    $f = fopen($filename, 'w');
    sql_export_structure($f, $table);
    fclose($f);
}

// test if a database table exists
function sql_table_exists($table)
{
    global $opt;

    return sql_value(
        "SELECT COUNT(*)
         FROM `information_schema`.`tables`
         WHERE `table_schema`='&1' AND `table_name`='&2'",
        0,
        $opt['db']['placeholder']['db'],
        $table
    ) > 0;
}

// test if a database field exists
function sql_field_exists($table, $field)
{
    global $opt;

    return sql_value(
        "SELECT COUNT(*)
         FROM `information_schema`.`columns`
         WHERE `table_schema`='&1' AND `table_name`='&2' AND `column_name`='&3'",
        0,
        $opt['db']['placeholder']['db'],
        $table,
        $field
    ) > 0;
}

// get type of a database field
function sql_field_type($table, $field)
{
    global $opt;

    return strtoupper(
        sql_value(
            "SELECT `data_type`
             FROM `information_schema`.`columns`
             WHERE `table_schema`='&1' AND `table_name`='&2' AND `column_name`='&3'",
            '',
            $opt['db']['placeholder']['db'],
            $table,
            $field
        )
    );
}

// test if a database index exists
function sql_index_exists($table, $index)
{
    global $opt;

    return sql_value(
        "SELECT COUNT(*)
         FROM `information_schema`.`statistics`
         WHERE `table_schema`='&1' AND `table_name`='&2' AND `index_name`='&3'",
        0,
        $opt['db']['placeholder']['db'],
        $table,
        $index
    ) > 0;
}

// test if a function or procedure exists
function sql_fp_exists($type, $name)
{
    global $opt;

    $rs = sql("SHOW $type STATUS LIKE '&1'", $name);
    $r = sql_fetch_assoc($rs);
    sql_free_result($rs);

    return ($r &&
        $r['Db'] == $opt['db']['placeholder']['db'] &&
        $r['Name'] == $name &&
        $r['Type'] == $type);
}

// test if a function exists
function sql_function_exists($name)
{
    return sql_fp_exists('FUNCTION', $name);
}

// delete a function
function sql_dropFunction($name)
{
    sql('DROP FUNCTION IF EXISTS `&1`', $name);
}

// test if a procedure exists
function sql_procedure_exists($name)
{
    return sql_fp_exists('PROCEDURE', $name);
}

// delete a procedure
function sql_dropProcedure($name)
{
    sql('DROP PROCEDURE IF EXISTS `&1`', $name);
}

function sql_dropTrigger($triggername)
{
    $rs = sql("SHOW TRIGGERS");
    while ($r = sql_fetch_assoc($rs)) {
        if ($r['Trigger'] == $triggername) {
            sql('DROP TRIGGER `&1`', $triggername);

            return;
        }
    }
    sql_free_result($rs);
}
