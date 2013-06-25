<?php
require_once "Schema.php";

abstract class BDL_Test_Component extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'COMPONENT';
	}
	
	
	public function getColumnList()
	{
		return array(
			'COMPONENT_CODE'	=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	8,
											'max_length'	=>	8,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"87654321",	),
			'SUPPLIERS_CODE'	=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	25,
											'max_length'	=>	25,
											'nullable'		=>	false,
											'test_value'	=>	"B87654321",	),
			'DESCRIPTION'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	100,
											'max_length'	=>	100,
											'nullable'		=>	false,
											'test_value'	=>	"Some component",	),
			'STOCK_COUNT'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	10000000,
											'nullable'		=>	false,
											'test_value'	=>	"456",	),
			'SUPPLIER_ID'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"1",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'COMPONENT_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array( 'SUPPLIER' => array ( 'SUPPLIER_ID' ) );
	}
}
?>
