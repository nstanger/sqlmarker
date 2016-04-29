<?php
require_once "Schema.php";

abstract class BDL_Test_Sale_head extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'SALE_HEAD';
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
										'test_value'	=>	"8765432109",	),
			'SALE_DATE'		=>	array(	'generic_type'	=>	'DATE-FUNCTION',
										'sql_type'		=>	array( 'DATE' ),
										'nullable'		=>	false,
										'overflow'		=>	"SYSDATE + 1",
										'test_value'	=>	"TO_DATE( '2012-09-22', 'YYYY-MM-DD')",	),
			'STATUS'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	11,
										'max_length'	=>	15,
										'nullable'		=>	false,
										'legal_values'	=>	array( "pending", "in progress", "cancelled", "backordered", "shipped" ),
										'illegal_values' => array( "Pending", "PENDING", "foobar", "blurk", "   " ),
										'test_value'	=>	"pending",	),
			'CUSTOMER_ID'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"303",	),
			'STAFF_ID'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"326",	),
			'COMMENTS'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'CLOB' ),
										'min_length'	=>	500,
										'nullable'		=>	true,
										'test_value'	=>	"Blah blah blah",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'SALE_NUM' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'STAFF' => array( 'STAFF_ID' ),
			'CUSTOMER' => array( 'CUSTOMER_ID' ),
		);
	}
	
	
	public function getUniqueColumnList()
	{
		return array();
	}
}
?>
