<?php
require_once "Schedule/STINK_student_records_Test_Schedule.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class STINK_student_records_Test_Schedule_data extends STINK_student_records_Test_Schedule
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Schedule/STINK_student_records_Fixture_Schedule.xml' );
	}
}
?>
