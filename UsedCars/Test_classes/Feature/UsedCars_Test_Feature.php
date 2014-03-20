<?php
require_once "Schema.php";

abstract class UsedCars_Test_Feature extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'FEATURE';
    }
    
    
    public function getColumnList()
    {
        return array(
            'FEATURE_CODE'      =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  5,
                                            'max_length'    =>  5,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'AIRCN', ),
            'DESCRIPTION'       =>  array(  'generic_type'  =>  'TEXT',
                                            'sql_type'      =>  array( 'VARCHAR2', 'VARCHAR' ),
                                            'min_length'    =>  100,
                                            'nullable'      =>  false,
                                            'test_value'    =>  'Air conditioning', ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'FEATURE_CODE' );
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
