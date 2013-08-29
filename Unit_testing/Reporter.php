<?php

abstract class Reporter
{
	// 0 == no output
	// 1 == brief output (students?)
	// 2 == verbose output (marking)
	private $verbosity = 0;
	
	const STATUS_PASS		= 'PASSED';
	const STATUS_SKIPPED	= 'SKIPPED';
	const STATUS_INCOMPLETE	= 'INCOMPLETE';
	const STATUS_FAILURE	= 'FAILED';
	const STATUS_ERROR		= 'ERROR';
	const STATUS_WARNING	= 'WARNING';
	const STATUS_NOTE		= 'NOTE';
	const STATUS_TEST		= 'TEST';
	
	function __construct( $verbosity )
	{
		$this->verbosity = $verbosity;
	}
	
	public static function pluralise( $count, $oneText, $manyText )
	{
		return ( abs( $count ) !== 1 ) ? $manyText : $oneText;
	}
	
	public function setVerbosity( $newVerbosity )
	{
		$this->verbosity = $newVerbosity;
	}
	
	public function getVerbosity()
	{
		return $this->verbosity;
	}
	
	/**
	 *	$reportText is one of: PASSED, FAILED, ERROR, INCOMPLETE, SKIPPED, WARNING, NOTE, MISC, ...?
	 *	$reportText is a printf-style string (although we actually use vprintf because of the array)
	 *	$printfArguments is an array of arguments to $reportText
	 */
	public function report( $statusText, $reportText, $printfArguments = null )
	{
		if ( $this->verbosity ) vprintf( $statusText . $reportText . "\n", $printfArguments );
	}
	
	abstract public function hr();
}

?>
