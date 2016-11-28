<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->filter(function(SplFileInfo $file) {
        return $file->getFilename();
    })
    ->exclude('/htdocs/cache2')
    ->exclude('/htdocs/vendor')
    ->in(__DIR__ . '/htdocs')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder)
    ;
