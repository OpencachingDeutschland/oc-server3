<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Class MailerRepository
 *
 * @package Oc\Repository
 */
class MailerRepository
{
    /** @var Connection */
    private Connection $connection;

    /** @var LoggerInterface */
    public LoggerInterface $logger;

    /** @var MailerInterface */
    public MailerInterface $mailer;

    /**
     * @param Connection $connection
     * @param LoggerInterface $logger
     * @param MailerInterface $mailer
     */
    public function __construct(Connection $connection, LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * Example implementation of email send function. Can be adapted if necessary.
     *
     * @param string $fromEmail
     * @param string $toEmail
     * @param string $ccEmail
     * @param string $bccEmail
     * @param string $subject
     * @param string $textMessage
     * @param string $htmlMessage
     * @param string|null $replyEmail
     * @param int $priority
     *
     * @return void
     * @throws TransportExceptionInterface
     *
     * https://symfony.com/doc/4.1/email/dev_environment.html#viewing-from-the-web-debug-toolbar
     */
    public function sendEmail(
        string $fromEmail,
        string $toEmail,
        string $ccEmail,
        string $bccEmail,
        string $subject,
        string $textMessage,
        string $htmlMessage,
        string $replyEmail = null,
        int $priority = Email::PRIORITY_NORMAL
    )
    : void {
        $emailTemplate = (new Email())
            ->from($fromEmail)
            ->to($toEmail)
            ->cc($ccEmail)
            ->bcc($bccEmail)
            ->replyTo($replyEmail)
            ->priority($priority)
            ->subject($subject)
            ->text($textMessage)
            ->html($htmlMessage);

        $this->mailer->send($emailTemplate);
        $this->logger->info('### Email sent (' . $fromEmail . ', ' . $toEmail . ', ' . $ccEmail . ', ' . $bccEmail . ' ### ');
    }

    /**
     * // TODO: ggf. mal mit obiger, allgemeingültiger Emailsendefunktion zusammenlegen
     * sends activation email for newly registeered users
     *
     * @param string $userName
     * @param string $email
     * @param string $activationCode
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendActivationEmail(string $userName, string $email, string $activationCode)
    : void {
        // TODO: Absenderadressen anpassen. Eventuell globale Variablen verwenden?
        $emailTemplate = (new Email())
            ->from('irgendwas@opencaching.de')
            ->to($email)
            ->replyTo('do_not_reply@opencaching.de')
            ->subject('Your registration at Opencaching.de!')
            ->text('Hello ' . $userName . '! Here is your activation code: ' . $activationCode . '. Have fun!');

        $this->mailer->send($emailTemplate);
        $this->logger->info('### Activation mail sent to ' . $email . ' ### ');
    }
}
