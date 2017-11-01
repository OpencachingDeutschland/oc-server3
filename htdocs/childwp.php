<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Oc\Libse\ChildWp\ControllerChildWp;
use Oc\Libse\ChildWp\HandlerChildWp;

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'childwp';
$tpl->menuitem = MNU_CACHES_HIDE;

$login->verify();

if ($login->userid == 0) {
    $tpl->redirect_login();
}

$isSubmit = isset($_POST['submitform']);
$redirect = isset($_POST['back']);

$cacheManager = AppKernel::Container()->get(Oc\Libse\Cache\ManagerCache::class);;
$handler = new HandlerChildWp();
$controller = new ControllerChildWp();
$presenter = $controller->createPresenter($tpl, $cacheManager, $handler);

if ($isSubmit && $presenter->validate()) {
    $presenter->doSubmit();
    $redirect = true;
}

if ($redirect) {
    $tpl->redirect('editcache.php?cacheid=' . $presenter->getCacheId());
}

$presenter->prepare($tpl);

$tpl->display();
