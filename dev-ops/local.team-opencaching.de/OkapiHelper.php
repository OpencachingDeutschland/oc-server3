<?php

class OkapiHelper
{
    public static function updateOkapiMeta()
    {
        $meta = [
            'version_number' => self::getVersionNumber(),
            'git_revision' => self::getGitRevision(),
        ];

        self::saveMeta($meta);
    }

    private static function saveMeta($meta)
    {
        $metaFile = '<?php return ' . var_export($meta, true) . ';';
        file_put_contents(__DIR__ . '/../../htdocs/okapi/meta.php', $metaFile);
    }

    private static function getVersionNumber()
    {
        $okapiRootPath = dirname(dirname(__DIR__)) . '/htdocs/vendor/opencaching/okapi';

        return ((int) shell_exec('cd ' . $okapiRootPath . ' && git rev-list HEAD --count') + 318);
    }

    private static function getGitRevision()
    {
        $okapiRootPath = dirname(dirname(__DIR__)) . '/htdocs/vendor/opencaching/okapi';

        return shell_exec('cd ' . $okapiRootPath . ' && git rev-parse HEAD');
    }
}
