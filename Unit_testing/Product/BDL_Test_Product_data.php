<?php
require_once "BDL_Test_Product.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class BDL_Test_Product_data extends BDL_Test_Product
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
		return $this->createXMLDataSet("BDL_Fixture_Product.xml");
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
	
	
	/**
	 *	@dataProvider provideColumnOverflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnOverflowValueExplicit( $columnName, $overflowValue )
	{
   		$this->assertColumnOverflowValueExplicit( $columnName, $overflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnOverflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage length exceeded
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnOverflowValueImplicit( $columnName, $overflowValue )
	{
   		$this->assertColumnOverflowValueImplicit( $columnName, $overflowValue );
	}
}
?>
