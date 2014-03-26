<?php
require_once "Schema.php";

abstract class UsedCars_Test_Sales extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'SALES';
    }
    
    
    public function getColumnList()
    {
        return array(
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'STAFF_ID'          =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                            'min_length'    =>  4,
                                            'max_length'    =>  4,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'aliases'       =>  array( 'SALES_ID', 'SALES_STAFF_ID', 'SALESREP_ID' ),
                                            'test_value'    =>  '4571',  ), // Has to be an unused Person_ID in the fixture because of the FK.
            'ON_COMMISSION'     =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  1,
                                            'nullable'      =>  false,
                                            'default'       =>  FALSE_VALUE,
                                            'legal_values'  =>  array( TRUE_VALUE, FALSE_VALUE ),
//                                              'f', 'F', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', '0',
//                                              't', 'T', 'true', 'True', 'TRUE', 'y', 'Y', 'yes', 'Yes', 'YES', '1',   ),
                                            'illegal_values'=>  array( ' ', 'X', '9', '@' ),
                                            'test_value'    =>  TRUE_VALUE,    ),
            'COMMISSION_RATE'   =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'DECIMAL' ),
                                            'min_length'    =>  2,
                                            'max_length'    =>  3,
                                            'decimals'      =>  2,
                                            'underflow'     =>  -0.01,
                                            'overflow'      =>  0.31,
                                            'legal_values'  =>  array( 0, 0.1, 0.25, 0.3 ),
                                            'illegal_values'=>  array( -9.99, -1, 1.54, 9.99 ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  0.2,    ),
            'GROSS_EARNINGS'    =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'DECIMAL' ),
                                            'min_length'    =>  8,
                                            'decimals'      =>  2,
                                            'underflow'     =>  -0.01,
                                            'overflow'     =>  1000000,
                                            'legal_values'  =>  array( 0, 1, 10.27, 100.38, 1000.49, 100000.51, 999999.99 ),
                                            'illegal_values'=>  array( -10, 2000000 ),
                                            'nullable'      =>  false,
                                            'test_value'    =>  12345.67,    ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'STAFF_ID' );
    }
    
    
    public function getFKColumnList()
    {
        return array( 'STAFF' => array( 'STAFF_ID' ) );
    }
    
    
    public function getUniqueColumnList()
    {
        return array();
    }
}
?>
