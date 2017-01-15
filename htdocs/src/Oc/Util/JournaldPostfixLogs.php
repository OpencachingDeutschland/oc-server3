<?php
/****************************************************************************
 * For license information see doc/license.txt
 * extract bounced and sent logs from postfix journal log and process
 * informations for maillog cronjob
 ****************************************************************************/

namespace Oc\Util;

/**
 * Class JournaldPostfixLogs
 */
class JournaldPostfixLogs
{
    /**
     * class variables
     */
    private $config;


    /**
     * constructor
     *
     * @param array $config
     *
     * $config = [
     *     'hostname' => 'host.domain.tld',
     *     'status' => ['sent', 'bounced', ...],
     * ]
     */
    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     * getLogs(\DateTimeImmutable $start)
     *
     * @param \DateTimeImmutable $start
     */
    public function getLogs(\DateTimeImmutable $start)
    {
        
        // prepare end date time
        $end = new \DateTimeImmutable();
        
        // prepare return array
        $return = [
            'nextStart' => $end->add(new \DateInterval('PT1S')),
            'logEntries' => [],
        ];
        
        // get journal entries as array of JSON objects
        $journal = $this->egrepStatus($this->config['status'], $start, $end);
        
        // process information
        $logEntries = [];
        foreach($journal as $json)
        {
            // prepare log entry
            $logEntry = [];
            
            // JSON to assoc array
            $entry = json_decode($json, true);
            
            // check if log entry is for configured hostname
            if($entry['_HOSTNAME'] === $this->config['hostname'])
            {
                
                // prepare id
                $cursor = [];
                $cursorArray = explode(';', $entry['__CURSOR']);
                foreach($cursorArray as $field)
                {
                    list($key, $value) = explode('=', $field);
                    $cursor[$key] = $value;
                }
                $logEntry['id'] = hexdec($cursor['i']);
                
                // prepare created
                $logEntry['created'] = \DateTimeImmutable::createFromFormat('U.u', substr($entry['__REALTIME_TIMESTAMP'], 0, -6).'.'.substr($entry['__REALTIME_TIMESTAMP'], -6));
                
                // prepare status
                if(preg_match('/ status=(.*) /U', $entry['MESSAGE'], $matchStatus))
                {
                    $logEntry['status'] = $matchStatus[1];
                }
                else
                {
                    $logEntry['status'] = null;
                }
                
                // prepare email address
                if(preg_match('/ to=<(.*)>,/U', $entry['MESSAGE'], $matchEmail))
                {
                    $logEntry['emailaddress'] = $matchEmail[1];
                }
                else
                {
                    $logEntry['emailaddress'] = null;
                }
            }
            
            // add to array
            $logEntries[] = $logEntry;
        }
        
        // add to return
        $return['logEntries'] = $logEntries;
        
        // return
        return $return;
    }
    
    
    /**
     * egrepJournal(array $status, \DateTimeImmutable $start, \DateTimeImmutable $end)
     * 
     * @param array $status
     * @param \DateTimeImmutable $start
     * @param \DateTimeImmutable $end
     * @return array
     */
    private function egrepStatus(array $status, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        
        // prepare status
        $grepStatus = '';
        foreach($status as $entry)
        {
            $grepStatus .= 'status='.$entry.'|';
        }
        $grepStatus = substr($grepStatus, 0, -1);
        
        // get journal
        $journalRaw = shell_exec('journalctl --since="'.$start->format('Y-m-d H:i:s').'" --until="'.$end->format('Y-m-d H:i:s').'" -u postfix -o json | egrep "'.$grepStatus.'"');
        $journal = explode(PHP_EOL, trim($journalRaw));
        
        // return array with JSON objects
        return $journal;
    }
}

