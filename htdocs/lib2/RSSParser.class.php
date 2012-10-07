<?php



class RSSParser {
	
	/**
	 * parse
	 * @param int $items number of feeditems to parse from feed
	 * @param string $url url of the feed to parse
	 * @return string $item feeditems as HTML-string
	 */
	public static function parse($items,$url) {
		
    if ($items <= 0)
      return '';
    
		// error
		$error = false;
		
		// check $url
		if(!preg_match('!^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$!',$url)) {
			$error = true;
		} else {
			
			// output
			$html = '<div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>'."\n";
			
			// get xml-data
			$data = file_get_contents($url);
			
			// check data
			if($data === false || strpos($data, 'rss version=') === false) {
				$error = true;
			} else {
				
				// parse XML
				try {
	
					// get SimpleXML-object
					$xml = new SimpleXMLElement($data);
					
					// walk through items
					$i=0;
					foreach($xml->channel->item as $item) {
						
						// check length
						if($items != 0 && $i >= $items) {
							break;
						} else {
							
							// add html
							$html .= '<p class="content-title-noshade-size2" style="display: inline;">'."\n";
							$html .= strftime('%e. %B %Y',strtotime($item->pubDate)).' - '. $item->title;
							$html .= '</p> <p style="line-height: 1.6em;display: inline;">&emsp;[<b><a class="link" href="'.$item->link.'">mehr...</a></b>]</p>'."\n";
							$html .= '<p>'.$item->description.'</p>'."\n";
						}
						
						// increment counter
						$i++;
					}
				
					// finish html
					$html .= '<div class="buffer" style="width: 500px;">&nbsp;</div>'."\n";
				}
				catch(Exception $e) {
					$error = true;
				}
			}
		}
		
		// return
		if(!$error) {
			return $html;
		} else {
			return '';
		}
	}
}

?>