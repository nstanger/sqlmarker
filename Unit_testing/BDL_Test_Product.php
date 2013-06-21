<?php
require_once "Schema.php";

class BDL_Test_Product extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'PRODUCT';
	}
	
	
	public function getColumnList()
	{
		return array(
			'PRODUCT_CODE'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	8,
											'max_length'	=>	8,
											'decimals'		=>	0,
// 											'underflow'		=>	-1,
											'nullable'		=>	false,
											'test_value'	=>	"87654321",	),
			'DESCRIPTION'		=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
											'min_length'	=>	50,
											'max_length'	=>	50,
											'nullable'		=>	false,
											'test_value'	=>	"'Some product'",	),
			'STOCK_COUNT'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	100000,
											'nullable'		=>	false,
											'test_value'	=>	"456",	),
			'RESTOCK_LEVEL'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	100000,
											'nullable'		=>	true,
											'test_value'	=>	"654",	),
			'MINIMUM_LEVEL'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
											'min_length'	=>	5,
											'max_length'	=>	5,
											'decimals'		=>	0,
											'underflow'		=>	-1,
											'overflow'		=>	100000,
											'nullable'		=>	true,
											'test_value'	=>	"456",	),
			'LIST_PRICE'		=>	array(	'type'			=>	array( 'NUMBER', 'DECIMAL' ),
											'min_length'	=>	7,
											'max_length'	=>	7,
											'decimals'		=>	2,
											'underflow'		=>	-0.01,
											'overflow'		=>	100000.00,
											'nullable'		=>	false,
											'test_value'	=>	"123.99",	),
			'ASSEMBLY_MANUAL'	=>	array(	'type'			=>	array( 'BLOB' ),
											'nullable'		=>	true,
											'test_value'	=>	"NULL",	),
			'ASSEMBLY_PROGRAM'	=>	array(	'type'			=>	array( 'BLOB' ),
											'nullable'		=>	true,
											'test_value'	=>	"NULL",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'PRODUCT_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
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
		return $this->createXMLDataSet("BDL_Fixture_Product.xml");
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
	 *	@dataProvider provideConstraintNames
	 */
	public function testConstraintsNamed( $constraintName, $constraintType )
	{
		$this->assertConstraintNamed( $constraintName, $constraintType );
	}
}
?>
