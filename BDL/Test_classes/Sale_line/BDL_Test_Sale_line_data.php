<?php
require_once "BDL_Test_Sale_line.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class BDL_Test_Sale_line_data extends BDL_Test_Sale_line
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Sale_line/BDL_Fixture_Sale_line.xml' );
	}
	
	
	/**
	 *	Return whether or not the fixture should be loaded.
	 *
	 *	@return boolean
	 */
	protected function willLoadFixture()
	{
	    return true;
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
	 */
	public function testColumnUnderflowValue( $columnName, $underflowValue )
	{
   		$this->assertColumnUnderflowValue( $columnName, $underflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnOverflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 */
	public function testColumnOverflowValueExplicit( $columnName, $overflowValue )
	{
   		$this->assertColumnOverflowValueExplicit( $columnName, $overflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnOverflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage length exceeded
	 */
	public function testColumnOverflowValueImplicit( $columnName, $overflowValue )
	{
   		$this->assertColumnOverflowValueImplicit( $columnName, $overflowValue );
	}
}
?>
