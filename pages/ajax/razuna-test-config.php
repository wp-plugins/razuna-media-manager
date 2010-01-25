<?php

/*
Copyright (C) 2010 Christof Dorner

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$_POST['hostid'] = urldecode($_POST['hostid']);
$_POST['hostname'] = urldecode($_POST['hostname']);
$_POST['username'] = urldecode($_POST['username']);
$_POST['password'] = urldecode($_POST['password']);

require_once('../../../../../wp-load.php');
require_once('../../razuna.php');
$razuna_api = new Razuna($_POST['hostid'], $_POST['hostname'], $_POST['username'], $_POST['password'], false, Razuna::HOST_TYPE_ID);

$response = array('status' => '0');

try {
	if($_SESSION['razuna-sessiontoken'] == '') {
		$razuna_api->login();
		$_SESSION['razuna-sessiontoken'] = $razuna_api->getSessionToken();
	} else {
		$razuna_api->set_session_token($_SESSION['razuna-sessiontoken']);
	}
	$response['message'] = 'success';
} catch(Exception $e) {
	$response['status'] = '1';
	$response['exception'] = get_class($e);
}

_e(json_encode($response));
	
?>