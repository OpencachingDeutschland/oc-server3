<?php

namespace Oc\Language;

use Oc\Repository\Exception\RecordsNotFoundException;

class LanguageService
{
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * Fetches all languages.
     *
     * @return LanguageEntity[]
     */
    public function fetchAll(): array
    {
        try {
            $result = $this->languageRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetches all translated languages.
     *
     * @return LanguageEntity[]
     */
    public function fetchAllTranslated(): array
    {
        try {
            $result = $this->languageRepository->fetchAllTranslated();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Creates a language in the database.
     */
    public function create(LanguageEntity $entity): LanguageEntity
    {
        return $this->languageRepository->create($entity);
    }

    /**
     * Update a language in the database.
     */
    public function update(LanguageEntity $entity): LanguageEntity
    {
        return $this->languageRepository->update($entity);
    }

    /**
     * Removes a language from the database.
     */
    public function remove(LanguageEntity $entity): LanguageEntity
    {
        return $this->languageRepository->remove($entity);
    }

    /**
     * Fetches all translated languages and aggregates them to an array of locales.
     *
     * @return string[]
     */
    public function getAvailableTranslations(): array
    {
        $translatedLanguages = $this->fetchAllTranslated();

        $locales = [];

        foreach ($translatedLanguages as $translatedLanguage) {
            $locales[] = strtolower($translatedLanguage->short);
        }

        return $locales;
    }
}
