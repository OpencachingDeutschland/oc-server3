<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Form\CachesFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_index")
     * @Security("is_granted('ROLE_TEAM')")
     */
    public function index()
    : Response
    {
        $searchForm = $this->createForm(CachesFormType::class);

        return $this->render('backend/user/index.html.twig', [
                                                               'userSearchForm' => $searchForm->createView(),
                                                           ]
        );
    }
}
