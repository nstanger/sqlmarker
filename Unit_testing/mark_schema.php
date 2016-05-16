<?php

// HACK: This enables us to force testing of structure or data regardless of any internal state.
if ($argc > 1)
{
    define('ALWAYS_RUN_STRUCTURE_TESTS', $argv[1] === 'structure' || $argv[1] === 'both');
    define('ALWAYS_RUN_DATA_TESTS', $argv[1] === 'data' || $argv[1] === 'both');
}
else
{
    define('ALWAYS_RUN_STRUCTURE_TESTS', true);
    define('ALWAYS_RUN_DATA_TESTS', false);
}

// Define things that need to be globally accessible as constants.
define( 'ORACLE_USERNAME', 'stani797' );
define( 'ORACLE_PASSWORD', 'b1ggles' );

$outputMode = 'ANSI';
	
define( 'OUTPUT_VERBOSITY', 2 );
define( 'RUN_MODE', 'staff' );

require_once 'test_config.php';
require_once 'test.php';

?>
