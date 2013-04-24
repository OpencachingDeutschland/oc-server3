<?php



class RSSParser {
	
	/**
	 * parse
	 * @param int $items number of feeditems to parse from feed
	 * @param string $url url of the feed to parse
	 * @return string $item feeditems as HTML-string
	 */
	public static function parse($items,$url,$includetext) {
	
		global $tpl;
		
    if ($items <= 0)
      return '';
    
		// error
		$error = false;
		$rss = array();
		
		// check $url
		if(!preg_match('!^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\:\,\?\'\\\+&amp;%\$#\=~_\-]+))*$!',$url)) {
			$error = true;
		} else {
			
			$tpl->assign('includetext',$includetext);
			
			// get xml-data
			$data = @file_get_contents($url);
			
			// check data
			if($data === false || strpos($data, 'rss version=') === false) {
				$error = true;
			} else {
				
				// parse XML
				try {
	
					// get SimpleXML-object
					$xml = new SimpleXMLElement($data);
					
					$i=0;
					$headlines = array();
					// walk through items
					foreach($xml->channel->item as $item) {
						
						// check length
						if($items != 0 && $i >= $items) {
							break;
						} else {
							
							// add html
							if ($includetext)
							{
								// fill array
								$rss[] = array(
										'pubDate' => strftime('%e. %B %Y',strtotime($item->pubDate)),
										'title' => $item->title,
										'link' => $item->link,
										'description' => $item->description
									);
								// increment counter
								$i++;
							}
							else if (!in_array($item->title,$headlines) &&
							         strpos($item->title,'VERSCHOBEN') === FALSE)  // hack to exclude forum thread-move messages
							{
								// fill array
								$rss[] = array(
										'pubDate' => strftime('%e. %B %Y',strtotime($item->pubDate)),
										'title' => $item->title,
										'link' => $item->link
									);
								$headlines[] = "" . $item->title;
								// increment counter
								$i++;
							}
						}
					}
				}
				catch(Exception $e) {
					$error = true;
				}
			}
		}
		
		// assign to template
		$tpl->assign('rsserror',$error);
		
		// return
		return $rss;
	}
}

?>