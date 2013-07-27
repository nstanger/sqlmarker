<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Assessment extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'ASSESSMENT';
	}
	
	
	public function getColumnList()
	{
		return array(
			'ASSESSMENT_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	10,
											'max_length'	=>	10,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"9876543210",	),
			'ASSESSMENT_YEAR'	=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	4,
											'max_length'	=>	4,
											'nullable'		=>	false,
											'underflow'		=>	1981,
											'test_value'	=>	"2013",	),
			'NAME'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"Assignment thing",	),
			'DESCRIPTION'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	500,
											'max_length'	=>	500,
											'nullable'		=>	true,
											'test_value'	=>	"Blah blah blah blah",	),
			'TYPE'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	1,
											'max_length'	=>	1,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'A', 'P', 'T', 'X' ),
											'illegal_values'=>	array( 'a', ' ', 'Q', '9', '@' ),
											'test_value'	=>	"A",	),
			'RELEASE'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	1,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'T', 'F' ),
// 												'f', 'F', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', '0',
// 												't', 'T', 'true', 'True', 'TRUE', 'y', 'Y', 'yes', 'Yes', 'YES', '1',	),
											'illegal_values'=>	array( ' ', 'X', '9', '@' ),
											'test_value'	=>	"T",	),
			'WEIGHT'			=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	3,
											'max_length'	=>	3,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	101,
											'nullable'		=>	false,
											'test_value'	=>	"15",	),
			'MAXIMUM_MARK'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	3,
											'max_length'	=>	3,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'nullable'		=>	true,
											'test_value'	=>	"15",	),
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'PAPER_CODE'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'nullable'		=>	false,
											'test_value'	=>	"INFO214",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'ASSESSMENT_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array( 'PAPER' => array( 'PAPER_CODE' ), );
	}
}
?>
