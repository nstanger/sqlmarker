<?php

require_once "PHPUnit/Autoload.php";
require_once 'TestListener/HTMLTestListener.php';
require_once 'TestListener/TextTestListener.php';
require_once 'Reporter/TextReporter.php';
require_once 'Reporter/HTMLReporter.php';
require_once "Staff/BDL_Test_Staff_structure.php";
require_once "Staff/BDL_Test_Staff_data.php";

$outputMode = 'TEXT';
$verbosity = 2;

PHPUnit_Framework_Error_Warning::$enabled = FALSE;
PHPUnit_Framework_Error_Notice::$enabled = FALSE;

switch ( $outputMode )
{
	case 'HTML':
		$reporter = new HTMLReporter( $verbosity );
		$listener = new HTMLTestListener;
		break;
	case 'TEXT':
		$reporter = new TextReporter( $verbosity );
		$listener = new TextTestListener;
		break;
}

$result = new PHPUnit_Framework_TestResult;
$result->addListener( $listener );

/*
	Hack! There's no easy way to inject the connection details into the test classes, as they're being implicitly created by the test suite below. To work around this, the connection details are stored as private static members in PHPUnit_Extensions_Database_TestCase_CreateTable, with corresponding public get/set static methods. Set them once at the start, and they'll stay set for the entire test run. Yay!
*/
PHPUnit_Extensions_Database_TestCase_CreateTable::setServiceID( "isorcl-400" );
PHPUnit_Extensions_Database_TestCase_CreateTable::setUsername( "stani07p" );
PHPUnit_Extensions_Database_TestCase_CreateTable::setPassword( "b1ggles" );
PHPUnit_Extensions_Database_TestCase_CreateTable::setReporter( $reporter );

$suite = new PHPUnit_Framework_TestSuite( 'BDL_Test_Staff_structure' );

// Critical to data testing.
// TODO: is $testResult needed anymore?
$testResult = $suite->run( $result, '/testTableExists/' );
$structurePassed = $listener->wasSuccessful( 'testTableExists' );
if ( $structurePassed )
{
	$reporter->report( Reporter::STATUS_PASS, 'Table exists.', null );
	
	// Critical to data testing.
	$testResult = $suite->run( $result, '/testColumnExists/' );
	$structurePassed = $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnExists' );
	if ( $structurePassed )
	{
		$reporter->report( Reporter::STATUS_PASS, 'Table contains all the expected columns.', null );
	
		$testResult = $suite->run( $result, '/testColumnDataType/' );
		$structurePassed = $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnDataType' );
		if ( $structurePassed )
		{
			$reporter->report( Reporter::STATUS_PASS, 'All columns have the expected data types.', null );
		
			$testResult = $suite->run( $result, '/testColumnLength/' );
			$structurePassed = $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnLength' );
			if ( $structurePassed )
			{
				$reporter->report( Reporter::STATUS_PASS, 'All columns have the expected lengths.', null );
			}
			else
			{
				$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns %s an unexpected column length.',
					array(
						$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnLength' ),
						$listener->countPasses( 'BDL_Test_Staff_structure::testColumnLength' ),
						Reporter::pluralise( 'BDL_Test_Staff_structure::testColumnLength' )
					) ) ;
			}
		}
		else
		{
			$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns %s unexpected data types.',
				array(
					$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnDataType' ),
					$listener->countPasses( 'BDL_Test_Staff_structure::testColumnDataType' ),
					Reporter::pluralise( $listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnDataType' ), "has", "have" )
				) ) ;
			$reporter->report( Reporter::STATUS_SKIPPED, 'column length tests, as the data types do not match what was expected.', null );
		}
		
		$testResult = $suite->run( $result, '/testColumnNullability/' );
		if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnNullability' ) )
		{
			$reporter->report( Reporter::STATUS_PASS, 'All columns have the expected nullability.', null );
		}
		else
		{
			$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns %s unexpected nullability.',
				array(
					$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnNullability' ),
					$listener->countPasses( 'BDL_Test_Staff_structure::testColumnNullability' ),
					Reporter::pluralise( $listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnNullability' ), "has", "have" )
				) ) ;
		}
	}
	else
	{
		$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d expected columns %s either missing or misnamed.',
			array(
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnExists' ),
				$listener->countPasses( 'BDL_Test_Staff_structure::testColumnExists' ),
				Reporter::pluralise( $listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnExists' ), 'is', 'are' )
			) ) ;
		$reporter->report( Reporter::STATUS_SKIPPED, 'data type, length and nullability tests as they will include spurious errors.', null );
	}
	
	// Not critical to data testing. Need to run both tests in one pass as the columns test depends on the existence test.
	$testResult = $suite->run( $result, '/testPK.*/' );
	if ( $listener->wasSuccessful( 'testPKExists' ) )
	{
		$reporter->report( Reporter::STATUS_PASS, 'Primary key exists.', null );
	}
	else
	{
		$reporter->report( Reporter::STATUS_FAILURE, 'Primary key missing.', null );
	}
	if ( $listener->wasSuccessful( 'testPKColumns' ) )
	{
		$reporter->report( Reporter::STATUS_PASS, 'Primary key includes (only) the expected columns.', null );
	}
	else
	{
		$reporter->report( Reporter::STATUS_FAILURE, 'Primary key does not include (only) the expected columns.', null );
	}
	
	// Not critical to data testing.
	$testResult = $suite->run( $result, '/testConstraintsNamed/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testConstraintsNamed' ) )
	{
		$reporter->report( Reporter::STATUS_PASS, 'All constraints that should be are explicitly named.', null );
	}
	else
	{
		$reporter->report( Reporter::STATUS_FAILURE, 'Some constraints are not explicitly named that should be.', null );
	}
}

/*
	If the table or required columns are missing or misnamed, we need to skip the data testing entirely, as the INSERTs will just error out. We can't incorporate this into the if above, because we're using a completely different test suite.
*/
if ( $structurePassed )
{
	$suite = new PHPUnit_Framework_TestSuite( 'BDL_Test_Staff_data' );
	
	$testResult = $suite->run( $result, '/testColumnLegalValue/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_data::testColumnLegalValue' ) )
	{
		$reporter->report( Reporter::STATUS_PASS, 'All %d legal values tested were accepted.', 
			array( $listener->countTests( 'BDL_Test_Staff_data::testColumnLegalValue' ) ) );
	}

	$testResult = $suite->run( $result, '/testColumnIllegalValueExplicit/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ) )
	{
		$reporter->report( Reporter::STATUS_PASS, 'All %d illegal values tested were rejected by a CHECK constraint.', 
			array( $listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ) ) );
	}
	else
	{
		$checkFails = $listener->countFails( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' );
		$reporter->report( Reporter::STATUS_ALERT, '%d of %d illegal values tested %s not rejected by a CHECK constraint.',
			array(
				$checkFails,
				$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ),
				Reporter::pluralise( $checkFails, 'was', 'were' )
			) ) ;
		$reporter->report( Reporter::STATUS_ALERT, 'Checking values against column length...', null );
		
		/*
			Unfortunately, we can't test just the columns that failed the CHECK test. The failed TestCases are in $testResult->failures(), but we need the column name, which is hidden away in the private $data member of TestCase. We therefore have to test all the illegal values again to see if they're larger than the column. We then make the big assumption that if all the values that failed the CHECK test did so because they exceeded the column length. If this is correct, then the number of CHECK fails will equal the number of column length passes. If not, then something more serious has probably gone wrong!
		*/
		$testResult = $suite->run( $result, '/testColumnIllegalValueImplicit/' );
		$implicitPasses = $listener->countPasses( 'BDL_Test_Staff_data::testColumnIllegalValueImplicit' );
		$reporter->report( Reporter::STATUS_PASSED, '%d of %d illegal values tested %s rejected by exceeding the column length.',
			array(
				$implicitPasses,
				$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueImplicit' ),
				Reporter::pluralise( $implicitPasses, 'was', 'were' )
			) ) ;
		
		// Any leftovers?
		if ( $implicitPasses != $checkFails )
		{
			/*
				$checkFails must by definition be >= $implicitPasses, as a "length exceeded" will /always/ fail the CHECK test, and a "check constraint" will /always/ fail the column length test. The two values will only differ when there are other exceptions in the mix, which will fail both tests.
				
				For example, suppose that two values fail the CHECK test with "length exceeded", one fails with "foo exception" and the remaining two pass. The first two will pass the column length test, and the remaining three will fail.
			*/
			$reporter->report( Reporter::STATUS_FAILURE, '%d illegal values %s rejected in both tests—check for something unusual.',
				array(
					$checkFails - $implicitPasses,
					Reporter::pluralise( $checkFails - $implicitPasses, 'was', 'were' )
				) ) ;
		}
		else
		{
			$reporter->report( Reporter::STATUS_NOTE, 'This is OK, but not necessarily safe, as the column length may change in future.', null );
		}
	}
}
else
{
	$reporter->report( Reporter::STATUS_SKIPPED, 'data tests, as failures in the structure testing mean that they may not work.', null );
}
	
?>
