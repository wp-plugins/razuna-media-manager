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

require_once('../../../../../wp-load.php');
require_once('../../razuna.php');

if(get_option('razuna_servertype') == 'self')
	$razuna_api = new Razuna(get_option('razuna_hostid'), get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false, Razuna::HOST_TYPE_ID);
else
	$razuna_api = new Razuna(get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false, Razuna::HOST_TYPE_NAME);

$response = array('status' => '0');

try {
	if($_SESSION['razuna-sessiontoken'] == '') {
		$razuna_api->login();
		$_SESSION['razuna-sessiontoken'] = $razuna_api->getSessionToken();
	} else {
		$razuna_api->set_session_token($_SESSION['razuna-sessiontoken']);
	}
	
	$response['sessiontoken'] = $_SESSION['razuna-sessiontoken'];
	if(get_option('razuna_servertype') == 'self')
		$response['formaction'] = 'http://'. get_option('razuna_hostname') . get_option('razuna_dampath') .'/index.cfm';
	else
		$response['formaction'] = 'http://'. get_option('razuna_hostname') .'/index.cfm';
	$response['folders'] = array();
	
	
	$folders = $razuna_api->getFoldersTreeFlat();
	if(count($folders) > 0) {
		foreach($folders as $folder) {
			$response['folders'][] = json_encode2($folder);
		}
	}
	
} catch(Exception $e) {
	$response['status'] = '1';
	$response['exception'] = get_class($e);
}

_e(json_encode($response));
	
?>