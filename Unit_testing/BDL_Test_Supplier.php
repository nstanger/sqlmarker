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
			'SUPPLIER_ID'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
// 											'underflow'		=>	-1,
											'nullable'		=>	false,
											'test_value'	=>	"123456",	),
			'NAME'				=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"'Bolgoxed'",	),
			'CONTACT_PERSON'	=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	true,
											'test_value'	=>	"'Henrietta Bloggs'",	),
			'PHONE'				=>	array(	'type'			=>	array( 'VARCHAR2', 'CHAR', 'VARCHAR' ),
											'min_length'	=>	12,
											'max_length'	=>	25,
											'nullable'		=>	false,
											'test_value'	=>	"'646139981234'",	),
			'ADDRESS'			=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	200,
											'max_length'	=>	200,
											'nullable'		=>	false,
											'test_value'	=>	"'12 Western Way'",	),
			'EMAIL'				=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	true,
											'test_value'	=>	"'henrietta@bolgoxed.com'",	),
			'COMMENTS'			=>	array(	'type'			=>	array( 'VARCHAR2', 'CLOB' ),
											'min_length'	=>	1000,
											'max_length'	=>	4000,
											'nullable'		=>	true,
											'test_value'	=>	"'Blah blah blah'",	),	);
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
