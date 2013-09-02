<?php

require_once 'test_config.php';

require_once "PHPUnit/Autoload.php";
require_once 'TestListener/HTMLTestListener.php';
require_once 'TestListener/TextTestListener.php';
require_once 'TestListener/ANSITestListener.php';
require_once 'Reporter/TextReporter.php';
require_once 'Reporter/ANSIReporter.php';
require_once 'Reporter/HTMLReporter.php';
require_once 'Searchable_TestSuite.php';
require_once "Schema.php";

// I don't know that these two settings make any difference, but I'll leave them in for now.
PHPUnit_Framework_Error_Warning::$enabled = FALSE;
PHPUnit_Framework_Error_Notice::$enabled = FALSE;

switch ( $outputMode )
{
	case 'HTML':
		$reporter = new HTMLReporter( OUTPUT_VERBOSITY );
		$listener = new HTMLTestListener;
		break;
	case 'TEXT':
		$reporter = new TextReporter( OUTPUT_VERBOSITY );
		$listener = new TextTestListener;
		break;
	case 'ANSI':
		$reporter = new ANSIReporter( OUTPUT_VERBOSITY );
		$listener = new ANSITestListener;
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
	
	$reporter->report( Reporter::STATUS_NOTE, 'Checking structure of table %s.', array( $table ) );
	
	$suite = new Searchable_TestSuite( $structureTest );
	
	// Critical to data testing.
	// TODO: is $testResult needed anymore?
	if ( $suite->testExists( 'testTableExists' ) )
	{
		$testResult = $suite->run( $result, '/testTableExists/' );
		$structurePassed = $listener->wasSuccessful( 'testTableExists' );
		if ( $structurePassed )
		{
			$reporter->report( Reporter::STATUS_PASS, 'Table %s exists.', array( $table ) );
			
			// Critical to data testing.
			if ( $suite->testExists( "${structureTest}::testColumnExists" ) )
			{
				$testResult = $suite->run( $result, '/testColumnExists/' );
				$structurePassed = $listener->wasSuccessful( "${structureTest}::testColumnExists" );
				if ( $structurePassed )
				{
					$reporter->report( Reporter::STATUS_PASS, 'Table %s contains all the expected columns.', array( $table ) );
				
					if ( $suite->testExists( "${structureTest}::testColumnDataType" ) )
					{
						$testResult = $suite->run( $result, '/testColumnDataType/' );
						$structurePassed = $listener->wasSuccessful( "${structureTest}::testColumnDataType" );
						if ( $structurePassed )
						{
							$reporter->report(	Reporter::STATUS_PASS,
												'All columns of table %s have data types compatible with the specification.',
												array( $table )	);
						
							if ( $suite->testExists( "${structureTest}::testColumnLength" ) )
							{
								$testResult = $suite->run( $result, '/testColumnLength/' );
								$structurePassed = $listener->wasSuccessful( "${structureTest}::testColumnLength" );
								if ( $structurePassed )
								{
									$reporter->report(	Reporter::STATUS_PASS,
														'All columns of table %s have lengths compatible with the specification.',
														array( $table )	);
								}
								else
								{
									$reporter->report(	Reporter::STATUS_FAILURE,
														'%d of the %d columns of table %s %s an unexpected column length.',
														array(	$listener->countNonPasses( "${structureTest}::testColumnLength" ),
																$listener->countTests( "${structureTest}::testColumnLength" ),
																$table,
																Reporter::pluralise( "${structureTest}::testColumnLength", 'has', 'have' ) ) ) ;
								}
							}
						}
						else
						{
							$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns of table %s %s an unexpected data type.',
								array(
									$listener->countNonPasses( "${structureTest}::testColumnDataType" ),
									$listener->countTests( "${structureTest}::testColumnDataType" ),
									$table,
									Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnDataType" ), "has", "have" )
								) ) ;
							$reporter->report( Reporter::STATUS_SKIPPED, 'column length tests, as the data types do not match what was expected.', null );
						}
					}
					
					if ( RUN_MODE !== 'student' )
					{
						if ( $suite->testExists( "${structureTest}::testColumnNullability" ) )
						{
							$testResult = $suite->run( $result, '/testColumnNullability/' );
							if ( $listener->wasSuccessful( "${structureTest}::testColumnNullability" ) )
							{
								$reporter->report( Reporter::STATUS_PASS, 'All columns of table %s have the expected nullability.', array( $table ) );
							}
							else
							{
								$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d columns of table %s %s an unexpected nullability.',
									array(
										$listener->countNonPasses( "${structureTest}::testColumnNullability" ),
										$listener->countTests( "${structureTest}::testColumnNullability" ),
										$table,
										Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnNullability" ), "has", "have" )
									) ) ;
							}
						}
						if ( $suite->testExists( "${structureTest}::testColumnDefault" ) )
						{
							$testResult = $suite->run( $result, '/testColumnDefault/' );
							if ( $listener->wasSuccessful( "${structureTest}::testColumnDefault" ) )
							{
								$reporter->report( Reporter::STATUS_PASS, 'All expected columns of table %s have the correct default values.', array( $table ) );
							}
							else
							{
								$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d expected columns of table %s %s an incorrect default value.',
									array(
										$listener->countNonPasses( "${structureTest}::testColumnDefault" ),
										$listener->countTests( "${structureTest}::testColumnDefault" ),
										$table,
										Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnDefault" ), "has", "have" )
									) ) ;
							}
						}
					}
				}
				else
				{
					$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d expected columns of table %s %s either missing or misnamed.',
						array(
							$listener->countNonPasses( "${structureTest}::testColumnExists" ),
							$listener->countTests( "${structureTest}::testColumnExists" ),
							$table,
							Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testColumnExists" ), 'is', 'are' )
						) ) ;
					$reporter->report( Reporter::STATUS_SKIPPED, 'data type, length and nullability tests as they will include spurious errors.', null );
				}
			}
			
			if ( RUN_MODE !== 'student' )
			{
				// Not critical to data testing. Need to run both PK tests in one pass as the columns test depends on the existence test.
				if ( $suite->testExists( 'testPKExists' ) && $suite->testExists( 'testPKColumns' ) )
				{
					$testResult = $suite->run( $result, '/testPK.*/' );
					if ( $listener->wasSuccessful( 'testPKExists' ) )
					{
						$reporter->report( Reporter::STATUS_PASS, 'Primary key of table %s exists.', array( $table ) );
					}
					else
					{
						$reporter->report( Reporter::STATUS_FAILURE, 'Primary key of table %s missing.', array( $table ) );
					}
                    if ( $listener->wasSuccessful( 'testPKColumns' ) )
                    {
                        $reporter->report( Reporter::STATUS_PASS, 'Primary key of table %s includes (only) the expected columns.', array( $table ) );
                    }
                    else
                    {
                        $reporter->report( Reporter::STATUS_FAILURE, 'Primary key of table %s does not include (only) the expected columns.', array( $table ) );
                    }
				}
				
				// Not critical to data testing.
				if ( $suite->testExists( "${structureTest}::testFKsExist" ) )
				{
					$testResult = $suite->run( $result, '/testFKsExist/' );
					if ( $listener->wasSuccessful( "${structureTest}::testFKsExist" ) )
					{
						$reporter->report( Reporter::STATUS_PASS, 'All expected foreign keys for table %s exist.', array( $table ) );
						
						if ( $suite->testExists( "${structureTest}::testFKColumns" ) )
						{
							$testResult = $suite->run( $result, '/testFKColumns/' );
							if ( $listener->wasSuccessful( "${structureTest}::testFKColumns" ) )
							{
								$reporter->report( Reporter::STATUS_PASS, 'All foreign keys for table %s include (only) the expected columns.', array( $table ) );
							}
							else
							{
								$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d foreign keys for table %s %s not include (only) the expected columns.', 
									array(
										$listener->countNonPasses( "${structureTest}::testFKColumns" ),
										$listener->countTests( "${structureTest}::testFKColumns" ),
										$table,
										Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testFKColumns" ), 'does', 'do' )
									) );
							}
						}
					}
					else
					{
						$reporter->report( Reporter::STATUS_FAILURE, '%d of the %d expected foreign keys for table %s %s missing.', 
							array(
								$listener->countNonPasses( "${structureTest}::testFKsExist" ),
								$listener->countTests( "${structureTest}::testFKsExist" ),
								$table,
								Reporter::pluralise( $listener->countNonPasses( "${structureTest}::testFKsExist" ), 'is', 'are' )
							) );
						
						$reporter->report( Reporter::STATUS_SKIPPED, 'testing expected columns of FKs to avoid spurious errors', null );
					}
				}
				
				// Not critical to data testing.
				if ( $suite->testExists( "${structureTest}::testConstraintsNamed" ) )
				{
					$testResult = $suite->run( $result, '/testConstraintsNamed/' );
					if ( $listener->wasSuccessful( "${structureTest}::testConstraintsNamed" ) )
					{
						$reporter->report( Reporter::STATUS_PASS, 'All constraints of table %s that should be are explicitly named.', array( $table ) );
					}
					else
					{
						$reporter->report( Reporter::STATUS_FAILURE, 'Some constraints of table %s are not explicitly named that should be.', array( $table ) );
					}
				}
			}
		}
	}
	
	if ( RUN_MODE !== 'student' )
	{
		$reporter->report( Reporter::STATUS_NOTE, 'Testing constraints of table %s.', array( $table ) );
		
		/*
			If the table or required columns are missing or misnamed, we need to skip the data testing entirely, as the INSERTs will just error out. We can't incorporate this into the if above, because we're using a completely different test suite.
		*/
		if ( $structurePassed )
		{
			$suite = new Searchable_TestSuite( $dataTest );
			
			if ( $suite->testExists( "${dataTest}::testColumnLegalValue" ) )
			{
				$testResult = $suite->run( $result, '/testColumnLegalValue/' );
				if ( $listener->wasSuccessful( "${dataTest}::testColumnLegalValue" ) )
				{
					$reporter->report( Reporter::STATUS_PASS, 'All %d legal values tested were accepted.', 
						array( $listener->countTests( "${dataTest}::testColumnLegalValue" ) ) );
				}
				else
				{
					$reporter->report( Reporter::STATUS_FAILURE, '%d of %d legal values tested %s rejected by a CHECK constraint.', 
						array(
							$listener->countFails( "${dataTest}::testColumnLegalValue" ),
							$listener->countTests( "${dataTest}::testColumnLegalValue" ),
							Reporter::pluralise( $listener->countFails( "${dataTest}::testColumnLegalValue" ), 'was', 'were' )
						) ) ;
				}
			}
		
			if ( $suite->testExists( "${dataTest}::testColumnUnderflowValue" ) )
			{
				$testResult = $suite->run( $result, '/testColumnUnderflowValue/' );
				if ( $listener->wasSuccessful( "${dataTest}::testColumnUnderflowValue" ) )
				{
					$reporter->report( Reporter::STATUS_PASS, 'All %d underflow values were rejected by a CHECK constraint.',
					    array( $listener->countTests( "${dataTest}::testColumnUnderflowValue" ) ) );
				}
				else
				{
					$reporter->report( Reporter::STATUS_FAILURE, '%d of %d underflow values %s not rejected by a CHECK constraint.',
						array(
							$listener->countFails( "${dataTest}::testColumnUnderflowValue" ),
							$listener->countTests( "${dataTest}::testColumnUnderflowValue" ),
							Reporter::pluralise( $listener->countFails( "${dataTest}::testColumnUnderflowValue" ), 'was', 'were' )
						) ) ;
				}
			}
		
			if ( $suite->testExists( "${dataTest}::testColumnOverflowValueExplicit" ) )
			{
				$testResult = $suite->run( $result, '/testColumnOverflowValueExplicit/' );
				if ( $listener->wasSuccessful( "${dataTest}::testColumnOverflowValueExplicit" ) )
				{
					$reporter->report( Reporter::STATUS_PASS, 'All %d overflow values were rejected by a CHECK constraint.',
					    array( $listener->countTests( "${dataTest}::testColumnOverflowValueExplicit" ) ) );
				}
				else
				{
					$reporter->report( Reporter::STATUS_FAILURE, '%d of %d overflow values %s not rejected by a CHECK constraint.',
						array(
							$listener->countFails( "${dataTest}::testColumnOverflowValueExplicit" ),
							$listener->countTests( "${dataTest}::testColumnOverflowValueExplicit" ),
							Reporter::pluralise( $listener->countFails( "${dataTest}::testColumnOverflowValueExplicit" ), 'was', 'were' )
						) ) ;
					$reporter->report( Reporter::STATUS_WARNING, 'Checking values against column length...', null );
					
					if ( $suite->testExists( "${dataTest}::testColumnOverflowValueImplicit" ) )
					{
						/*
							Unfortunately, we can't test just the columns that failed the CHECK test. The failed TestCases are in $testResult->failures(), but we need the column name, which is hidden away in the private $data member of TestCase. We therefore have to test all the illegal values again to see if they're larger than the column.
						*/
						$testResult = $suite->run( $result, '/testColumnOverflowValueImplicit/' );
						if ( $listener->wasSuccessful( "${dataTest}::testColumnOverflowValueImplicit" ) )
						{
    						$reporter->report( Reporter::STATUS_PASS, 'All %d overflow values were rejected by exceeding the column length.',
					            array( $listener->countTests( "${dataTest}::testColumnOverflowValueImplicit" ) ) );
    					}
    					else
						{
							$reporter->report( Reporter::STATUS_FAILURE, 
							    '%d of %d overflow values %s not rejected by exceeding the column length.',
                                array(
                                    $listener->countFails( "${dataTest}::testColumnOverflowValueImplicit" ),
                                    $listener->countTests( "${dataTest}::testColumnOverflowValueImplicit" ),
                                    Reporter::pluralise( $listener->countFails( "${dataTest}::testColumnOverflowValueImplicit" ), 'was', 'were' )
                                ) ) ;
                        }
					}
					else
					{
						$reporter->report( Reporter::STATUS_SKIPPED, 'testColumnOverflowValueImplicit() because it is missing.', null );
					}
				}
			}
		    
		    // Now do much the same for the enumerated values. This needs to be refactored!
			if ( $suite->testExists( "${dataTest}::testColumnIllegalValueExplicit" ) )
			{
				$testResult = $suite->run( $result, '/testColumnIllegalValueExplicit/' );
				if ( $listener->wasSuccessful( "${dataTest}::testColumnIllegalValueExplicit" ) )
				{
					$reporter->report( Reporter::STATUS_PASS, 'All %d illegal values tested were rejected by a CHECK constraint.', 
						array( $listener->countTests( "${dataTest}::testColumnIllegalValueExplicit" ) ) );
				}
				else
				{
					$checkFails = $listener->countFails( "${dataTest}::testColumnIllegalValueExplicit" );
					$reporter->report( Reporter::STATUS_WARNING, '%d of %d illegal values tested %s not rejected by a CHECK constraint.',
						array(
							$checkFails,
							$listener->countTests( "${dataTest}::testColumnIllegalValueExplicit" ),
							Reporter::pluralise( $checkFails, 'was', 'were' )
						) ) ;
					$reporter->report( Reporter::STATUS_WARNING, 'Checking values against column length...', null );
					
					if ( $suite->testExists( "${dataTest}::testColumnIllegalValueImplicit" ) )
					{
						/*
							Unfortunately, we can't test just the columns that failed the CHECK test. The failed TestCases are in $testResult->failures(), but we need the column name, which is hidden away in the private $data member of TestCase. We therefore have to test all the illegal values again to see if they're larger than the column. We then make the big assumption that if all the values that failed the CHECK test did so because they exceeded the column length. If this is correct, then the number of CHECK fails will equal the number of column length passes. If not, then something more serious has probably gone wrong!
						*/
						$testResult = $suite->run( $result, '/testColumnIllegalValueImplicit/' );
						$implicitPasses = $listener->countPasses( "${dataTest}::testColumnIllegalValueImplicit" );
						$reporter->report( Reporter::STATUS_PASS, '%d of %d illegal values tested %s rejected by exceeding the column length.',
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
							$reporter->report( Reporter::STATUS_FAILURE, '%d illegal %s rejected in both tests—check for something unusual.',
								array(
									$checkFails - $implicitPasses,
									Reporter::pluralise( $checkFails - $implicitPasses, 'value was', 'values were' )
								) ) ;
						}
						else
						{
							$reporter->report( Reporter::STATUS_NOTE, 'This is OK, but not necessarily safe, as the column length may change in future.', null );
						}
					}
					else
					{
						$reporter->report( Reporter::STATUS_SKIPPED, 'testColumnIllegalValueImplicit() because it is missing.', null );
					}
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
