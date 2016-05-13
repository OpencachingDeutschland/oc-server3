<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class xml2Array
{

    public $stack = [];
    public $stack_ref;
    public $arrOutput = [];
    public $resParser;
    public $strXmlData;

    public function push_pos(&$pos)
    {
        $this->stack[count($this->stack)] =& $pos;
        $this->stack_ref =& $pos;
    }

    public function pop_pos()
    {
        unset($this->stack[count($this->stack) - 1]);
        $this->stack_ref =& $this->stack[count($this->stack) - 1];
    }

    public function parse($strInputXML)
    {

        $this->resParser = xml_parser_create("UTF-8");
        xml_set_object($this->resParser, $this);
        xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

        xml_set_character_data_handler($this->resParser, "tagData");

        $this->push_pos($this->arrOutput);

        $this->strXmlData = xml_parse($this->resParser, $strInputXML);
        if (!$this->strXmlData) {
            die(sprintf(
                "XML error: %s at line %d",
                xml_error_string(xml_get_error_code($this->resParser)),
                xml_get_current_line_number($this->resParser)
            ));
        }

        xml_parser_free($this->resParser);

        return $this->arrOutput;
    }

    public function tagOpen($parser, $name, $attrs)
    {
        if (isset($this->stack_ref[$name])) {
            if (!isset($this->stack_ref[$name][0])) {
                $tmp = $this->stack_ref[$name];
                unset($this->stack_ref[$name]);
                $this->stack_ref[$name][0] = $tmp;
            }
            $cnt = count($this->stack_ref[$name]);
            $this->stack_ref[$name][$cnt] = [];
            if (isset($attrs)) {
                $this->stack_ref[$name][$cnt] = $attrs;
            }
            $this->push_pos($this->stack_ref[$name][$cnt]);
        } else {
            $this->stack_ref[$name] = [];
            if (isset($attrs)) {
                $this->stack_ref[$name] = $attrs;
            }
            $this->push_pos($this->stack_ref[$name]);
        }
    }

    public function tagData($parser, $tagData)
    {
        if (mb_trim($tagData)) {
            if (isset($this->stack_ref['DATA'])) {
                $this->stack_ref['DATA'] .= $tagData;
            } else {
                $this->stack_ref['DATA'] = $tagData;
            }
        }
    }

    public function tagClosed($parser, $name)
    {
        $this->pop_pos();
    }
}
