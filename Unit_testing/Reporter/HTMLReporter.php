<?php
require_once 'Reporter.php';

class HTMLReporter extends Reporter
{
    private $outputStream;

	function __construct( $verbosity )
	{
	    $this->outputStream = fopen( 'php://output', 'w' );
		parent::__construct( $verbosity );
	}
	
	public function report( $status, $reportText, $printfArguments = null )
	{
	    if ( ( $status === Reporter::STATUS_DEBUG ) && ( $this->getVerbosity() !== Reporter::VERBOSITY_DEBUG ) ) return;
	    
		if ( $this->getVerbosity() > Reporter::VERBOSITY_NONE )
		{
			$statusText = '<p class="blackboard';
			switch ( $status )
			{
				case Reporter::STATUS_PASS:
					$statusText .= ' greenbg result"><span style="font-size: large">✔</span> ';
					break;
				case Reporter::STATUS_SKIPPED:
					$statusText .= '" style="padding-left: 2em;"><strong style="font-size: large">#</strong> ';
					break;
				case Reporter::STATUS_INCOMPLETE:
					$statusText .= ' yellow-ou result"><strong style="font-size: large">%</strong> ';
					break;
				case Reporter::STATUS_FAILURE:
					$statusText .= ' red-ou result""><span style="font-size: large">✘</span> ';
					break;
				case Reporter::STATUS_ERROR:
					$statusText .= ' red-ou result"><span style="font-size: large">☠</span> ';
					break;
				case Reporter::STATUS_WARNING:
					$statusText .= ' yellow-ou result"><span style="font-size: large">⚠</span> ';
					break;
				case Reporter::STATUS_NOTE:
				case Reporter::STATUS_DEBUG:
					$statusText .= ' grey-light result">';
					break;
				case Reporter::STATUS_TEST:
					$statusText .= ' style="font-weight: bold;">';
					break;
				default:
					$statusText .= ' yellow-ou result"><strong style="font-size: large">?</strong> ';
					break;
			}
			if ( $this->getVerbosity() > Reporter::VERBOSITY_STUDENT ) $statusText .= "<strong>" . ucfirst( strtolower( $status ) ) . ':</strong> ';
			
		    $message = vsprintf( $statusText . $reportText . "</span></p>\n", $printfArguments );
		    fwrite( $this->outputStream, $message );
		}
	}
	
	public function hr()
	{
		echo "<hr />\n";
	}
}

?>
