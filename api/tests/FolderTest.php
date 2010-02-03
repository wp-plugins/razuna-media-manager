<?php

class FolderTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false, Razuna::HOST_TYPE_ID);
		$session_token = $this->api->login();
	}
	
	public function testGetFolders() {
		$folders = $this->api->getFolders();
		$this->assertGreaterThan(0, count($folders));
	}
	
	public function testGetRootFolders() {
		$folders = $this->api->getRootFolders();
		$this->assertNotNull($folders);
		$this->assertGreaterThan(0, count($folders));
	}
	
	public function testGetFoldersTree() {
		$folders = $this->api->getFoldersTree();
		foreach($folders as $folder) {
			if($folder->name == 'phpunit')
				$home_folder = $folder;
		}
		$this->assertEquals('phpunit', $home_folder->name);
		
		$my_folder_subfolders = $home_folder->subfolders;
		$this->assertEquals('Folder 1', $my_folder_subfolders[0]->name);
		$this->assertEquals('Folder 2', $my_folder_subfolders[1]->name);
		
		$folder_1_subfolders = $my_folder_subfolders[0]->subfolders;
		$this->assertEquals('Folder 1.1', $folder_1_subfolders[0]->name);
	}
	
	public function testGetFoldersTreeFlat() {
		$folders = $this->api->getFoldersTreeFlat();
		$this->assertGreaterThan(0, count($folders));
		
		$found_my_folder = false;
		$found_folder_1 = false;
		$found_folder_2 = false;
		$found_folder_1_1 = false;
		foreach($folders as $folder) {
			switch($folder->name) {
			case 'My Folder':
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
	
	public function testGetAssets() {
		$folders = $this->api->getRootFolders();
		foreach($folders as $folder) {
			if($folder->name == 'phpunit')
				$home_folder = $folder;
		}
		
		$assets = $this->api->getAssets($home_folder->id);
		$found = false;
		foreach($assets as $asset) {
			if($asset->filename == '1.jpg')
				$found = true;
		}
		$this->assertTrue($found);
		
		$assets = $this->api->getAssets($home_folder->id, null, 1);
		$found = false;
		foreach($assets as $asset) {
			if($asset->filename == '2.jpg')
				$found = true;
		}
		$this->assertTrue($found);
	}
	
	public function testSetAssetShared() {
		$folders = $this->api->getRootFolders();
		foreach($folders as $folder) {
			if($folder->name == 'My Folder')
				$home_folder = $folder;
		}
		
		$assets = $this->api->getAssets($home_folder->id);
		if(count($assets) > 0) {
			$asset = $assets[0];
			if($asset->shared) {
				$this->setSharedFalse($asset, $home_folder);
				$this->setSharedTrue($asset, $home_folder);
			} else {
				$this->setSharedTrue($asset, $home_folder);
				$this->setSharedFalse($asset, $home_folder);
			}
		}
	}
	private function setSharedTrue($asset, $home_folder) {
		$this->assertTrue($this->api->setAssetShared($asset->id, $asset->kind, 1));
		$assets = $this->api->getAssets($home_folder->id);
		foreach($assets as $asset_new) {
			if($asset_new->id == $asset->id)
				$this->assertTrue($asset_new->shared);
		}
	}
	private function setSharedFalse($asset, $home_folder) {
		$this->assertTrue($this->api->setAssetShared($asset->id, $asset->kind, 0));
		$assets = $this->api->getAssets($home_folder->id);
		foreach($assets as $asset_new) {
			if($asset_new->id == $asset->id)
				$this->assertFalse($asset_new->shared);
		}
	}
	
	public function testGetAsset() {
		$folders = $this->api->getRootFolders();
		foreach($folders as $folder) {
			if($folder->name == 'My Folder')
				$home_folder = $folder;
		}
		
		$assets = $this->api->getAssets($home_folder->id);
		if(count($assets) > 0) {
			$asset_one = $assets[0];
			$asset_two = $this->api->getAsset($asset_one->id, $home_folder->id);
			
			$this->assertNotNull($asset_two);
			$this->assertEquals($asset_one->filename, $asset_two->filename);
		}
	}
}

?>