<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Paper extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'PAPER';
	}
	
	
	public function getColumnList()
	{
		return array(
			'PAPER_CODE'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'ACCT112', 'COMP160', 'BSNS106' ),
											'illegal_values'=>	array( 'acct112', 'COMPABC', '1234XYZ', '1234567', '*^!%@$+' ),
											'test_value'	=>	"INFO214",	),
			'TITLE'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"ICT Business Infrastructure",	),
			'DESCRIPTION'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	500,
											'max_length'	=>	500,
											'nullable'		=>	false,
											'test_value'	=>	"Blah blah blah boring blah blah blah blah.",	),
			'POINTS'			=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	2,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	37,
											'default'		=>	18,
											'nullable'		=>	false,
											'test_value'	=>	"18",	),
			'PERIOD'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	2,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'S1', 'S2', 'SS', 'FY' ),
											'illegal_values'=>	array( 'fy', 'S3', 'XX', '12', '^@' ),
											'test_value'	=>	"S1",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'PAPER_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
}
?>
