<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SymfonyTestController extends AbstractController
{
    /**
     * @Route("/symfony-test/", name="symfony-test")
     *
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $this->setMenu(MNU_CACHES_SEARCH);
        $this->setTitle('Symfony Controlled');

        return $this->render('default/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/symfony-test/forced-login/", name="symfony-test.forced-login")
     *
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forcedLoginAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        $user = $this->getUser();
        dump($user->getRoles());

        $this->setMenu(MNU_CACHES_SEARCH);
        $this->setTitle('Symfony Controlled');

        return $this->render('default/index.html.twig', [
            'user' => $user
        ]);
    }
}
