<?php
require_once "Feature/UsedCars_Test_Feature.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class UsedCars_Test_Feature_data extends UsedCars_Test_Feature
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Feature/UsedCars_Fixture_Feature.xml' );
	}
	
	
	protected function willLoadFixture()
	{
	    return true;
	}
}
?>
