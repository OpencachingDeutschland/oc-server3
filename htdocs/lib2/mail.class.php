<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

require_once __DIR__ . '/../vendor/autoload.php';

class mail extends Smarty
{
    public $name = 'sys_nothing';
    public $main_template = 'sys_main';
    public $recipient_locale = null;

    public $from = '';
    public $to = '';
    public $subject = '';

    public $replyTo = null;
    public $returnPath = null;

    public $headers = array();

    /**
     * mail constructor.
     */
    public function __construct()
    {
        parent::__construct();

        global $opt;

        $this->template_dir = __DIR__ . '/../templates2/mail/';
        $this->compile_dir = __DIR__ . '/../var/cache2/smarty/compiled/';
        $this->plugins_dir = [
            'plugins',
            __DIR__ . '/../src/OcLegacy/SmartyPlugins',
        ];

        // disable caching ...
        $this->caching = 0;

        // register additional functions
        $this->load_filter('pre', 't');

        // cache control
        if (($opt['debug'] & DEBUG_TEMPLATES) == DEBUG_TEMPLATES) {
            $this->force_compile = true;
        }

        $this->from = $opt['mail']['from'];
    }

    /**
     * @return string
     */
    public function get_compile_id()
    {
        global $opt;

        return 'mail|' . $opt['template']['locale'] . '|' . $this->compile_id;
    }

    /**
     * @param $name
     * @param $rs
     */
    public function assign_rs($name, $rs): void
    {
        $items = array();
        while ($r = sql_fetch_assoc($rs)) {
            $items[] = $r;
        }
        $this->assign($name, $items);
    }

    /**
     * @param bool $page_url
     *
     * @return bool
     */
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
        $domain = self::getToMailDomain($this->to);
        if (self::is_existent_maildomain($domain) == false) {
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

        $mailHeaders = implode("\n", array_merge($aAddHeaders, $this->headers));

        return mb_send_mail($this->to, $opt['mail']['subject'] . $this->subject, $body, $mailHeaders);
    }

    /**
     * @param string $domain
     *
     * @return bool
     */
    public static function is_existent_maildomain($domain)
    {
        if ($domain === 'localhost') {
            return true;
        }  // allow maintenance mails e.g. to root

        $smtpServerList = [];
        $smtpServerWeight = [];

        if (getmxrr($domain, $smtpServerList, $smtpServerWeight) !== false && count($smtpServerList) > 0) {
            return true;
        }

        // check if A exists
        $a = dns_get_record($domain, DNS_A);

        return count($a) > 0;
    }

    /**
     * @param string $mail
     *
     * @return string
     */
    public static function getToMailDomain($mail)
    {
        if ($mail === '') {
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
