<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private Connection $connection,
    ) {}

    #[Route('/user', name: 'user_index')]
    public function index(): Response
    {
        return $this->render('app/user/search.html.twig');
    }

    #[Route('/api/users/search', name: 'api_users_search')]
    public function apiSearch(Request $request): JsonResponse
    {
        $q = trim($request->query->get('q', ''));

        if ($q === '') {
            return new JsonResponse(['items' => []]);
        }

        $rows = $this->connection->fetchAllAssociative(
            'SELECT u.user_id, u.username, u.date_created,
                    IFNULL(s.found, 0)  AS find_count,
                    IFNULL(s.hidden, 0) AS hide_count
             FROM user u
             LEFT JOIN stat_user s ON u.user_id = s.user_id
             WHERE u.user_id = :exact
                OR u.email   = :exact
                OR u.username LIKE :like
             ORDER BY u.username ASC
             LIMIT 200',
            ['exact' => $q, 'like' => '%' . $q . '%']
        );

        $items = array_map(fn($r) => [
            'userId'      => (int)$r['user_id'],
            'username'    => $r['username'],
            'joinedDate'  => substr((string)$r['date_created'], 0, 10),
            'findCount'   => (int)$r['find_count'],
            'hideCount'   => (int)$r['hide_count'],
            'profileUrl'  => '/user/profile/' . $r['user_id'],
        ], $rows);

        return new JsonResponse(['items' => $items]);
    }

    #[Route('/user/profile/{userID}', name: 'user_by_id')]
    public function search_by_user_id(int $userID): Response
    {
        $fetchedUser = $this->userRepository->search_by_user_id($userID);

        return $this->render('app/user/detailview.html.twig', ['user_by_id' => $fetchedUser]);
    }
}
