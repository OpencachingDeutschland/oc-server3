<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\EventSubscriber\LocaleSubscriber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class LocaleController extends AbstractController
{
    #[Route('/locale/{locale}', name: 'locale_set', requirements: ['locale' => 'de|en'], methods: ['GET', 'POST'])]
    public function set(string $locale, Request $request): RedirectResponse
    {
        if (!in_array($locale, LocaleSubscriber::SUPPORTED, true)) {
            throw new NotFoundHttpException();
        }

        $request->getSession()->set(LocaleSubscriber::SESSION_KEY, $locale);

        $referer = $request->headers->get('referer');
        $target = $referer && str_starts_with($referer, $request->getSchemeAndHttpHost())
            ? $referer
            : '/';

        return new RedirectResponse($target);
    }
}
