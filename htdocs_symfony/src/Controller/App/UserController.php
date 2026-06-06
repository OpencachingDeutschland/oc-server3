<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Oc\Security\Auth;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private Auth $auth,
    ) {}

    #[Route('/user', name: 'user_index')]
    public function index(): Response
    {
        return $this->render('app/user/search.html.twig', [
            'isSupport' => $this->auth->isGranted('ROLE_SUPPORT_TRAINEE'),
        ]);
    }

    #[Route('/api/users/search', name: 'api_users_search')]
    public function apiSearch(Request $request): JsonResponse
    {
        $q         = trim($request->query->get('q', ''));
        $isSupport = $this->auth->isGranted('ROLE_SUPPORT_TRAINEE');

        if ($q === '') {
            return new JsonResponse(['items' => []]);
        }

        $rows = $this->userRepository->searchUsers($q, $isSupport);

        $items = array_map(fn($r) => array_filter([
            'userId'     => (int)$r['user_id'],
            'username'   => $r['username'],
            'email'      => $isSupport ? ($r['email'] ?? '') : null,
            'joinedDate' => $isSupport ? substr((string)$r['date_created'], 0, 10) : null,
            'findCount'  => (int)$r['find_count'],
            'hideCount'  => (int)$r['hide_count'],
            'profileUrl' => '/user/profile/' . $r['user_id'],
        ], fn($v) => $v !== null), $rows);

        return new JsonResponse(['items' => array_values($items)]);
    }

    #[Route('/user/profile/{userID}', name: 'user_by_id')]
    public function search_by_user_id(int $userID): Response
    {
        $fetchedUser = $this->userRepository->search_by_user_id($userID);

        $stats = $this->userRepository->fetchUserStats($userID);

        if ($stats) {
            $fetchedUser['findCount'] = (int)$stats['findCount'];
            $fetchedUser['hideCount'] = (int)$stats['hideCount'];
        }

        return $this->render('app/user/detailview.html.twig', ['user_by_id' => $fetchedUser]);
    }
}
