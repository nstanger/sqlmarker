<?php
require_once "Schema.php";

abstract class UsedCars_Test_Car_Feature extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'CAR_FEATURE';
    }
    
    
    public function getColumnList()
    {
        return array(
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'VIN'               =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  17,
                                            'max_length'    =>  17,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '7A8DH1E0701123456', ),
            // No need to test legal values because of the FK. If the FK is missing it's broken anyway!
            'FEATURE_CODE'      =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  5,
                                            'max_length'    =>  5,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'SABAG',  ),  );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'VIN', 'FEATURE_CODE' );
    }
    
    
    public function getFKColumnList()
    {
        return array(
            'CAR'       =>  array( 'VIN' ),
            'FEATURE'   =>  array( 'FEATURE_CODE' ),
        );
    }
    
    
    public function getUniqueColumnList()
    {
        return array();
    }
}
?>
