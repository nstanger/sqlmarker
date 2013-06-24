<?php
require_once "Schema.php";

abstract class BDL_Test_Order_line extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'ORDER_LINE';
	}
	
	
	public function getColumnList()
	{
		return array(
			'ORDER_NUM'			=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	10,
											'max_length'	=>	10,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"36",	),
			'COMPONENT_CODE'	=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	8,
											'max_length'	=>	8,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"687",	),
			'SUPPLIERS_CODE'	=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	25,
											'max_length'	=>	25,
											'nullable'		=>	false,
											'test_value'	=>	"'4058704'",	),
			'QTY_ORDERED'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	0,
											'overflow'		=>	100000,
											'nullable'		=>	false,
											'test_value'	=>	"20",	),
			'QTY_RECEIVED'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
											'min_length'	=>	5,
											'max_length'	=>	6,
											'decimals'		=>	0,
											'underflow'		=>	-1,
// 											'overflow'		=>	100000,
											'nullable'		=>	false,
											'test_value'	=>	"20",	),
			'PRICE'				=>	array(	'type'			=>	array( 'NUMBER', 'DECIMAL' ),
											'min_length'	=>	6,
											'max_length'	=>	6,
											'decimals'		=>	2,
											'underflow'		=>	-0.01,
											'overflow'		=>	100000.00,
											'nullable'		=>	false,
											'test_value'	=>	"24.99",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'ORDER_NUM', 'COMPONENT_CODE', 'SUPPLIERS_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'ORDER_HEAD' => array( 'ORDER_NUM' ),
			'COMPONENT' => array( 'COMPONENT_CODE', 'SUPPLIERS_CODE' ),
		);
	}
}
?>
