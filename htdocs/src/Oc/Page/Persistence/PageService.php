<?php

namespace Oc\Page\Persistence;

use Oc\Repository\Exception\RecordNotFoundException;

class PageService
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Fetches a page by slug.
     */
    public function fetchOneBy(array $where = []): ?PageEntity
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
     */
    public function create(PageEntity $entity): PageEntity
    {
        return $this->pageRepository->create($entity);
    }

    /**
     * Update a page in the database.
     */
    public function update(PageEntity $entity): PageEntity
    {
        return $this->pageRepository->update($entity);
    }

    /**
     * Removes a page from the database.
     */
    public function remove(PageEntity $entity): PageEntity
    {
        return $this->pageRepository->remove($entity);
    }
}
