<?php
require_once "Schema.php";

abstract class UsedCars_Test_Staff extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
    public function getTableName()
    {
        return 'STAFF';
    }
    
    
    public function getColumnList()
    {
        return array(
            'STAFF_ID'          =>  array(  'generic_type'  =>  'NUMBER',
                                            'sql_type'      =>  array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
                                            'min_length'    =>  4,
                                            'max_length'    =>  4,
                                            'decimals'      =>  0,
                                            'nullable'      =>  false,
                                            'test_value'    =>  '8462',  ),
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
            'DATE_HIRED'        =>  array(  'generic_type'  =>  'DATE',
                                            'sql_type'      =>  array( 'DATE' ),
                                            'nullable'      =>  false,
                                            'default'       =>  'SYSDATE',
                                            'test_value'    =>  "TO_DATE( '2012-03-28', 'YYYY-MM-DD' )" ),
            'DATE_OF_BIRTH'     =>  array(  'generic_type'  =>  'DATE',
                                            'sql_type'      =>  array( 'DATE' ),
                                            'nullable'      =>  false,
                                            'underflow'     => "SYSDATE - TO_YMINTERVAL( '18-0' ) + 1",
                                            'test_value'    =>  "TO_DATE( '2012-03-28', 'YYYY-MM-DD' )" ),
        );
    }
    
    
    public function getPKColumnList()
    {
        return array( 'STAFF_ID' );
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
