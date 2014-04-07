<?php
require_once "Schema.php";

abstract class UsedCars_Test_Car extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'CAR';
    }
    
    
    public function getColumnList()
    {
        return array(
            'VIN'               =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  17,
                                            'max_length'    =>  17,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '7AT0DH1EX09123456', ),
            'REGISTRATION'      =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  6,
                                            'max_length'    =>  6,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'ABC123',   ),
            'MAKE'              =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  20,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Toyota',   ),
            'MODEL'             =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  30,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Camry',    ),
            'YEAR'              =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                            'min_length'    =>  4,
                                            'max_length'    =>  4,
                                            'nullable'      =>  false,
                                            'underflow'     =>  1994,
                                            'legal_values'  =>  array( 1995, 2014 ),
                                            'illegal_values'=>  array( 1980 ),
                                            'test_value'    =>  '2012',   ),
            'COLOUR'            =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  20,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Purple',   ),
            'ODOMETER'          =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'DECIMAL' ),
                                            'min_length'    =>  7,
                                            'decimals'      =>  1,
                                            'underflow'     =>  -0.1,
                                            'legal_values'  =>  array( 0, 1234.5, 88765, 210000.4, 999999.9 ),
                                            'illegal_values'=>  array( -10 ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  '80000',   ),
            'FIRST_REGISTERED'  =>  array(  'generic_type'  =>  'DATE',
                                            'sql_type'      =>  array( 'DATE' ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  "TO_DATE( '2014-03-28', 'YYYY-MM-DD' )" ),
            'LAST_SERVICED'     =>  array(  'generic_type'  =>  'DATE',
                                            'sql_type'      =>  array( 'DATE' ),
                                            'nullable'      =>  true,
                                            'underflow'     =>  "TO_DATE( '2014-03-27', 'YYYY-MM-DD' )",
                                            'legal_values'  =>  array( "TO_DATE( '2014-03-29', 'YYYY-MM-DD' )",  "TO_DATE( '2014-04-01', 'YYYY-MM-DD' )" ),
                                            'illegal_values'=>  array( "TO_DATE( '2013-03-29', 'YYYY-MM-DD' )",  "TO_DATE( '1995-04-01', 'YYYY-MM-DD' )" ),
                                            'test_value'    =>  "TO_DATE( '2014-03-28', 'YYYY-MM-DD' )" ),
            'PRICE'             =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  6,
                                            'decimals'      =>  0,
                                            'underflow'     =>  -1,
                                            'legal_values'  =>  array( 0, 1234, 88765, 210000, 999999 ),
                                            'illegal_values'=>  array( -100 ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  '25000',   ),
            'FLAT_RATE'         =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                            'min_length'    =>  4,
                                            'decimals'      =>  0,
                                            'underflow'     =>  0,
                                            'legal_values'  =>  array( 1, 10, 100, 1000 ),
                                            'illegal_values'=>  array( -100, -1 ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  '150',   ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'VIN' );
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
