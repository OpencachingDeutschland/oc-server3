<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new phpbbtopics());

class phpbbtopics
{
	var $name = 'phpbbtopics';
	var $interval = 600;
	var $topiclist = array();

	function run()
	{
		global $opt;

		foreach ($opt['cron']['phpbbtopics']['forumids'] AS $id)
		{
			$url = $opt['cron']['phpbbtopics']['url'];
			$url = str_replace('{id}', $id, $url);

			if ($this->processUrl($url) == false)
				return;
		}

		usort($this->topiclist, 'sort_compare_updated');
		$this->topiclist = array_slice($this->topiclist, 0, $opt['cron']['phpbbtopics']['count']);

		$f = fopen($opt['rootpath'] . 'cache2/phpbb.inc.php', 'w');
		fwrite($f, '<?php' . "\n");
		fwrite($f, '$phpbb_topics = unserialize("' . str_replace('"', '\\"', serialize($this->topiclist)) . '");' . "\n");
		fwrite($f, '?>');
		fclose($f);
	}

	function processUrl($url)
	{
		global $opt;

		$maxsize = 100 * 1024; // max. 100kB

		$content = @read_file($url, $maxsize);
		if ($content === false)
			return false;

		$xml = @simplexml_load_string($content);
		if ($xml === false)
			return false;

		if (!isset($xml->entry))
			return false;

		foreach ($xml->entry AS $item)
		{
			$posting = array();

			$sTitle = $item->title;
			if (mb_strpos($sTitle, '•') !== false)
				$sTitle = mb_trim(mb_substr($sTitle, mb_strpos($sTitle, '•') + 1));
			$sTitle = strip_tags($sTitle);
			$sTitle = htmlspecialchars_decode($sTitle);
			$sTitle = preg_replace('/\\&.*\\;/U', '', $sTitle);

			$nTopicId = crc32($item->id); // workaround ...
/*
			$nTopicId = $item->id;
			if (mb_strpos($nTopicId, 't=') !== false)
				$nTopicId = mb_trim(mb_substr($nTopicId, mb_strpos($nTopicId, 't=') + 2));
			if (mb_strpos($nTopicId, '&') !== false)
				$nTopicId = mb_trim(mb_substr($nTopicId, 0, mb_strpos($nTopicId, '&')));
			$nTopicId = $nTopicId+0;
*/
			$tUpdated = strtotime($item->updated);

			$sUsername = (string)$item->author->name;
			$sUsername = htmlspecialchars_decode($sUsername);
			$sUsername = preg_replace('/\\&.*\\;/U', '', $sUsername);

			foreach ($item->link->Attributes() AS $key=>$value)
			{
				if ($key == 'href')
					$sLink = (string)$value;
			}
/*
			$sContent = $item->content;
			$sContent = str_replace('<br />', ' ', $sContent);
			$sContent = preg_replace('/\\<div class\\=\\"quotetitle\\"\\>.*\\<\\/div\\>/U', '', $sContent);
			$sContent = preg_replace('/\\<div class\\=\\"quotecontent\\"\\>.*\\<\\/div\\>/U', '', $sContent);
			if (mb_strpos($sContent, '<p>Statistik:') !== false)
				$sContent = mb_trim(mb_substr($sContent, 0, mb_strpos($sContent, '<p>Statistik:')));
			$sContent = strip_tags($sContent);
			$sContent = htmlspecialchars_decode($sContent);
			$sContent = preg_replace('/\\&.*\\;/U', '', $sContent);
			if (mb_strlen($sContent) > $opt['cron']['phpbbtopics']['maxcontentlength'])
				$sContent = mb_substr($sContent, 0, $opt['cron']['phpbbtopics']['maxcontentlength']) . "(...)";
*/
			$posting['id'] = $nTopicId;
			$posting['title'] = $sTitle;
			$posting['updated'] = $tUpdated;
			$posting['username'] = $sUsername;
			$posting['link'] = $sLink;
//			$posting['content'] = $sContent;

			if ($nTopicId != 0)
			{
				if (isset($this->topiclist[$nTopicId]) && $posting['updated']>$this->topiclist[$nTopicId]['updated'])
					$this->topiclist[$nTopicId] = $posting;
				else if (!isset($this->topiclist[$nTopicId]))
					$this->topiclist[$nTopicId] = $posting;
			}
		}

		return true;
	}
}

function sort_compare_updated($a, $b)
{
	return ($b['updated'] - $a['updated']);
}
?>