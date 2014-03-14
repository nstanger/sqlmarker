<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Result extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'RESULT';
	}
	
	
	public function getColumnList()
	{
		return array(
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'ASSESSMENT_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	10,
											'max_length'	=>	10,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"1234567890",	),
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'ENROLMENT_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	10,
											'max_length'	=>	10,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"1234567892",	),
			'RAW_MARK'			=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'DECIMAL' ),
											'min_length'	=>	4,
											'max_length'	=>	4,
											'decimals'		=>	1,
											'underflow'		=>	-1,
											'legal_values'  =>  array( 0 ),
											'nullable'		=>	false,
											'test_value'	=>	"15",	),
			'WEIGHTED_MARK'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'FLOAT' ),
											'underflow'		=>	-1,
											'legal_values'  =>  array( 0 ),
											'nullable'		=>	false,
											'test_value'	=>	"23.4625",	),
			'PERCENTAGE_MARK'	=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'DECIMAL' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	2,
											'underflow'		=>	-1,
											'overflow'		=>	101,
											'legal_values'  =>  array( 0, 100 ),
											'nullable'		=>	false,
											'test_value'	=>	"63",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'ASSESSMENT_ID', 'ENROLMENT_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'ASSESSMENT'	=>	array( 'ASSESSMENT_ID' ),
			'ENROLMENT'		=>	array( 'ENROLMENT_ID' ),
		);
	}
	
	
	public function getUniqueColumnList()
	{
		return array();
	}
}
?>
