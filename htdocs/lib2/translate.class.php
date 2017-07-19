<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

require_once __DIR__ . '/translationHandler.class.php';

/**
 * @param int $backtrace_level
 *
 * @return translate|translateEdit
 */
function createTranslate($backtrace_level = 0)
{
    $access = new translateAccess();

    if ($access->hasAccess()) {
        global $cookie;

        $translateMode = $cookie->get('translate_mode');

        if ($translateMode) {
            return new translateEdit($translateMode == 'all', $backtrace_level);
        }
    }

    return new translate();
}

$translate = createTranslate();

/**
 * Class translate
 */
class translate
{
    /**
     * translate the given string
     *
     * @param $message
     * @param string $style
     * @param string $resource_name
     * @param string $line
     * @param string $plural
     * @param int $count
     * @param null $lang
     *
     * @return string
     */
    public function t($message, $style, $resource_name, $line, $plural = '', $count = 1, $lang = null)
    {
        global $opt, $locale; // $locale is for lib1 compatibility

        if ($message == '') {
            return '';
        }

        if ($plural != '' && $count != 1) {
            $message = $plural;
        }
        $search = $this->prepare_text($message);

        $loc = isset($opt['template']['locale']) ? $opt['template']['locale'] : $locale;
        if (!$lang || ($lang == $loc)) {
            $trans = gettext($search);
        } else {
            // do not use sql_value(), as this is also used from lib1
            $rs = sql("SELECT IFNULL(`sys_trans_text`.`text`, '&3')
                       FROM `sys_trans`
                       LEFT JOIN `sys_trans_text`
                         ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                         AND `sys_trans_text`.`lang`='&1'
                       WHERE `sys_trans`.`text`='&2' LIMIT 1", $lang, $search, $message);
            $trans = '';

            if ($r = sql_fetch_array($rs)) {
                $trans = $r[0];
            }

            sql_free_result($rs);
        }

        // safe w/o mb because asc(%) < 128
        if (strpos($trans, '%') >= 0) {
            $trans = $this->v($trans);
        }

        return $trans;
    }

    /**
     * strip whitespaces
     *
     * @param $text
     *
     * @return string
     */
    protected function prepare_text($text)
    {
        $text = mb_ereg_replace("\t", ' ', $text);
        $text = mb_ereg_replace("\r", ' ', $text);
        $text = mb_ereg_replace("\n", ' ', $text);
        while (mb_strpos($text, '  ') !== false) {
            $text = mb_ereg_replace('  ', ' ', $text);
        }

        return $text;
    }

    /**
     * @param $message
     * @return string
     */
    public function v($message)
    {
        if ($message) {
            global $translationHandler;
            global $opt;

            if (!isset($language)) {
                global $locale;

                $language = $locale;
            }

            $variables = array();
            $language_lower = mb_strtolower($language);
            $translationHandler->loadNodeTextFile($variables, $opt['logic']['node']['id'] . '.txt', $language_lower);
            $translationHandler->loadNodeTextFile(
                $variables,
                $opt['logic']['node']['id'] . '-' . $language_lower . '.txt',
                $language_lower
            );
            $message = $translationHandler->substitueVariables($variables, $language_lower, $message);
        }

        return $message;
    }
}

/**
 * Class translateEdit
 */
class translateEdit extends translate
{
    private $editAll;
    private $backtrace_level;

    /**
     * translateEdit constructor.
     *
     * @param bool $editAll
     * @param int $backtrace_level
     */
    public function __construct($editAll = true, $backtrace_level = 0)
    {
        $this->editAll = $editAll;
        $this->backtrace_level = $backtrace_level;
    }

    /**
     * @param string $message
     * @param string $style
     * @param string $resource_name
     * @param string $line
     * @param string $plural
     * @param int $count
     * @param null $lang
     *
     * @return string
     */
    public function t($message, $style, $resource_name, $line, $plural = '', $count = 1, $lang = null)
    {
        global $opt;

        if ($message == '') {
            return '';
        }

        if ($message == 'INTERNAL_LANG') {
            return parent::t($message, $style, $resource_name, $line, $plural, $count);
        }

        if ($plural != '' && $count != 1) {
            $message = $plural;
        }

        $search = $this->prepare_text($message);
        $language = $opt['template']['locale'];

        if (!isset($language)) {
            global $locale;

            $language = $locale;
        }

        $rs = sql(
            "
            SELECT `sys_trans`.`id`, `sys_trans_text`.`text`
            FROM `sys_trans`
            LEFT JOIN `sys_trans_text`
                ON `sys_trans`.`id` = `sys_trans_text`.`trans_id`
                AND `sys_trans_text`.`lang` = '&1'
            WHERE `sys_trans`.`text` = '&2'",
            $language,
            $search
        );
        $r = sql_fetch_assoc($rs);

        $trans = $r['text'];
        $trans = $this->v($trans);

        if ($trans && !$this->editAll) {
            return $trans;
        }

        if (empty($r['id'])) {
            global $translationHandler;

            if (empty($resource_name)) {
                $backtrace = debug_backtrace();
                $item = $backtrace[$this->backtrace_level];
                $resource_name = $item['file'];
                $line = $item['line'];
            }

            $translationHandler->addText($search, $resource_name, $line);

            return $this->t($message, $style, $resource_name, $line, $plural, $count);
        }

        $text = $trans ? $trans : gettext($search);

        return $text . ' <a href= translate.php?action=edit&id=' . $r['id'] . '>Edit</a>';
    }
}
