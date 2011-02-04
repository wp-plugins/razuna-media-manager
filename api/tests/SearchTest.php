<?php

class SearchTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false, Razuna::HOST_TYPE_ID);
		$session_token = $this->api->login();
	}
	
	public function testSearchAssets() {
		$assets = $this->api->searchAssets("1.jpg");
		$found = false;
		
		foreach($assets as $asset) {
			if($asset->filename == '1.jpg') {
				$found = true;
			}
		}
		
		$this->assertTrue($found);
	}
}

?>