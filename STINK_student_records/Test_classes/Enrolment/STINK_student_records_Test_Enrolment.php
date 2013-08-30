<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Enrolment extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'ENROLMENT';
	}
	
	
	public function getColumnList()
	{
		return array(
			'ENROLMENT_ID'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	10,
										'max_length'	=>	10,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"8765432109",	),
			'DESCRIPTION'	=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	100,
										'max_length'	=>	100,
										'nullable'		=>	false,
										'test_value'	=>	"Blah blah blah",	),
			'YEAR_ENROLLED'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	4,
										'max_length'	=>	4,
										'nullable'		=>	false,
										'underflow'		=>	1981,
										'legal_values'	=>	array( 1982 ),
										'test_value'	=>	"2013",	),
			'COMMENTS'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'CLOB' ),
										'min_length'	=>	500,
										'nullable'		=>	true,
										'test_value'	=>	"Blah blah blah",	),
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'STUDENT_ID'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"1234568",	), // Has to be an unused Person_ID in the fixture because of the FK.
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'PAPER_CODE'	=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'nullable'		=>	false,
										'test_value'	=>	"ACCT112",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'ENROLMENT_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'STUDENT' => array( 'STUDENT_ID' ),
			'PAPER' => array( 'PAPER_CODE' ),
		);
	}
}
?>
