<?php

namespace OcLegacy\Translation;

use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationService implements TranslatorInterface
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct()
    {
        $translator = new Translator('de', new MessageFormatter());
        $translator->setFallbackLocales(['en']);

        $yamlLoader = new YamlFileLoader();
        $translator->addLoader('yml', $yamlLoader);

        foreach (['de', 'fr', 'nl', 'es', 'pl', 'it', 'ru'] as $languageKey) {
            $translator->addResource('yml', __DIR__ . '/../../../app/Resources/translations/constants.' . $languageKey . '.yml', $languageKey);
            $translator->addResource('yml', __DIR__ . '/../../../app/Resources/translations/messages.' . $languageKey . '.yml', $languageKey);
            $translator->addResource('yml', __DIR__ . '/../../../app/Resources/translations/validators.' . $languageKey . '.yml', $languageKey);
        }

        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        return $this->translator->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }
}
