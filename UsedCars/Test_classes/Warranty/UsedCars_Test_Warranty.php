<?php
require_once "Schema.php";

abstract class UsedCars_Test_Warranty extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'WARRANTY';
    }
    
    
    public function getColumnList()
    {
        return array(
            'W_CODE'        =>  array(  'generic_type'  =>  'TEXT',
                                        'sql_type'      =>  array( 'CHAR', 'VARCHAR2', 'VARCHAR' ),
                                        'min_length'    =>  1,
                                        'max_length'    =>  1,
                                        'nullable'      =>  false,
                                        'test_value'    =>  'A',   ),
            'MAX_AGE'       =>  array(  'generic_type'  =>  'NUMBER',
                                        'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                        'min_length'    =>  1,
                                        'max_length'    =>  1,
                                        'nullable'      =>  true,
                                        'test_value'    =>  '4', ),
            'MAX_KM'        =>  array(  'generic_type'  =>  'NUMBER',
                                        'sql_type'      =>  array( 'NUMBER', 'INTEGER' ),
                                        'min_length'    =>  6,
                                        'max_length'    =>  6,
                                        'nullable'      =>  true,
                                        'test_value'    =>  '50000', ),
            'DURATION'      =>  array(  'generic_type'  =>  'NUMBER',
                                        'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                        'min_length'    =>  1,
                                        'max_length'    =>  1,
                                        'nullable'      =>  true,
                                        'test_value'    =>  '3', ),
            'DISTANCE'      =>  array(  'generic_type'  =>  'NUMBER',
                                        'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                        'min_length'    =>  4,
                                        'max_length'    =>  4,
                                        'nullable'      =>  true,
                                        'test_value'    =>  '5000', ),
            'NOTES'         =>  array(  'generic_type'  =>  'TEXT',
                                        'sql_type'      =>  array( 'VARCHAR2' ),
                                        'min_length'    =>  250,
                                        'nullable'      =>  true,
                                        'test_value'    =>  'Category A motor vehicle',   ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'W_CODE' );
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
