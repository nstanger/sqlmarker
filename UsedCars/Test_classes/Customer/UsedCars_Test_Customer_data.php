<?php
require_once "Customer/UsedCars_Test_Customer.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class UsedCars_Test_Customer_data extends UsedCars_Test_Customer
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Customer/UsedCars_Fixture_Customer.xml' );
	}
	
	
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
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnIllegalValueExplicit( $columnName, $illegalValue )
	{
   		$this->assertColumnIllegalValueExplicit( $columnName, $illegalValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnIllegalValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage length exceeded
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnIllegalValueImplicit( $columnName, $illegalValue )
	{
   		$this->assertColumnIllegalValueImplicit( $columnName, $illegalValue );
	}
}
?>
