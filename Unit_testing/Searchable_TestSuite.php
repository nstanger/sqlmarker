<?php

class Searchable_TestSuite extends PHPUnit_Framework_TestSuite
{

	public function testExists( $name )
	{
		foreach ( $this->tests() as $test )
		{
			if ( $test->getName() === $name ) return $test;
		}
		return false;
	}

}
?>
