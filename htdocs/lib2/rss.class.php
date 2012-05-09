<?php
/* ***
* RSS Class for Opencaching.de
* *************************************
* @author: Tobias Hannaske (poker4ace)
* @date: 7th of December 2010
* @todo-ticket-id: #422
*** */


	class RSS
	{
		var $title;
		var $link;
		var $description;
		var $language = "de-DE";
		var $pubDate;
		var $items;
		var $tags;

		function RSS()
		{
			$this->items = array();
			$this->tags  = array();
		}

		function addItem($item)
		{
			$this->items[] = $item;
		}

		function setPubDate($when)
		{
		$this->pubDate = date("d.m.y H:i:s");
		}

		function getPubDate()
		{
  			if(empty($this->pubDate))
				return date("d.m.y H:i:s") . "GMT";
			else
				return $this->pubDate;
		}

		function addTag($tag, $value)
		{
			$this->tags[$tag] = $value;
		}

		function out()
		{
			$out  = $this->header();
			$out .= "<channel>\n";
			$out .= "<title>" . $this->title . "</title>\n";
			$out .= "<link>" . $this->link . "</link>\n";
			$out .= "<description>" . $this->description . "</description>\n";
			$out .= "<language>" . $this->language . "</language>\n";
			$out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

			foreach($this->tags as $key => $val) $out .= "<$key>$val</$key>\n";
			foreach($this->items as $item) $out .= $item->out();

			$out .= "</channel>\n";
			
			$out .= $this->footer();

			$out = str_replace("&", "&amp;", $out);

			return $out;
		}
		
		function serve($contentType = "application/xml")
		{
			$xml = $this->out();
			header("Content-type: $contentType");
			echo $xml;
		}

		function header()
		{
			$out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			$out .= '<rss version="2.0">' . "\n";
			return $out;
		}

		function footer()
		{
			return '</rss>';
		}
	}

	class RSSItem
	{
		var $title;
		var $link;
		var $description;
		var $pubDate;
		var $guid;
		var $tags;
		var $attachment;
		var $length;
		var $mimetype;

		function RSSItem()
		{ 
			$this->tags = array();
		}

		function setPubDate($when)
		{
			
				$this->pubDate = date("d.m.y H:i:s") ;
							
		}

		function getPubDate()
		{
			if(empty($this->pubDate))
				return date("d.m.y H:i:s");
			else
				return $this->pubDate;
		}

		function addTag($tag, $value)
		{
			$this->tags[$tag] = $value;
		}

		function out()
		{
			$out = "<item>\n";
			$out .= "<title>" . $this->title . "</title>\n";
			$out .= "<link>" . $this->link . "</link>\n";
			$out .= "<description>" . $this->description . "</description>\n";
			$out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

			if($this->attachment != "")
				$out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";

			if(empty($this->guid)) $this->guid = $this->link;
			$out .= "<guid>" . $this->guid . "</guid>\n";

			foreach($this->tags as $key => $val) $out .= "<$key>$val</$key\n>";
			$out .= "</item>\n";
			return $out;
		}

		function enclosure($url, $mimetype, $length)
		{
			$this->attachment = $url;
			$this->mimetype   = $mimetype;
			$this->length     = $length;
		}
	
		function rss_clear($input) {
			$umlaute = Array("/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/");
			$replace = Array("ae","oe","ue","Ae","Oe","Ue","ss");
			$output = preg_replace($umlaute, $replace, $input);
			return $output;		
}	


	}
?>