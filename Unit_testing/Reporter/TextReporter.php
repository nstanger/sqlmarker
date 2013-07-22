<?php
require_once( 'Reporter.php' );

class TextReporter extends Reporter
{
	public function report ( $status, $reportText, $printfArguments, $nl = true )
	{
		if ( $this->getVerbosity() )
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
					$statusText .= 'EEE ';
					break;
				case Reporter::STATUS_WARNING:
					$statusText .= '!!! ';
					break;
				case Reporter::STATUS_NOTE:
					$statusText .= '!!! ';
					break;
				case Reporter::STATUS_MISC:
					$statusText .= '    ';
					break;
				default:
					$statusText .= '??? ';
					break;
			}
			if ( $this->getVerbosity() > 1 ) $statusText .= $status . ': ';
			
			parent::report( $statusText, $reportText, $printfArguments, $nl );
		}
	}
}

?>
