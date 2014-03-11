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
    
	public function report ( $status, $reportText, $printfArguments = null )
	{
	    if ( ( $status === Reporter::STATUS_DEBUG ) && ( $this->getVerbosity() !== Reporter::VERBOSITY_DEBUG ) ) return;
	    
		if ( $this->getVerbosity() > Reporter::VERBOSITY_NONE )
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
				case Reporter::STATUS_DEBUG:
					$bg = 'light_gray';
					break;
				default:
					break;
			}
			
			fwrite( STDOUT, $this->ANSI->setColor( $fg, $bg ) );
			
			parent::report( $status, $reportText . $this->ANSI->resetANSI(), $printfArguments );
		}
	}
}

?>
