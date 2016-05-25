<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$translationHandler = new TranslationHandler();

class TranslationHandler
{
    /* create all files in cache2/translate
     */
    public function createMessageFiles()
    {
        $rs = sqlf("SELECT DISTINCT `lang` FROM `sys_trans_text`");
        while ($r = sql_fetch_assoc($rs)) {
            $this->createMessageFile($r['lang']);
        }
        sql_free_result($rs);
    }

    /* create file in cache2/translate/$language/LC_MESSAGES/...
     */
    private function createMessageFile($language)
    {
        global $opt;

        $language_upper = mb_strtoupper($language);
        $language_lower = mb_strtolower($language);
        if (!isset($opt['locale'][$language_upper])) {
            return;
        }

        if (!is_dir($opt['rootpath'] . 'cache2/translate/' . $language_lower)) {
            mkdir($opt['rootpath'] . 'cache2/translate/' . $language_lower);
        }
        if (!is_dir($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES')) {
            mkdir($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES');
        }

        $f = fopen($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES/messages.po', 'w');

        fwrite($f, 'msgid ""' . "\n");
        fwrite($f, 'msgstr ""' . "\n");
        fwrite($f, '"MIME-Version: 1.0\n"' . "\n");
        fwrite($f, '"Content-Type: text/plain; charset=UTF-8\n"' . "\n");
        fwrite($f, '"Content-Transfer-Encoding: 8bit\n"' . "\n");
        fwrite($f, "\n");

        // English texts in sys_trans_text are more up-to-date than the original translation
        // strings in sys_trans. Therefore sys_trans_text/EN is used whenever a translation
        // is missing and sys_trans_text/EN differs from sys_trans.

        $rs = sqlf(
            "
            SELECT
                `sys_trans`.`text` AS `text`,
                IFNULL(`stt`.`text`, `stt_en`.`text`) AS `trans`
            FROM
                `sys_trans`
                LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`sys_trans`.`id` AND `stt`.`lang`='&1'
                LEFT JOIN `sys_trans_text` `stt_en` ON `stt_en`.`trans_id`=`sys_trans`.`id` AND `stt_en`.`lang`='EN'
                WHERE `sys_trans`.`text`!=''",
            $language_upper
        );

        $variables = array();
        $this->loadNodeTextFile($variables, $opt['logic']['node']['id'] . '.txt', $language_lower);
        $this->loadNodeTextFile(
            $variables,
            $opt['logic']['node']['id'] . '-' . $language_lower . '.txt',
            $language_lower
        );

        while ($r = sql_fetch_assoc($rs)) {
            $trans = $r['trans'];
            if ($trans !== null && $trans != $r['text']) {
                $trans = $this->substitueVariables($variables, $language_lower, $trans);

                fwrite($f, 'msgid "' . $this->escape_text($r['text']) . '"' . "\n");
                fwrite($f, 'msgstr "' . $this->escape_text($trans) . '"' . "\n");
                fwrite($f, "\n");
            }
        }
        sql_free_result($rs);

        fclose($f);

        try {
            exec(
                'msgfmt -o ' . escapeshellcmd(
                    $opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES/messages.mo'
                ) . ' ' . escapeshellcmd(
                    $opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES/messages.po'
                )
            );
        } catch (Exception $e) {
            // @todo implement login
        }
    }

    /* escape string for po-file
     */
    private function escape_text($text)
    {
        $text = mb_ereg_replace('\\\\', '\\\\', $text);
        $text = mb_ereg_replace('"', '\"', $text);
        $text = mb_ereg_replace("\r", "", $text);
        while (mb_substr($text, - 1, 1) == "\n") {
            $text = mb_substr($text, 0, mb_strlen($text) - 1);
        }
        $text = mb_ereg_replace("\n", "\\n\"\n\"", $text);

        return $text;
    }

    private function prepare_text($text)
    {
        $text = mb_ereg_replace("\t", ' ', $text);
        $text = mb_ereg_replace("\r", ' ', $text);
        $text = mb_ereg_replace("\n", ' ', $text);
        while (mb_strpos($text, '  ') !== false) {
            $text = mb_ereg_replace('  ', ' ', $text);
        }

        return $text;
    }

    /* add text to database
     */
    public function addText($text, $resource_name, $line)
    {
        global $opt;

        if ($text == '') {
            return;
        }

        $text = $this->prepare_text($text);

        $trans_id = sqlf_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $text);
        if ($trans_id == 0) {
            sqlf("INSERT INTO `sys_trans` (`text`) VALUES ('&1')", $text);
            $trans_id = sql_insert_id();
        }

        sqlf(
            "INSERT IGNORE INTO `sys_trans_ref` (`trans_id`, `resource_name`, `line`) VALUES ('&1', '&2', '&3')",
            $trans_id,
            $resource_name,
            $line
        );
    }

    /* clear sys_trans_ref to begin new translation of resource
     */
    public function clearReferences()
    {
        global $opt, $db;
        sqlf("DELETE FROM `sys_trans_ref`");
    }

    /* import strings from given field to sys_trans_text
     */
    public function importFromTable($table, $fname = 'name', $fid = 'trans_id')
    {
        $rs = sqlf(
            "SELECT `&1`.`&2`
            FROM `&1`
            LEFT JOIN `sys_trans`
                ON `&1`.`&3`=`sys_trans`.`id`
                AND `&1`.`&2`=`sys_trans`.`text`",
            $table,
            $fname,
            $fid
        );
        while ($r = sql_fetch_array($rs)) {
            if ($r[$fname] == '') {
                continue;
            }

            $lastId = sqlf_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $r[$fname]);
            if ($lastId == 0) {
                sqlf("INSERT INTO `sys_trans` (`text`) VALUES ('&1')", $r[$fname]);
                $lastId = sql_insert_id();
            }

            sqlf(
                "INSERT IGNORE INTO `sys_trans_ref` (`trans_id`, `resource_name`, `line`) VALUES ('&1', '&2', 0)",
                $lastId,
                'table:' . $table . ';field=' . $fname
            );
        }
        sql_free_result($rs);
        sqlf("UPDATE `&1` SET `&2`=0", $table, $fid);
        sqlf(
            "UPDATE `&1`, `sys_trans`
            SET `&1`.`&3`=`sys_trans`.`id`
            WHERE `&1`.`&2`=`sys_trans`.`text`",
            $table,
            $fname,
            $fid
        );
    }

    /* import variables for substition from config2/nodetext/
     */
    public function loadNodeTextFile(&$variables, $file, $language)
    {
        // generic load
        global $opt;

        $filename = $opt['rootpath'] . '/config2/nodetext/' . $file;
        if (file_exists($filename)) {
            $fhandle = fopen($filename, 'r');

            if ($fhandle) {
                while ($line = fgets($fhandle, 4096)) {
                    $pos = mb_strpos($line, ' ');
                    $variable = mb_substr($line, 0, $pos);
                    $substitution = mb_substr($line, $pos + 1, mb_strlen($line));
                    $substitution = rtrim($substitution);
                    $variables[$language][$variable] = $substitution;
                }
                fclose($fhandle);

                return true;
            }
        }

        return false;
    }

    public function substitueVariables(&$variables, $lang, $str)
    {
        $langstr = $str;

        // replace variables in string
        if (mb_ereg_search_init($langstr)) {
            while (false != $vars = mb_ereg_search_regs("%[^%]*%")) {
                foreach ($vars as $curly_pattern) {
                    // $curly_pattern contatins %pattern% in replacement string
                    $pattern = mb_substr($curly_pattern, 1, mb_strlen($curly_pattern) - 2);

                    // avoid recursive loop
                    if ($pattern != $str) {
                        if (isset($variables[$lang][$pattern])) {
                            $pattern_replacement = $variables[$lang][$pattern];

                            $langstr = mb_ereg_replace($curly_pattern, $pattern_replacement, $langstr);
                        }
                    }
                }
            }
        }

        return $langstr;
    }
}
