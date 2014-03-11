<?php

abstract class Reporter
{
	// 0 == no output
	// 1 == brief output (students?)
	// 2 == verbose output (marking)
	// 3 == debug output
	const VERBOSITY_NONE    = 0;
	const VERBOSITY_STUDENT = 1;
	const VERBOSITY_STAFF   = 2;
	const VERBOSITY_DEBUG   = 3;
	
	private $verbosity      = self::VERBOSITY_NONE;
	
	const STATUS_PASS		= 'PASSED';
	const STATUS_SKIPPED	= 'SKIPPED';
	const STATUS_INCOMPLETE	= 'INCOMPLETE';
	const STATUS_FAILURE	= 'FAILED';
	const STATUS_ERROR		= 'ERROR';
	const STATUS_WARNING	= 'WARNING';
	const STATUS_NOTE		= 'NOTE';
	const STATUS_TEST		= 'TEST';
	const STATUS_DEBUG      = 'DEBUG';
	
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
	 *	$statusText is one of: PASSED, FAILED, ERROR, INCOMPLETE, SKIPPED, WARNING, NOTE, MISC, ...?
	 *	$reportText is a printf-style string (although we actually use vprintf because of the array)
	 *	$printfArguments is an array of arguments to $reportText
	 */
	abstract public function report( $statusText, $reportText, $printfArguments = null );
	
	abstract public function hr();
}

?>
