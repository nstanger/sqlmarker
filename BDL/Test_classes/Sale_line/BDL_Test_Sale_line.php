<?php
require_once "Schema.php";

abstract class BDL_Test_Sale_line extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'SALE_LINE';
	}
	
	
	public function getColumnList()
	{
		return array(
			'SALE_NUM'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	10,
										'max_length'	=>	10,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"223",	),
			'PRODUCT_CODE'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	8,
										'max_length'	=>	8,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"246",	),
			'QUANTITY'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
										'min_length'	=>	4,
										'max_length'	=>	4,
										'decimals'		=>	0,
										'underflow'		=>	0,
										'overflow'		=>	10000,
										'nullable'		=>	false,
										'test_value'	=>	"20",	),
			'ACTUAL_PRICE'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'DECIMAL' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	2,
										'underflow'		=>	-0.01,
										'overflow'		=>	100000.00,
										'nullable'		=>	false,
										'test_value'	=>	"24.99",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'SALE_NUM', 'PRODUCT_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'SALE_HEAD' => array( 'SALE_NUM' ),
			'PRODUCT' => array( 'PRODUCT_CODE' ),
		);
	}
	
	
	public function getUniqueColumnList()
	{
		return array();
	}
}
?>
