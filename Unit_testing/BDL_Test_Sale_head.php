<?php
require_once "Schema.php";

class BDL_Test_Sale_head extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'SALE_HEAD';
	}
	
	
	public function getColumnList()
	{
		return array(
			'SALE_NUM'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	10,
										'max_length'	=>	10,
										'decimals'		=>	0,
// 										'underflow'		=>	-1,
										'nullable'		=>	false,
										'test_value'	=>	"8765432109",	),
			'SALE_DATE'		=>	array(	'type'			=>	array( 'DATE' ),
										'nullable'		=>	false,
										'overflow'		=>	"SYSDATE + 1",
										'test_value'	=>	"TO_DATE( '2012-09-22', 'YYYY-MM-DD')",	),
			'STATUS'		=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	11,
										'max_length'	=>	15,
										'nullable'		=>	false,
										'legal_values' => array(
											"'pending'", "'in progress'", "'cancelled'", "'backordered'", "'shipped'"	),
										'illegal_values' => array(
											"'foobar'", "'blurk'", "'   '"	),
										'test_value'	=>	"'pending'",	),
			'CUSTOMER_ID'	=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"303",	),
			'STAFF_ID'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
										'nullable'		=>	false,
										'test_value'	=>	"326",	),
			'COMMENTS'		=>	array(	'type'			=>	array( 'VARCHAR2', 'CLOB' ),
										'min_length'	=>	1000,
										'max_length'	=>	4000,
										'nullable'		=>	true,
										'test_value'	=>	"'Blah blah blah'",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'SALE_NUM' );
	}
	
	
	public function getFKColumnList()
	{
		return array(
			'STAFF' => array( 'STAFF_ID' ),
			'CUSTOMER' => array( 'CUSTOMER_ID' ),
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
		return $this->createXMLDataSet("BDL_Fixture_Sale_head.xml");
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
	 *	@dataProvider provideColumnIllegalValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnIllegalValue( $columnName, $illegalValue )
	{
   		$this->assertColumnIllegalValue( $columnName, $illegalValue );
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
