<?php
require_once "BDL_Test_Sale_line.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class BDL_Test_Sale_line_structure extends BDL_Test_Sale_line
{
	/**
	 *	Return fixture data set for current database connection.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_DataSet_IDataSet
	 *	@todo Parameterise the fixture filename.
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Sale_line/BDL_Fixture_Sale_line_Empty.xml' );
	}
	
	
	/**
	 *	Return whether or not the fixture should be loaded.
	 *
	 *	@return boolean
	 */
	protected function willLoadFixture()
	{
	    return false;
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
	public function testColumnLength( $columnName, $columnType, $minLength, $maxLength, $numDecimals )
	{
   		$this->assertColumnLength( $columnName, $columnType, $minLength, $maxLength, $numDecimals );
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
