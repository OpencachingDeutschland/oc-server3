<?php

require_once __DIR__ .'/settings-sample-vagrant.inc.php';

// installation paths
$dev_basepath = '/home/travis/';
$dev_codepath = '*';
$dev_baseurl = 'http://127.0.0.1';

// database access
$opt['db']['servername'] = '127.0.0.1';
$opt['db']['username'] = 'root';
$opt['db']['password'] = 'root';
$opt['db']['pconnect'] = false;
