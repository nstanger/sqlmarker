<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Student extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'STUDENT';
	}
	
	
	public function getColumnList()
	{
		return array(
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'STUDENT_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"1234571",	), // Has to be an unused Person_ID in the fixture because of the FK.
			'HOME_PHONE'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	15,
											'nullable'		=>	true,
											'test_value'	=>	"612245678764",	),
			'HOME_ADDRESS'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	200,
											'max_length'	=>	200,
											'nullable'		=>	false,
											'test_value'	=>	"30 Pinewood Ave, Kalumba, NSW",	),
			'INTERNATIONAL'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	1,
											'nullable'		=>	false,
											'default'       =>  FALSE_VALUE,
											'legal_values'	=>	array( TRUE_VALUE, FALSE_VALUE ),
// 												'f', 'F', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', '0',
// 												't', 'T', 'true', 'True', 'TRUE', 'y', 'Y', 'yes', 'Yes', 'YES', '1',	),
											'illegal_values'=>	array( ' ', 'X', '9', '@' ),
											'test_value'	=>	TRUE_VALUE,	),
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'SUPERVISOR_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'aliases'		=>	array( 'STAFF_ID', 'PERSON_ID' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'nullable'		=>	true,
											'test_value'	=>	"1234567",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'STUDENT_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'PERSON' => array( 'STUDENT_ID' ),
			'STAFF' => array( 'SUPERVISOR_ID' ),
		);
	}
}
?>
