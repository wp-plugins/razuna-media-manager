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

$_POST['dir'] = urldecode($_POST['dir']);

require_once('../../../../wp-load.php');
require_once('../razuna.php');
$razuna_api = new RazunaAPI(get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false);

try {
	if($_SESSION['razuna-sessiontoken'] == '') {
		$razuna_api->login();
		$_SESSION['razuna-sessiontoken'] = $razuna_api->get_session_token();
	} else {
		$razuna_api->set_session_token($_SESSION['razuna-sessiontoken']);
	}
	
?>