<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

  require('./lib2/web.inc.php');

  $tpl->name = 'childwp';
  $tpl->menuitem = MNU_CACHES_HIDE;

  $login->verify();

  if ($login->userid == 0)
    $tpl->redirect_login();

  $isSubmit = isset($_POST['submitform']);
  $redirect = isset($_POST['back']);

  $cacheManager = new Cache_Manager();
  $handler = new ChildWp_Handler();
  $controller = new ChildWp_Controller();
  $presenter = $controller->createPresenter($tpl, $cacheManager, $handler);

  if ($isSubmit && $presenter->validate())
  {
    $presenter->doSubmit();
    $redirect = true;
  }

  if ($redirect)
    $tpl->redirect('editcache.php?cacheid=' . $presenter->getCacheId());

  $presenter->prepare($tpl);

  $tpl->display();

?>