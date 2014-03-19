<?php
require_once "Car_Feature/UsedCars_Test_Car_Feature.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class UsedCars_Test_Car_Feature_data extends UsedCars_Test_Car_Feature
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Car_Feature/UsedCars_Fixture_Car_Feature.xml' );
	}
	
	
	protected function willLoadFixture()
	{
	    return true;
	}
}
?>
