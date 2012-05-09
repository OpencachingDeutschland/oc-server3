<?php

require_once($opt['rootpath'] . 'lib2/error.inc.php');

class ChildWp_Controller
{
  const req_cache_id = 'cacheid';
  const req_child_id = 'childid';
  const req_delete_id = 'deleteid';

  private $request;
  private $translator;

  public function __construct($request = false, $translator = false)
  {
    $this->request = $this->initRequest($request);
    $this->translator = $this->initTranslator($translator);
  }

  private function initRequest($request)
  {
    if ($request)
      return $request;

    return new Http_Request();
  }

  private function initTranslator($translator)
  {
    if ($translator)
      return $translator;

    return new Language_Translator();
  }

  public function createPresenter($template, $cacheManager, $childWpHandler)
  {
    $cacheId = $this->request->getForValidation(self::req_cache_id);

    $this->verifyCacheId($template, $cacheId, $cacheManager);

    $presenter = false;
    $childId = $this->request->getForValidation(self::req_child_id);
    $deleteId = $this->request->getForValidation(self::req_delete_id);

    if ($childId || $deleteId)
      $presenter = $this->createEditDeletePresenter($template, $childWpHandler, $cacheId, $childId, $deleteId);
    else
      $presenter = new ChildWp_AddPresenter($this->request, $this->translator);

    $presenter->init($childWpHandler, $cacheId);

    return $presenter;
  }

  private function createEditDeletePresenter($template, $childWpHandler, $cacheId, $childId, $deleteId)
  {
    $id = $childId ? $childId : $deleteId;

    $childWp = $childWpHandler->getChildWp($id);

    $this->verifyChildWp($template, $childWp, $cacheId);

    if ($childId)
      $presenter = new ChildWp_EditPresenter($this->request, $this->translator);
    else
      $presenter = new ChildWp_DeletePresenter($this->request, $this->translator);

    $presenter->initChildWp($id, $childWp);

    return $presenter;
  }

  private function verifyCacheId($template, $cacheId, $cacheManager)
  {
    if (!$cacheManager->exists($cacheId) || !$cacheManager->userMayModify($cacheId))
      $template->error(ERROR_CACHE_NOT_EXISTS);
  }

  private function verifyChildWp($template, $childWp, $cacheId)
  {
    if (empty($childWp) || $cacheId != $childWp['cacheid'])
      $template->error(ERROR_CACHE_NOT_EXISTS);
  }
}

?>
