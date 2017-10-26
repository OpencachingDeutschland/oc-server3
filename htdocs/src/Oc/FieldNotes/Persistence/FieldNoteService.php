<?php

namespace Oc\FieldNotes\Persistence;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class FieldNoteService
{
    /**
     * @var FieldNoteRepository
     */
    private $fieldNoteRepository;

    /**
     * @param FieldNoteRepository $fieldNoteRepository
     */
    public function __construct(FieldNoteRepository $fieldNoteRepository)
    {
        $this->fieldNoteRepository = $fieldNoteRepository;
    }

    /**
     * Fetches all field notes.
     *
     * @return FieldNoteEntity[]
     */
    public function fetchAll()
    {
        try {
            $result = $this->fieldNoteRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetch by given where clause.
     *
     * @param array $where
     * @param array $order
     *
     * @return FieldNoteEntity[]
     */
    public function fetchBy(array $where = [], array $order = [])
    {
        try {
            $result = $this->fieldNoteRepository->fetchBy($where, $order);
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetches a page by slug.
     *
     * @param array $where
     *
     * @return null|FieldNoteEntity
     */
    public function fetchOneBy(array $where = [])
    {
        try {
            $result = $this->fieldNoteRepository->fetchOneBy($where);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Creates a field note in the database.
     *
     * @param FieldNoteEntity $entity
     *
     * @return FieldNoteEntity
     */
    public function create(FieldNoteEntity $entity)
    {
        return $this->fieldNoteRepository->create($entity);
    }

    /**
     * Update a field note in the database.
     *
     * @param FieldNoteEntity $entity
     *
     * @return FieldNoteEntity
     */
    public function update(FieldNoteEntity $entity)
    {
        return $this->fieldNoteRepository->update($entity);
    }

    /**
     * Removes a field note from the database.
     *
     * @param FieldNoteEntity $entity
     *
     * @return FieldNoteEntity
     */
    public function remove(FieldNoteEntity $entity)
    {
        return $this->fieldNoteRepository->remove($entity);
    }

    /**
     * Fetch all field notes for field note listing in profile.
     *
     * @param int $userId
     *
     * @return FieldNoteEntity[]
     */
    public function getUserListing($userId)
    {
        $fieldNotes = $this->fetchBy([
            'user_id' => $userId
        ], [
            'date' => 'ASC',
            'id' => 'ASC'
        ]);

        return $fieldNotes ?: [];
    }

    /**
     * Fetches the latest field note for given user id.
     *
     * @param int $userId
     *
     * @return FieldNoteEntity|null
     */
    public function getLatestUserFieldNote($userId)
    {
        try {
            $result = $this->fieldNoteRepository->getLatestUserFieldNote($userId);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }
}
