<?php

namespace Oc\Page\Controller;

use Exception;
use Oc\AbstractController;
use Oc\Page\Exception\PageNotFoundException;
use Oc\Page\Exception\PageTranslationNotFoundException;
use Oc\Page\PageProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class PageController extends AbstractController
{
    /**
     * @var PageProvider
     */
    private $pageProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(PageProvider $pageProvider, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->pageProvider = $pageProvider;
    }

    /**
     * Index action to show given page by slug.
     *
     * @Route("/page/{slug}/", name="page")
     */
    public function indexAction(string $slug): Response
    {
        $this->setMenu(MNU_START);

        try {
            $pageStruct = $this->pageProvider->getPageBySlug($slug);
        } catch (PageNotFoundException $e) {
            throw $this->createNotFoundException();
        } catch (PageTranslationNotFoundException $e) {
            return $this->render('page/fallback.html.twig');
        } catch (Exception $e) {
            throw $this->createNotFoundException();
        }

        if ($pageStruct->isFallback()) {
            $this->addInfoMessage(
                $this->translator->trans('page.fallback.wrong_locale', [
                    '%language%' => $this->translator->trans('language.' . $pageStruct->getFallbackLocale()),
                ])
            );
        }

        return $this->render('page/index.html.twig', [
            'pageStruct' => $pageStruct,
        ]);
    }
}
