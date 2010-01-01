<?php

$_POST['dir'] = urldecode($_POST['dir']);

require_once('../../../wp-load.php');
require_once('razuna.php');
$razuna_api = new RazunaAPI(get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false);

try {
	if($_SESSION['razuna-sessiontoken'] == '') {
		$razuna_api->login();
		$_SESSION['razuna-sessiontoken'] = $razuna_api->get_session_token();
	} else {
		$razuna_api->set_session_token($_SESSION['razuna-sessiontoken']);
	}
	
?>