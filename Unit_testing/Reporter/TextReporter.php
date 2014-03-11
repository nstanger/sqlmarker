<?php
require_once 'Reporter.php';

class TextReporter extends Reporter
{
	public function report ( $status, $reportText, $printfArguments = null )
	{
	    if ( ( $status === Reporter::STATUS_DEBUG ) && ( $this->getVerbosity() !== Reporter::VERBOSITY_DEBUG ) ) return;
	    
		if ( $this->getVerbosity() > Reporter::VERBOSITY_NONE )
		{
			$statusText = '';
			switch ( $status )
			{
				case Reporter::STATUS_PASS:
					$statusText .= '+++ ';
					break;
				case Reporter::STATUS_SKIPPED:
					$statusText .= '### ';
					break;
				case Reporter::STATUS_INCOMPLETE:
					$statusText .= '%%% ';
					break;
				case Reporter::STATUS_FAILURE:
					$statusText .= '--- ';
					break;
				case Reporter::STATUS_ERROR:
					$statusText .= 'XXX ';
					break;
				case Reporter::STATUS_WARNING:
					$statusText .= '!!! ';
					break;
				case Reporter::STATUS_NOTE:
				case Reporter::STATUS_TEST:
				case Reporter::STATUS_DEBUG:
					break;
				default:
					$statusText .= '??? ';
					break;
			}
			if ( $this->getVerbosity() > Reporter::VERBOSITY_STUDENT ) $statusText .= $status . ': ';
			
		    $output = vsprintf( $statusText . $reportText . "\n", $printfArguments );
		    fwrite( STDOUT, $output );
		}
	}
	
	public function hr()
	{
		echo "------------------------------------------------------------\n";
	}
}

?>
