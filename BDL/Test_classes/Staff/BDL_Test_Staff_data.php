<?php
require_once "Staff/BDL_Test_Staff.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class BDL_Test_Staff_data extends BDL_Test_Staff
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Staff/BDL_Fixture_Staff.xml' );
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
	 *	@dataProvider provideColumnIllegalValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 */
	public function testColumnIllegalValueExplicit( $columnName, $illegalValue )
	{
   		$this->assertColumnIllegalValueExplicit( $columnName, $illegalValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnIllegalValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage length exceeded
	 */
	public function testColumnIllegalValueImplicit( $columnName, $illegalValue )
	{
   		$this->assertColumnIllegalValueImplicit( $columnName, $illegalValue );
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
