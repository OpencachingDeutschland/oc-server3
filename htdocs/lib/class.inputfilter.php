<?php

	// Unicode Reminder メモ
			
$allowedtags = mb_split(',', 'a,b,i,p,q,s,u,br,dd,dl,dt,em,h1,h2,h3,h4,h5,h6,hr,li,td,th,tr,tt,ol,ul,big,bdo,col,dfn,del,dir,div,ins,img,kbd,map,pre,sub,sup,var,abbr,area,cite,code,font,menu,samp,span,small,thead,tfoot,tbody,table,strong,center,strike,acronym,address,caption,isindex,colgroup,fieldset');
$allowedattr = mb_split(',', 'id,src,alt,dir,rel,rev,abbr,axis,char,cite,face,href,lang,name,size,span,type,align,class,clear,color,frame,ismap,rules,scope,shape,start,style,title,value,width,border,coords,height,hspace,nowrap,nohref,target,usemap,vspace,valign,bgcolor,charoff,charset,colspan,compact,headers,noshade,rowspan,summary,longdesc,hreflang,datetime,tabindex,accesskey,background,cellspacing,cellpadding');


/** @class: InputFilter (PHP4 & PHP5, with comments)
  * @project: PHP Input Filter
  * @date: 10-05-2005
  * @version: 1.2.2_php4/php5
  * @author: Daniel Morris
  * @contributors: Gianpaolo Racca, Ghislain Picard, Marco Wandschneider, Chris Tobin and Andrew Eddie.
  * @copyright: Daniel Morris
  * @email: dan@rootcube.com
  * @license: GNU General Public License (GPL)
  */
class InputFilter 
{
	var $tagsArray;			// default = empty array
	var $attrArray;			// default = empty array

	var $tagsMethod;		// default = 0
	var $attrMethod;		// default = 0

	var $xssAuto;           // default = 1
	var $tagBlacklist = array('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
	var $attrBlacklist = array('action', 'codebase', 'dynsrc', 'lowsrc');  // also will strip ALL event handlers
		
	/** 
	  * Constructor for inputFilter class. Only first parameter is required.
	  * @access constructor
	  * @param Array $tagsArray - list of user-defined tags
	  * @param Array $attrArray - list of user-defined attributes
	  * @param int $tagsMethod - 0= allow just user-defined, 1= allow all but user-defined
	  * @param int $attrMethod - 0= allow just user-defined, 1= allow all but user-defined
	  * @param int $xssAuto - 0= only auto clean essentials, 1= allow clean blacklisted tags/attr
	  */
	function inputFilter($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1) {		
		// make sure user defined arrays are in lowercase
		for ($i = 0; $i < count($tagsArray); $i++)
			$tagsArray[$i] = mb_strtolower($tagsArray[$i]);

		for ($i = 0; $i < count($attrArray); $i++)
			$attrArray[$i] = mb_strtolower($attrArray[$i]);

		// assign to member vars
		$this->tagsArray = (array)$tagsArray;
		$this->attrArray = (array)$attrArray;
		$this->tagsMethod = $tagsMethod;
		$this->attrMethod = $attrMethod;
		$this->xssAuto = $xssAuto;
	}
	
	/** 
	  * Method to be called by another php script. Processes for XSS and specified bad code.
	  * @access public
	  * @param Mixed $source - input string/array-of-string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function process($source)
	{
		// clean all elements in this array
		if (is_array($source))
		{
			// filter element for XSS and other 'bad' code etc.
			foreach($source as $key => $value)
				if (is_string($value)) $source[$key] = $this->remove($this->decode($value));
			
			return $source;
		
			// clean this string
		}
		else if (is_string($source)) 
		{
			// filter source for XSS and other 'bad' code etc.
			return $this->remove($this->decode($source));
			
			// return parameter as given
		} 
		else
			return $source;	
	}

	/** 
	  * Internal method to iteratively remove all unwanted tags and attributes
	  * @access protected
	  * @param String $source - input string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function remove($source)
	{
		$loopCounter=0;

		// provides nested-tag protection
		while($source != $this->filterTags($source)) 
		{
			$source = $this->filterTags($source);
			$loopCounter++;
		}
		
		return $source;
	}	
	
	/** 
	  * Internal method to strip a string of certain tags
	  * @access protected
	  * @param String $source - input string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function filterTags($source) 
	{
		// filter pass setup
		$preTag = NULL;
		$postTag = $source;
		
		// find initial tag's position
		$tagOpen_start = mb_strpos($source, '<');
		
		// interate through string until no tags left
		while($tagOpen_start !== FALSE) 
		{
			// process tag interatively
			$preTag .= mb_substr($postTag, 0, $tagOpen_start);
			$postTag = mb_substr($postTag, $tagOpen_start);
			$fromTagOpen = mb_substr($postTag, 1);

			// end of tag
			$tagOpen_end = mb_strpos($fromTagOpen, '>');
			if ($tagOpen_end === false) break;

			// next start of tag (for nested tag assessment)
			$tagOpen_nested = mb_strpos($fromTagOpen, '<');
			if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end)) 
			{
				$preTag .= mb_substr($postTag, 0, ($tagOpen_nested+1));
				$postTag = mb_substr($postTag, ($tagOpen_nested+1));
				$tagOpen_start = mb_strpos($postTag+1, '<');
				continue;
			}

			$tagOpen_nested = (mb_strpos($fromTagOpen, '<') + $tagOpen_start + 1);
			$currentTag = mb_substr($fromTagOpen, 0, $tagOpen_end);
			$tagLength = mb_strlen($currentTag);
			if (!$tagOpen_end) 
			{
				$preTag .= $postTag;
				$tagOpen_start = mb_strpos($postTag, '<');			
			}

			// this is needed when additional spaces between attrname and attrvalue or tagname and first attrname
			$currentTag = $this->wellFormTagWithAttr($currentTag);

			// iterate through tag finding attribute pairs - setup
			$tagLeft = $currentTag;
			$attrSet = array();
			$currentSpace = mb_strpos($tagLeft, ' ');
			
			// is end tag
			if (mb_substr($currentTag, 0, 1) == "/") 
			{
				$isCloseTag = TRUE;
				list($tagName) = mb_split(' ', $currentTag);
				$tagName = mb_substr($tagName, 1);
			
				// is start tag
			}
			else
			{
				$isCloseTag = FALSE;
				list($tagName) = mb_split(' ', $currentTag);
			}

			// excludes all "non-regular" tagnames OR no tagname OR remove if xssauto is on and tag is blacklisted
			if ((!mb_eregi("^[a-z][a-z0-9]*$",$tagName)) || (!$tagName) || ((in_array(mb_strtolower($tagName), $this->tagBlacklist)) && ($this->xssAuto)))
			{ 				
				$postTag = mb_substr($postTag, ($tagLength + 2));
				$tagOpen_start = mb_strpos($postTag, '<');

				// don't append this tag
				continue;
			}

			// this while is needed to support attribute values with spaces in!
			while ($currentSpace !== FALSE) 
			{
				$fromSpace = mb_substr($tagLeft, ($currentSpace+1));
				$nextSpace = mb_strpos($fromSpace, ' ');
				$openQuotes = mb_strpos($fromSpace, '"');
				$closeQuotes = mb_strpos(mb_substr($fromSpace, ($openQuotes+1)), '"') + $openQuotes + 1;

				// another equals exists
				if (mb_strpos($fromSpace, '=') !== FALSE) 
				{
					if (($openQuotes !== FALSE) && (mb_strpos(mb_substr($fromSpace, ($openQuotes+1)), '"') !== FALSE) && ($openQuotes < $nextSpace))
					{
						// opening and closing quotes exists
						$attr = mb_substr($fromSpace, 0, ($closeQuotes + 1));
					}
					else
					{
						// one or neither exist
						$attr = mb_substr($fromSpace, 0, $nextSpace);
					}
				}
				else
				{
					// no more equals exist
					$attr = mb_substr($fromSpace, 0, $nextSpace);
				}
				
				// last attr pair
				if (!$attr) $attr = $fromSpace;
				
				// add to attribute pairs array
				$attrSet[] = $attr;
				
				// next inc
				$tagLeft = mb_substr($fromSpace, mb_strlen($attr));
				$currentSpace = mb_strpos($tagLeft, ' ');
			}
			
			// check the last element of attrSet ... maybe empty or attr="value"/
			if (count($attrSet) > 0)
			{
				if ($attrSet[count($attrSet) - 1] == '')
					unset($attrSet[count($attrSet) - 1]);
				
				if (mb_substr($attrSet[count($attrSet) - 1], -1) == '/')
					$attrSet[count($attrSet) - 1] = mb_substr($attrSet[count($attrSet) - 1], 0, mb_strlen($attrSet[count($attrSet) - 1]) - 1);
			}

			// appears in array specified by user
			$tagFound = in_array(mb_strtolower($tagName), $this->tagsArray);			
			
			// remove this tag on condition
			if ((!$tagFound && $this->tagsMethod) || ($tagFound && !$this->tagsMethod)) 
			{
				// reconstruct tag with allowed attributes
				if (!$isCloseTag) 
				{
					$attrSet = $this->filterAttr($attrSet);
					$preTag .= '<' . $tagName;

					for ($i = 0; $i < count($attrSet); $i++)
						$preTag .= ' ' . $attrSet[$i];
					
					// reformat single tags to XHTML
					if (mb_strpos($fromTagOpen, "</" . $tagName))
						$preTag .= '>';
					else
						$preTag .= ' />';
				
				// just the tagname
			   } 
			   else 
					$preTag .= '</' . $tagName . '>';
			}

			// find next tag's start
			$postTag = mb_substr($postTag, ($tagLength + 2));
			$tagOpen_start = mb_strpos($postTag, '<');			
		}

		// append any code after end of tags
		$preTag .= $postTag;
		return $preTag;
	}

	/** 
	  * Internal method to strip a tag of certain attributes
	  * @access protected
	  * @param Array $attrSet
	  * @return Array $newSet
	  */
	function filterAttr($attrSet) 
	{	
		$newSet = array();

		// process attributes
		for ($i = 0; $i <count($attrSet); $i++) 
		{
			// skip blank spaces in tag
			if (!$attrSet[$i]) continue;

			// split into attr name and value
			$attrSubSet = mb_split('=', trim($attrSet[$i]));
			list($attrSubSet[0]) = mb_split(' ', $attrSubSet[0]);

			// bugfix ... '=' inside attributes
			$aCount = count($attrSubSet);
			for ($aN = 2; $aN < $aCount; $aN++)
				$attrSubSet[1] .= '=' . $attrSubSet[$aN];
			while (count($attrSubSet) > 2)
				unset($attrSubSet[count($attrSubSet) - 1]);

			// removes all "non-regular" attr names AND also attr blacklisted
			if ((!mb_eregi("^[a-z]*$",$attrSubSet[0])) || (($this->xssAuto) && ((in_array(mb_strtolower($attrSubSet[0]), $this->attrBlacklist)) || (mb_substr($attrSubSet[0], 0, 2) == 'on')))) 
				continue;

			// xss attr value filtering
			if ($attrSubSet[1]) 
			{
				// strips unicode, hex, etc
				$attrSubSet[1] = mb_ereg_replace('&#', '', $attrSubSet[1]);

				// strip normal newline within attr value
				$attrSubSet[1] = mb_ereg_replace('[\t\n\r\f]+', '', $attrSubSet[1]);

				// strip double quotes
				$attrSubSet[1] = mb_ereg_replace('"', '', $attrSubSet[1]);

				// [requested feature] convert single quotes from either side to doubles (Single quotes shouldn't be used to pad attr value)
				if ((mb_substr($attrSubSet[1], 0, 1) == "'") && (mb_substr($attrSubSet[1], (mb_strlen($attrSubSet[1]) - 1), 1) == "'"))
					$attrSubSet[1] = mb_substr($attrSubSet[1], 1, (mb_strlen($attrSubSet[1]) - 2));

				// strip slashes
				$attrSubSet[1] = stripslashes($attrSubSet[1]);
			}

			// auto strip attr's with "javascript:
			if (	((mb_strpos(mb_strtolower($attrSubSet[1]), 'expression') !== false) &&	(mb_strtolower($attrSubSet[0]) == 'style')) ||
					(mb_strpos(mb_strtolower($attrSubSet[1]), 'javascript:') !== false) ||
					(mb_strpos(mb_strtolower($attrSubSet[1]), 'behaviour:') !== false) ||
					(mb_strpos(mb_strtolower($attrSubSet[1]), 'vbscript:') !== false) ||
					(mb_strpos(mb_strtolower($attrSubSet[1]), 'mocha:') !== false) ||
					(mb_strpos(mb_strtolower($attrSubSet[1]), 'livescript:') !== false) 
			) continue;

			// if matches user defined array
			$attrFound = in_array(mb_strtolower($attrSubSet[0]), $this->attrArray);

			// keep this attr on condition
			if ((!$attrFound && $this->attrMethod) || ($attrFound && !$this->attrMethod)) 
			{
				// attr has value
				if (isset($attrSubSet[1]))
				{
					$newSet[] = $attrSubSet[0] . '="' . $attrSubSet[1] . '"';
				}
				else
				{
					// reformat single attributes to XHTML
					$newSet[] = $attrSubSet[0] . '="' . $attrSubSet[0] . '"';
				}
			}	
		}

		return $newSet;
	}
	
	/** 
	  * Try to convert to plaintext
	  * @access protected
	  * @param String $source
	  * @return String $source
	  */
	function decode($source) {
		// url decode
		// $source = html_entity_decode($source, ENT_QUOTES, "UTF-8");

		// convert decimal
		// $source = mb_ereg_replace('&#(\d+);',"chr(\\1)", $source);				// decimal notation

		// convert hex
		// $source = mb_eregi_replace('&#x([a-f0-9]+);',"chr(0x\\1)", $source);	// hex notation

		return $source;
	}

	/** 
	  * Method to be called by another php script. Processes for SQL injection
	  * @access public
	  * @param Mixed $source - input string/array-of-string to be 'cleaned'
	  * @param Buffer $connection - An open MySQL connection
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function safeSQL($source, &$connection) 
	{
		// clean all elements in this array
		if (is_array($source)) 
		{
			// filter element for SQL injection
			foreach($source as $key => $value)
				if (is_string($value))
					$source[$key] = $this->quoteSmart($this->decode($value), $connection);
			
			return $source;
		
		// clean this string
		}
		else if (is_string($source)) 
		{
			// filter source for SQL injection
			if (is_string($source)) return $this->quoteSmart($this->decode($source), $connection);
		
		// return parameter as given
		}
		else 
			return $source;	
	}

	/** 
	  * @author Chris Tobin
	  * @author Daniel Morris
	  * @access protected
	  * @param String $source
	  * @param Resource $connection - An open MySQL connection
	  * @return String $source
	  */
	function quoteSmart($source, &$connection) 
	{
		// strip slashes
		if (get_magic_quotes_gpc()) $source = stripslashes($source);

		// quote both numeric and text
		$source = $this->escapeString($source, $connection);

		return $source;
	}
	
	/** 
	  * @author Chris Tobin
	  * @author Daniel Morris
	  * @access protected
	  * @param String $source
	  * @param Resource $connection - An open MySQL connection
	  * @return String $source
	  */	
	function escapeString($string, &$connection) 
	{
		// depreciated function
		if (version_compare(phpversion(),"4.3.0", "<"))
		{
			mysql_escape_string($string);
			// current function
		}
		else
			mysql_real_escape_string($string);
		
		return $string;
	}
	
	/** 
	  * @author Oliver Dietz
	  * @access protected
	  * @param String $tag
	  * @return String $tag
	  *
	  * this function well forms the attrlist
    *
	  * examples
	  * input   ' a  href =  "abc" '
	  * output  'a href="abc"'
	  *
	  * input   ' / a href =  "abc" '
	  * output  '/a'
	  *
	  * input   ' a  href =  abc '
	  * output  'a href=abc'
	  *
	  */	
	function wellFormTagWithAttr($tag)
	{
		/** replace '  ' by ' '
		  * remove ' ' left and right from '='
		  * remove ' ' from beginning and end
		  * add a single or double quote if last quote is not terminated
		  * remove all attrs from closing tags
		  * remove cr's, lf's tab's and such things
		  * and do all that things (expect the last) only outside (single or double) quotes
		  */
	
		$tag = mb_ereg_replace('[\t\n\r\f]+', ' ', $tag);
		
		$pos = 0;
		$retval = '';
		$appendTermchar = false;
		
		while ($pos < mb_strlen($tag))
		{
			$nextdPos = mb_strpos($tag, '"', $pos);
			$nextsPos = mb_strpos($tag, '\'', $pos);

			if (($nextdPos === false) && ($nextsPos === false))
			{
				// keine weiteren Tags ... bis zum ende filtern
				$filter_len = mb_strlen($tag) - $pos;
				$no_filter_len = 0;
			}
			else
			{

				if ($nextdPos === false) $nextdPos = mb_strlen($tag) + 1;
				if ($nextsPos === false) $nextsPos = mb_strlen($tag) + 1;
				
				if ($nextsPos < $nextdPos)
				{
					$nextPos = $nextsPos;
					$termchar = '\'';
				}
				else
				{
					$nextPos = $nextdPos;
					$termchar = '"';
				}
				$filter_len = $nextPos - $pos + 1;

				// ok, wir haben einen Anfang ... nach dem Ende suchen
				$endFilter = mb_strpos($tag, $termchar, $nextPos + 1);
				
				if ($endFilter === false)
				{
					$appendTermchar = true;
					$no_filter_len = mb_strlen($tag) - $nextPos - 1;
				}
				else
				{
					$no_filter_len = $endFilter - $nextPos + 1;
				}
			}
			
			$retval .= $this->spaceReplace(mb_substr($tag, $pos, $filter_len));
			$pos += $filter_len;
			
			$retval .= mb_substr($tag, $pos, $no_filter_len);
			$pos += $no_filter_len;
		}
		
		if ($appendTermchar == true)
			$retval .= $termchar;
		
		if (mb_substr($retval, 0, 1) == '/')
		{
			//alle Attribute entfernen
			$spacePos = mb_strpos($retval, ' ');
			
			if ($spacePos !== false)
				$retval = mb_substr($retval, 0, $spacePos);
		}
		
		return $retval;
	}
	
	function spaceReplace($str)
	{
		while (mb_strpos($str, '  ') !== false)
  		$str = mb_ereg_replace('  ', ' ', $str);

  	if (mb_substr($str, 0, 1) == ' ')
  		$str = mb_substr($str, 1);

  	if (mb_substr($str, -1) == ' ')
  		$str = mb_substr($str, 0, mb_strlen($str) - 1);

		$str = mb_ereg_replace(' =', '=', $str);
		$str = mb_ereg_replace('= ', '=', $str);
		$str = mb_ereg_replace('/ ', '/', $str);
		
		if (mb_substr($str, -1) == '/')
			if (mb_substr($str, -2) != ' /')
				$str = mb_substr($str, 0, mb_strlen($str) - 1);

		return $str;
	}
}

?>