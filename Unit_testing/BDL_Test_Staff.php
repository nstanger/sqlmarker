<?php
require_once "Schema.php";

abstract class BDL_Test_Staff extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'STAFF';
	}
	
	
	public function getColumnList()
	{
		return array(
			'STAFF_ID'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"123456",	),
			'SURNAME'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	50,
										'max_length'	=>	50,
										'nullable'		=>	false,
										'test_value'	=>	"Bloggs",	),
			'FIRSTNAMES'	=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	50,
										'max_length'	=>	50,
										'nullable'		=>	false,
										'test_value'	=>	"Harold",	),
			'PHONE'			=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'CHAR', 'VARCHAR' ),
										'min_length'	=>	11,
										'max_length'	=>	20,
										'nullable'		=>	false,
											'legal_values'	=>	array( "034511010", "2718391780", "+2496391734", "92819209365" ),
										'test_value'	=>	"16139981234",	),
			'ADDRESS'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	150,
										'max_length'	=>	150,
										'nullable'		=>	false,
										'test_value'	=>	"12 Western Way",	),
			'DEPARTMENT'	=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	18,
										'max_length'	=>	25,
										'nullable'		=>	false,
										'legal_values'	=> array(
											"Central Management", "Sales & Marketing", "Personnel",
											"Manufacturing", "Inventory", "Accounts"	),
										'illegal_values' => array(	"personnel", "ACCOUNTS", "foobar", "blurk", "   "	),
										'test_value'	=>	"Accounts",	),
			'POSITION'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	20,
										'max_length'	=>	30,
										'nullable'		=>	false,
										'legal_values'	=> array(
											"CEO", "CTO", "CFO", "CIO", "Director", "President", "Vice-President",
											"Manager", "Personal Assistant", "Secretary", "Technician", "Researcher",
											"Designer", "Assembler", "Programmer", "Contractor", "Sales Representative",
											"Accountant", "Inventory", "Assistant"	),
										'illegal_values' => array(	"ceo", "ASSISTANT", "barfoo", "blargh", "   "	),
										'test_value'	=>	"CEO",	),
			'SALARY'		=>	array(	'generic_type'	=>	'NUMBER',
										'sql_type'		=>	array( 'NUMBER', 'DECIMAL' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	2,
										'underflow'		=>	"999.99",
										'overflow'		=>	"100000",
										'nullable'		=>	false,
										'test_value'	=>	"50000",	),
			'COMMENTS'		=>	array(	'generic_type'	=>	'TEXT',
										'sql_type'		=>	array( 'VARCHAR2', 'CLOB' ),
										'min_length'	=>	500,
										'nullable'		=>	true,
										'test_value'	=>	"Blah blah blah",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'STAFF_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
}
?>
