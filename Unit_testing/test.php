<?php

require_once 'test_config.php';

require_once "PHPUnit/Autoload.php";
require_once 'TestListener/HTMLTestListener.php';
require_once 'TestListener/TextTestListener.php';
require_once 'Reporter/TextReporter.php';
require_once 'Reporter/HTMLReporter.php';
require_once "Schema.php";

// I don't know that these two settings make any difference, but I'll leave them in for now.
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

// List of tables to be tested. This is used to build the corresponding test class names.
require_once "${scenario}_table_list.php";


/*
	Hack! There's no easy way to create a global object constant, so the global report object is stored as a private static member in PHPUnit_Extensions_Database_TestCase_CreateTable, with corresponding public get/set static methods. Set it once at the start, and it'll stay set for the entire test run. Yay!
*/
PHPUnit_Extensions_Database_TestCase_CreateTable::setReporter( $reporter );

foreach ( $testTables as $table )
{
	$structureTest	= "${scenario}_Test_${table}_structure";
	$dataTest		= "${scenario}_Test_${table}_data";
	
	require_once "${table}/${structureTest}.php";
	require_once "${table}/${dataTest}.php";
	
	$listener->reset();
	$reporter->hr();
	
	$suite = new PHPUnit_Framework_TestSuite( $structureTest );
	
	// Critical to data testing.
	// TODO: is $testResult needed anymore?
	$testResult = $suite->run( $result, '/testTableExists/' );
	$structurePassed = $listener->wasSuccessful( 'testTableExists' );
	if ( $structurePassed )
	{
		$reporter->report( Reporter::STATUS_PASS, 'Table exists.', null );
		
		// Critical to data testing.
		$testResult = $suite->run( $result, '/testColumnExists/' );
		$structurePassed = $listener->wasSuccessful( "${structureTest}::testColumnExists" );
		if ( $structurePassed )
		{
			$reporter->report( Reporter::STATUS_PASS, 'Table contains all the expected columns.', null );
		
			$testResult = $suite->run( $result, '/testColumnDataType/' );
			$structurePassed = $listener->wasSuccessful( "${structureTest}::testColumnDataType" );
			if ( $structurePassed )
			{
				$reporter->report( Reporter::STATUS_PASS, 'All columns have compatible data types.', null );
			
				$testResult = $suite->run( $result, '/testColumnLength/' );
				$structurePassed = $listener->wasSuccessful( "${structureTest}::testColumnLength" );
				if ( $structurePassed )
				{
					$reporter->report( Reporter::STATUS_PASS, 'All columns have compatible lengths.', null );
				}
				else
				{
					$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns %s an unexpected column length.',
						array(
							$listener->countNonPasses( "${structureTest}::testColumnLength" ),
							$listener->countPasses( "${structureTest}::testColumnLength" ),
							Reporter::pluralise( "${structureTest}::testColumnLength" )
						) ) ;
				}
			}
			else
			{
				$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns %s unexpected data types.',
					array(
						$listener->countNonPasses( "${structureTest}::testColumnDataType" ),
						$listener->countPasses( "${structureTest}::testColumnDataType" ),
						Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnDataType" ), "has", "have" )
					) ) ;
				$reporter->report( Reporter::STATUS_SKIPPED, 'column length tests, as the data types do not match what was expected.', null );
			}
			
			if ( $runMode !== 'student' )
			{
				$testResult = $suite->run( $result, '/testColumnNullability/' );
				if ( $listener->wasSuccessful( "${structureTest}::testColumnNullability" ) )
				{
					$reporter->report( Reporter::STATUS_PASS, 'All columns have the expected nullability.', null );
				}
				else
				{
					$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns %s unexpected nullability.',
						array(
							$listener->countNonPasses( "${structureTest}::testColumnNullability" ),
							$listener->countPasses( "${structureTest}::testColumnNullability" ),
							Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnNullability" ), "has", "have" )
						) ) ;
				}
			}
		}
		else
		{
			$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d expected columns %s either missing or misnamed.',
				array(
					$listener->countNonPasses( "${structureTest}::testColumnExists" ),
					$listener->countPasses( "${structureTest}::testColumnExists" ),
					Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnExists" ), 'is', 'are' )
				) ) ;
			$reporter->report( Reporter::STATUS_SKIPPED, 'data type, length and nullability tests as they will include spurious errors.', null );
		}
		
		if ( $runMode !== 'student' )
		{
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
			if ( $listener->wasSuccessful( "${structureTest}::testConstraintsNamed" ) )
			{
				$reporter->report( Reporter::STATUS_PASS, 'All constraints that should be are explicitly named.', null );
			}
			else
			{
				$reporter->report( Reporter::STATUS_FAILURE, 'Some constraints are not explicitly named that should be.', null );
			}
		}
	}
	
	if ( $runMode !== 'student' )
	{
		/*
			If the table or required columns are missing or misnamed, we need to skip the data testing entirely, as the INSERTs will just error out. We can't incorporate this into the if above, because we're using a completely different test suite.
		*/
		if ( $structurePassed )
		{
			$suite = new PHPUnit_Framework_TestSuite( $dataTest );
			
			$testResult = $suite->run( $result, '/testColumnLegalValue/' );
			if ( $listener->wasSuccessful( "${dataTest}::testColumnLegalValue" ) )
			{
				$reporter->report( Reporter::STATUS_PASS, 'All %d legal values tested were accepted.', 
					array( $listener->countTests( "${dataTest}::testColumnLegalValue" ) ) );
			}
		
			$testResult = $suite->run( $result, '/testColumnIllegalValueExplicit/' );
			if ( $listener->wasSuccessful( "${dataTest}::testColumnIllegalValueExplicit" ) )
			{
				$reporter->report( Reporter::STATUS_PASS, 'All %d illegal values tested were rejected by a CHECK constraint.', 
					array( $listener->countTests( "${dataTest}::testColumnIllegalValueExplicit" ) ) );
			}
			else
			{
				$checkFails = $listener->countFails( "${dataTest}::testColumnIllegalValueExplicit" );
				$reporter->report( Reporter::STATUS_ALERT, '%d of %d illegal values tested %s not rejected by a CHECK constraint.',
					array(
						$checkFails,
						$listener->countTests( "${dataTest}::testColumnIllegalValueExplicit" ),
						Reporter::pluralise( $checkFails, 'was', 'were' )
					) ) ;
				$reporter->report( Reporter::STATUS_ALERT, 'Checking values against column length...', null );
				
				/*
					Unfortunately, we can't test just the columns that failed the CHECK test. The failed TestCases are in $testResult->failures(), but we need the column name, which is hidden away in the private $data member of TestCase. We therefore have to test all the illegal values again to see if they're larger than the column. We then make the big assumption that if all the values that failed the CHECK test did so because they exceeded the column length. If this is correct, then the number of CHECK fails will equal the number of column length passes. If not, then something more serious has probably gone wrong!
				*/
				$testResult = $suite->run( $result, '/testColumnIllegalValueImplicit/' );
				$implicitPasses = $listener->countPasses( "${dataTest}::testColumnIllegalValueImplicit" );
				$reporter->report( Reporter::STATUS_PASSED, '%d of %d illegal values tested %s rejected by exceeding the column length.',
					array(
						$implicitPasses,
						$listener->countTests( "${dataTest}::testColumnIllegalValueImplicit" ),
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
	}
}

$reporter->hr();
?>
