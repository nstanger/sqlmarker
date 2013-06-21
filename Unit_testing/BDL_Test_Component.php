<?php
require_once "Schema.php";

class BDL_Test_Component extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'COMPONENT';
	}
	
	
	public function getColumnList()
	{
		return array(
			'COMPONENT_CODE'	=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	8,
											'max_length'	=>	8,
											'decimals'		=>	0,
// 											'underflow'		=>	-1,
											'nullable'		=>	false,
											'test_value'	=>	"87654321",	),
			'SUPPLIERS_CODE'	=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	25,
											'max_length'	=>	25,
											'nullable'		=>	false,
											'test_value'	=>	"'B87654321'",	),
			'DESCRIPTION'		=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	100,
											'max_length'	=>	100,
											'nullable'		=>	false,
											'test_value'	=>	"'Some component'",	),
			'STOCK_COUNT'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	10000000,
											'nullable'		=>	false,
											'test_value'	=>	"456",	),
			'SUPPLIER_ID'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	0,
											'nullable'		=>	false,
											'test_value'	=>	"1",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'COMPONENT_CODE', 'SUPPLIERS_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array( 'SUPPLIER' => array ( 'SUPPLIER_ID' ) );
	}
	
	
	/**
	 *	Return fixture data set for current database connection.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_DataSet_IDataSet
	 *	@todo Parameterise the fixture filename.
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet("BDL_Fixture_Component.xml");
	}
	
	
	public function testTableExists()
	{
		$this->assertTableExists();
	}
	
	
	/**
	 *	@dataProvider provideColumnNames
	 */
	public function testColumnExists( $columnName )
	{
   		$this->assertColumnExists( $columnName );
	}
	
	
	/**
	 *	@dataProvider provideColumnTypes
	 */
	public function testColumnDataType( $columnName, $columnTypeList )
	{
   		$this->assertColumnDataType( $columnName, $columnTypeList );
	}
	
	
	/**
	 *	@dataProvider provideColumnLengths
	 */
	public function testColumnLength( $columnName, $columnLengthList )
	{
   		$this->assertColumnLength( $columnName, $columnLengthList );
	}
	
	
	/**
	 *	@dataProvider provideColumnLegalValues
	 */
	public function testColumnLegalValue( $columnName, $legalValue )
	{
   		$this->assertColumnLegalValue( $columnName, $legalValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnUnderflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnUnderflowValue( $columnName, $underflowValue )
	{
   		$this->assertColumnUnderflowValue( $columnName, $underflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnOverflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnOverflowValue( $columnName, $overflowValue )
	{
   		$this->assertColumnOverflowValue( $columnName, $overflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnNullabilities
	 */
	public function testColumnNullability( $columnName, $columnNullability )
	{
   		$this->assertColumnNullability( $columnName, $columnNullability );
	}
	
	
	public function testPKExists()
	{
		return $this->assertPKExists();
	}
	
	
	/**
	 *	@depends testPKExists
	 */
	public function testPKColumns( $constraintName )
	{
		$this->assertPKColumns( $constraintName );
	}
	
	
	/**
	 *	@dataProvider provideFKReferencedTables
	 */
	public function testFKsExist( $referencedTableName )
	{
		return $this->assertFKsExist( $referencedTableName );
	}
	
	
	/**
	 *	@dataProvider provideFKReferencedTables
	 */
	public function testFKColumns( $referencedTableName )
	{
		$this->assertFKColumns( $referencedTableName );
	}
	
	
	/**
	 *	@dataProvider provideConstraintNames
	 */
	public function testConstraintsNamed( $constraintName, $constraintType )
	{
		$this->assertConstraintNamed( $constraintName, $constraintType );
	}
}
?>
