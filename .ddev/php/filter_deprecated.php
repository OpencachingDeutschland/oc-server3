<?php
// Strip PHP 8.4 deprecation notices from output.
// These come from legacy Symfony 3.x vendor code and corrupt HTML responses
// by appearing before headers are sent. The notices go to the error log instead.
ob_start(function ($output) {
    return preg_replace(
        '/<br\s*\/?>\s*\n?<b>Deprecated<\/b>:[^<]*(?:<[^>]+>[^<]*)*<br\s*\/?>\s*\n?/i',
        '',
        $output
    );
});
