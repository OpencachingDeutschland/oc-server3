<?php

namespace Oc\Country;

use Oc\Repository\Exception\RecordsNotFoundException;

class CountryService
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Fetches all countries.
     *
     * @return CountryEntity[]
     */
    public function fetchAll(): array
    {
        try {
            $result = $this->countryRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Creates a country in the database.
     */
    public function create(CountryEntity $entity): CountryEntity
    {
        return $this->countryRepository->create($entity);
    }

    /**
     * Update a country in the database.
     */
    public function update(CountryEntity $entity): CountryEntity
    {
        return $this->countryRepository->update($entity);
    }

    /**
     * Removes a country from the database.
     */
    public function remove(CountryEntity $entity): CountryEntity
    {
        return $this->countryRepository->remove($entity);
    }
}
