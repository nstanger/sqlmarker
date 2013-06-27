<?php
	class SimpleTestListener implements PHPUnit_Framework_TestListener
	{
		/**
		 *	When you're running test suites, the various count methods in TestResult return only the total number of tests, fails, etc., for the entire Test, even if you filter the tests to be run within a given suite. We therefore need to keep track of these within the test listener for each suite. The results are indexed by suite name, which will be something like "BDL_Test_Staff_structure::testFoo".
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
		private $passCount = 0;
		private $failCount = 0;
		private $errorCount = 0;
		private $incompleteCount = 0;
		private $skipCount = 0;
		private $testCount = 0;
		
		public function countPasses( $suiteName )
		{
			return $this->passes[ $suiteName ];
		}
		
		public function countFails( $suiteName )
		{
			return $this->fails[ $suiteName ];
		}
		
		public function countErrors( $suiteName )
		{
			return $this->errors[ $suiteName ];
		}
		
		public function countIncompletes( $suiteName )
		{
			return $this->incompletes[ $suiteName ];
		}
		
		public function countSkips( $suiteName )
		{
			return $this->skips[ $suiteName ];
		}
		
		public function countTests( $suiteName )
		{
			return $this->tests[ $suiteName ];
		}
		
		public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
		{
			echo "ERROR! " . $e->getMessage() . "\n";
			$this->errorCount++;
		}
	 
		public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
		{
			echo "FAILED! " . $e->getMessage() . "\n";
			$this->failCount++;
		}
	 
		public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
		{
			echo "INCOMPLETE: " . $e->getMessage() . "\n";
			$this->incompleteCount++;
		}
	 
		public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
		{
			echo "SKIPPED: " . $e->getMessage() . "\n";
			$this->skipCount++;
		}
	 
		public function startTest(PHPUnit_Framework_Test $test)
		{
// 			printf("Test '%s' started.\n", $test->getName());
			$this->testCount++;
		}
	 
		public function endTest(PHPUnit_Framework_Test $test, $time)
		{
// 			printf("Test '%s' ended.\n", $test->getName());
			if ( $test->getStatus() === PHPUnit_Runner_BaseTestRunner::STATUS_PASSED )
			{
				echo "OK\n";
				$this->passCount++;
			}
		}
	 
		public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
		{
// 			printf("TestSuite '%s' started.\n", $suite->getName());
			$this->passCount = $this->failCount = $this->errorCount = $this->incompleteCount = $this->skipCount = $this->testCount = 0;
		}
	 
		public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
		{
// 			printf("TestSuite '%s' ended.\n", $suite->getName());
			$this->passes[ $suite->getName() ] = $this->passCount;
			$this->fails[ $suite->getName() ] = $this->failCount;
			$this->errors[ $suite->getName() ] = $this->errorCount;
			$this->incompletes[ $suite->getName() ] = $this->incompleteCount;
			$this->skips[ $suite->getName() ] = $this->skipCount;
			$this->tests[ $suite->getName() ] = $this->testCount;
		}
	}
?>
