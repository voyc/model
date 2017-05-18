<?php
/**
	user authentication svc entry point
**/
require_once(dirname(__FILE__).'/../../config.php');
require_once('../phplib/str.php');
require_once('../phplib/db.php');
require_once('../phplib/validate.php');
require_once('../phplib/crypto.php');
require_once('../phplib/cors.php');
require_once('../phplib/log.php');

writeHeader();

$supported_svcs = array(
	'getprofile',
	'setprofile'
);

function validateSvc($taint) {
	global $supported_svcs;
	$clean = in_array($taint, $supported_svcs) ? $taint : 0;
	return $clean;
}

function svchub() {
	// get the svc name
	$taint_svc = isset($_POST['svc']) ? $_POST['svc'] : 0;

	// validate the svc name
	$svc = validateSvc($taint_svc);
	if (!$svc) {
		Log::open("unsupported svc: $svc.$taint_svc");
		return;
	}

	$includefile = $svc . '.php';

	Log::open($svc);
	require_once($includefile);
	$a = $svc();
	echo json_encode($a);
	Log::close($a['status']);
	return;
}
?>
