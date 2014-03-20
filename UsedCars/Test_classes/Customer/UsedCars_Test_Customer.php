<?php
require_once "Schema.php";

abstract class UsedCars_Test_Customer extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'CUSTOMER';
    }
    
    
    public function getColumnList()
    {
        return array(
            'CUSTOMER_ID'       =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  6,
                                            'max_length'    =>  6,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '234571',  ),
            'FIRSTNAME'         =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  50,
                                            'max_length'    =>  50,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Sarah',   ),
            'LASTNAME'          =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  50,
                                            'max_length'    =>  50,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Smith',    ),
            'ADDRESS'           =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  150,
                                            'max_length'    =>  150,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '123 George Street, Dunedin',   ),
            'PHONE'             =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  11,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '02144679437',  ),
            'EMAIL'             =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  50,
                                            'max_length'    =>  50,
                                            'nullable'      =>  true,
                                            'legal_values'  =>  array(
                                                'email@example.com', 'EMAIL@EXAMPLE.COM', 'email@example.co.nz',
                                                'test.email@some-place.co', 'email@thing.example.co.uk', 'email_address@example.com' ),
                                            'illegal_values'=>  array(
                                                '@example.com', 'email@', 'email@@example.com', 'email.com', 'email@com',
                                                'email@example..com' ),
                                            'test_value'    =>  'test.email@example.com',   ),
            'CREDIT_RATING'     =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  1,
                                            'max_length'    =>  1,
                                            'nullable'      =>  true,
                                            'legal_values'  =>  array( 'A', 'B', 'C', 'D' ),
                                            'illegal_values'=>  array( ' ', 'X', '9', '@', 'a', 'b', 'c', 'd' ),
                                            'test_value'    =>  'A',   ),
            'COMMENTS'          =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR', 'CLOB' ),
                                            'min_length'    =>  500,
                                            'nullable'      =>  true,
                                            'test_value'    =>  'Blah blah blah',   ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'CUSTOMER_ID' );
    }
    
    
    public function getFKColumnList()
    {
        return array();
    }
    
    
    public function getUniqueColumnList()
    {
        return array();
    }
}
?>
