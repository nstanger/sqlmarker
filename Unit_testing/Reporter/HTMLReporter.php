<?php
require_once( 'Reporter.php' );

class HTMLReporter extends Reporter
{
	public function report ( $status, $reportText, $printfArguments )
	{
		if ( $this->getVerbosity() )
		{
			$statusText = '<p><span';
			switch ( $status )
			{
				case Reporter::STATUS_PASS:
					$statusText .= ' style="color: green;">✔✔✔ ';
					break;
				case Reporter::STATUS_SKIPPED:
					$statusText .= '>### ';
					break;
				case Reporter::STATUS_INCOMPLETE:
					$statusText .= ' style="background-color: yellow;">%%% ';
					break;
				case Reporter::STATUS_FAILURE:
					$statusText .= ' style="color: red;">✘✘✘ ';
					break;
				case Reporter::STATUS_ERROR:
					$statusText .= ' style="color: red;">☠☠☠ ';
					break;
				case Reporter::STATUS_WARNING:
					$statusText .= ' style="color: orange;">!!! ';
					break;
				case Reporter::STATUS_NOTE:
					$statusText .= ' style="background-color: yellow;">!!! ';
					break;
				default:
					$statusText .= ' style="background-color: yellow;">??? ';
					break;
			}
			if ( $this->getVerbosity() > 1 ) $statusText .= $status . ': ';
			
			parent::report( $statusText, $reportText . "</span></p>\n", $printfArguments );
		}
	}
}

?>
