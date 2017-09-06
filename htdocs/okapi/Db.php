<?php

namespace okapi;

#
# Database access abstraction layer.
#
use okapi\Exception\DbException;
use okapi\Exception\DbInitException;
use okapi\Exception\DbLockWaitTimeoutException;
use okapi\Exception\DbTooManyRowsException;
use PDO;
use PDOException;

/**
 * Database access abstraction layer class. Use this instead of "raw" mysql,
 * mysqli or PDO functions.
 *
 * Currently, this class wraps the PDO class in a way which is backwards
 * compatible with the previously used mysql_* functions (see issue #297 for
 * details). This is perfectly safe if it is used correctly - and OKAPI was
 * thoroughly reviewed in this matter, so we're quite confident there are no
 * SQL injections anyware to be found.
 *
 * On the other hand, this is obviously not the way PDO was supposed to be
 * used. We may choose to deprecate parts of this class in the future, and
 * expose a PDO-compatible object instead. Until we do that, please use the Db
 * class and Db::escape_string method, as you'd do in the old mysql-family
 * functions.
 */
class Db
{
    private static $connected = false;
    private static $dbh = null;

    public static function connect()
    {
        $dsnarr = array(
            'host' => Settings::get('DB_SERVER'),
            'dbname' => Settings::get('DB_NAME'),
            'charset' => Settings::get('DB_CHARSET')
        );

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        );

        /* Older PHP versions do not support the 'charset' DSN option. */

        if ($dsnarr['charset'] and version_compare(PHP_VERSION, '5.3.6', '<')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $dsnarr['charset'];
        }

        $dsnpairs = array();
        foreach ($dsnarr as $k => $v) {
            if ($v === null) {
                continue;
            }
            $dsnpairs[] = $k . "=" . $v;
        }

        $dsn = 'mysql:' . implode(';', $dsnpairs);
        try {
            self::$dbh = new PDO(
                $dsn, Settings::get('DB_USERNAME'), Settings::get('DB_PASSWORD'), $options
            );
        } catch (PDOException $e) {
            throw new DbInitException($e->getMessage());
        }
        self::$connected = true;
    }

    /** Fetch [{row}], return {row}. */
    public static function select_row($query)
    {
        $rows = self::select_all($query);
        switch (count($rows))
        {
            case 0: return null;
            case 1: return $rows[0];
            default:
                throw new DbTooManyRowsException("Invalid query. Db::select_row returned more than one row for:\n\n".$query."\n");
        }
    }

    /** Fetch all [{row}, {row}], return [{row}, {row}]. */
    public static function select_all($query)
    {
        $rows = array();
        self::select_and_push($query, $rows);
        return $rows;
    }

    /** Private. */
    private static function select_and_push($query, & $arr, $keyField = null)
    {
        $rs = self::query($query);
        while (true)
        {
            $row = Db::fetch_assoc($rs);
            if ($row === false)
                break;
            if ($keyField == null)
                $arr[] = $row;
            else
                $arr[$row[$keyField]] = $row;
        }
        Db::free_result($rs);
    }

    /** Fetch all [(A,A), (A,B), (B,A)], return {A: [{row}, {row}], B: [{row}]}. */
    public static function select_group_by($keyField, $query)
    {
        $groups = array();
        $rs = self::query($query);
        while (true)
        {
            $row = Db::fetch_assoc($rs);
            if ($row === false)
                break;
            $groups[$row[$keyField]][] = $row;
        }
        Db::free_result($rs);
        return $groups;
    }

    /** Fetch [(A)], return A. */
    public static function select_value($query)
    {
        $column = self::select_column($query);
        if ($column == null)
            return null;
        if (count($column) == 1)
            return $column[0];
        throw new DbTooManyRowsException("Invalid query. Db::select_value returned more than one row for:\n\n".$query."\n");
    }

    /** Fetch all [(A), (B), (C)], return [A, B, C]. */
    public static function select_column($query)
    {
        $column = array();
        $rs = self::query($query);
        while (true)
        {
            $values = Db::fetch_row($rs);
            if ($values === false)
                break;
            array_push($column, $values[0]);
        }
        Db::free_result($rs);
        return $column;
    }

    public static function last_insert_id()
    {
        return self::$dbh->lastInsertId();
    }

    public static function fetch_assoc($rs)
    {
        return $rs->fetch(PDO::FETCH_ASSOC);
    }

    public static function fetch_row($rs)
    {
        return $rs->fetch(PDO::FETCH_NUM);
    }

    public static function free_result($rs)
    {
        return $rs->closeCursor();
    }

    public static function escape_string($value)
    {
        if (!self::$connected)
            self::connect();
        return substr(self::$dbh->quote($value), 1, -1);  // soo ugly!
    }

    /**
     * Execute a given *non-SELECT* SQL statement. Return number of affected
     * rows (that is, rows updated, inserted or deleted by the statement).
     */
    public static function execute($query)
    {
        if (!self::$connected)
            self::connect();
        try {
            return self::$dbh->exec($query);
        } catch (PDOException $e) {
            self::throwProperDbException($e, $query);
        }
    }

    /**
     * Given a PDOException, check its properties and throw one of the DbException subclasses
     * based on these properties.
     *
     * This allows developers to catch specific subclasses of exceptions in the code.
     */
    private static function throwProperDbException($e, $query)
    {
        list($sqlstate, $errno, $msg) = $e->errorInfo;
        $msg = "SQL Error $errno: $msg\n\nThe query was:\n".$query."\n";
        switch ($errno) {
            case 1205: throw new DbLockWaitTimeoutException($msg);
            default: throw new DbException($msg);
        }
    }

    /**
     * Execute a given SQL statement. Return a PDOStatement object.
     */
    public static function query($query)
    {
        if (!self::$connected)
            self::connect();
        try
        {
            $rs = self::$dbh->query($query);
        }
        catch (PDOException $e)
        {
            list($sqlstate, $errno, $msg) = $e->errorInfo;

            /* Detect issue #340 and try to repair... */

            if (in_array($errno, array(144, 130)) && strstr($msg, "okapi_cache")) {

                /* MySQL claims that is tries to repair it automatically. We'll
                 * try outselves. */

                try {
                    self::execute("repair table okapi_cache");
                    Okapi::mail_admins(
                        "okapi_cache - Automatic repair",
                        "Hi.\n\nOKAPI detected that okapi_cache table needed ".
                        "repairs and it has performed such\nrepairs automatically. ".
                        "However, this should not happen regularly!"
                    );
                } catch (\Exception $e) {

                    /* Last resort. */

                    try {
                        self::execute("truncate okapi_cache");
                        Okapi::mail_admins(
                            "okapi_cache was truncated",
                            "Hi.\n\nOKAPI detected that okapi_cache table needed ".
                            "repairs, but it failed to repair\nthe table automatically. ".
                            "In order to counteract more severe errors, \nwe have ".
                            "truncated the okapi_cache table to make it alive.\n".
                            "However, this should not happen regularly!"
                        );
                    } catch (\Exception $e) {
                        # pass
                    }
                }
            }

            self::throwProperDbException($e, $query);
        }
        return $rs;
    }

    public static function field_exists($table, $field)
    {
        if (!preg_match("/[a-z0-9_]+/", $table.$field))
            return false;
        try {
            $spec = self::select_all("desc ".$table.";");
        } catch (\Exception $e) {
            /* Table doesn't exist, probably. */
            return false;
        }
        foreach ($spec as &$row_ref) {
            if (strtoupper($row_ref['Field']) == strtoupper($field))
                return true;
        }
        return false;
    }
}
