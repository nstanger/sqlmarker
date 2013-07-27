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
		<td class="blackboard grey-light result"><strong>Note:</strong> These messagesa are just for information.</td>
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
		<td class="blackboard red-ou result"><strong style="font-size: large">☠</strong> <strong>Error:</strong> There was an error on the server—please <a href="mailto:nigel.stanger@otago.ac.nz">contact Nigel</a>.</td>
		<td />
	</tr>
</table>

<hr />

<p class="blackboard grey-light result">Your results may take a few seconds to appear…</p>

<div style="border: 2px solid grey; width: 50%; padding: 1em 4em 1em 4em;">

<?php

$continue = true;

if ( empty( $_POST['username'] ) )
{
	echo '<p class="unired">Missing username, please return to the login page and try again.</p>';
	$continue = false;
}
if ( empty( $_POST['password'] ) )
{
	echo '<p class="unired">Missing password, please return to the login page and try again.</p>';
	$continue = false;
}

if ( $continue )
{
	// Define things that need to be globally accessible as constants.
	define( 'ORACLE_USERNAME', $_POST['username'] );
	define( 'ORACLE_PASSWORD', $_POST['password'] );
	
	$outputMode	= 'HTML';
	
	define( 'OUTPUT_VERBOSITY', 2 );
	define( 'RUN_MODE', 'student' );
	
	require_once 'test.php';
}
else
{
	echo '<p>Redirecting you back to the login page…</p>';
	header( "refresh:7;url=student_login.html" );
}
?>
</div>
</body>
</html>
