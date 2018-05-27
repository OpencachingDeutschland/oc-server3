<?php

namespace Oc\Page\Persistence;

use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class BlockService
 */
class BlockService
{
    /**
     * @var BlockRepository
     */
    private $blockRepository;

    /**
     * BlockService constructor.
     *
     * @param BlockRepository $blockRepository
     */
    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    /**
     * Fetch by.
     *
     * @param array $where
     *
     * @return BlockEntity[]
     */
    public function fetchBy(array $where = [])
    {
        try {
            $result = $this->blockRepository->fetchBy($where);
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Creates a page in the database.
     *
     * @param BlockEntity $entity
     *
     * @return BlockEntity
     */
    public function create(BlockEntity $entity)
    {
        return $this->blockRepository->create($entity);
    }

    /**
     * Update a page in the database.
     *
     * @param BlockEntity $entity
     *
     * @return BlockEntity
     */
    public function update(BlockEntity $entity)
    {
        return $this->blockRepository->update($entity);
    }

    /**
     * Removes a page from the database.
     *
     * @param BlockEntity $entity
     *
     * @return BlockEntity
     */
    public function remove(BlockEntity $entity)
    {
        return $this->blockRepository->remove($entity);
    }
}
