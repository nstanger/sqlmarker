<?php
require_once "Schema.php";

abstract class UsedCars_Test_Other extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'OTHER';
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
                                            'test_value'    =>  '4571',  ), // Has to be an unused Person_ID in the fixture because of the FK.
            'SALARY'            =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'DECIMAL' ),
                                            'min_length'    =>  8,
                                            'max_length'    =>  8,
                                            'decimals'      =>  2,
                                            'nullable'      =>  false,
                                            'underflow'     =>  28079.99,
                                            'overflow'      =>  1000000,
                                            'legal_values'  =>  array( 28080, 999999.99 ),
                                            'illegal_values'=>  array( 10000, 2000000 ),
                                            'test_value'    =>  '45000',  ),
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
