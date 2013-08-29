<?php

class ANSIColors
{
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }
    
    // Generate ANSI escape sequence.
    public function ansiEscape( $arg )
    {
        return sprintf( "\033[%sm", $arg );
    }

    // Return colorized string.
    public function colorizeString( $string, $foreground_color = null, $background_color = null )
    {
        return sprintf( "%s%s%s%s",
                        $this->setForegroundColor( $foreground_color ),
                        $this->setBackgroundColor( $background_color ),
                        $string,
                        $this->resetANSI()  );
    }

    // Set foreground color (if it exists).
    public function setForegroundColor( $foreground_color = null )
    {
        return ( isset( $this->foreground_colors[ $foreground_color ] ) )
                ? $this->ansiEscape( $this->foreground_colors[ $foreground_color ] ) : '';
    }

    // Set background color (if it exists).
    public function setBackgroundColor( $background_color = null )
    {
        return ( isset( $this->background_colors[ $background_color ] ) )
                ? $this->ansiEscape( $this->background_colors[ $background_color ] ) : '';
    }
    
    // Set the color.
    public function setColor( $foreground_color = null, $background_color = null )
    {
        return $this->setForegroundColor( $foreground_color ) . $this->setBackgroundColor( $background_color );
    }
    
    // Reset all ANSI formatting.
    public function resetANSI()
    {
        return $this->ansiEscape( '0' );
    }

    // Returns specified foreground color.
    public function getForegroundColor( $name )
    {
        return ( isset( $this->foreground_colors[$name] ) ) ? $this->foreground_colors[$name] : '';
    }

    // Returns specified background color.
    public function getBackgroundColor( $name )
    {
        return ( isset( $this->background_colors[$name] ) ) ? $this->background_colors[$name] : '';
    }

    // Returns all foreground color names.
    public function getForegroundColors()
    {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names.
    public function getBackgroundColors()
    {
        return array_keys($this->background_colors);
    }
}

?>
