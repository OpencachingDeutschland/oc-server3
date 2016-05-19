<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once __DIR__. '/../vendor/autoload.php';

class mail extends Smarty
{
    public $name = 'sys_nothing';
    public $main_template = 'sys_main';
    public $compile_id = null;
    public $recipient_locale = null;

    public $from = '';
    public $to = '';
    public $subject = '';

    public $replyTo = null;
    public $returnPath = null;

    public $headers = array();

    public function __construct()
    {
        parent::__construct();

        global $opt;

        $this->template_dir = $opt['rootpath'] . 'templates2/mail/';
        $this->compile_dir = $opt['rootpath'] . 'cache2/smarty/compiled/';
        $this->plugins_dir = [
            'plugins',
            __DIR__ . '/../src/Oc/SmartyPlugins'
        ];

        // disable caching ...
        $this->caching = false;

        // register additional functions
        $this->load_filter('pre', 't');

        // cache control
        if (($opt['debug'] & DEBUG_TEMPLATES) == DEBUG_TEMPLATES) {
            $this->force_compile = true;
        }

        $this->from = $opt['mail']['from'];
    }

    public function get_compile_id()
    {
        global $opt;

        return 'mail|' . $opt['template']['locale'] . '|' . $this->compile_id;
    }

    public function assign_rs($name, $rs)
    {
        $items = array();
        while ($r = sql_fetch_assoc($rs)) {
            $items[] = $r;
        }
        $this->assign($name, $items);
    }

    public function send($page_url = false)
    {
        global $tpl, $opt;

        if (!$this->template_exists($this->name . '.tpl')) {
            $tpl->error(ERROR_MAIL_TEMPLATE_NOT_FOUND);
        }
        $this->assign('template', $this->name);
        if (!$this->recipient_locale) {
            $this->recipient_locale = $opt['template']['locale'];
        }

        $optn['mail']['contact'] = $opt['mail']['contact'];
        $optn['page']['absolute_url'] = ($page_url ? $page_url : $opt['page']['absolute_url']);
        $optn['page']['sitename'] = $opt['page']['sitename'];
        $optn['format'] = $opt['locale'][$this->recipient_locale]['format'];
        $this->assign('opt', $optn);

        $this->assign('to', $this->to);
        $this->assign('from', $this->from);
        $this->assign('subject', $this->subject);

        // This is nasty, but as there is only a global translation system
        // (based on gettext) and there are no precompiled, language-dependend email
        // templates available, we must temporarily change the locale according to
        // the recipient's locale. If some error occurs while running fetch(),
        // the error message may be displayed in the recipient's language.

        $sender_locale = $opt['template']['locale'];
        if ($this->recipient_locale != $sender_locale) {
            $opt['template']['locale'] = $this->recipient_locale;
            set_php_locale();
        }

        $body = $this->fetch($this->main_template . '.tpl', '', $this->get_compile_id());

        if ($this->recipient_locale != $sender_locale) {
            $opt['template']['locale'] = $sender_locale;
            set_php_locale();
        }

        // check if the target domain exists if the domain does not
        // exist, the mail is sent to the own domain (?!)
        $domain = mail::getToMailDomain($this->to);
        if (mail::is_existent_maildomain($domain) == false) {
            return false;
        }

        $aAddHeaders = array();
        $aAddHeaders[] = 'From: "' . $this->from . '" <' . $this->from . '>';

        if ($this->replyTo !== null) {
            $aAddHeaders[] = 'Reply-To: ' . $this->replyTo;
        }

        if ($this->returnPath !== null) {
            $aAddHeaders[] = 'Return-Path: ' . $this->returnPath;
        }

        $mailheaders = implode("\n", array_merge($aAddHeaders, $this->headers));

        return mb_send_mail($this->to, $opt['mail']['subject'] . $this->subject, $body, $mailheaders);
    }

    public static function is_existent_maildomain($domain)
    {
        if ($domain == "localhost") {
            return true;
        }  // allow maintenance mails e.g. to root

        $smtp_serverlist = array();
        $smtp_serverweight = array();

        if (getmxrr($domain, $smtp_serverlist, $smtp_serverweight) != false) {
            if (count($smtp_serverlist) > 0) {
                return true;
            }
        }

        // check if A exists
        $a = dns_get_record($domain, DNS_A);
        if (count($a) > 0) {
            return true;
        }

        return false;
    }

    public static function getToMailDomain($mail)
    {
        if ($mail == '') {
            return '';
        }

        if (strrpos($mail, '@') === false) {
            $domain = 'localhost';
        } else {
            $domain = substr($mail, strrpos($mail, '@') + 1);
        }

        return $domain;
    }
}
