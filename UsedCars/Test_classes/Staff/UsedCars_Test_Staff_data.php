<?php
require_once "Staff/UsedCars_Test_Staff.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class UsedCars_Test_Staff_data extends UsedCars_Test_Staff
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Staff/UsedCars_Fixture_Staff.xml' );
	}
	
	
	protected function willLoadFixture()
	{
	    return true;
	}
}
?>
