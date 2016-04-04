<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  check if the mailserver returns an 550 or 553 code
 *
 *  This class is currently not in use anywhere (2013-07-15).
 ***************************************************************************/

define('CA_OK', 0);
define('CA_ERROR_TEMPORARY', 1);
define('CA_ERROR_USER_UNKOWN', 2);
define('CA_ERROR_CONNECT', 3);
define('CA_ERROR_ADDRESS_INVALID', 4);
define('CA_ERROR_UNKOWN', 5);

class mailcheck
{
    public $sHostname = 'somehost.org';
    public $sFrom = 'postmaster@somehost.org';

    public $nConnectTimeout = 15; // (sec)
    public $nReadTimeout = 25;   // (sec)

    /* check if the mailserver of $sAddress
     * explicit says that the user does not exist
     *
     * CA_OK                    ... mailserver has been connected and he does not say that the account does not exist
     * CA_ERROR_TEMPORARY       ... mailserver rejected with 4xx code (temporary failure)
     * CA_ERROR_USER_UNKOWN     ... mailserver said that user mailbox does not exist (550 or 553)
     * CA_ERROR_CONNECT         ... mailserver(s) could not be connected
     * CA_ERROR_ADDRESS_INVALID ... E-Mail format not valid
     * CA_ERROR_UNKOWN          ... any other error
     */
    public function checkAddress($sAddress)
    {
        if (!is_valid_email_address($sAddress)) {
            return CA_ERROR_ADDRESS_INVALID;
        }

        /* get MX records
         */
        $sDomain = substr($sAddress, strpos($sAddress, '@') + 1);
        if (getmxrr($sDomain, $mx_records, $mx_weight) == false) {
            $mx_records = [$sDomain];
            $mx_weight = [0];
        }

        // sort MX records
        $mxs = [];
        for ($i = 0; $i < count($mx_records); $i ++) {
            $mxs[$i] = [
                'mx' => $mx_records[$i],
                'prio' => $mx_weight[$i]
            ];
        }
        usort($mxs, "mailcheck_cmp");
        reset($mxs);

        // check address with each MX until one mailserver can be connected
        for ($i = 0; $i < count($mxs); $i ++) {
            $retval = $this->pCheckAddress($sAddress, $mxs[$i]['mx']);
            if ($retval != CA_ERROR_CONNECT) {
                return $retval;
            }
        }

        return CA_ERROR_CONNECT;
    }


    /* check if the specified mailserver
     * explicit says that the $sAddress does not exist
     *
     * CA_OK                    ... mailserver has been connected and he does not say that the account does not exist
     * CA_ERROR_TEMPORARY       ... mailserver rejected with 4xx code (temporary failure)
     * CA_ERROR_USER_UNKOWN     ... mailserver said that user mailbox does not exist (550 or 553)
     * CA_ERROR_CONNECT         ... mailserver(s) could not be connected
     * CA_ERROR_ADDRESS_INVALID ... E-Mail format not valid
     * CA_ERROR_UNKOWN          ... any other error
     */
    public function pCheckAddress($sAddress, $sMailserver)
    {
        if (!is_valid_email_address($sAddress)) {
            return CA_ERROR_ADDRESS_INVALID;
        }

        $fp = @fsockopen($sMailserver, 25, $errno, $errstr, $this->nConnectTimeout);
        if (!$fp) {
            return CA_ERROR_CONNECT;
        }

        $sResp = $this->send_command($fp, "HELO " . $this->sHostname);
        $sCode = $this->extract_return_code($sResp);
        if ($sCode != '220') {
            $this->close($fp);

            return CA_ERROR_UNKOWN;
        }

        $sResp = $this->send_command($fp, "MAIL FROM: <" . $this->sFrom . ">");
        $sCode = $this->extract_return_code($sResp);
        if ($sCode != '250') {
            $this->close($fp);

            return CA_ERROR_UNKOWN;
        }

        $sResp = $this->send_command($fp, "RCPT TO: <" . $sAddress . ">");
        $sCode = $this->extract_return_code($sResp);
        if (strlen($sCode) == 3 && substr($sCode, 0, 1) == '4') {
            $this->close($fp);

            return CA_ERROR_TEMPORARY;
        } elseif ($sCode == '553' && $sCode == '550') {
            $this->close($fp);

            return CA_ERROR_USER_UNKOWN;
        } elseif ($sCode == '250') {
            $this->close($fp);

            return CA_OK;
        }

        $this->close($fp);

        return CA_ERROR_UNKOWN;
    }

    public function close($fp)
    {
        fwrite($fp, "QUIT\r\n");
        fclose($fp);
    }

    public function extract_return_code($sResp)
    {
        $nPos1 = strpos($sResp, ' ');
        $nPos2 = strpos($sResp, '-');

        if ($nPos1 === false && $nPos2 === false) {
            return $sResp;
        } elseif ($nPos1 === false) {
            $nPos = $nPos2;
        } elseif ($nPos2 === false) {
            $nPos = $nPos1;
        } else {
            if ($nPos1 < $nPos2) {
                $nPos = $nPos1;
            } else {
                $nPos = $nPos2;
            }
        }

        return substr($sResp, 0, $nPos);
    }

    public function send_command($fp, $out)
    {
        fwrite($fp, $out . "\r\n");

        return $this->get_data($fp);
    }

    public function get_data($fp)
    {
        $s = "";
        stream_set_timeout($fp, $this->nReadTimeout);

        for ($i = 0; $i < 2; $i ++) {
            $s .= fgets($fp, 1024);
        }

        return $s;
    }
}

function mailcheck_cmp($a, $b)
{
    if ($a == $b) {
        return 0;
    }

    return (($a['prio'] + 0) < ($b['prio'] + 0)) ? - 1 : 1;
}
