<?php
require_once "Warranty/UsedCars_Test_Warranty.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class UsedCars_Test_Warranty_data extends UsedCars_Test_Warranty
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Warranty/UsedCars_Fixture_Warranty.xml' );
	}
	
	
	protected function willLoadFixture()
	{
	    return true;
	}
}
?>
