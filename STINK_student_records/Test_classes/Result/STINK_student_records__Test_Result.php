<?php
require_once "Schema.php";

abstract class BDL_Test_Supplier extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'SUPPLIER';
	}
	
	
	public function getColumnList()
	{
		return array(
			'SUPPLIER_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
// 											'underflow'		=>	-1,
											'nullable'		=>	false,
											'test_value'	=>	"123456",	),
			'NAME'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"Bolgoxed",	),
			'CONTACT_PERSON'	=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	true,
											'test_value'	=>	"Henrietta Bloggs",	),
			'PHONE'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'CHAR', 'VARCHAR' ),
											'min_length'	=>	11,
											'max_length'	=>	20,
											'nullable'		=>	false,
											'legal_values'	=>	array( "034511010", "2718391780", "+2496391734", "92819209365" ),
											'test_value'	=>	"646139981234",	),
			'ADDRESS'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	200,
											'max_length'	=>	200,
											'nullable'		=>	false,
											'test_value'	=>	"12 Western Way",	),
			'EMAIL'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	true,
											'test_value'	=>	"henrietta@bolgoxed.com",	),
			'COMMENTS'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'CLOB' ),
											'min_length'	=>	500,
											'nullable'		=>	true,
											'test_value'	=>	"Blah blah blah",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'SUPPLIER_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
}
?>
