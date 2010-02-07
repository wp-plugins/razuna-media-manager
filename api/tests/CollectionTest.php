<?php

class CollectionTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false, Razuna::HOST_TYPE_ID);
		$session_token = $this->api->login();
	}
	
	public function testGetFoldersTree() {
		$folders = $this->api->getCollectionsTree();
		foreach($folders as $folder) {
			if($folder->name == 'Collections')
				$home_folder = $folder;
		}
		$this->assertEquals('Collections', $home_folder->name);
		
		$my_folder_subfolders = $home_folder->subfolders;
		$this->assertEquals('Folder 1', $my_folder_subfolders[0]->name);
		$this->assertEquals('Folder 2', $my_folder_subfolders[1]->name);
		
		$folder_1_subfolders = $my_folder_subfolders[0]->subfolders;
		$this->assertEquals('Folder 1.1', $folder_1_subfolders[0]->name);
	}
	
	public function testGetCollections() {
		$folders = $this->api->getCollections();
		$this->assertGreaterThan(0, count($folders));
	}
	
	
	
	public function testGetFoldersTreeFlat() {
		$folders = $this->api->getCollectionsTreeFlat();
		$this->assertGreaterThan(0, count($folders));
		
		$found_my_folder = false;
		$found_folder_1 = false;
		$found_folder_2 = false;
		$found_folder_1_1 = false;
		foreach($folders as $folder) {
			switch($folder->name) {
			case 'Collections':
				$found_my_folder = true;
				break;
			case 'Folder 1':
				$found_folder_1 = true;
				break;
			case 'Folder 2':
				$found_folder_2 = true;
				break;
			case 'Folder 1.1':
				$found_folder_1_1 = true;
				break;
			}
		}
		
		$this->assertTrue($found_my_folder);
		$this->assertTrue($found_folder_1);
		$this->assertTrue($found_folder_2);
		$this->assertTrue($found_folder_1_1);
	}
	
	public function testGetCollectionAssets() {
		$collections = $this->api->getCollections();
		foreach($collections as $collection) {
			$home_collection = $collection;
		}
		
		$assets = $this->api->getCollectionAssets($home_collection->id);
		$found = false;
		foreach($assets as $asset) {
			if($asset->filename == '1.jpg')
				$found = true;
		}
		$this->assertTrue($found);
	}
}

?>