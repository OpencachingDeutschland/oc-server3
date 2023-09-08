<?php

namespace Oc\FieldNotes\Import;

use Oc\FieldNotes\Struct\FieldNote;

class StructMapper
{
    /**
     * Maps given array to field note struct.
     *
     * @return FieldNote[]
     */
    public function map(array $rows): array
    {
        $fieldNotes = [];

        foreach ($rows as $row) {
            $fieldNote = new FieldNote();
            $fieldNote->waypoint = $row[0] ?? null;
            $fieldNote->noticedAt = $row[1] ?? null;
            $fieldNote->logType = $row[2] ?? null;
            $fieldNote->notice = $row[3] ?? null;

            $fieldNotes[] = $fieldNote;
        }

        return $fieldNotes;
    }
}
