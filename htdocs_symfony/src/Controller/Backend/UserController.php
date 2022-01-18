<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class UserController extends AbstractController
{
    /** @var Connection */
    private $connection;

    /** @var UserRepository */
    private $userRepository;

    /**
     * @param Connection $connection
     * @param UserRepository $userRepository
     */
    public function __construct(Connection $connection, UserRepository $userRepository)
    {
        $this->connection = $connection;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/user", name="user_index")
     * @Security("is_granted('ROLE_TEAM')")
     */
    public function index(Request $request)
    : Response
    {
        $fetchedUsers = '';
        $searchForm = $this->createForm(CachesFormType::class);

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $inputData = $searchForm->getData();

            $fetchedUsers = $this->getUsersForSearchField($inputData['content_searchfield']);
        }


        return $this->render('backend/user/index.html.twig', [
                                                               'userSearchForm' => $searchForm->createView(),
                                                               'all_users_by_searchfield' => $fetchedUsers
                                                           ]
        );
    }

    /**
     * @param string $searchtext
     *
     * @return array
     */
    public function getUsersForSearchField(string $searchtext)
    : array {
        //        SELECT user_id, username
        //        FROM user
        //        WHERE user_id      =       "' . $searchtext . '"
        //        OR user.username   LIKE    "%' . $searchtext . '%"'
        $qb = $this->connection->createQueryBuilder();
        $qb->select('user.user_id', 'user.username')
            ->from('user')
            ->where('user.user_id = :searchTerm')
            ->orWhere('user.username LIKE :searchTermLIKE')
            ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
            ->orderBy('user.username', 'ASC');

        return $qb->execute()->fetchAll();
    }

    /**
     * @param int $userID
     *
     * @return Response
     * @Route("/user/{userID}", name="user_by_id")
     */
    public function search_by_user_id(int $userID)
    : Response {
        $fetchedUser = [];

        try {
            $fetchedUser = $this->userRepository->fetchOneById($userID);
        } catch (Exception $e) {
            //  tue was.. (status_not_found = true);
        }

        return $this->render('backend/user/detailview.html.twig', ['user_by_id' => $fetchedUser]);
    }
}
