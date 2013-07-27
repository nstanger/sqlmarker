<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Qualification extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'QUALIFICATION';
	}
	
	
	public function getColumnList()
	{
		return array(
			'ABBREVIATION'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	10,
											'nullable'		=>	false,
											'test_value'	=>	"BCom",	),
			'FULL_NAME'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	100,
											'max_length'	=>	100,
											'nullable'		=>	false,
											'test_value'	=>	"Bachelor of Commerce",	),
			'TYPE'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	11,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'Degree', 'Diploma', 'Certificate' ),
											'illegal_values'=>	array( 'degree', 'DEGREE', 'foobar', '*^!%@$+', '    ' ),
											'test_value'	=>	"Degree",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'ABBREVIATION' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
}
?>
