<?php
// Define things that need to be globally accessible as constants.

$scenario = 'BDL';

// Used to load XML fixture files, as createXMLDataSet requires an absolute path.
define( 'TEST_CLASS_PATH', realpath( "../${scenario}/Test_classes" ) );

set_include_path( get_include_path() . PATH_SEPARATOR . TEST_CLASS_PATH );

define( 'ORACLE_SERVICE_ID', 'isorcl-400' );

?>
