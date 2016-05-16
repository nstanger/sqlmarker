<?php
require_once "Schema.php";

abstract class BDL_Test_Order_head extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'ORDER_HEAD';
	}
	
	
	public function getColumnList()
	{
		return array(
			'ORDER_NUM'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	10,
										'max_length'	=>	10,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"8765432109",	),
			'ORDER_DATE'	=>	array(	'generic_type'	=>	'DATE-FUNCTION',
										'sql_type'		=>	array( 'DATE' ),
										'nullable'		=>	false,
										'overflow'		=>	"SYSDATE + 1",
										'test_value'	=>	"TO_DATE( '2012-10-22', 'YYYY-MM-DD')",	),
			'DUE_DATE'		=>	array(	'generic_type'	=>	'DATE-FUNCTION',
										'sql_type'		=>	array( 'DATE' ),
										'nullable'		=>	false,
										'underflow'		=>	"TO_DATE( '2012-10-21', 'YYYY-MM-DD')",
										'test_value'	=>	"TO_DATE( '2012-10-23', 'YYYY-MM-DD')",	),
			'DATE_COMPLETED'=>	array(	'generic_type'	=>	'DATE-FUNCTION',
										'sql_type'		=>	array( 'DATE' ),
										'nullable'		=>	true,
										'underflow'		=>	"TO_DATE( '2012-10-21', 'YYYY-MM-DD')",
										'test_value'	=>	"TO_DATE( '2012-10-23', 'YYYY-MM-DD')",	),
			'STATUS'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	11,
										'max_length'	=>	15,
										'nullable'		=>	false,
										'legal_values'	=> array( "complete", "in progress" ),
										'illegal_values' => array( "Complete", "COMPLETE", "foobar", "blurk", "   " ),
										'test_value'	=>	"complete",	),
			'SUPPLIER_ID'	=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"1",	),
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
		return array( 'ORDER_NUM' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'STAFF' => array( 'STAFF_ID' ),
			'SUPPLIER' => array( 'SUPPLIER_ID' ),
		);
	}
	
	
	public function getUniqueColumnList()
	{
		return array();
	}
}
?>
