<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use DateTime;
use Exception;
use Oc\Form\UserLoginBlockConfirm;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\UserLoginBlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserLoginBlockController extends AbstractController
{
    private SecurityController $securityController;

    private UrlGeneratorInterface $urlGenerator;

    private UserLoginBlockRepository $userLoginBlockRepository;

    public function __construct(SecurityController $securityController, UrlGeneratorInterface $urlGenerator, UserLoginBlockRepository $userLoginBlockRepository)
    {
        $this->securityController = $securityController;
        $this->urlGenerator = $urlGenerator;
        $this->userLoginBlockRepository = $userLoginBlockRepository;
    }

    /**
     * @Route("/ulb", name="user_login_block")
     * @throws Exception
     */
    public function showUserLoginBlockMessageOrRedirect(): Response
    {
        $form = $this->createForm(UserLoginBlockConfirm::class);
        $user = $this->getUser();

        if ($user) {
            try {
                $userLoginBlock = $this->userLoginBlockRepository->fetchOneBy(['user_id' => $user->userId]);
                $expirationTime = date_create_from_format('Y-m-d H:i:s', $userLoginBlock->loginBlockUntil);

                // Force user logout. User first has to confirm reading the block message before the login block is removed and he can login normally.
                // TODO: logout() einbauen, sobald Funktion verfügbar ist. (wird erst ab Symfony 6.2 bereitgestellt. Davor gibt es nichts praktikables..)
                // https://github.com/symfony/symfony/issues/40663
                // https://symfony.com/doc/current/security.html

                return $this->render('app/user/showuserloginblock.html.twig', [
                        'confirmButton' => $form->createView(),
                        'user_login_block' => $userLoginBlock,
                        'user_id' => $user->userId,
                        'login_block_expired' => $expirationTime < new DateTime("now")
                ]);
            } catch (RecordNotFoundException $e) {
            }
        }

        return new RedirectResponse($this->urlGenerator->generate('app_index_index'));
    }

    /**
     * @Route("/cRM", name="confirm_read_block_message")
     * @throws Exception
     */
    public function confirmReadBlockMessage(Request $request): Response
    {
        $form = $this->createForm(UserLoginBlockConfirm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $userLoginBlock = $this->userLoginBlockRepository->fetchOneBy(['user_id' => (string)$inputData['hidden_user_id']]);
            $this->userLoginBlockRepository->remove($userLoginBlock);
        }
        return new RedirectResponse($this->urlGenerator->generate('app_index_index'));
    }
}
