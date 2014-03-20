<?php
require_once "Schema.php";

abstract class UsedCars_Test_Service extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'SERVICE';
    }
    
    
    public function getColumnList()
    {
        return array(
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'STAFF_ID'          =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                            'min_length'    =>  4,
                                            'max_length'    =>  4,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '4571',  ), // Has to be an unused Person_ID in the fixture because of the FK.
            'HOURLY_RATE'       =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'DECIMAL' ),
                                            'min_length'    =>  5,
                                            'max_length'    =>  5,
                                            'decimals'      =>  2,
                                            'nullable'      =>  false,
                                            'underflow'     =>  13.49,
                                            'overflow'      =>  1000,
                                            'legal_values'  =>  array( 13.50, 20, 135.95, 999.99 ),
                                            'test_value'    =>  '25.50',  ),
            'TOTAL_HOURS'       =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'DECIMAL' ),
                                            'min_length'    =>  6,
                                            'max_length'    =>  6,
                                            'decimals'      =>  2,
                                            'nullable'      =>  false,
                                            'default'       =>  0,
                                            'underflow'     =>  -0.01,
                                            'overflow'      =>  4500.01,
                                            'legal_values'  =>  array( 0, 10.25, 100.5, 1000.75, 4500 ),
                                            'illegal_values'=>  array( 10.24, 100.55, 1000.76, 48.64 ),
                                            'test_value'    =>  '500.5',  ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'STAFF_ID' );
    }
    
    
    public function getFKColumnList()
    {
        return array(
            'STAFF' => array( 'STAFF_ID' ),
        );
    }
    
    
    public function getUniqueColumnList()
    {
        return array();
    }
}
?>
