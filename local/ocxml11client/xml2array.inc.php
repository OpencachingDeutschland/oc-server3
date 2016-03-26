<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class xml2Array {

  var $stack=array();
  var $stack_ref;
   var $arrOutput = array();
   var $resParser;
   var $strXmlData;

  function push_pos(&$pos) {
   $this->stack[count($this->stack)]=&$pos;
   $this->stack_ref=&$pos;
  }

  function pop_pos() {
   unset($this->stack[count($this->stack)-1]);
   $this->stack_ref=&$this->stack[count($this->stack)-1];
  }

  function parse($strInputXML) {

   $this->resParser = xml_parser_create("UTF-8");
   xml_set_object($this->resParser,$this);
   xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

   xml_set_character_data_handler($this->resParser, "tagData");

   $this->push_pos($this->arrOutput);

   $this->strXmlData = xml_parse($this->resParser,$strInputXML );
   if(!$this->strXmlData) {
       die(sprintf("XML error: %s at line %d",
   xml_error_string(xml_get_error_code($this->resParser)),
   xml_get_current_line_number($this->resParser)));
   }

   xml_parser_free($this->resParser);

   return $this->arrOutput;
  }

  function tagOpen($parser, $name, $attrs) {
   if (isset($this->stack_ref[$name])) {
     if (!isset($this->stack_ref[$name][0])) {
       $tmp=$this->stack_ref[$name];
       unset($this->stack_ref[$name]);
       $this->stack_ref[$name][0]=$tmp;
     }
     $cnt=count($this->stack_ref[$name]);
     $this->stack_ref[$name][$cnt]=array();
     if (isset($attrs))
       $this->stack_ref[$name][$cnt]=$attrs;
     $this->push_pos($this->stack_ref[$name][$cnt]);
   }
   else {
     $this->stack_ref[$name]=array();
     if (isset($attrs))
       $this->stack_ref[$name]=$attrs;
     $this->push_pos($this->stack_ref[$name]);
   }
  }

  function tagData($parser, $tagData) {
   if(mb_trim($tagData)) {
     if(isset($this->stack_ref['DATA']))
       $this->stack_ref['DATA'] .= $tagData;
     else
       $this->stack_ref['DATA'] = $tagData;
   }
  }

   function tagClosed($parser, $name) {
       $this->pop_pos();
   }
}
