<?php
require_once "Schema.php";

class BDL_Test_Sale_line extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'SALE_LINE';
	}
	
	
	public function getColumnList()
	{
		return array(
			'SALE_NUM'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	10,
										'max_length'	=>	10,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"223",	),
			'PRODUCT_CODE'	=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	8,
										'max_length'	=>	8,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"246",	),
			'QUANTITY'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER', 'SMALLINT' ),
										'min_length'	=>	4,
										'max_length'	=>	4,
										'decimals'		=>	0,
										'underflow'		=>	0,
										'overflow'		=>	10000,
										'nullable'		=>	false,
										'test_value'	=>	"20",	),
			'ACTUAL_PRICE'	=>	array(	'type'			=>	array( 'NUMBER', 'DECIMAL' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	2,
										'underflow'		=>	-0.01,
										'overflow'		=>	100000.00,
										'nullable'		=>	false,
										'test_value'	=>	"24.99",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'SALE_NUM', 'PRODUCT_CODE' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'SALE_HEAD' => array( 'SALE_NUM' ),
			'PRODUCT' => array( 'PRODUCT_CODE' ),
		);
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
		return $this->createXMLDataSet("BDL_Fixture_Sale_line.xml");
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
