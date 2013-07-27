<?php
require_once 'Reporter.php';

class HTMLReporter extends Reporter
{
	public function report( $status, $reportText, $printfArguments )
	{
		if ( $this->getVerbosity() )
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
					$statusText .= ' uniyellbg result"><strong style="font-size: large">%</strong> ';
					break;
				case Reporter::STATUS_FAILURE:
					$statusText .= ' uniredbg result""><span style="font-size: large">✘</span> ';
					break;
				case Reporter::STATUS_ERROR:
					$statusText .= ' uniredbg result"><span style="font-size: large">☠</span> ';
					break;
				case Reporter::STATUS_WARNING:
					$statusText .= ' uniyellbg result"><span style="font-size: large">⚠</span> ';
					break;
				case Reporter::STATUS_NOTE:
					$statusText .= ' grey-light result">';
					break;
				default:
				case Reporter::STATUS_TEST:
					$statusText .= ' style="font-weight: bold;">';
					break;
				default:
					$statusText .= ' uniyellbg result"><strong style="font-size: large">?</strong> ';
					break;
			}
			if ( $this->getVerbosity() > 1 ) $statusText .= "<strong>" . ucfirst( strtolower( $status ) ) . ':</strong> ';
			
			parent::report( $statusText, $reportText . "</span></p>\n", $printfArguments );
		}
	}
	
	public function hr()
	{
		echo "<hr />\n";
	}
}

?>
