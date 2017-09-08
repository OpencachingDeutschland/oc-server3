<?php

namespace okapi\core\Response;

class OkapiRedirectResponse extends OkapiHttpResponse
{
    public $url;
    public function __construct($url) { $this->url = $url; }
    public function display()
    {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$this->url);
    }
}
