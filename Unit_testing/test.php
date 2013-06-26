<?php
	require_once "PHPUnit/Autoload.php";
	require_once "BDL_Test_Staff_structure.php";
	
	class SimpleTestListener implements PHPUnit_Framework_TestListener
	{
		private $totalMark = 0;
		
		public function getTotalMark()
		{
			return $this->totalMark;
		}
		
		public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
		{
			printf("Error while running test '%s'.\n", $test->getName());
		}
	 
		public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
		{
			printf("Test '%s' failed.\n", $test->getName());
		}
	 
		public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
		{
			printf("Test '%s' is incomplete.\n", $test->getName());
		}
	 
		public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
		{
			printf("Test '%s' has been skipped.\n", $test->getName());
		}
	 
		public function startTest(PHPUnit_Framework_Test $test)
		{
			printf("Test '%s' started.\n", $test->getName());
		}
	 
		public function endTest(PHPUnit_Framework_Test $test, $time)
		{
			printf("Test '%s' ended.\n", $test->getName());
		}
	 
		public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
		{
			printf("TestSuite '%s' started.\n", $suite->getName());
		}
	 
		public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
		{
			printf("TestSuite '%s' ended.\n", $suite->getName());
		}
	}
	
	$suite = new PHPUnit_Framework_TestSuite('BDL_Test_Staff_structure');
	
	$result = new PHPUnit_Framework_TestResult;
	$listener = new SimpleTestListener;
	$result->addListener($listener);
	
	$suite->run( $result, '/test(Table|Column)Exists/' );
	
	if ( count( $result->passed() ) == 10 ) echo "Table exists with all the expected columns.\n";
?>
