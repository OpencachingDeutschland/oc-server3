<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class UserControllerBackend extends AbstractController
{
    /** @var Connection */
    private Connection $connection;

    /** @var UserRepository */
    private UserRepository $userRepository;

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
     * @throws Exception
     * @Route("/user", name="user_index")
     * @Security("is_granted('ROLE_TEAM')")
     */
    public function index(Request $request)
    : Response {
        $fetchedUsers = '';
        $searchForm = $this->createForm(CachesFormType::class);

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $inputData = $searchForm->getData();

            $fetchedUsers = $this->userRepository->getUsersForSearchField($inputData['content_searchfield']);
        }

        return $this->render('backend/user/index.html.twig', [
                                                               'userSearchForm' => $searchForm->createView(),
                                                               'all_users_by_searchfield' => $fetchedUsers
                                                           ]
        );
    }

    /**
     * @param int $userID
     *
     * @return Response
     * @Route("/user/profile/{userID}", name="user_by_id")
     * @throws RecordNotFoundException
     */
    public function search_by_user_id(int $userID)
    : Response {
        $fetchedUser = $this->userRepository->search_by_user_id($userID);

        return $this->render('backend/user/detailview.html.twig', ['user_by_id' => $fetchedUser]);
    }
}
