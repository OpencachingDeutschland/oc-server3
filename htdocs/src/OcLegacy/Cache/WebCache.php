<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace OcLegacy\Cache;

class WebCache
{
    /**
     * @var array
     */
    private $opt;

    /** @var \translate|\translateEdit $translate */
    private $translate;

    /**
     * WebCache constructor.
     */
    public function __construct()
    {
        global $opt, $translate;

        $this->opt = $opt;
        $this->translate = $translate;

        if (!isset($this->opt['rootpath'])) {
            $this->opt['rootpath'] = dirname(dirname(dirname(__DIR__))) . '/';
        }
    }
    
    public function clearCache(): void
    {
        $this->unlinkFiles('var/cache2', 'php');

        $this->unlinkFiles('var/cache2/smarty/cache', 'tpl');
        $this->unlinkFiles('var/cache2/smarty/compiled', 'inc');
        $this->unlinkFiles('var/cache2/smarty/compiled', 'php');
    }

    private function unlinkFiles(string $relBaseDir, string $ext): void
    {
        if (substr($relBaseDir, -1, 1) !== '/') {
            $relBaseDir .= '/';
        }

        if ($this->opt['rootpath'] . $relBaseDir) {
            if ($dh = opendir($this->opt['rootpath'] . $relBaseDir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' && is_file($this->opt['rootpath'] . $relBaseDir . $file)) {
                        if (substr($file, -(strlen($ext) + 1), strlen($ext) + 1) === '.' . $ext) {
                            unlink($this->opt['rootpath'] . $relBaseDir . $file);
                        }
                    }
                }
                closedir($dh);
            }
        }
    }

    public function createMenuCache(): void
    {
        global $opt;
        foreach ($this->opt['locale'] as $sLanguage => $v) {
            if ($this->opt['template']['locales'][$sLanguage]['status'] !== OC_LOCALE_DISABLED) {
                // cheating a little bit
                $opt['template']['locale'] = $sLanguage;
                \set_php_locale();

                if ($this->translate->t('INTERNAL_LANG', 'all', 'OcSmarty.class.php', '') !== $sLanguage) {
                    echo 'setlocale() failed to set language to ' . $sLanguage . "\n";
                    die("Is the translation of INTERNAL_LANG correct?\n");
                }

                // this will create the cache file
                $menu = new \Menu();

                // change to file owner
                chown($menu->sMenuFilename, $this->opt['httpd']['user']);
                chgrp($menu->sMenuFilename, $this->opt['httpd']['group']);
            }
        }
    }

    public function createLabelCache(): void
    {
        global $opt;

        foreach ($this->opt['locale'] as $sLanguage => $v) {
            if ($this->opt['template']['locales'][$sLanguage]['status'] !== OC_LOCALE_DISABLED) {
                // cheating a little bit
                $opt['template']['locale'] = $sLanguage;

                \labels::CreateCacheFile();

                // change to file owner
                $sFilename = $this->opt['rootpath'] . 'var/cache2/labels-' . $this->opt['template']['locale'] . '.inc.php';
                chown($sFilename, $this->opt['httpd']['user']);
                chgrp($sFilename, $this->opt['httpd']['group']);
            }
        }
    }

    public function preCompileAllTemplates(): void
    {
        if ($hDir = opendir($this->opt['stylepath'])) {
            while (($sFilename = readdir($hDir)) !== false) {
                if (substr($sFilename, -4) === '.tpl') {
                    $this->preCompileTemplate(substr($sFilename, 0, strlen($sFilename) - 4));
                }
            }
            closedir($hDir);
        }

        // fix file ownership
        $sCompileDir = $this->opt['rootpath'] . 'var/cache2/smarty/compiled/';
        if ($hDir = opendir($sCompileDir)) {
            while (($sFilename = readdir($hDir)) !== false) {
                if (filetype($sCompileDir . $sFilename) === 'file') {
                    chown($sCompileDir . $sFilename, $this->opt['httpd']['user']);
                    chgrp($sCompileDir . $sFilename, $this->opt['httpd']['group']);
                }
            }
            closedir($hDir);
        }
    }

    private function preCompileTemplate(string $sTemplate): void
    {
        foreach ($this->opt['locale'] as $sLanguage => $v) {
            if ($this->opt['template']['locales'][$sLanguage]['status'] !== OC_LOCALE_DISABLED) {
                $this->preCompileTemplateWithLanguage($sTemplate, $sLanguage);
            }
        }
    }

    private function preCompileTemplateWithLanguage(string $sTemplate, string $sLanguage): void
    {
        global $opt;

        // cheating a little bit
        $opt['template']['locale'] = $sLanguage;
        \set_php_locale();

        if ($this->translate->t('INTERNAL_LANG', 'all', 'OcSmarty.class.php', '') != $sLanguage) {
            die('setlocale() failed to set language to ' . $sLanguage . ". Is the translation of INTERNAL_LANG correct?\n");
        }

        $preTemplate = new \OcSmarty();
        $preTemplate->name = $sTemplate;
        $preTemplate->compile($sTemplate . '.tpl', $preTemplate->get_compile_id());
    }
}
