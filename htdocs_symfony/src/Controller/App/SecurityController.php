<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Security\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    public function __construct(
        private Auth $auth,
    ) {}

    #[Route("/login", name: "security_login")]
    public function login(Request $request): Response
    {
        $error = null;
        $lastUsername = '';

        if ($request->isMethod('POST')) {
            $username = trim((string) $request->request->get('username', ''));
            $password = (string) $request->request->get('password', '');

            if ($username !== '' && $password !== '') {
                $user = $this->auth->login($username, $password);
                if ($user) {
                    $response = $this->redirectToRoute('app_index_index');
                    $cookie = $this->auth->getLoginCookie();
                    if ($cookie) {
                        $response->headers->setCookie($cookie);
                    }
                    return $response;
                }
                $error = 'Invalid credentials.';
                $lastUsername = $username;
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route("/logout", name: "security_logout", methods: ["GET"])]
    public function logout(): Response
    {
        $this->auth->logout();
        return $this->redirectToRoute('app_security_login');
    }
}
