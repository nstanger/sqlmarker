<?php
require_once 'TextTestListener.php';

class ANSITestListener extends TextTestListener
{
    private $ANSI;
    
    public function __construct()
    {
        $this->ANSI = new ANSIColors();
    }
    
	public function printPass()
	{
	    echo $this->ANSI->setForegroundColor( 'green' );
	    parent::printPass();
		echo $this->ANSI->resetANSI();
	}
	
	public function printFailure( Exception $e )
	{
	    echo $this->ANSI->setForegroundColor( 'red' );
	    parent::printFailure( $e );
		echo $this->ANSI->resetANSI();
	}
	
	public function printError( Exception $e )
	{
	    echo $this->ANSI->setForegroundColor( 'red' );
	    parent::printError( $e );
		echo $this->ANSI->resetANSI();
	}
	
	public function printIncomplete( Exception $e )
	{
	    echo $this->ANSI->setForegroundColor( 'yellow' );
	    parent::printIncomplete( $e );
		echo $this->ANSI->resetANSI();
	}
}

?>
