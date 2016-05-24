<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  This is included from both lib1 and lib2.
 ***************************************************************************/

$error_handled = false;


function register_errorhandlers()
{
    global $opt;

    if (isset($opt['gui']) && $opt['gui'] == GUI_HTML) {
        set_error_handler('errorhandler', E_ERROR);
        register_shutdown_function('shutdownhandler');
    }
}

function errorhandler($errno, $errstr, $errfile, $errline)
{
    // will catch a few runtime errors

    global $error_handled;

    if (!$error_handled) {
        $error_handled = true;
        $errtitle = "PHP-Fehler";

        $error = "($errno) $errstr at line $errline in $errfile";
        php_errormail($error);

        if (display_error()) {
            $errmsg = $error;
        } else {
            $errmsg = "";
        }

        require __DIR__ . '/../html/error.php';
        exit;
    }
}

function shutdownhandler()
{
    // see http://stackoverflow.com/questions/1900208/php-custom-error-handler-handling-parse-fatal-errors
    //
    // will catch anything but parse errors

    global $error_handled;

    if (!$error_handled &&
        function_exists("error_get_last") && /* PHP >= 5.2.0 */
        ($error = error_get_last()) &&
        in_array(
            $error['type'],
            [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR
            ]
        )
    ) {
        $error_handled = true;

        $error = "(" . $error['type'] . ") " . $error['message'] .
            " at line " . $error['line'] . " of " . $error['file'];
        php_errormail($error);

        $errtitle = "PHP-Fehler";
        $errmsg = "";
        if (display_error()) {
            $errmsg = $error;
        }

        require __DIR__ . '/../html/error.php';
    }
}

function display_error()
{
    global $opt, $debug_page;

    return (isset($opt['db']['error']['display']) && $opt['db']['error']['display']) ||
    (isset($debug_page) && $debug_page);
}

function php_errormail($errmsg)
{
    global $opt, $sql_errormail, $absolute_server_URI;

    $sendMail = true;
    $subject = '[' . $opt['page']['domain'] . '] PHP error';

    if (isset($opt['db']['error']['mail']) && $opt['db']['error']['mail'] != '') {
        $sendMail = mb_send_mail($opt['db']['error']['mail'], $subject, $errmsg);
    } elseif (isset($sql_errormail) && $sql_errormail != '') {
        $sendMail = mb_send_mail($sql_errormail, $subject, $errmsg);
    }

    if ($sendMail === false) {
        throw new \RuntimeException('the E-Mail can not be send.');
    }
}


// throttle admin error mails;
// currently used only for SQL errors and warnings

function admin_errormail($to, $errortype, $message, $headers)
{
    global $opt;
    $errorlog_dir = $opt['rootpath'] . 'var/errorlog';
    $errorlog_path = $errorlog_dir . "/errorlog-" . date("Y-m-d");

    $error_mail_limit = 32768;    // send max 32 KB = ca. 5-20 errors per day/logfile

    // All errors which may happen here are ignored, to avoid error recursions.

    if (!is_dir($errorlog_dir)) {
        @mkdir($errorlog_dir);
    }
    $old_logsize = @filesize($errorlog_path) + 0;
    $msg = date("Y-m-d H:i:s.u") . " " . $errortype . "\n" . $message . "\n" .
        "-------------------------------------------------------------------------\n\n";
    try {
        error_log(
            $msg,
            3, // log to file
            $errorlog_path
        );
    } catch (Exception $e) {
        // @todo implement login
    }
    // @filesize() may still return the old size here, because logging takes place
    // asynchronously. Instead we calculate the new size:
    $new_logsize = $old_logsize + strlen($msg);

    if ($old_logsize < $error_mail_limit && $new_logsize >= $error_mail_limit) {
        mb_send_mail(
            $to,
            "too many " . $errortype,
            "Errors/Warnings are recorded in " . $errorlog_path . ".\n" .
            "Email Reporting is DISABLED for today now. Please check the logfile\n" .
            "and RENAME or delete it when done, so that logging is re-enabled.",
            $headers
        );

        return false;
    } else {
        return ($old_logsize < $error_mail_limit);
    }
}
