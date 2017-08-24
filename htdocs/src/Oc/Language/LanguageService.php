<?php

namespace Oc\Language;

use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class LanguageService
 *
 * @package Oc\Language
 */
class LanguageService
{
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * LanguageService constructor.
     *
     * @param LanguageRepository $languageRepository
     */
    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * Fetches all languages.
     *
     * @return LanguageEntity[]
     */
    public function fetchAll()
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
    public function fetchAllTranslated()
    {
        try {
            $result = $this->languageRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Creates a language in the database.
     *
     * @param LanguageEntity $entity
     *
     * @return LanguageEntity
     */
    public function create(LanguageEntity $entity)
    {
        return $this->languageRepository->create($entity);
    }

    /**
     * Update a language in the database.
     *
     * @param LanguageEntity $entity
     *
     * @return LanguageEntity
     */
    public function update(LanguageEntity $entity)
    {
        return $this->languageRepository->update($entity);
    }

    /**
     * Removes a language from the database.
     *
     * @param LanguageEntity $entity
     *
     * @return LanguageEntity
     */
    public function remove(LanguageEntity $entity)
    {
        return $this->languageRepository->remove($entity);
    }

    /**
     * Fetches all translated languages and aggregates them to an array of locales.
     *
     * @return string[]
     */
    public function getAvailableTranslations()
    {
        $translatedLanguages = $this->fetchAllTranslated();

        $locales = [];

        foreach ($translatedLanguages as $translatedLanguage) {
            $locales[] = strtolower($translatedLanguage->short);
        }

        return $locales;
    }
}
