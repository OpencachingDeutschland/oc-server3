<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use DateTime;
use Doctrine\DBAL\Connection;
use Exception;
use Oc\Form\UserLoginBlockConfirm;
use Oc\Security\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserLoginBlockController extends AbstractController
{
    public function __construct(
        private Connection $connection,
        private UrlGeneratorInterface $urlGenerator,
        private Auth $auth,
    ) {}

    /**
     * @throws Exception
     */
    #[Route("/ulb", name: "user_login_block")]
    public function showUserLoginBlockMessageOrRedirect(): Response
    {
        $form = $this->createForm(UserLoginBlockConfirm::class);
        $user = $this->auth->getUser();

        if ($user) {
            $block = $this->connection->createQueryBuilder()
                ->select('*')->from('user_login_block')
                ->where('user_id = :uid')->setParameter('uid', $user['user_id'])
                ->executeQuery()->fetchAssociative();

            if ($block) {
                $expirationTime = date_create_from_format('Y-m-d H:i:s', $block['login_block_until']);

                return $this->render('app/user/showuserloginblock.html.twig', [
                    'confirmButton' => $form->createView(),
                    'user_login_block' => $block,
                    'user_id' => $user['user_id'],
                    'login_block_expired' => $expirationTime < new DateTime("now")
                ]);
            }
        }

        return new RedirectResponse($this->urlGenerator->generate('app_index_index'));
    }

    /**
     * @throws Exception
     */
    #[Route("/cRM", name: "confirm_read_block_message")]
    public function confirmReadBlockMessage(Request $request): Response
    {
        $form = $this->createForm(UserLoginBlockConfirm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();
            $userId = (int) $inputData['hidden_user_id'];
            $this->connection->delete('user_login_block', ['user_id' => $userId]);
        }
        return new RedirectResponse($this->urlGenerator->generate('app_index_index'));
    }
}
