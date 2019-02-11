<?php
/****************************************************************************
 * for license information see LICENSE.md
 * extract bounced and sent logs from postfix journal log and process
 * information for mail-log cronjob
 ****************************************************************************/

namespace Oc\Postfix;

use Doctrine\DBAL\Connection;

class JournalLogs
{
    /**
     * @var array|bool
     */
    private $config;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * constructor
     *
     * @param array|bool $config
     * $config = [
     *     'hostname' => 'host.domain.tld',
     *     'status' => ['sent', 'bounced', ...],
     * ]
     */
    public function __construct(Connection $connection, $config = false)
    {
        // TODO move this config to a configuration file
        if (!$config) {
            $config = [
                'hostname' => 'oc0002.opencaching.de',
                'status' => ['sent', 'bounced'],
            ];
        }
        $this->config = $config;
        $this->connection = $connection;
    }

    /**
     * getLogs(\DateTimeInterface $start)
     *
     * @return LogEntity[]
     */
    public function getLogs(\DateTimeInterface $start): array
    {
        $journals = $this->eGrepStatus($this->config['status'], $start);

        $logEntries = [];
        foreach ($journals as $journal) {
            $logEntry = new LogEntity();

            $entry = json_decode($journal, true);

            if ($entry['_HOSTNAME'] === $this->config['hostname']) {
                $cursor = [];
                $cursorArray = explode(';', $entry['__CURSOR']);
                foreach ($cursorArray as $field) {
                    list($key, $value) = explode('=', $field);
                    $cursor[$key] = $value;
                }

                $logEntry->id = (int) hexdec($cursor['i']);

                $timeStamp = $entry['__REALTIME_TIMESTAMP'];
                $logEntry->created = \DateTimeImmutable::createFromFormat(
                    'U.u',
                    substr($timeStamp, 0, -6) . '.' . substr($timeStamp, -6)
                );

                if (preg_match('/ status=(.*) /U', $entry['MESSAGE'], $matchStatus)) {
                    $logEntry->status = $matchStatus[1];
                }

                if (preg_match('/ to=<(.*)>,/U', $entry['MESSAGE'], $matchEmail)) {
                    $logEntry->email = $matchEmail[1];
                    $logEntries[] = $logEntry;
                }
            }
        }

        return $logEntries;
    }

    private function eGrepStatus(array $status, \DateTimeInterface $start): array
    {
        $grepStatus = '';
        foreach ($status as $entry) {
            $grepStatus .= 'status=' . $entry . '|';
        }

        $grepStatus = substr($grepStatus, 0, -1);

        $journalRaw = shell_exec(
            'journalctl --since="' . $start->format(
                'Y-m-d H:i:s'
            ) . '" -u postfix -o json | egrep "' . $grepStatus . '"'
        );

        return explode(PHP_EOL, trim($journalRaw));
    }

    public function processJournalLogs(): void
    {
        $start = $this->connection
            ->fetchColumn(
                'SELECT `value` FROM `sysconfig` WHERE `name` = :name',
                [':name' => 'syslog_maillog_lastdate']
            );

        $start = \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $start
        );

        $logs = $this->getLogs($start);

        foreach ($logs as $log) {
            $dateTime = $log->created->format('Y-m-d H:i:s');
            switch ($log->status) {
                case 'bounced':
                    $this->updateEmailStatusToBounced($log->email, $dateTime);
                    break;
                case 'sent':
                    $this->updateEmailStatusToSent($log->email);
                    break;
            }
        }

        $end = new \DateTimeImmutable();

        $this->connection
            ->executeUpdate(
                'UPDATE `sysconfig` SET `value` = :value WHERE `name` = :name',
                [
                    ':name' => 'syslog_maillog_lastdate',
                    ':value' => $end->format('Y-m-d H:i:s'),
                ]
            );
    }

    private function updateEmailStatusToBounced(string $email, string $dateTime): void
    {
        $this->connection
            ->executeQuery(
                'UPDATE `user`
                 SET `email_problems`=`email_problems`+1,
                     `last_email_problem`= :dateTime
                 WHERE email = :email
                 AND DATE(IFNULL(`last_email_problem`, "")) < DATE(:dateTime)',
                [
                    ':email' => $email,
                    ':dateTime' => $dateTime,
                ]
            );
    }

    private function updateEmailStatusToSent(string $email): void
    {
        $this->connection
            ->executeQuery(
                'UPDATE `user`
                 SET `email_problems`=0
                 WHERE email = :email',
                [
                    ':email' => $email,
                ]
            );
    }
}
