<?php
require_once "BDL_Test_Component.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class BDL_Test_Component_data extends BDL_Test_Component
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Component/BDL_Fixture_Component.xml' );
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
