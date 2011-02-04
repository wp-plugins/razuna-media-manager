<?php

class UserTest extends PHPUnit_Framework_TestCase implements Base {
	
	protected $api;
	protected $session_token;
	
	protected function setUp() {
		$this->api = new Razuna(self::CONFIG_HOST_ID, self::CONFIG_HOST_NAME, self::CONFIG_USERNAME, self::CONFIG_PASSWORD_CLEAR, false, Razuna::HOST_TYPE_ID);
		$session_token = $this->api->login();
	}
	
	public function testGetCurrentUser() {
		$user = $this->api->getSessionUser();
		$this->assertType('RazunaUser', $user);
		$this->assertEquals(self::EXPECTED_USER_ID, $user->getId());
		$this->assertEquals(self::CONFIG_USERNAME, $user->getLoginname());
		$this->assertEquals(self::EXPECTED_USER_EMAIL, $user->getEmail());
		$this->assertEquals(self::EXPECTED_USER_FIRST_NAME, $user->getFirstName());
		$this->assertEquals(self::EXPECTED_USER_LAST_NAME, $user->getLastName());
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