<?php
require_once "Schema.php";

abstract class UsedCars_Test_Sale extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'SALE';
    }
    
    
    public function getColumnList()
    {
        return array(
            'SALE_ID'           =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  8,
                                            'max_length'    =>  8,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '12345670',   ),
            'SALE_DATE'         =>  array(  'generic_type'  =>  'DATE',
                                            'sql_type'      =>  array( 'DATE' ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  "TO_DATE( '2012-03-28', 'YYYY-MM-DD' )" ),
            'DETAILS'           =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR', 'CLOB' ),
                                            'min_length'    =>  500,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Blah blah blah',   ),
            'AMOUNT'             =>  array( 'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  6,
                                            'nullable'      =>  false,
                                            'decimals'      =>  0,
                                            'underflow'     =>  -1,
                                            'legal_values'  =>  array( 0, 1234, 88765, 210000, 999999 ),
                                            'illegal_values'=>  array( -100 ),
                                            'test_value'    =>  '25995',   ),
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'VIN'               =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  17,
                                            'max_length'    =>  17,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '7A8DH1E0701341652', ),
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'CUSTOMER_ID'       =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  6,
                                            'max_length'    =>  6,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '234571',  ),
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'SALESREP_ID'       =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  4,
                                            'max_length'    =>  4,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'aliases'       =>  array( 'STAFF_ID', 'SALES_ID', 'SALES_STAFF_ID' ),
                                            'test_value'    =>  '4567',  ),
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'W_CODE'            =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  1,
                                            'max_length'    =>  1,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'A',   ),
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'TRADEIN_ID'        =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  8,
                                            'max_length'    =>  8,
                                            'decimals'      =>  0,
                                            'nullable'      =>  true,
                                            'aliases'       =>  array( 'PURCHASE_ID' ),
                                            'test_value'    =>  '12345678',   ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'SALE_ID' );
    }
    
    
    public function getFKColumnList()
    {
        return array(
            'CAR'       =>  array( 'VIN' ),
            'CUSTOMER'  =>  array( 'CUSTOMER_ID' ),
            'SALES'     =>  array( 'SALESREP_ID' ),
            'WARRANTY'  =>  array( 'W_CODE' ),
            'PURCHASE'  =>  array( 'TRADEIN_ID' ),
        );
    }
    
    
    public function getUniqueColumnList()
    {
        return array();
    }
}
?>
