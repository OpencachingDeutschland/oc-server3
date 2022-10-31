<?php

declare(strict_types=1);

namespace Oc\Repository;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerRepository
{
    public LoggerInterface $logger;

    public MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * Example implementation of email send function. Can be adapted if necessary.
     *
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
    ): void {
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
     * @throws TransportExceptionInterface
     */
    public function sendActivationEmail(string $userName, string $email, string $activationCode): void
    {
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
