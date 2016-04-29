<?php
require_once "Schema.php";

abstract class BDL_Test_Assembly extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'ASSEMBLY';
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
											'test_value'	=>	"237",	),
			'COMPONENT_CODE'	=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	8,
											'max_length'	=>	8,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"660",	),
			'QUANTITY'			=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
											'min_length'	=>	4,
											'max_length'	=>	4,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	10000,
											'nullable'		=>	false,
											'test_value'	=>	"456",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'PRODUCT_CODE', 'COMPONENT_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'PRODUCT' => array ( 'PRODUCT_CODE' ),
			'COMPONENT' => array ( 'COMPONENT_CODE' ),
		);
	}
	
	
	public function getUniqueColumnList()
	{
		return array();
	}
}
?>
