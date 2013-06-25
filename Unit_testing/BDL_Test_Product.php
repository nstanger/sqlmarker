<?php
require_once "Schema.php";

abstract class BDL_Test_Product extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'PRODUCT';
	}
	
	
	public function getColumnList()
	{
		return array(
			'PRODUCT_CODE'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	8,
											'max_length'	=>	8,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"87654321",	),
			'DESCRIPTION'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"Some product",	),
			'STOCK_COUNT'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	100000,
											'nullable'		=>	false,
											'test_value'	=>	"456",	),
			'RESTOCK_LEVEL'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	100000,
											'nullable'		=>	true,
											'test_value'	=>	"654",	),
			'MINIMUM_LEVEL'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	100000,
											'nullable'		=>	true,
											'legal_values'	=>	array( 0, 653 ),
											'illegal_values' =>	array( 654, 655 ),
											'test_value'	=>	"456",	),
			'LIST_PRICE'		=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'DECIMAL' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	2,
											'underflow'		=>	-0.01,
											'overflow'		=>	100000.00,
											'nullable'		=>	false,
											'test_value'	=>	"123.99",	),
			'ASSEMBLY_MANUAL'	=>	array(	'generic_type'	=>	'BINARY',
											'sql_type'		=>	array( 'BLOB' ),
											'nullable'		=>	true,
											'test_value'	=>	"NULL",	),
			'ASSEMBLY_PROGRAM'	=>	array(	'generic_type'	=>	'BINARY',
											'sql_type'		=>	array( 'BLOB' ),
											'nullable'		=>	true,
											'test_value'	=>	"NULL",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'PRODUCT_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
}
?>
