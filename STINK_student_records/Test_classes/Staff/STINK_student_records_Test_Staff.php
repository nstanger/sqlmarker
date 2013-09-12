<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Staff extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'STAFF';
	}
	
	
	public function getColumnList()
	{
		return array(
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'STAFF_ID'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"1234569",	), // Has to be an unused Person_ID in the fixture because of the FK.
			'RANK'			=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	2,
										'nullable'		=>	false,
										'legal_values'	=>	array( 'T', 'AL', 'L', 'SL', 'AP', 'P', ),
										'illegal_values'=>	array( 't', '12', ' ', ),
										'test_value'	=>	"L",	),
			'SALARY'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'DECIMAL' ),
										'min_length'	=>	8,
										'decimals'		=>	2,
										'underflow'		=>	40449.99,
										'legal_values'  =>  array( 40450 ),
										'nullable'		=>	false,
										'test_value'	=>	"85000",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'STAFF_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array( 'PERSON' => array( 'STAFF_ID' ) );
	}
}
?>
