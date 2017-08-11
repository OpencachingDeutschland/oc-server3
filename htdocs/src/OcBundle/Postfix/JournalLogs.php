<?php
/****************************************************************************
 * for license information see LICENSE.md
 * extract bounced and sent logs from postfix journal log and process
 * information for mail-log cronjob
 ****************************************************************************/

namespace OcBundle\Postfix;

use Oc\Util\DbalConnection;

/**
 * Class JournalLogs
 */
class JournalLogs
{
    /**
     * @var array|bool
     */
    private $config;

    /**
     * @var DbalConnection
     */
    private $dbalConnection;

    /**
     * constructor
     *
     * @param DbalConnection $dbalConnection
     * @param array|bool $config
     * $config = [
     *     'hostname' => 'host.domain.tld',
     *     'status' => ['sent', 'bounced', ...],
     * ]
     */
    public function __construct(DbalConnection $dbalConnection, $config = false)
    {
        // TODO move this config to a configuration file
        if (!$config) {
            $config = [
                'hostname' => 'oc0002.opencaching.de',
                'status' => ['sent', 'bounced'],
            ];
        }
        $this->config = $config;
        $this->dbalConnection = $dbalConnection;
    }

    /**
     * getLogs(\DateTimeInterface $start)
     *
     * @param \DateTimeInterface $start
     * @return LogEntity[]
     */
    public function getLogs(\DateTimeInterface $start)
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

    /**
     * @param array $status
     * @param \DateTimeInterface $start
     * @return array
     */
    private function eGrepStatus(array $status, \DateTimeInterface $start)
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

    public function processJournalLogs()
    {
        $start = $this->dbalConnection->getConnection()
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

        $this->dbalConnection->getConnection()
            ->executeUpdate(
                'UPDATE `sysconfig` SET `value` = :value WHERE `name` = :name',
                [
                    ':name' => 'syslog_maillog_lastdate',
                    ':value' => $end->format('Y-m-d H:i:s'),
                ]
            );
    }

    /**
     * @param $email
     * @param $dateTime
     */
    private function updateEmailStatusToBounced($email, $dateTime)
    {
        $this->dbalConnection->getConnection()
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

    /**
     * @param $email
     */
    private function updateEmailStatusToSent($email)
    {
        $this->dbalConnection->getConnection()
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
