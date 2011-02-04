<?php

class HostsTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false, Razuna::HOST_TYPE_ID);
		$session_token = $this->api->login();
	}
	
	public function testGetHosts() {
		$hosts = $this->api->getHosts();
		$found = false;
		
		foreach($hosts as $host) {
			if($host->id == $host->id) {
				$found = true;
			}
		}
		
		$this->assertTrue($found);
	}
}

?>