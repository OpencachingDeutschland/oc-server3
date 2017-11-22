<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/lib2/web.inc.php';
$login->verify();

$loader = require __DIR__ . '/app/autoload.php';

$kernel = AppKernel::getInstance();
$request = Request::createFromGlobals();

$locale = strtolower($opt['template']['locale']);
$request->setLocale($locale);
$response = $kernel->handle($request);

/**
 * @var array $excludeRoutes Which routes should be displayed in the legacy layout?
 */
$excludeRoutes = [
    'field_notes.index'
];

if ($response->getStatusCode() === 404) {
    include __DIR__ . '/404.php';
    exit;
}

/**
 * @var array $excludeRoutes Which controller should not be compiled in symfony?
 */
if ($request->isXmlHttpRequest()
    || $response->isRedirection()
    || $request->getRequestFormat() !== 'html'
    || !in_array($request->attributes->get('_route'), $excludeRoutes, true)
    || preg_match('/\/_/', $request->getPathInfo()) === 1 // e.g. /_profiler/
    || ($response->headers->has('Content-Type')
    && strpos($response->headers->get('Content-Type'), 'html') === false)
) {
    $response->send();
    $kernel->terminate($request, $response);
    exit;
}

$response->sendHeaders();

if ($response->getStatusCode() === 404) {
    include __DIR__ . '/404.php';
    exit;
}

$content = $response->getContent();
$kernel->terminate($request, $response);

// the debug toolbar is appended only if there is a </body> tag. So we add one in base.html.twig and remove it here
$content = str_replace('</body>', '', $content);

$tpl->name = 'symfony';
$tpl->assign('content', $content);
$tpl->display();
