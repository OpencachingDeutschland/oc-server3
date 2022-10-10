<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Exception;
use Oc\Repository\OCOnly81Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OCOnly81Controller extends AbstractController
{
    private OCOnly81Repository $ocOnly81Repository;

    public function __construct(OCOnly81Repository $ocOnly81Repository)
    {
        $this->ocOnly81Repository = $ocOnly81Repository;
    }

    /**
     * @Route("/oconly81", name="oconly81_index")
     *
     * @throws Exception
     */
    public function ocOnly81Controller_index(): Response
    {
        $userData = $this->ocOnly81Repository->ocOnly81_get_user_counts();
        $matrixData = $this->ocOnly81Repository->ocOnly81_get_matrixData();

        return $this->render(
                'app/oconly81/index.html.twig', [
                        'ocOnly81_user' => $userData,
                        'ocOnly81_matrix' => $matrixData[0],
                        'ocOnly81_dsum' => $matrixData[1],
                        'ocOnly81_tsum' => $matrixData[2],
                        'ocOnly81_overall_sum' => $matrixData[3]
                ]
        );
    }
}
