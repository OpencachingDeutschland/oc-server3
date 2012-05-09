<?php

require_once($opt['rootpath'] . 'lib2/error.inc.php');

abstract class ChildWp_Presenter
{
  const req_wp_type = 'wp_type';
  const req_wp_desc = 'desc';
  const tpl_page_title = 'pagetitle';
  const tpl_cache_id = 'cacheid';
  const tpl_child_id = 'childid';
  const tpl_delete_id = 'deleteid';
  const tpl_wp_type = 'wpType';
  const tpl_wp_desc = 'wpDesc';
  const tpl_wp_type_ids = 'wpTypeIds';
  const tpl_wp_type_names = 'wpTypeNames';
  const tpl_wp_type_error = 'wpTypeError';
  const tpl_submit_button = 'submitButton';
  const tpl_disabled = 'disabled';

  private $request;
  private $translator;
  protected $coordinate;
  private $waypointTypes = array();
  private $waypointTypeValid = true;
  protected $type = '0';
  protected $description;
  protected $cacheId;
  protected $childId;
  protected $childWpHandler;

  protected function __construct($request = false, $translator = false)
  {
    $this->request = $request;
    $this->translator = $translator;
    $this->coordinate = new Coordinate_Presenter($this->request, $this->translator);
  }

  public function doSubmit()
  {
    $this->onDoSubmit($this->coordinate->getCoordinate(), $this->getDesc());
  }

  abstract protected function onDoSubmit($coordinate, $description);

  protected function getType()
  {
    return $this->request->get(self::req_wp_type, $this->type);
  }

  private function getDesc()
  {
    return $this->request->get(self::req_wp_desc, $this->description);
  }

  public function init($childWpHandler, $cacheId)
  {
    $this->childWpHandler = $childWpHandler;
    $this->cacheId = $cacheId;
    $this->waypointTypes = $childWpHandler->getChildWpIdAndNames();
  }

  public function initChildWp($childId, $childWp)
  {
    $this->childId = $childId;
    $this->type = $childWp['type'];
    $this->description = $childWp['description'];
    $this->coordinate->init($childWp['latitude'], $childWp['longitude']);
  }

  public function prepare($template)
  {
    $template->assign(self::tpl_page_title, $this->translator->Translate($this->getTitle()));
    $template->assign(self::tpl_submit_button, $this->translator->Translate($this->getSubmitButton()));
    $template->assign(self::tpl_cache_id, $this->cacheId);
    $template->assign(self::tpl_wp_desc, $this->getDesc());
    $template->assign(self::tpl_wp_type, $this->getType());
    $template->assign(self::tpl_disabled, false);
    $this->prepareTypes($template);
    $this->coordinate->prepare($template);

    if (!$this->waypointTypeValid)
      $template->assign(self::tpl_wp_type_error, $this->translator->translate('Select waypoint type'));

    $this->onPrepare($template);
  }

  protected function onPrepare($template)
  {
  }

  private function prepareTypes($template)
  {
    $template->assign(self::tpl_wp_type_ids, $this->getWaypointTypeIds());
    $template->assign(self::tpl_wp_type_names, $this->waypointTypes);
  }

  abstract protected function getTitle();

  abstract protected function getSubmitButton();

  private function getWaypointTypeIds()
  {
    return array_keys($this->waypointTypes);
  }

  public function validate()
  {
    $wpTypeValidator = new Validator_Array($this->getWaypointTypeIds());

    $this->request->validate(self::req_wp_desc, new Validator_AlwaysValid());
    $this->waypointTypeValid = $this->request->validate(self::req_wp_type, $wpTypeValidator);
    $coordinateValid = $this->coordinate->validate();

    return $this->waypointTypeValid && $coordinateValid;
  }

  public function getCacheId()
  {
    return $this->cacheId;
  }
}

?>
