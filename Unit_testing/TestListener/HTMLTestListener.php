<?php
require_once 'SimpleTestListener.php';

class HTMLTestListener extends SimpleTestListener
{
	public function printPass()
	{
		echo "<p class='blackboard' style='color: green; padding-left: 2em;'>✔ <strong>OK</strong></p>\n";
	}
	
	public function printFailure( Exception $e )
	{
	 	echo "<p class='blackboard' style='color: red; padding-left: 2em;'>✘ <strong>Failed!</strong> " . htmlspecialchars( $e->getMessage() ) . "</p>\n";
	}
	
	public function printError( Exception $e )
	{
		echo "<p class='blackboard' style='color: red; padding-left: 2em;'>☠ <strong>Error!</strong> " . htmlspecialchars( $e->getMessage() ) . "</p>\n";
	}
	
	public function printIncomplete( Exception $e )
	{
		echo "<p class='blackboard' style='background-color: yellow; padding-left: 2em;'><strong>Incomplete:</strong> " . htmlspecialchars( $e->getMessage() ) . "</p>\n";
	}
	
	public function printSkip( Exception $e )
	{
		echo "<p class='blackboard' style='padding-left: 2em;'><strong>Skipped:</strong> " . htmlspecialchars( $e->getMessage() ) . "</p>\n";
	}
}

?>
