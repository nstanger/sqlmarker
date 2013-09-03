<?php
// Define things that need to be globally accessible as constants.

$scenario = 'STINK_student_records';

// Used to load XML fixture files, as createXMLDataSet requires an absolute path.
define( 'TEST_CLASS_PATH', realpath( "../${scenario}/Test_classes" ) );

set_include_path( get_include_path() . PATH_SEPARATOR . TEST_CLASS_PATH );

define( 'ORACLE_SERVICE_ID', 'isorcl-214' );

define( 'TRUE_VALUE', 'true' );
define( 'FALSE_VALUE', 'false' );

?>
