<?php
require_once 'SimpleTestListener.php';

class TextTestListener extends SimpleTestListener
{
	public function printPass()
	{
		echo "    + OK\n";
	}
	
	public function printFailure( Exception $e )
	{
	 	echo "    - FAILED! " . $e->getMessage() . "\n";
	}
	
	public function printError( Exception $e )
	{
		echo "    X ERROR! " . $e->getMessage() . "\n";
	}
	
	public function printIncomplete( Exception $e )
	{
		echo "    % INCOMPLETE: " .$e->getMessage() . "\n";
	}
	
	public function printSkip( Exception $e )
	{
		echo "    # SKIPPED: " . $e->getMessage() . "\n";
	}
}

?>
