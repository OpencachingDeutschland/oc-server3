<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  This module is included by each site with SOAP-output and contains 
 *  functions that are specific to SOAP-output. common.inc.php is included
 *  and will do the setup.
 *
 *  If you include this script from any subdir, you have to set the 
 *  variable $opt['rootpath'], so that it points (relative or absolute)
 *  to the root.
 ***************************************************************************/

	// SOAP Exceptions
	
	// Unknown webservice error
	define('WS_ERR_UNKOWN_ID', 500);
	define('WS_ERR_UNKOWN_STR', 'WS_ERR_UNKOWN');

	// Currently out of service
	define('WS_ERR_OUTOFSERVICE_ID', 501);
	define('WS_ERR_OUTOFSERVICE_STR', 'WS_ERR_OUTOFSERVICE');

	// Connection to database failed
	define('WS_ERR_DATABASE_CONNECT_ID', 502);
	define('WS_ERR_DATABASE_CONNECT_STR', 'WS_ERR_DATABASE_CONNECT');

	// Invalid operation
	define('WS_ERR_INVALID_OP_ID', 503);
	define('WS_ERR_INVALID_OP_STR', 'WS_ERR_INVALID_OP');

	// https required
	define('WS_ERR_REQUIRE_HTTPS_ID', 504);
	define('WS_ERR_REQUIRE_HTTPS_STR', 'WS_ERR_REQUIRE_HTTPS');

	// authentication required
	define('WS_ERR_REQUIRE_AUTH_ID', 505);
	define('WS_ERR_REQUIRE_AUTH_STR', 'WS_ERR_REQUIRE_AUTH');

	// setup rootpath
	if (!isset($opt['rootpath'])) $opt['rootpath'] = './';

	// chicken-egg problem ...
	require($opt['rootpath'] . 'lib2/const.inc.php');

	// do all output in text format
	$opt['gui'] = GUI_NUSOAP;

	// include the main library
	require_once($opt['rootpath'] . 'lib2/common.inc.php');
	require_once($opt['rootpath'] . 'lib2/nusoap/nusoap.php');

	function initSoapRequest($namespace, $nsurl)
	{
		global $nuserver, $HTTP_RAW_POST_DATA;

		if(!$HTTP_RAW_POST_DATA)
		{ 
			$HTTP_RAW_POST_DATA = file_get_contents('php://input'); 
		} 

		$nuserver = new nusoap_server();

		$nuserver->configureWSDL($namespace, $nsurl);
		$nuserver->wsdl->schemaTargetNamespace = $nsurl;

		/*
			Define string und integer-arrays
		*/
		$nuserver->wsdl->addComplexType( 
			'ArrayOfstring', 
			'complexType', 
			'array', 
			'', 
			'SOAP-ENC:Array', 
			array(), 
			array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:string[]')), 
			'xsd:string' 
		);

		$nuserver->wsdl->addComplexType( 
			'ArrayOfint', 
			'complexType', 
			'array', 
			'', 
			'SOAP-ENC:Array', 
			array(), 
			array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:int[]')), 
			'xsd:int' 
		);
	}

	function finishSoapRequest()
	{
		global $nuserver, $HTTP_RAW_POST_DATA;

		$nuserver->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '');
		exit;
	}

	function initSoapFunction()
	{
		global $opt, $db;

		// init faults
		if (($opt['debug'] & DEBUG_OUTOFSERVICE) == DEBUG_OUTOFSERVICE)
		{
			return new nusoap_fault(WS_ERR_OUTOFSERVICE_ID, '' , WS_ERR_OUTOFSERVICE_STR);
		}

		if ($opt['page']['nusoap_require_https'] == true)
		{
			if (!isset($_REQUEST['HTTPS']))
			{
				return new nusoap_fault(WS_ERR_REQUIRE_HTTPS_ID, '' , WS_ERR_REQUIRE_HTTPS_STR);
			}
		}

		if ($db['dblink'] == false)
		{
			// try connect
			sql_connect(null, null, false);
			if ($db['dblink'] == false)
			{
				return new nusoap_fault(WS_ERR_DATABASE_CONNECT_ID, '' , WS_ERR_DATABASE_CONNECT_STR);
			}
		}

		return false;
	}
?>