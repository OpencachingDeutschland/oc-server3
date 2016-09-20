<?php

/* ***
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 
* RSS Class for Opencaching.de
* *************************************
* @author: Tobias Hannaske (poker4ace)
* @date: 7th of December 2010
* @todo-ticket-id: #422
*** */

/**
 * Class RSS
 */
class RSS
{
    public $title;
    public $link;
    public $description;
    public $language = 'de-DE';
    public $pubDate;
    public $items;
    public $tags;

    /**
     * RSS constructor.
     */
    public function __construct()
    {
        $this->items = [];
        $this->tags = [];
    }

    /**
     * @param $item
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @param $when
     */
    public function setPubDate($when)
    {
        $this->pubDate = date('d.m.y H:i:s');
    }

    /**
     * @return string
     */
    public function getPubDate()
    {
        if (empty($this->pubDate)) {
            return date('d.m.y H:i:s') . 'GMT';
        } else {
            return $this->pubDate;
        }
    }

    /**
     * @param $tag
     * @param $value
     */
    public function addTag($tag, $value)
    {
        $this->tags[$tag] = $value;
    }

    /**
     * @return string
     */
    public function out()
    {
        $out = $this->header();
        $out .= "<channel>\n";
        $out .= '<title>' . $this->title . "</title>\n";
        $out .= '<link>' . $this->link . "</link>\n";
        $out .= '<description>' . $this->description . "</description>\n";
        $out .= '<language>' . $this->language . "</language>\n";
        $out .= '<pubDate>' . $this->getPubDate() . "</pubDate>\n";

        foreach ($this->tags as $key => $val) {
            $out .= "<$key>$val</$key>\n";
        }
        foreach ($this->items as $item) {
            $out .= $item->out();
        }

        $out .= "</channel>\n";

        $out .= $this->footer();

        $out = str_replace('&', '&amp;', $out);

        return $out;
    }

    /**
     * @param string $contentType
     */
    public function serve($contentType = 'application/xml')
    {
        $xml = $this->out();
        header("Content-type: $contentType");
        echo $xml;
    }

    /**
     * @return string
     */
    public function header()
    {
        $out = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $out .= '<rss version="2.0">' . "\n";

        return $out;
    }

    /**
     * @return string
     */
    public function footer()
    {
        return '</rss>';
    }
}

/**
 * Class RSSItem
 */
class RSSItem
{
    public $title;
    public $link;
    public $description;
    public $pubDate;
    public $guid;
    public $tags;
    public $attachment;
    public $length;
    public $mimetype;

    /**
     * RSSItem constructor.
     */
    public function __construct()
    {
        $this->tags = array();
    }

    /**
     * @param $when
     */
    public function setPubDate($when)
    {

        $this->pubDate = date('d.m.y H:i:s');

    }

    /**
     * @return false|string
     */
    public function getPubDate()
    {
        if (empty($this->pubDate)) {
            return date('d.m.y H:i:s');
        } else {
            return $this->pubDate;
        }
    }

    /**
     * @param $tag
     * @param $value
     */
    public function addTag($tag, $value)
    {
        $this->tags[$tag] = $value;
    }

    /**
     * @return string
     */
    public function out()
    {
        $out = "<item>\n";
        $out .= '<title>' . $this->title . "</title>\n";
        $out .= '<link>' . $this->link . "</link>\n";
        $out .= '<description>' . $this->description . "</description>\n";
        $out .= '<pubDate>' . $this->getPubDate() . "</pubDate>\n";

        if ($this->attachment !== '') {
            $out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";
        }

        if (empty($this->guid)) {
            $this->guid = $this->link;
        }
        $out .= '<guid>' . $this->guid . "</guid>\n";

        foreach ($this->tags as $key => $val) {
            $out .= "<$key>$val</$key\n>";
        }
        $out .= "</item>\n";

        return $out;
    }

    /**
     * @param $url
     * @param $mimetype
     * @param $length
     */
    public function enclosure($url, $mimetype, $length)
    {
        $this->attachment = $url;
        $this->mimetype = $mimetype;
        $this->length = $length;
    }

    /**
     * @param $input
     *
     * @return string
     */
    public function rss_clear($input)
    {
        $umlaute = [
            "/ä/",
            "/ö/",
            "/ü/",
            "/Ä/",
            "/Ö/",
            "/Ü/",
            "/ß/"
        ];
        $replace = [
            "ae",
            "oe",
            "ue",
            "Ae",
            "Oe",
            "Ue",
            "ss"
        ];

        return mb_ereg_replace($umlaute, $replace, $input);
    }
}
