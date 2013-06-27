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
$testResult = $suite->run( $result, '/testTableExists/' );
if ( $listener->wasSuccessful( 'testTableExists' ) )
{
	echo ">>> Table exists.\n";
	
	// Critical to data testing.
	$testResult = $suite->run( $result, '/testColumnExists/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnExists' ) )
	{
		echo ">>> Table contains all the expected columns.\n";
	
		$testResult = $suite->run( $result, '/testColumnDataType/' );
		if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnDataType' ) ) echo ">>> All columns have the expected data types.\n";
		
		$testResult = $suite->run( $result, '/testColumnLength/' );
		if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnLength' ) ) echo ">>> All columns have the expected lengths.\n";
		
		$testResult = $suite->run( $result, '/testColumnNullability/' );
		if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnNullability' ) ) echo ">>> All columns have the expected nullability.\n";
	}
	
	// Not critical to data testing.
	$testResult = $suite->run( $result, '/testPK.*/' );
	if ( $listener->wasSuccessful( 'testPKExists' ) )
	{
		echo ">>> Primary key exists.\n";
	}
	if ( $listener->wasSuccessful( 'testPKColumns' ) )
	{
		echo ">>> Primary key includes only the expected columns.\n";
	}
	
	// Not critical to data testing.
	$testResult = $suite->run( $result, '/testConstraintsNamed/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testConstraintsNamed' ) ) echo ">>> All constraints that should be are explicitly named.\n";
}

/*
	If the table or required columns are missing or misnamed, we need to skip the data testing entirely, as the INSERTs will just error out. We can't incorporate this into the if above, because we're using a completely different test suite.
*/
if ( ( $listener->wasSuccessful( 'testTableExists' ) ) && ( $listener->wasSuccessful( 'BDL_Test_Staff_structure::testColumnExists' ) ) )
{
	$suite = new PHPUnit_Framework_TestSuite( 'BDL_Test_Staff_data' );
	
	$testResult = $suite->run( $result, '/testColumnLegalValue/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_data::testColumnLegalValue' ) )
	{
		printf( ">>> All %d legal values tested were accepted.\n",
			$listener->countTests( 'BDL_Test_Staff_data::testColumnLegalValue' )
		);
	}

	$testResult = $suite->run( $result, '/testColumnIllegalValueExplicit/' );
	if ( $listener->wasSuccessful( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ) )
	{
		printf( ">>> All %d illegal values tested were rejected by a CHECK constraint.\n",
			$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' )
		);
	}
	else
	{
		$checkFails = $listener->countFails( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' );
		printf( ">>> %d of %d illegal values tested %s not rejected by a CHECK constraint.\n",
			$checkFails,
			$listener->countTests( 'BDL_Test_Staff_data::testColumnIllegalValueExplicit' ),
			( $checkFails > 1 ) ? "were" : "was"
		);
		echo ">>> Checking values against column length...\n";
		
		/*
			Unfortunately, we can't test just the columns that failed the CHECK test. The failed TestCases are in $testResult->failures(), but we need the column name, which is hidden away in the private $data member of TestCase. We therefore have to test all the illegal values again to see if they're larger than the column. We then make the big assumption that if all the values that failed the CHECK test did so because they exceeded the column length. If this is correct, then the number of CHECK fails will equal the number of column length passes. If not, then something more serious has probably gone wrong!
		*/
		$testResult = $suite->run( $result, '/testColumnIllegalValueImplicit/' );
		$implicitPasses = $listener->countPasses( 'BDL_Test_Staff_data::testColumnIllegalValueImplicit' );
		printf( ">>> %d of %d illegal values tested %s rejected by exceeding the column length.\n",
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
			printf( ">>> %d illegal values %s were rejected in both tests—check for something unusual.\n",
				$checkFails - $implicitPasses,
				( ( $checkFails - $implicitPasses ) > 1 ) ? "were" : "was"
			);
		}
		else
		{
			echo ">>> This is OK, but not necessarily safe, as the column length may change in future.\n";
		}
	}
}
	
?>
