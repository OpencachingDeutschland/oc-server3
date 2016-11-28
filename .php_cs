<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->filter(function(SplFileInfo $file) {
        return $file->getFilename();
    })
    ->in(__DIR__ . '/htdocs')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder)
    ;
