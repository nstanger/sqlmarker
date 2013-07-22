<?php
require_once 'Reporter.php';

class HTMLReporter extends Reporter
{
	public function report( $status, $reportText, $printfArguments )
	{
		if ( $this->getVerbosity() )
		{
			$statusText = '<p><span';
			switch ( $status )
			{
				case Reporter::STATUS_PASS:
					$statusText .= ' style="color: green; padding-left: 2em;">✔ ';
					break;
				case Reporter::STATUS_SKIPPED:
					$statusText .= ' style="padding-left: 2em;"># ';
					break;
				case Reporter::STATUS_INCOMPLETE:
					$statusText .= ' style="background-color: yellow; padding-left: 2em;">% ';
					break;
				case Reporter::STATUS_FAILURE:
					$statusText .= ' style="color: red; padding-left: 2em;">✘ ';
					break;
				case Reporter::STATUS_ERROR:
					$statusText .= ' style="color: red; padding-left: 2em;">☠ ';
					break;
				case Reporter::STATUS_WARNING:
					$statusText .= ' style="color: orange; padding-left: 2em;">⚠ ';
					break;
				case Reporter::STATUS_NOTE:
					$statusText .= ' style="background-color: yellow; padding-left: 2em;">⚠ ';
					break;
				default:
				case Reporter::STATUS_TEST:
					$statusText .= ' style="font-weight: bold; font-size: large;">';
					break;
				default:
					$statusText .= ' style="background-color: yellow; padding-left: 2em;">? ';
					break;
			}
			if ( $this->getVerbosity() > 1 ) $statusText .= "<strong>" . ucfirst( strtolower( $status ) ) . ':</strong> ';
			
			parent::report( $statusText, $reportText . "</span></p>\n", $printfArguments );
		}
	}
}

?>
