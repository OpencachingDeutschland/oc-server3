<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\CacheNote;

use Oc\Libse\Coordinate\CoordinateCoordinate;
use Oc\Libse\Coordinate\PresenterCoordinate;
use Oc\Libse\Validator\AlwaysValidValidator;

class PresenterCacheNote
{
    const REQ_NOTE = 'note';
    const REQ_INCL_COORD = 'incl_coord';
    const TPL_CACHE_ID = 'cacheid';
    const TPL_NOTE_ID = 'noteid';
    const TPL_NOTE = 'note';
    const TPL_INCL_COORD = 'inclCoord';
    const IMAGE = 'resource2/ocstyle/images/misc/wp_note.png';

    private $request;
    private $translator;
    private $coordinate;
    private $userId;
    private $noteId;
    private $cacheId;
    private $note;
    private $cacheNoteHandler;

    public function __construct($request = false, $translator = false)
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->coordinate = new PresenterCoordinate($this->request, $this->translator);
    }

    public function init($cacheNoteHandler, $userId, $cacheId)
    {
        $this->cacheNoteHandler = $cacheNoteHandler;
        $this->userId = $userId;
        $this->cacheId = $cacheId;

        $cacheNote = $cacheNoteHandler->getCacheNote($userId, $cacheId);
        $this->noteId = $cacheNote['id'];
        $this->note = $cacheNote['note'];
        $this->coordinate->init($cacheNote['latitude'], $cacheNote['longitude']);
    }

    public function prepare($template)
    {
        $template->assign(self::TPL_NOTE_ID, $this->noteId);
        $template->assign(self::TPL_CACHE_ID, $this->cacheId);
        $template->assign(self::TPL_NOTE, $this->getNote());
        $template->assign(self::TPL_INCL_COORD, $this->coordinate->hasCoordinate());
        $this->coordinate->prepare($template);
    }

    public function validate()
    {
        $this->request->validate(self::REQ_INCL_COORD, new AlwaysValidValidator());
        $this->request->validate(self::REQ_NOTE, new AlwaysValidValidator());

        if ($this->includeCoordinate()) {
            $this->coordinate->validate();
            // Removed false-return for invalid coordinate, so that at least the note will be saved.
            // validate() produces some formal valid coordinate.
            // -- following 25 May 2015
        } else {
            $this->coordinate->init(0, 0);
        }

        return true;
    }

    public function doSubmit()
    {
        $coordinate = $this->getCoordinate();

        $this->cacheNoteHandler->save(
            $this->noteId,
            $this->userId,
            $this->cacheId,
            $this->getNote(),
            $coordinate->latitude(),
            $coordinate->longitude()
        );
    }

    private function getNote()
    {
        return $this->request->get(self::REQ_NOTE, $this->note);
    }

    private function getCoordinate()
    {
        if ($this->includeCoordinate()) {
            return $this->coordinate->getCoordinate();
        }

        return new CoordinateCoordinate(0, 0);
    }

    private function includeCoordinate()
    {
        return $this->request->get(self::REQ_INCL_COORD);
    }
}
