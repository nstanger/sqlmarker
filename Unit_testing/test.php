<?php

require_once "PHPUnit/Autoload.php";
require_once 'SimpleTestListener.php';
require_once "BDL_Test_Staff_structure.php";
require_once "BDL_Test_Staff_data.php";

$result = new PHPUnit_Framework_TestResult;
$listener = new SimpleTestListener;
$result->addListener( $listener );

$suite = new PHPUnit_Framework_TestSuite( 'BDL_Test_Staff_structure' );

// Critical to data testing.
// TODO: is $testResult needed anymore?
$testResult = $suite->run( $result, '/testTableExists/' );
$structurePassed = $listener->wasSuccessful( 'testTableExists' );
if ( $structurePassed )
{
	echo "+++ PASSED: Table exists.\n";
	
	// Critical to data testing.
	$testResult = $suite->run( $result, '/testColumnExists/' );
	$structurePassed = $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnExists' );
	if ( $structurePassed )
	{
		echo "+++ PASSED: Table contains all the expected columns.\n";
	
		$testResult = $suite->run( $result, '/testColumnDataType/' );
		$structurePassed = $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnDataType' );
		if ( $structurePassed )
		{
			echo "+++ PASSED: All columns have the expected data types.\n";
		}
		else
		{
			printf( "--- FAILED: %d of the %d columns %s unexpected data types.\n",
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnDataType' ),
				$listener->countPasses( 'BDL_Test_Staff_structure::testColumnDataType' ),
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnDataType' ) > 1 ? "have" : "has"
			);
		}
		
		$testResult = $suite->run( $result, '/testColumnLength/' );
		$structurePassed = $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnLength' );
		if ( $structurePassed )
		{
			echo "+++ PASSED: All columns have the expected lengths.\n";
		}
		else
		{
			printf( "--- FAILED: %d of the %d columns %s an unexpected column length.\n",
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnLength' ),
				$listener->countPasses( 'BDL_Test_Staff_structure::testColumnLength' ),
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnLength' ) > 1 ? "have" : "has"
			);
		}
		
		$testResult = $suite->run( $result, '/testColumnNullability/' );
		if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnNullability' ) )
		{
			echo "+++ PASSED: All columns have the expected nullability.\n";
		}
		else
		{
			printf( "--- FAILED: %d of the %d columns %s unexpected nullability.\n",
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnNullability' ),
				$listener->countPasses( 'BDL_Test_Staff_structure::testColumnNullability' ),
				$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnNullability' ) > 1 ? "have" : "has"
			);
		}
	}
	else
	{
		printf( "--- FAILED: %d of the %d expected columns %s either missing or misnamed.\n",
			$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnExists' ),
			$listener->countPasses( 'BDL_Test_Staff_structure::testColumnExists' ),
			$listener->countNonPasses( 'BDL_Test_Staff_structure::testColumnExists' ) > 1 ? "are" : "is"
		);
		echo ">>> SKIPPED: data type, length and nullability tests as they will include spurious errors.\n";
	}
	
	// Not critical to data testing. Need to run both tests in one pass as the columns test depends on the existence test.
	$testResult = $suite->run( $result, '/testPK.*/' );
	if ( $listener->wasSuccessful( 'testPKExists' ) )
	{
		echo "+++ PASSED: Primary key exists.\n";
	}
	else
	{
		echo "--- FAILED: Primary key missing.\n";
	}
	if ( $listener->wasSuccessful( 'testPKColumns' ) )
	{
		echo "+++ PASSED: Primary key includes (only) the expected columns.\n";
	}
	else
	{
		echo "--- FAILED: Primary key does not include (only) the expected columns.\n";
	}
	
	// Not critical to data testing.
	$testResult = $suite->run( $result, '/testConstraintsNamed/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testConstraintsNamed' ) ) echo "+++ PASSED: All constraints that should be are explicitly named.\n";
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
		printf( "+++ PASSED: All %d legal values tested were accepted.\n",
			$listener->countTests( 'BDL_Test_Staff_data::testColumnLegalValue' )
		);
	}

	$testResult = $suite->run( $result, '/testColumnIllegalValueExplicit/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ) )
	{
		printf( "+++ PASSED: All %d illegal values tested were rejected by a CHECK constraint.\n",
			$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' )
		);
	}
	else
	{
		$checkFails = $listener->countFails( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' );
		printf( "!!! ALERT: %d of %d illegal values tested %s not rejected by a CHECK constraint.\n",
			$checkFails,
			$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ),
			( $checkFails > 1 ) ? "were" : "was"
		);
		echo "!!! ALERT: Checking values against column length...\n";
		
		/*
			Unfortunately, we can't test just the columns that failed the CHECK test. The failed TestCases are in $testResult->failures(), but we need the column name, which is hidden away in the private $data member of TestCase. We therefore have to test all the illegal values again to see if they're larger than the column. We then make the big assumption that if all the values that failed the CHECK test did so because they exceeded the column length. If this is correct, then the number of CHECK fails will equal the number of column length passes. If not, then something more serious has probably gone wrong!
		*/
		$testResult = $suite->run( $result, '/testColumnIllegalValueImplicit/' );
		$implicitPasses = $listener->countPasses( 'BDL_Test_Staff_data::testColumnIllegalValueImplicit' );
		printf( "+++ PASSED: %d of %d illegal values tested %s rejected by exceeding the column length.\n",
			$implicitPasses,
			$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueImplicit' ),
			( $implicitPasses > 1 ) ? "were" : "was"
		);
		
		// Any leftovers?
		if ( $implicitPasses != $checkFails )
		{
			/*
				$checkFails must by definition be >= $implicitPasses, as a "length exceeded" will /always/ fail the CHECK test, and a "check constraint" will /always/ fail the column length test. The two values will only differ when there are other exceptions in the mix, which will fail both tests.
				
				For example, suppose that two values fail the CHECK test with "length exceeded", one fails with "foo exception" and the remaining two pass. The first two will pass the column length test, and the remaining three will fail.
			*/
			printf( "--- FAILED: %d illegal values %s rejected in both tests—check for something unusual.\n",
				$checkFails - $implicitPasses,
				( ( $checkFails - $implicitPasses ) > 1 ) ? "were" : "was"
			);
		}
		else
		{
			echo "!!! NOTE: This is OK, but not necessarily safe, as the column length may change in future.\n";
		}
	}
}
else
{
	echo ">>> SKIPPED: data tests, as failures in the structure testing mean that they may not work.\n";
}
	
?>
