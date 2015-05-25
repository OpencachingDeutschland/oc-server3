<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class CacheNote_Presenter
{
  const req_note = 'note';
  const req_incl_coord = 'incl_coord';
  const tpl_cache_id = 'cacheid';
  const tpl_note_id = 'noteid';
  const tpl_note = 'note';
  const tpl_incl_coord = 'inclCoord';
  const image = 'resource2/ocstyle/images/misc/wp_note.png';

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
    $this->coordinate = new Coordinate_Presenter($this->request, $this->translator);
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
    $template->assign(self::tpl_note_id, $this->noteId);
    $template->assign(self::tpl_cache_id, $this->cacheId);
    $template->assign(self::tpl_note, $this->getNote());
    $template->assign(self::tpl_incl_coord, $this->coordinate->hasCoordinate());
    $this->coordinate->prepare($template);
  }

  public function validate()
  {
    $this->request->validate(self::req_incl_coord, new Validator_AlwaysValid());
    $this->request->validate(self::req_note, new Validator_AlwaysValid());

    if ($this->includeCoordinate())
    {
      $this->coordinate->validate();
      // Removed false-return for invalid coordinate, so that at least the note will be saved.
      // validate() produces some formal valid coordinate.
      // -- following 25 May 2015
    }
    else
      $this->coordinate->init(0, 0);

    return true;
  }

  public function doSubmit()
  {
    $coordinate = $this->getCoordinate();

    $this->cacheNoteHandler->save($this->noteId, $this->userId, $this->cacheId, $this->getNote(), $coordinate->latitude(), $coordinate->longitude());
  }

  private function getNote()
  {
    return $this->request->get(self::req_note, $this->note);
  }

  private function getCoordinate()
  {
    if ($this->includeCoordinate())
      return $this->coordinate->getCoordinate();

    return new Coordinate_Coordinate(0, 0);
  }

  private function includeCoordinate()
  {
    return $this->request->get(self::req_incl_coord);
  }
}

?>