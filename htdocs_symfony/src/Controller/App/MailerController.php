<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\MailerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 */
class MailerController extends AbstractController
{
    /** @var MailerRepository */
    public MailerRepository $mailerRepository;

    /**
     * @param MailerRepository $mailerRepository
     */
    public function __construct(MailerRepository $mailerRepository)
    {
        $this->mailerRepository = $mailerRepository;
    }
}
