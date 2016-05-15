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
    public $name = 'phpbbtopics';
    public $interval = 600;
    public $topiclist = [];

    public function run()
    {
        global $opt;

        foreach ($opt['cron']['phpbbtopics']['forumids'] as $id) {
            $url = $opt['cron']['phpbbtopics']['url'];
            $url = str_replace('{id}', $id, $url);

            if ($this->processUrl($url) == false) {
                return;
            }
        }

        usort($this->topiclist, 'sort_compare_updated');
        $this->topiclist = array_slice($this->topiclist, 0, $opt['cron']['phpbbtopics']['count']);

        $f = fopen(__DIR__ . '/../../cache2/phpbb.inc.php', 'w');
        fwrite($f, '<?php' . "\n");
        fwrite(
            $f,
            '$phpbb_topics = unserialize("' . str_replace('"', '\\"', serialize($this->topiclist)) . '");' . "\n"
        );
        fwrite($f, '?>');
        fclose($f);
    }

    public function processUrl($url)
    {
        global $opt;

        $maxsize = 100 * 1024; // max. 100kB

        $content = @read_file($url, $maxsize);
        if ($content === false) {
            return false;
        }

        $xml = @simplexml_load_string($content);
        if ($xml === false) {
            return false;
        }

        if (!isset($xml->entry)) {
            return false;
        }

        foreach ($xml->entry as $item) {
            $posting = [];

            $sTitle = $item->title;
            if (mb_strpos($sTitle, '•') !== false) {
                $sTitle = mb_trim(mb_substr($sTitle, mb_strpos($sTitle, '•') + 1));
            }
            $sTitle = strip_tags($sTitle);
            $sTitle = htmlspecialchars_decode($sTitle);
            $sTitle = preg_replace('/\\&.*\\;/U', '', $sTitle);

            $nTopicId = crc32($item->id); // workaround ...
            $tUpdated = strtotime($item->updated);

            $sUsername = (string)$item->author->name;
            $sUsername = htmlspecialchars_decode($sUsername);
            $sUsername = preg_replace('/\\&.*\\;/U', '', $sUsername);

            foreach ($item->link->Attributes() as $key => $value) {
                if ($key == 'href') {
                    $sLink = (string)$value;
                }
            }
            $posting['id'] = $nTopicId;
            $posting['title'] = $sTitle;
            $posting['updated'] = $tUpdated;
            $posting['username'] = $sUsername;
            $posting['link'] = $sLink;

            if ($nTopicId != 0) {
                if (isset($this->topiclist[$nTopicId]) && $posting['updated'] > $this->topiclist[$nTopicId]['updated']) {
                    $this->topiclist[$nTopicId] = $posting;
                } else {
                    if (!isset($this->topiclist[$nTopicId])) {
                        $this->topiclist[$nTopicId] = $posting;
                    }
                }
            }
        }

        return true;
    }
}

function sort_compare_updated($a, $b)
{
    return ($b['updated'] - $a['updated']);
}
