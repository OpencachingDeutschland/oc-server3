<?php

namespace okapi\core\Response;

class OkapiHttpResponse
{
    public $status = "200 OK";
    public $cache_control = "no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0";
    public $content_type = "text/plain; charset=utf-8";
    public $content_disposition = null;
    public $allow_gzip = true;
    public $connection_close = false;
    public $etag = null;

    /** Use this only as a setter, use get_body or print_body for reading! */
    public $body;

    /** This could be set in case when body is a stream of known length. */
    public $stream_length = null;

    public function get_length()
    {
        if (is_resource($this->body))
            return $this->stream_length;
        return strlen($this->body);
    }

    /** Note: You can call this only once! */
    public function print_body()
    {
        if (is_resource($this->body))
        {
            while (!feof($this->body))
                print fread($this->body, 1024*1024);
        }
        else
            print $this->body;
    }

    /**
     * Note: You can call this only once! The result might be huge (a stream),
     * it is usually better to print it directly with ->print_body().
     */
    public function get_body()
    {
        if (is_resource($this->body))
        {
            ob_start();
            fpassthru($this->body);
            return ob_get_clean();
        }
        else
            return $this->body;
    }

    /**
     * Print the headers and the body. This should be the last thing your script does.
     */
    public function display()
    {
        header("HTTP/1.1 ".$this->status);
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: ".$this->content_type);
        header("Cache-Control: ".$this->cache_control);
        if ($this->connection_close)
            header("Connection: close");
        if ($this->content_disposition)
            header("Content-Disposition: ".$this->content_disposition);
        if ($this->etag)
            header("ETag: $this->etag");

        # Make sure that gzip is supported by the client.
        $use_gzip = $this->allow_gzip;
        if (empty($_SERVER["HTTP_ACCEPT_ENCODING"]) || (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") === false))
            $use_gzip = false;

        # We will gzip the data ourselves, while disabling gziping by Apache. This way, we can
        # set the Content-Length correctly which is handy in some scenarios.

        if ($use_gzip && is_string($this->body))
        {
            # Apache won't gzip a response which is already gzipped.

            header("Content-Encoding: gzip");
            $gzipped = gzencode($this->body, 5);
            header("Content-Length: ".strlen($gzipped));
            print $gzipped;
        }
        else
        {
            # We don't want Apache to gzip this response. Tell it so.

            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', 1);
            }

            $length = $this->get_length();
            if ($length)
                header("Content-Length: ".$length);
            $this->print_body();
        }
    }
}
