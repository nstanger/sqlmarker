<?php
require_once 'SimpleTestListener.php';

class TextTestListener extends SimpleTestListener
{
	public function printPass()
	{
		echo "    + OK\n";
	}
	
	public function printFailure( PHPUnit_Framework_AssertionFailedError $e )
	{
	 	echo "    - FAILED! " . $e->getMessage() . "\n";
	}
	
	public function printError( PHPUnit_Framework_AssertionFailedError $e )
	{
		echo "    X ERROR! " . $e->getMessage() . "\n";
	}
	
	public function printIncomplete( PHPUnit_Framework_AssertionFailedError $e )
	{
		echo "    % INCOMPLETE: " .$e->getMessage() . "\n";
	}
	
	public function printSkip( PHPUnit_Framework_AssertionFailedError $e )
	{
		echo "    # SKIPPED: " . $e->getMessage() . "\n";
	}
}

?>
