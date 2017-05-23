<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    /**
     * Index action to show given page by slug.
     *
     * @param string $slug
     *
     * @return Response
     *
     * @Route("/page/{slug}/", name="page")
     */
    public function indexAction($slug)
    {
        $slug = strtolower($slug);
//        $this->setMenu(MNU_MYPROFILE_CONTENT_PAGES);

        $repository = $this->getDoctrine()->getRepository('AppBundle:PageGroup');
        $pageBlocksQuery = $repository->getPageBlocksBySlugQuery($slug);

        $result = $pageBlocksQuery->execute();

        echo'<pre>';
        print_r($result);
        echo'</pre>';
        die();


//        $pageGroup->getPageBlocks();
//
//        if (count($contentPages) === 0) {
//            throw $this->createNotFoundException('Page not found.');
//        }

        return $this->render('@App/Impressum/index.html.twig', [
//            'contentPages' => $contentPages
        ]);
    }

}
