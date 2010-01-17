<!---
*
* Copyright (C) 2005-2008 Razuna Ltd.
*
* This file is part of Razuna - Enterprise Digital Asset Management.
*
* Razuna is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Razuna is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero Public License for more details.
*
* You should have received a copy of the GNU Affero Public License
* along with Razuna. If not, see <http://www.gnu.org/licenses/>.
*
* You may restribute this Program with a special exception to the terms
* and conditions of version 3.0 of the AGPL as described in Razuna's
* FLOSS exception. You should have received a copy of the FLOSS exception
* along with Razuna. If not, see <http://www.razuna.com/licenses/>.
*
--->
<!--
HISTORY:
Date (US Format)	User					Note
2009/11/01			Sébastien Massiaux		Initial Library
-->
<?php
define('API_HOST',        'http://api.razuna.com');
define('AUTH_URI',        API_HOST.'/global/api/authentication.cfc?wsdl');
define('FOLDER_URI',      API_HOST.'/global/api/folder.cfc?wsdl');
define('COLLECTION_URI',   API_HOST.'/global/api/collection.cfc?wsdl');
define('HOSTS_URI',       API_HOST.'/global/api/hosts.cfc?wsdl');
define('SEARCH_URI',      API_HOST.'/global/api/search.cfc?wsdl');
define('USER_URI',        API_HOST.'/global/api/user.cfc?wsdl');

class razuna {
  var $token;
  var $auth;
  var $folder;
  var $collection;
  var $hosts;
  var $search;
  var $user;

  function buildSoapError($e, $el) {
    $str = '<error></error>';
    $xml = new SimpleXMLElement($str);
    $xml->addChild('responsecode', '999');
    $xml->addChild($el, 'Soap error');
    $xml->addChild('soaperror', $e);
    return $xml->asXML();
  }

  /* ************************
   *          AUTH          *
   ************************** */

  function initAuth() {
    if (!is_object($this->auth)) {
      $this->auth = new SoapClient(AUTH_URI);
    }
  }

// TODO : manage session timeout
// responsecode = 1
// message = Session timeout
// TODO : remove cookies
  /**
  * Login : Login Method
  * The Login method is used to login to the system and generate a Session Token restricted to the caller's IP address.
  * @param string $hostid : This is the host id under which you want to access the assets
  * @param string $user : A user in the system administrator or administrator group
  * @param string $pass : The password of the user
  * @param int $passhashed : Password is MD5 encrypted or not (1 = true ; 0 = false)
  * @return an Array containing :
  * ['fault']['fault_code']
  * ['fault']['fault_string']
  * ['response']['response_string']
  * ['response']['response_object']
  * ['sessiontoken']
  */
  function login($hostid, $user, $pass, $passhashed=1) {
    // build the auth object
    $this->initAuth();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->auth->login($hostid, $user, $pass, $passhashed);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'sessiontoken');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = (int)$xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = (string)$xml->sessiontoken;
      return $answer;
    }
    $this->token = (string)$xml->sessiontoken;
    setcookie("razuna[token]", $this->token, time()+3600);
    $answer['fault']['fault_string'] = "Success";
    // $answer['sessiontoken'] = (string)$xml->sessiontoken;
    return $answer;
  }

  /**
  * loginHost
  * @param string $host
  * @param string $user : A user in the system administrator or administrator group
  * @param string $pass : The password of the user
  * @param int $passhashed : Password is MD5 encrypted or not (1 = true ; 0 = false)
  * @return an Array containing :
  * ['fault']['fault_code']
  * ['fault']['fault_string']
  * ['response']['response_string']
  * ['response']['response_object']
  * ['sessiontoken']
  */
  function loginHost($host, $user, $pass, $passhashed=1) {
    // build the auth object
    $this->initAuth();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->auth->loginhost($host, $user, $pass, $passhashed);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'sessiontoken');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = (int)$xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $this->token = (string)$xml->sessiontoken;
      return $answer;
    }
    $this->token = (string)$xml->sessiontoken;
    // setcookie("razuna[token]", $this->token, time()+3600);
    $answer['fault']['fault_string'] = "Success";
    $answer['sessiontoken'] = (string)$xml->sessiontoken;
    return $answer;
  }

  /* ************************
   *          FOLDER        *
   ************************** */
  function initFolder() {
    if (!is_object($this->folder)) {
      $this->folder = new SoapClient(FOLDER_URI);
    }
    // $this->token = $_COOKIE['razuna']['token'];
  }

  /**
   * getFolders : Get a list of folders
   * This method will return a list of folders on ONE level. To iterate for subfolders you will need to call this method each time. If you rather like to retrieve ALL folder and subfolders at once please take a look at the below function.
   * @param integer $folderid : The ID of the folder you want to retrieve assets from.
   * @return an Array containing :
   * ['fault']['fault_code']
   * ['fault']['fault_string']
   * ['response']['response_string']
   * ['response']['response_object']
   * ['folders']
   */
  function getFolders($folderid) {
    // build the auth object
    $this->initFolder();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->folder->getfolders($this->token, $folderid, 0);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listfolders');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listfolders;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['folders'] = $xml->listfolders;
    return $answer;
  }

  /**
   * getFoldersTree : Get ALL folders
   * This method will return all folders and subfolders. Please be aware that with a lot of folders this can put a strain on your Razuna server!
   * @return an Array containing :
   * ['fault']['fault_code']
   * ['fault']['fault_string']
   * ['response']['response_string']
   * ['response']['response_object']
   * ['folders']
   * ['html']
   */
  function getFoldersTree() {
    // build the auth object
    $this->initFolder();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->folder->getfolderstree($this->token, 0);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listfolders');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listfolders;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['folders'] = $xml->listfolders;
    return $answer;
  }

  /**
   * getAssets : Retrieving all assets in a folder
   * @param integer $folderid : The ID of the folder you want to retrieve assets from.
   * @param integer $showsubfolders : To include assets from subfolders as well.
   * @param integer $offset : This request supports paging. Enter the offset here
   * @param integer $maxrows : The maximum rows you want to request.
   * @param string $show : What kind of assets to show
   * all = All assets
   * img = Images only
   * vid = Videos only
   * doc = Documents only
   * @return an Array containing :
   * ['fault']['fault_code']
   * ['fault']['fault_string']
   * ['response']['response_string']
   * ['response']['response_object']
   * ['assets']
   */
  function getFolderAssets($folderid, $showsubfolders = 0, $offset = 0, $maxrows = 0, $show = 'ALL') {
    // build the auth object
    $this->initFolder();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->folder->getassets($this->token, $folderid, $showsubfolders, $offset, $maxrows, $show);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listassets');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listassets;
      $answer['assets'] = $xml->listassets;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['assets'] = $xml->listassets;
    return $answer;
  }

  /* ************************
   *       COLLECTIONS        *
   ************************** */
  function initCollection() {
    if (!is_object($this->collection)) {
      $this->collection = new SoapClient(COLLECTION_URI);
    }
    // $this->token = $_COOKIE['razuna']['token'];
  }

  /**
   * getCollectionsTree : Get Collections folders in a tree
   * This method will return all collections and sub-collections in a tree. Please be aware that with a lot of collections this can put a strain on your Razuna server!
   * @return an Array containing :
   * ['fault']['fault_code']
   * ['fault']['fault_string']
   * ['response']['response_string']
   * ['response']['response_object']
   * ['collections']
   * ['html']
   */
  function getCollectionsFoldersTree() {
    // build the auth object
    $this->initCollection();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->collection->getcollectionstree($this->token, 0);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listcollections');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listcollections;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['collections'] = $xml->listcollections;
    return $answer;
  }

  /**
   * getCollections : Get a list of Collections
   * This method will return all collections within a collection "folder". To iterate for sub-collections you will need to call this method each time.
   * @param integer $folderid : The ID of the collection folder you want to retrieve collections from
   * @return an Array containing :
   * ['fault']['fault_code']
   * ['fault']['fault_string']
   * ['response']['response_string']
   * ['response']['response_object']
   * ['collections']
   */
  function getCollections($folderid) {
    // build the auth object
    $this->initCollection();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->collection->getcollections($this->token, $folderid, 0);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listcollections');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listcollections;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['collections'] = $xml->listcollections;
    return $answer;
  }

  /**
   * getAssets : Retrieving all assets in a collection
   * @param integer $collectionid : The ID of the collection you want to retrieve assets from
   * @return an Array containing :
   * ['fault']['fault_code']
   * ['fault']['fault_string']
   * ['response']['response_string']
   * ['response']['response_object']
   * ['assets']
   */
  function getCollectionAssets($collectionid) {
    // build the auth object
    $this->initCollection();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->collection->getassets($this->token, $collectionid);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listassets');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listassets;
      $answer['assets'] = $xml->listassets;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['assets'] = $xml->listassets;
    return $answer;
  }

  /* ************************
   *         HOSTS          *
   * /!\ This API is not available on the Razuna Hosted Platform /!\ *
   ************************** */
  function initHosts() {
    if (!is_object($this->hosts)) {
      $this->hosts = new SoapClient(HOSTS_URI);
    }
    // $this->token = $_COOKIE['razuna']['token'];
  }

  /**
   * getHosts : Retrieving all hosts
   * @return unknown_type
   */
  function getHosts() {
    // build the auth object
    $this->initHosts();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->hosts->gethosts($this->token);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'host');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->host;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['hosts'] = $xml->host;
    return $answer;
  }

  /* ************************
   *       SEARCH           *
   ************************** */
  function initSearch() {
    if (!is_object($this->search)) {
      $this->search = new SoapClient(SEARCH_URI);
    }
    // $this->token = $_COOKIE['razuna']['token'];
  }

  /**
   * searchAssets : Search and finding assets
   * @param $searchfor
   * @param $offset
   * @param $maxrows
   * @param $show
   * @param $doctype
   * @return unknown_type
   */
  function searchAssets($searchfor, $offset = 0, $maxrows = 0, $show = 'ALL', $doctype = NULL) {
    // build the auth object
    $this->initSearch();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->search->searchassets($this->token, $searchfor, $offset, $maxrows, $show, $doctype);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'listassets');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->listassets;
      return $answer;
    }
    $answer['fault']['fault_string'] = "Success";
    $answer['hosts'] = $xml->listassets;
    return $answer;
  }

  /* ************************
   *       USER               *
   ************************** */
  function initUser() {
    if (!is_object($this->user)) {
      $this->user = new SoapClient(USER_URI);
    }
    // $this->token = $_COOKIE['razuna']['token'];
  }

  /**
   * userAdd : Add a User
   * @param string $user_first_name : First Name of the user
   * @param string $user_last_name : Last Name of the user
   * @param string $user_email : eMail of the user
   * @param string $user_name : User name of the user
   * @param string $user_pass : Password of the user
   * @param string $user_active : Activate the user
   * @param int $groupid : Groupid (ID of the Group you want the user to belong to)
   * @return unknown_type
   */
  function userAdd($user_first_name, $user_last_name, $user_email, $user_name, $user_pass, $user_active, $groupid) {
    // build the auth object
    $this->initUser();
    // build the output array
    $answer = array();
    // get the answer as a xml string
    try {
      $answer['response']['response_string'] = $this->user->add((string)$this->token[0], $user_first_name, $user_last_name, $user_email, $user_name, $user_pass, $user_active, $groupid);
    }
    catch (Exception $e){
      $answer['response']['response_string'] = $this->buildSoapError($e, 'message');
    }
    // parse the answer to an object
    $xml = simplexml_load_string($answer['response']['response_string']);
    $answer['response']['response_object'] = $xml;
    $answer['fault']['fault_code'] = $xml->responsecode;
    if ($answer['fault']['fault_code'] != 0) {
      $answer['fault']['fault_string'] = $xml->message;
      return $answer;
    }
    $answer['fault']['fault_string'] = $xml->message;
    return $answer;
  }
}
class razunaRenderUtils {

  /**
   * foldersAsArray : build an associative array containing folders
   * @param object $folders : simpleXml object containing folders
   * @param string $type : array format output
   * @return an array containing folderid in key and foldername in value
   */
  function foldersAsArray($params) {
    $folders      = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'flat';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $output       = array();
    switch ($type) {
      case 'flat':
        foreach($folders->folder as $folder) {
          $id = (string)$folder->folderid;
          $output[$id] = (string)$folder->foldername;
          if ($hide_empty && !(int)$folder->totalassets) {
            unset($output[$id]);
          }
          if ($folder->hassubfolder) {
            $sub_params = array(
              'data'          => $folder->subfolder,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => '',
              'level_symbol'  => $level_symbol,
            );
            $output += razunaRenderUtils::crawlSubFoldersAsArray($sub_params);
          }
        }
        break;
    }
    return $output;
  }

  /**
   * crawlSubFoldersAsArray : build an associative array containing subfolders
   * @param object $folders : simple xml object containing subfolders
   * @param string $type : html format output
   * @param string $level : prefix string to build values
   * @return an array containing folderid in key and foldername in value
   */
  function crawlSubFoldersAsArray($params) {
    $folders      = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'flat';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $level        = $params['level'] ? $params['level'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $output       = array();
    switch ($type) {
      case 'flat':
        $level .= $level_symbol;
        foreach($folders as $folder) {
          $id = (string)$folder->folderid;
          $output[(string)$id] = $level. ' '. (string)$folder->foldername;
          if ($hide_empty && !(int)$folder->totalassets) {
            unset($output[$id]);
          }
          if ($folder->hassubfolder) {
            $sub_params = array(
              'data'          => $folder->subfolder,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => $level,
              'level_symbol'  => $level_symbol,
            );
            $output += razunaRenderUtils::crawlSubFoldersAsArray($sub_params);
          }
        }
        break;
    }
    return $output;
  }

  /**
   * Build an Html list of the folders tree
   * @param object $folders : simpleXml object containing folders
   * @param string $type : html format output
   * @return an html code formatted depending on the $type param
   */
  function foldersAsHtml($params) {
    $folders      = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'list';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $attributes   = $params['attributes'] ? $params['attributes'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $show_id      = $params['show_id'] ? $params['show_id'] : FALSE;
    $output       = '';
    switch ($type) {
      case 'list':
        $output .= '<ul '. $attributes. '>';
        foreach($folders->folder as $folder) {
          $line = '<li>';
          if ($show_id) {
            $line .= '('. $folder->folderid. ') ';
          }
          $line .= $folder->foldername;
          $line .= '</li>';
          if ($hide_empty && !(int)$folder->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($folder->hassubfolder) {
            $sub_params = array(
              'data'          => $folder->subfolder,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => '',
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubFoldersAsHtml($sub_params);
          }
        }
        $output .= '</ul>';
        break;
      case 'select':
        $output .= '<select '. $attributes. '>';
        foreach($folders->folder as $folder) {
          $line = '<option value="'. $folder->folderid. '">'. $folder->foldername. '</option>';
          if ($hide_empty && !(int)$folder->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($folder->hassubfolder) {
            $sub_params = array(
              'data'          => $folder->subfolder,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => '',
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubFoldersAsHtml($sub_params);
          }
        }
        $output .= '</select>';
        break;
    }
    return $output;
  }

  /**
   * Crawl subfolders
   * @param object $folders : simple xml object containing subfolders
   * @param string $type : html format output
   * @return an html code formatted depending on the $type param
   */
  function crawlSubFoldersAsHtml($params) {
    $folders      = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'list';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $attributes   = $params['attributes'] ? $params['attributes'] : '';
    $level        = $params['level'] ? $params['level'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $show_id      = $params['show_id'] ? $params['show_id'] : FALSE;
    $output       = '';
    switch ($type) {
      case 'list':
        $output .= '<ul>';
        foreach($folders as $folder) {
          $line = '<li>';
          if ($show_id) {
            $line .= '('. $folder->folderid. ') ';
          }
          $line .= $folder->foldername;
          $line .= '</li>';
          if ($hide_empty && !(int)$folder->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($folder->hassubfolder) {
            $sub_params = array(
              'data'          => $folder->subfolder,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => $level,
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubFoldersAsHtml($sub_params);
          }
        }
        $output .= '</ul>';
        break;
      case 'select':
        $level .= $level_symbol;
        foreach($folders as $folder) {
          $line = '<option value="'. $folder->folderid. '">'.$level. ' '. $folder->foldername.'</option>';
          if ($hide_empty && !(int)$folder->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($folder->hassubfolder) {
            $sub_params = array(
              'data'          => $folder->subfolder,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => $level,
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubFoldersAsHtml($sub_params);
          }
        }
        break;
    }
    return $output;
  }

  /**
   * collectionsAsArray : build an associative array containing collections
   * @param object $collections : simpleXml object containing collections
   * @param string $type : array format output
   * @return an array containing collectionid in key and collectionname in value
   */
  function collectionsAsArray($params) {
    $collections  = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'flat';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $level        = $params['level'] ? $params['level'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $output       = array();
    switch ($type) {
      case 'flat':
        foreach($collections->collection as $collection) {
          $id = (string)$collection->collectionid;
          $output[$id] = (string)$collection->collectionname;
          if ($hide_empty && !(int)$collection->totalassets) {
            unset($output[$id]);
          }
          if ($collection->hassubcollection) {
            $sub_params = array(
              'data'          => $collection->subcollection,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => '',
              'level_symbol'  => $level_symbol,
            );
            $output += razunaRenderUtils::crawlSubCollectionsAsArray($sub_params);
          }
        }
        break;
    }
    return $output;
  }

  /**
   * crawlSubCollectionsAsArray : build an associative array containing subcollections
   * @param object $collections : simple xml object containing subcollections
   * @param string $type : html format output
   * @param string $level : prefix string to build values
   * @return an array containing collectionid in key and collectionname in value
   */
  function crawlSubCollectionsAsArray($params) {
    $collections  = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'flat';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $level        = $params['level'] ? $params['level'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $output       = array();
    switch ($type) {
      case 'flat':
        $level .= '-';
        foreach($collections as $collection) {
          $id = (string)$collection->collectionid;
          $output[$id] = $level. ' '. (string)$collection->collectionname;
          if ($hide_empty && !(int)$collection->totalassets) {
            unset($output[$id]);
          }
          if ($collection->hassubcollection) {
            $sub_params = array(
              'data'          => $collection->subcollection,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'level'         => $level,
              'level_symbol'  => $level_symbol,
            );
            $output += razunaRenderUtils::crawlSubCollectionsAsArray($sub_params);
          }
        }
        break;
    }
    return $output;
  }

  /**
   * Build an Html list of the collections tree
   * @param object $collections : simpleXml object containing collections
   * @param string $type : html format output
   * @return an html code formatted depending on the $type param
   */
  function collectionsAsHtml($params) {
    $collections  = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'list';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $attributes   = $params['attributes'] ? $params['attributes'] : '';
    $level        = $params['level'] ? $params['level'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $show_id      = $params['show_id'] ? $params['show_id'] : FALSE;
    $output       = '';
    switch ($type) {
      case 'list':
        $output .= '<ul '. $attributes. '>';
        foreach($collections->collection as $collection) {
          $line = '<li>';
          if ($show_id) {
            $line .= '('. $collection->collectionid. ') ';
          }
          $line .= $collection->collectionname;
          $line .= '</li>';
          if ($hide_empty && !(int)$collection->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($collection->hassubcollection) {
            $sub_params = array(
              'data'          => $collection->subcollection,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'attributes'    => $attributes,
              'level'         => '',
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubCollectionsAsHtml($sub_params);
          }
        }
        $output .= '</ul>';
        break;
      case 'select':
        $output .= '<select '. $attributes. '>';
        foreach($collections->collection as $collection) {
          $line = '<option value="'. $collection->collectionid. '">'. $collection->collectionname.'</option>';
          if ($hide_empty && !(int)$collection->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($collection->hassubcollection) {
            $sub_params = array(
              'data'          => $collection->subcollection,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'attributes'    => $attributes,
              'level'         => '',
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubCollectionsAsHtml($sub_params);
          }
        }
        $output .= '</select>';
        break;
    }
    return $output;
  }

  /**
   * Crawl subcollections
   * @param object $collections : simple xml object containing subcollections
   * @param string $type : html format output
   * @return an html code formatted depending on the $type param
   */
  function crawlSubCollectionsAsHtml($params) {
    $collections  = $params['data'];
    $type         = $params['type'] ? $params['type'] : 'list';
    $hide_empty   = isset($params['hide_empty']) ? $params['hide_empty'] : FALSE;
    $attributes   = $params['attributes'] ? $params['attributes'] : '';
    $level        = $params['level'] ? $params['level'] : '';
    $level_symbol = $params['level_symbol'] ? $params['level_symbol'] : '-';
    $show_id      = $params['show_id'] ? $params['show_id'] : FALSE;
    $output       = '';
    switch ($type) {
      case 'list':
        $output .= '<ul>';
        foreach($collections as $collection) {
          $line = '<li>';
          if ($show_id) {
            $line .= '('. $collection->collectionid. ') ';
          }
          $line .= $collection->collectionname;
          $line .= '</li>';
          if ($hide_empty && !(int)$collection->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($collection->hassubcollection) {
            $sub_params = array(
              'data'          => $collection->subcollection,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'attributes'    => $attributes,
              'level'         => $level,
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubCollectionsAsHtml($sub_params);
          }
        }
        $output .= '</ul>';
        break;
      case 'select':
        $level .= $level_symbol;
        foreach($collections as $collection) {
          $line = '<option value="'. $collection->collectionid. '">'.$level. ' '. $collection->collectionname.'</option>';
          if ($hide_empty && !(int)$collection->totalassets) {
            $line = '';
          }
          $output .= $line;
          if ($collection->hassubcollection) {
            $sub_params = array(
              'data'          => $collection->subcollection,
              'type'          => $type,
              'hide_empty'    => $hide_empty,
              'attributes'    => $attributes,
              'level'         => $level,
              'level_symbol'  => $level_symbol,
            );
            $output .= razunaRenderUtils::crawlSubCollectionsAsHtml($sub_params);
          }
        }
        break;
    }
    return $output;
  }

}