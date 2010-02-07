<?php
require_once 'PHPUnit/Framework.php';
require_once 'Razuna.class.php';
require_once 'tests/Base.php';
require_once 'tests/AuthenticationTest.php';
require_once 'tests/UserTest.php';
require_once 'tests/FolderTest.php';
require_once 'tests/HostsTest.php';
require_once 'tests/CollectionTest.php';

class RazunaTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new RazunaTest();
		$suite->addTestSuite('AuthenticationTest');
		$suite->addTestSuite('UserTest');
		$suite->addTestSuite('FolderTest');
		$suite->addTestSuite('HostsTest');
		$suite->addTestSuite('CollectionTest');
		return $suite;
	}
}


?>