<?php

namespace OcTest\Modules\OcLegacy;

use AppKernel;
use OcLegacy\Translation\TranslationService;
use OcTest\Modules\AbstractModuleTest;

class TranslationServiceTest extends AbstractModuleTest
{
    /**
     * @var TranslationService
     */
    private $translationService;

    /**
     * @return TranslationService
     */
    private function getTranslationService()
    {
        if ($this->translationService) {
            return $this->translationService;
        }

        $this->translationService = AppKernel::Container()->get(TranslationService::class);

        return $this->translationService;
    }

    public function test_transChoise()
    {
        self::assertEquals('Gefunden', $this->getTranslationService()->transChoice('field_notes.log_type.1', 0));
    }

    public function test_getLocale()
    {
        self::assertEquals('de', $this->getTranslationService()->getLocale());
    }
}
