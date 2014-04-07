<?php
require_once "Other/UsedCars_Test_Other.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class UsedCars_Test_Other_data extends UsedCars_Test_Other
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Other/UsedCars_Fixture_Other.xml' );
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
}
?>
