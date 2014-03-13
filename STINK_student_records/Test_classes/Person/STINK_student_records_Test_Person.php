<?php
require_once "Schema.php";

abstract class STINK_student_records_Test_Person extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'PERSON';
	}
	
	
	public function getColumnList()
	{
		return array(
			'PERSON_ID'			=>	array(	'generic_type'	=>	'NUMBER',
											'sql_type'		=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"8765432",	),
			'SURNAME'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"Smith",	),
			'OTHER_NAMES'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"Sarah Jane",	),
			'CONTACT_PHONE'		=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	11,
											'nullable'		=>	true,
											'test_value'	=>	"02144679437",	),
			'CONTACT_ADDRESS'	=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	200,
											'max_length'	=>	200,
											'nullable'		=>	false,
											'test_value'	=>	"123 George Street, Dunedin",	),
			'EMAIL'				=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'legal_values'	=>	array(
												'email@example.com', 'EMAIL@EXAMPLE.COM', 'email@example.co.nz',
												'test.email@some-place.co', 'email@thing.example.co.uk', 'email_address@example.com' ),
											'illegal_values'=>	array(
												'@example.com', 'email@', 'email@@example.com', 'email.com', 'email@com',
												'email@example..com' ),
											'test_value'	=>	"test.email@example.com",	),
			'USERNAME'			=>	array(	'generic_type'	=>	'TEXT',
											'sql_type'		=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	10,
											'max_length'	=>	10,
											'nullable'		=>	false,
											'legal_values'	=>	array( 'username', 'a_username', 'username12', ),
											'illegal_values'=>	array( 'user name', '!@^%*&^!', ' ' ),
											'test_value'	=>	"smisa861",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'PERSON_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
}
?>
