<?php
require_once "Enrolment/STINK_student_records_Test_Enrolment.php";

/**
 *	@backupGlobals disabled
 *	@backupStaticAttributes disabled
 */
class STINK_student_records_Test_Enrolment_data extends STINK_student_records_Test_Enrolment
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
		return $this->createXMLDataSet( TEST_CLASS_PATH . '/Enrolment/STINK_student_records_Fixture_Enrolment.xml' );
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
