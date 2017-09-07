<?php

namespace okapi\views\devel\sysinfo;

use okapi\Response\OkapiHttpResponse;

class View
{
    public static function call()
    {
        # This is a hidden page for OKAPI developers. It will output some
        # useful, non-sensitive infos on system settings.

        $body = '';

        $loaded_extensions = get_loaded_extensions();
        $query_extensions = array('exif', 'gd');
        $ok = array_intersect($query_extensions, $loaded_extensions);
        $missing = array_diff($query_extensions, $loaded_extensions);
        $body .= "Loaded PHP extensions: " . ($ok ? implode(', ', $ok) : "-none-") . "\n";
        $body .= "Missing PHP extensions: " . ($missing ? implode(', ', $missing) : "-none-") . "\n";

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        $response->body = $body;

        return $response;
    }
}
