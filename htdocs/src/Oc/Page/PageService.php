<?php

namespace Oc\Page;

use Oc\Repository\Exception\RecordNotFoundException;

/**
 * Class PageService
 *
 * @package Oc\Page
 */
class PageService
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * PageService constructor.
     *
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Fetches a page by slug.
     *
     * @param array $where
     *
     * @return null|PageEntity
     */
    public function fetchOneBy(array $where = [])
    {
        try {
            $result = $this->pageRepository->fetchOneBy($where);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Creates a page in the database.
     *
     * @param PageEntity $entity
     *
     * @return PageEntity
     */
    public function create(PageEntity $entity)
    {
        return $this->pageRepository->create($entity);
    }

    /**
     * Update a page in the database.
     *
     * @param PageEntity $entity
     *
     * @return PageEntity
     */
    public function update(PageEntity $entity)
    {
        return $this->pageRepository->update($entity);
    }

    /**
     * Removes a page from the database.
     *
     * @param PageEntity $entity
     *
     * @return PageEntity
     */
    public function remove(PageEntity $entity)
    {
        return $this->pageRepository->remove($entity);
    }
}
