<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\MailerRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

/**
 *
 */
class MailerControllerBackend extends AbstractController
{
    /** @var LoggerInterface */
    public LoggerInterface $logger;

    /** @var MailerInterface */
    public MailerInterface $mailer;

    /** @var MailerRepository */
    public MailerRepository $mailerRepository;

    /**
     * @param LoggerInterface $logger
     * @param MailerInterface $mailer
     * @param MailerRepository $mailerRepository
     */
    public function __construct(LoggerInterface $logger, MailerInterface $mailer, MailerRepository $mailerRepository)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->mailerRepository = $mailerRepository;
    }
}
