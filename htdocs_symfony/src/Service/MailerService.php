<?php

declare(strict_types=1);

namespace Oc\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Simple mailer — replaces the old ORM-based MailerRepository.
 * Usage: $mailer->send($to, $subject, $body)
 */
class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromAddress = 'noreply@opencaching.de',
    ) {}

    public function send(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from($this->fromAddress)
            ->to($to)
            ->subject($subject)
            ->text($body);

        $this->mailer->send($email);
    }

    /** Send activation email for new user registration. */
    public function sendActivationEmail(string $username, string $to, string $activationCode): void
    {
        $subject = 'Activate your opencaching.de account';
        $body = "Hello $username,\n\n"
              . "Please activate your account by visiting:\n"
              . "https://www.opencaching.de/activate?code=$activationCode\n\n"
              . "Your opencaching.de team";

        $this->send($to, $subject, $body);
    }
}
