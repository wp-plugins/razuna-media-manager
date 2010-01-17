<?php

class AuthenticationTest extends PHPUnit_Framework_TestCase implements Base {
	
	public function testLoginHostIdPasswordClear() {
		try {
			$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false, Razuna::HOST_TYPE_ID);
			$session_token = $this->api->login();
			$this->assertGreaterThan(0, strlen($session_token));
		} catch(RazunaException $e) {
			$this->fail(get_class($e));
		}
	}
	
	public function testLoginHostIdPasswordHashed() {
		try {
			$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_HASHED, true, Razuna::HOST_TYPE_ID);
			$session_token = $this->api->login();
			$this->assertGreaterThan(0, strlen($session_token));
		} catch(RazunaException $e) {
			$this->fail(get_class($e));
		}
	}
	
	public function testLoginHostNamePasswordClear() {
		try {
			$this->api = new Razuna(self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false);
			$session_token = $this->api->login();
			$this->assertGreaterThan(0, strlen($session_token));
		} catch(RazunaException $e) {
			$this->fail(get_class($e));
		}
	}
	
	public function testLoginHostNamePasswordHashed() {
		try {
			$this->api = new Razuna(self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_HASHED, true);
			$session_token = $this->api->login();
			$this->assertGreaterThan(0, strlen($session_token));
		} catch(RazunaException $e) {
			$this->fail(get_class($e));
		}
	}
	
}

?>