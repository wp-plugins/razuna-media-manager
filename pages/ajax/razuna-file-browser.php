<?php

/*
Copyright (C) 2012 Razuna APS

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

require_once('../../../../../wp-load.php');
require_once('../../razuna.php');

if(get_option('razuna_servertype') == 'self')
	$razuna_api = new Razuna(get_option('razuna_hostid'), get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false, Razuna::HOST_TYPE_ID);
else
	$razuna_api = new Razuna(get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false, Razuna::HOST_TYPE_NAME);

$response = array('status' => '0');
$response['hostingtype'] = get_option('razuna_servertype');

try {
	if($_SESSION['razuna-sessiontoken'] == '') {
		$razuna_api->login();
		$_SESSION['razuna-sessiontoken'] = $razuna_api->getSessionToken();
	} else {
		$razuna_api->setSessionToken($_SESSION['razuna-sessiontoken']);
	}
	
	$response['files'] = array();
	// get folders
	if($_POST['dir'] == '0')
		$folders = $razuna_api->getRootFolders();
	else
		$folders = $razuna_api->getFolders($_POST['dir']);
	if(count($folders) > 0) {
		foreach($folders as $folder) {
			$folder_arr['type'] = get_class($folder);
			$folder_arr['obj'] = json_encode2($folder);
			$response['files'][] = $folder_arr;
		}
	}
		
	// get files
	$assets = $razuna_api->getAssets($_POST['dir']);
	if(count($assets) > 0) {
		foreach($assets as $asset) {
			$asset_arr['type'] = get_class($asset);
			$asset_arr['obj'] = json_encode2($asset);
			switch($asset->kind) {
			case Razuna::ASSET_TYPE_IMAGE:
				$asset_arr['kind_description'] = 'Image';
				break;
			case Razuna::ASSET_TYPE_VIDEO:
				$asset_arr['kind_description'] = 'Video';
				break;
			case Razuna::ASSET_TYPE_DOCUMENT:
				$asset_arr['kind_description'] = 'Document';
				break;
			case Razuna::ASSET_TYPE_AUDIO:
				$asset_arr['kind_description'] = 'Audio';
				break;
			}
			$asset_arr['shared_description'] = ($asset->shared) ? 'Yes' : 'No';
			$response['files'][] = $asset_arr;
		}
	}
	
} catch(Exception $e) {
	$response['status'] = '1';
	$response['exception'] = get_class($e);
	$response['message'] = $e->getMessage();
}

_e(json_encode($response));
	
?>