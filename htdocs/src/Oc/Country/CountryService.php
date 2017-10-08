<?php

namespace Oc\Country;

use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class CountryService
 *
 * @package Oc\Country
 */
class CountryService
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * CountryService constructor.
     *
     * @param CountryRepository $countryRepository
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Fetches all countries.
     *
     * @return CountryEntity[]
     */
    public function fetchAll()
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
     *
     * @param CountryEntity $entity
     *
     * @return CountryEntity
     */
    public function create(CountryEntity $entity)
    {
        return $this->countryRepository->create($entity);
    }

    /**
     * Update a country in the database.
     *
     * @param CountryEntity $entity
     *
     * @return CountryEntity
     */
    public function update(CountryEntity $entity)
    {
        return $this->countryRepository->update($entity);
    }

    /**
     * Removes a country from the database.
     *
     * @param CountryEntity $entity
     *
     * @return CountryEntity
     */
    public function remove(CountryEntity $entity)
    {
        return $this->countryRepository->remove($entity);
    }
}
