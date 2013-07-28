<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>INFO 214 Assignment 1 Minimum Requirement Tester: Results</title>
	<meta name="generator" content="BBEdit 9.6" />
	<link rel="Stylesheet" href="https://blackboard.otago.ac.nz/bbcswebdav/courses/INFO321_S2DNS_2013/db_styles.css" type="text/css" />
	<style type="text/css" media="screen">
		.greenbg	{ background-color: green; color: white; }
		.result		{ padding: 8px; }
	</style>
</head>
<body>

<h1>INFO 214 Assignment 1 Minimum Requirement Tester: Results</h1>

<h2>Key</h2>

<table>
	<tr>
		<td class="blackboard grey-light result"><strong>Note:</strong> These messages are just for information.</td>
		<td class="blackboard yellow-ou result"><strong style="font-size: large">%</strong> <strong>Incomplete:</strong> A test was not completed. It may or may not be a problem.</td>
	</tr>
	<tr>
		<td class="blackboard greenbg result"><span style="font-size: large">✔</span> <strong>Passed:</strong> Your schema passed a test. You want as many of these as possible!</td>
		<td class="blackboard yellow-ou result"><strong style="font-size: large">⚠</strong> <strong>Warning:</strong> This is a warning. It may or may not be a problem.</td>
	</tr>
	<tr>
		<td class="blackboard red-ou result"><span style="font-size: large">✘</span> <strong>Failed:</strong> Your schema failed a test. You want none of these!</td>
		<td class="blackboard result"><strong style="font-size: large">#</strong> <strong>Skipped:</strong> A test was skipped. It may or may not be a problem.</td>
	</tr>
	<tr>
		<td class="blackboard red-ou result"><span style="font-size: large">☠</span> <strong>Error:</strong> There was an error on the server—please <a href="mailto:nigel.stanger@otago.ac.nz">contact Nigel</a>.</td>
		<td />
	</tr>
</table>

<hr />

<p class="blackboard grey-light result">Your results may take a few seconds to appear below. If your schema passes all the tests then you have met the minimum requirement for Assignment 1 and are guaranteed to score at least 50%. Your final submission will be marked using an extended version of this schema checking tool. If you are unsure about anything in the report, please contact one of the teaching staff.</p>

<div style="border: 2px solid grey; width: 50%; padding: 1em 4em 1em 4em;">

<?php

try
{
	if ( empty( $_POST['username'] ) ) throw new Exception( 'No username entered. Please return to the login page and try again.' );
	if ( empty( $_POST['password'] ) ) throw new Exception( 'No password entered. Please return to the login page and try again.' );
	
	// Define things that need to be globally accessible as constants.
	define( 'ORACLE_USERNAME', $_POST['username'] );
	define( 'ORACLE_PASSWORD', $_POST['password'] );
	
	$outputMode	= 'HTML';
	
	define( 'OUTPUT_VERBOSITY', 2 );
	define( 'RUN_MODE', 'student' );
	
	require_once 'test_config.php';

	// Test that the database connection works.
	$testPDO = new PDO( "oci:dbname=" . ORACLE_SERVICE_ID, ORACLE_USERNAME, ORACLE_PASSWORD );
	unset( $testPDO );

	require_once 'test.php';
}
catch ( PDOException $e )
{
	echo '<p class="blackboard red-ou result"><span style="font-size: large">☠</span> <strong>Error:</strong> ';
	switch ( $e->getCode() )
	{
		case 1017:
			echo 'Oracle username and/or password are incorrect, cannot connect to schema. Please check your login details and try again.';
			break;
		default:
			echo 'Failed to connect to Oracle, error was:<br /><span class="blackboard">';
			echo $e->getMessage();
			echo '</span>';
			break;
	}
	echo "</p>\n";
	redirect();
}
catch ( Exception $e )
{
	echo '<p class="blackboard red-ou result"><span style="font-size: large">☠</span> <strong>Error:</strong> ', $e->getMessage(), "</p>\n";
	redirect();
}

function redirect()
{
	echo '<p class="blackboard grey-light result">Redirecting you back to the login page in a few seconds…</p>';
	header( "refresh:7;url=student_login.html" );
}
?>
</div>
</body>
</html>
