<?php

class FolderTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false);
		$session_token = $this->api->login();
	}
	
	public function testGetFolders() {
		$folders = $this->api->getFolders();
		$this->assertGreaterThan(0, count($folders));
	}
	
	public function testGetHomeFolder() {
		$folder = $this->api->getHomeFolder();
		$this->assertNotNull($folder);
		$this->assertEquals('My Folder', $folder->name);
	}
	
	public function testGetFoldersTree() {
		$folders = $this->api->getFoldersTree();
		$this->assertEquals('My Folder', $folders[0]->name);
		
		$my_folder_subfolders = $folders[0]->subfolders;
		$this->assertEquals('Folder 1', $my_folder_subfolders[0]->name);
		$this->assertEquals('Folder 2', $my_folder_subfolders[1]->name);
		
		$folder_1_subfolders = $my_folder_subfolders[0]->subfolders;
		$this->assertEquals('Folder 1.1', $folder_1_subfolders[0]->name);
	}
	
	public function testGetAssets() {
		$home_folder = $this->api->getHomeFolder();
		
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
		$home_folder = $this->api->getHomeFolder();
		
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
		$home_folder = $this->api->getHomeFolder();
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