<?php

abstract class SimpleTestListener implements PHPUnit_Framework_TestListener
{
	/**
	 *	When you're running test suites, the various count methods in TestResult return only the total number of tests, fails, etc., for the entire Test, even if you filter the tests to be run within a given suite. We therefore need to keep track of these within the test listener for each suite and test. The results are indexed by suite/test name, which will be something like "BDL_Test_Staff_structure::testFoo" or just "testFoo".
	 */
	private $passes = array();
	private $fails = array();
	private $errors = array();
	private $incompletes = array();
	private $skips = array();
	private $tests = array();
	
	/**
	 *	These keep track of the counts within a given suite execution, and are reset at the start of every new suite. I'm not sure whether nested suites are stored as actual TestSuite objects. If not, this could mean that the counts for the enclosing suites may be wrong?
	 */
	private $suitePassCount = 0;
	private $suiteFailCount = 0;
	private $suiteErrorCount = 0;
	private $incompleteCount = 0;
	private $suiteSkipCount = 0;
	private $suiteTestCount = 0;
	
	public function reset()
	{
		$this->passes = array();
		$this->fails = array();
		$this->errors = array();
		$this->incompletes = array();
		$this->skips = array();
		$this->tests = array();
		$this->suitePassCount = 0;
		$this->suiteFailCount = 0;
		$this->suiteErrorCount = 0;
		$this->incompleteCount = 0;
		$this->suiteSkipCount = 0;
		$this->suiteTestCount = 0;
	}
	
	public function countPasses( $name )
	{
		return $this->passes[ $name ];
	}
	
	// Should this also include incompletes?
	public function countNonPasses( $name )
	{
		return $this->tests[ $name ] - $this->passes[ $name ];
	}
	
	public function countFails( $name )
	{
		return $this->fails[ $name ];
	}
	
	public function countErrors( $name )
	{
		return $this->errors[ $name ];
	}
	
	public function countIncompletes( $name )
	{
		return $this->incompletes[ $name ];
	}
	
	public function countSkips( $name )
	{
		return $this->skips[ $name ];
	}
	
	public function countTests( $name )
	{
		return $this->tests[ $name ];
	}
	
	public function wasSuccessful( $name )
	{
		return ( $this->countPasses( $name ) === $this->countTests( $name ) );
	}
	
	public function getPasses( $name = NULL )
	{
		if ( $name === NULL ) return $this->passes;
		else return $this->passes[ $name ];
	}
	
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->printError( $e );
		$this->suiteErrorCount++;
		$this->errors[ $test->getName() ] = 1;
	}
 
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
	{
		$this->printFailure( $e );
		$this->suiteFailCount++;
		$this->fails[ $test->getName() ] = 1;
	}
 
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->printIncomplete( $e );
		$this->incompleteCount++;
		$this->incompletes[ $test->getName() ] = 1;
	}
 
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->printSkip( $e );
		$this->suiteSkipCount++;
		$this->skips[ $test->getName() ] = 1;
	}
 
	public function startTest(PHPUnit_Framework_Test $test)
	{
// 		printf( "@@@ Test '%s' started.\n", $test->getName() );
		$this->suiteTestCount++;
		$this->tests[ $test->getName() ] = 1;
	}
 
	public function endTest(PHPUnit_Framework_Test $test, $time)
	{
// 		printf( "@@@ Test '%s' ended with status %d.\n", $test->getName(), $test->getStatus() );
		if ( $test->getStatus() === PHPUnit_Runner_BaseTestRunner::STATUS_PASSED )
		{
			$this->printPass();
			$this->suitePassCount++;
			$this->passes[ $test->getName() ] = 1;
		}
	}
 
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
// 		printf( "@@@ TestSuite '%s' started.\n", $suite->getName() );
		$this->suitePassCount = $this->suiteFailCount = $this->suiteErrorCount =
		$this->incompleteCount = $this->suiteSkipCount = $this->suiteTestCount = 0;
	}
 
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
// 		printf( "@@@ TestSuite '%s' ended with a count of %d.\n", $suite->getName(), $this->suiteTestCount );
		/*
			The suite registers as having been started even if its tests have been filtered out. If so, the number of tests will be zero, and the results shouldn't be recorded, otherwise this could wipe out the results of earlier actual runs.
		*/
		if ( $this->suiteTestCount )
		{
			$this->passes[ $suite->getName() ] = $this->suitePassCount;
			$this->fails[ $suite->getName() ] = $this->suiteFailCount;
			$this->errors[ $suite->getName() ] = $this->suiteErrorCount;
			$this->incompletes[ $suite->getName() ] = $this->incompleteCount;
			$this->skips[ $suite->getName() ] = $this->suiteSkipCount;
			$this->tests[ $suite->getName() ] = $this->suiteTestCount;
		}
	}
	
	abstract public function printPass();
	abstract public function printFailure( Exception $e );
	abstract public function printError( Exception $e );
	abstract public function printIncomplete( Exception $e );
	abstract public function printSkip( Exception $e );
}

?>
