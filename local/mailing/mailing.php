<?php
/***************************************************************************
 * send mailing
 *
 * For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

// reads from addesses.txt
// writes protocol sent.txt
// sends email to all recipients from addresses.txt which are not contained in sent.txt
//
// may be resumed after interrupt

ini_set('memory_limit', '128M');

// read recipients' email addresses
$addresses = file('addresses.txt');
$protocol = @file('sent.txt');
if ($protocol === false) {
    $sendto = $addresses;
} else {
    $sendto = array_diff($addresses, $protocol);
}

// read message text
$message = file_get_contents('message.txt');
if (empty($message)) {
    die();
}

$total = count($sendto);
$n = 0;
echo 'sending email to ' . $total . ' of ' . count($addresses) . " recipients\n\n";

$subject = '....';
$from_adr = 'user@do.main';

$starttime = microtime(true);

foreach ($sendto as $receiver) {
    $receiver = trim($receiver);
    echo ++ $n . "/$total: $receiver";
    mail(
        $receiver,
        $subject,
        $message,
        "From: $from_adr\r\n" .
        "MIME-Version: 1.0\r\n" .
        "Content-Type: text/plain; charset=iso-8859-1\r\n" .
        'Content-Transfer-Encoding: 8bit'
    );
    echo "\n";
    file_put_contents('sent.txt', "$receiver\n", FILE_APPEND);
}

echo 'Time needed: ' . (microtime(true) - $starttime) . "s\n";
