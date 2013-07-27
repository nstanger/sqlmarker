<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Schedule extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'SCHEDULE';
	}
	
	
	public function getColumnList()
	{
		return array(
			// No need to test legal values because of the FK. If the FK is missing it's broken anyway!
			'ABBREVIATION'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	10,
											'nullable'		=>	false,
											'test_value'	=>	"BCom",	),
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
		return array( 'ABBREVIATION', 'PAPER_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'QUALIFICATION'	=>	array( 'ABBREVIATION' ),
			'PAPER'			=>	array( 'PAPER_CODE' ),
		);
	}
}
?>
