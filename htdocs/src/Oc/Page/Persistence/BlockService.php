<?php

namespace Oc\Page\Persistence;

use Oc\Repository\Exception\RecordsNotFoundException;

class BlockService
{
    /**
     * @var BlockRepository
     */
    private $blockRepository;

    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    /**
     * @return BlockEntity[]
     */
    public function fetchBy(array $where = []): array
    {
        try {
            $result = $this->blockRepository->fetchBy($where);
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Creates a block in the database.
     */
    public function create(BlockEntity $entity)
    {
        return $this->blockRepository->create($entity);
    }

    /**
     * Update a block in the database.
     */
    public function update(BlockEntity $entity): BlockEntity
    {
        return $this->blockRepository->update($entity);
    }

    /**
     * Removes a block from the database.
     */
    public function remove(BlockEntity $entity): BlockEntity
    {
        return $this->blockRepository->remove($entity);
    }
}
