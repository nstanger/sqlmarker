<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Teach extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'TEACH';
	}
	
	
	public function getColumnList()
	{
		return array(
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'STAFF_ID'			=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"1234569",	),
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'PAPER_CODE'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'nullable'		=>	false,
											'test_value'	=>	"COMP160",	),
			'YEAR_TAUGHT'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	4,
											'max_length'	=>	4,
											'decimals'		=>	0,
											'underflow'		=>	1981,
											'overflow'		=>	10000,
											'legal_values'	=>	array( 1982 ),
											'nullable'		=>	false,
											'test_value'	=>	"2012",	),
			'ROLE'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	11,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'Coordinator', 'Lecturer', 'Tutor' ),
											'illegal_values'=>	array( 'coordinator', 'COORDINATOR', 'foobar', '    ', '*^!%@$+' ),
											'test_value'	=>	"Coordinator",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'STAFF_ID', 'PAPER_CODE', 'YEAR_TAUGHT' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'STAFF'	=>	array( 'STAFF_ID' ),
			'PAPER'	=>	array( 'PAPER_CODE' ),
		);
	}
	
	
	public function getUniqueColumnList()
	{
		return array();
	}
}
?>
