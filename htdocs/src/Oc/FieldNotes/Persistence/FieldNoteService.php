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

    public function __construct(FieldNoteRepository $fieldNoteRepository)
    {
        $this->fieldNoteRepository = $fieldNoteRepository;
    }

    /**
     * Fetches all field notes.
     *
     * @return FieldNoteEntity[]
     */
    public function fetchAll(): array
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
     * @return FieldNoteEntity[]
     */
    public function fetchBy(array $where = [], array $order = []): array
    {
        try {
            $result = $this->fieldNoteRepository->fetchBy($where, $order);
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetches a page by given where clause.
     */
    public function fetchOneBy(array $where = []): ?FieldNoteEntity
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
     */
    public function create(FieldNoteEntity $entity): FieldNoteEntity
    {
        return $this->fieldNoteRepository->create($entity);
    }

    /**
     * Update a field note in the database.
     */
    public function update(FieldNoteEntity $entity): FieldNoteEntity
    {
        return $this->fieldNoteRepository->update($entity);
    }

    /**
     * Removes a field note from the database.
     */
    public function remove(FieldNoteEntity $entity): FieldNoteEntity
    {
        return $this->fieldNoteRepository->remove($entity);
    }

    /**
     * Fetch all field notes for field note listing in profile.
     *
     * @return FieldNoteEntity[]
     */
    public function getUserListing(int $userId): array
    {
        $fieldNotes = $this->fetchBy([
            'user_id' => $userId,
        ], [
            'date' => 'ASC',
            'id' => 'ASC',
        ]);

        return $fieldNotes ?: [];
    }

    /**
     * Fetches the latest field note for given user id.
     */
    public function getLatestUserFieldNote(int $userId): ?FieldNoteEntity
    {
        try {
            $result = $this->fieldNoteRepository->getLatestUserFieldNote($userId);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }
}
