<?php

class UserTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false);
		$session_token = $this->api->login();
	}
	
	public function testGetCurrentUser() {
		$user = $this->api->getSessionUser();
		$this->assertType('RazunaUser', $user);
		$this->assertEquals(655, $user->getId());
		$this->assertEquals('phpunit', $user->getLoginname());
		$this->assertEquals('admin@chdorner.com', $user->getEmail());
		$this->assertEquals('PHP Unit', $user->getFirstName());
		$this->assertEquals('User', $user->getLastName());
	}
	
	public function testAddUser() {
		try {
			$user = new RazunaUser('testuser', 'testuser@chdorner.com', 'Test', 'User', 'testuserPassword123', 'T');
			$this->assertTrue($this->api->addUser($user));
		} catch(RazunaException $e) {
			$this->fail(get_class($e) .": ". $e->getMessage());
		}
	}
}

?>