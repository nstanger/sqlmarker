<?php
require_once 'TextReporter.php';
require_once 'Util/ANSIColors.php';

class ANSIReporter extends TextReporter
{
    private $ANSI;
    
    public function __construct( $verbosity )
    {
        $this->ANSI = new ANSIColors();
        parent::__construct( $verbosity );
    }
    
	public function report ( $status, $reportText, $printfArguments )
	{
		if ( $this->getVerbosity() )
		{
			$fg = null;
			$bg = null;
			switch ( $status )
			{
				case Reporter::STATUS_PASS:
					$bg = 'green';
					break;
				case Reporter::STATUS_INCOMPLETE:
					$bg = 'yellow';
					break;
				case Reporter::STATUS_FAILURE:
				case Reporter::STATUS_ERROR:
				    $fg = 'white';
					$bg = 'red';
					break;
				case Reporter::STATUS_WARNING:
					$bg = 'yellow';
					break;
				case Reporter::STATUS_NOTE:
					$bg = 'light_gray';
					break;
				default:
					break;
			}
			
			echo $this->ANSI->setColor( $fg, $bg );
			
			parent::report( $status, $reportText . $this->ANSI->resetANSI(), $printfArguments );
		}
	}
}

?>
