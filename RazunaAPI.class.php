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

class RazunaAccessDeniedException extends Exception { }
class RazunaNotAvailableException extends Exception { }

class RazunaAPI {
	
	private $soap_base = "http://api.razuna.com/global/api/";
	private $config_host;
	private $config_username;
	private $config_password;
	private $config_passhashed;
	private $session_token;
	
	function __construct($host, $username, $password, $passhashed) {
		$this->config_host = $host;
		$this->config_username = $username;
		$this->config_password = $password;
		$this->config_passhashed = $passhashed;
	}
	
	public function login() {
		try {
			$client = new SoapClient($this->soap_base . 'authentication.cfc?wsdl');
			$result = $client->loginhost($this->config_host, $this->config_username, $this->config_password, $this->config_passhashed);
		} catch(SoapFault $e) {
			throw new RazunaNotAvailableException();
			return;
		}
		$xml_result = simplexml_load_string($result);
		if($xml_result->responsecode == 0) {
			$this->session_token = (string)$xml_result->sessiontoken;
		} else {
			throw new RazunaAccessDeniedException();
		}
	}
	
	public function list_files($parent_id) {
		try {
			$client = new SoapClient($this->soap_base . 'folder.cfc?wsdl');
			
			// get folders
			$result = $client->getfolders($this->session_token, $parent_id, 0);
		} catch(SoapFault $e) {
			throw new RazunaNotAvailableException();
			return;
		}
		$xml_result = simplexml_load_string($result);
		
		if($this->is_session_timed_out($xml_result)) {
			$this->login();
			$result = $client->getfolders($this->session_token, $parent_id, 0);
			$xml_result = simplexml_load_string($result);
		}
		
		if($xml_result->responsecode == 0) {
			foreach($xml_result->listfolders->folder as $xml_folder) {
				$folder = new RazunaFolder((int)$xml_folder->folderid, (string)$xml_folder->foldername);
				if($parent_id != $folder->get_id())
					$files_arr[] = $folder;
			}
		}
		
		// get files
		try {
			$result = $client->getassets($this->session_token, $parent_id, 1, 0, 0, 'all');
		} catch(SoapFault $e) {
			throw new RazunaNotAvailableException();
			return;
		}
		$xml_result = simplexml_load_string($result);
		
		if($this->is_session_timed_out($xml_result)) {
			$this->login();
			$result = $client->getassets($this->session_token, $parent_id, 1, 0, 0, 'all');
			$xml_result = simplexml_load_string($result);
		}
		
		if($xml_result->responsecode == 0) {
			foreach($xml_result->listassets->asset as $xml_asset) {
				$asset = new RazunaAsset((int)$xml_asset->id, (string)$xml_asset->kind, (string)$xml_asset->filename, ((strtoupper($xml_asset->shared) == 'T') ? true : false), (string)$xml_asset->url, (string)$xml_asset->thumbnail, (int)$xml_asset->folderid);
				$files_arr[] = $asset;
			}
		}
		
		return $files_arr;
	}
	
	public function get_asset($dir, $id) {
		try {
			$client = new SoapClient($this->soap_base . 'folder.cfc?wsdl');
			$result = $client->getassets($this->session_token, $dir, 1, 0, 0, 'all');
		} catch(SoapFault $e) {
			throw new RazunaNotAvailableException();
			return;
		}
		$xml_result = simplexml_load_string($result);
		
		if($this->is_session_timed_out($xml_result)) {
			$this->login();
			$result = $client->getfolders($this->session_token, $dir, 0);
			$xml_result = simplexml_load_string($result);
		}
		
		if($xml_result->responsecode == 0) {
			foreach($xml_result->listassets->asset as $xml_asset) {
				if(((int)$xml_asset->id) == $id) {
					$asset = new RazunaAsset((int)$xml_asset->id, (string)$xml_asset->kind, (string)$xml_asset->filename, (($xml_asset->shared == 'T') ? true : false), (string)$xml_asset->url, (string)$xml_asset->thumbnail, (int)$xml_asset->folderid);
					return $asset;
				}
			}
			return false;
		}
		
		return false;
	}
	
	public function set_asset_shared($asset_id, $asset_type) {
		try {
			$client = new SoapClient($this->soap_base . 'asset.cfc?wsdl');
			$result = $client->setshared($this->session_token, $asset_id, $asset_type, 1);
		} catch(SoapFault $e) {
			throw new RazunaNotAvailableException();
			return;
		}
		$xml_result = simplexml_load_string($result);
		if($xml_result->responsecode == 0) {
			return true;
		}
		return false;
	}
	
	protected function is_session_timed_out($xml_result) {
		return ($xml_result->responsecode == '1' && $xml_result->message == 'Session timeout');
	}
	
	protected function transform_query_parameters($params) {
		$query_string = "";
		foreach ($params as $key => $value) {
		    $query_string .= "$key=" . urlencode($value) . "&";
		}
		return $query_string;
	}
	
	public function get_rest_url($params){
		return $this->rest_base . "?" . $this->transform_query_parameters($params);
	}
	
	public function get_session_token() { return $this->session_token; }
	public function set_session_token($session_token) { $this->session_token = $session_token; }
}

class RazunaFolder {
	private $id;
	private $name;
	
	function __construct($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
}

class RazunaAsset {
	private $id;
	private $kind;
	private $name;
	private $shared = false;
	private $url;
	private $thumbnail;
	private $folder_id;
	
	function __construct($id, $kind, $name, $shared, $url, $thumbnail, $folder_id) {
		$this->id = $id;
		$this->kind = $kind;
		$this->name = $name;
		$this->shared = $shared;
		$this->url = $url;
		$this->thumbnail = $thumbnail;
		$this->folder_id = $folder_id;
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_kind() { return $this->kind; }
	public function set_kind($kind) { $this->kind = $kind; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
	public function is_shared() { return $this->shared; }
	public function set_shared($shared) { $this->shared = $shared; }
	public function get_url() { return $this->url; }
	public function set_url($url) { $this->url = $url; }
	public function get_thumbnail() { return $this->thumbnail; }
	public function set_thumbnail($thumbnail) { $this->thumbnail = $thumbnail; }
	public function get_folder_id() { return $this->folder_id; }
	public function set_folder_id($folder_id) { $this->folder_id = $folder_id; }
	
	public function get_kind_description() {
		if($this->kind == 'img')
			return "Image";
		else if($this->kind == 'vid')
			return "Video";
		else if($this->kind == 'doc')
			return "Document";
		else
			return $this->kind;
	}
}

?>