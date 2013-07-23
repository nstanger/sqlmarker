<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>INFO 214 Assignment 1 Minimum Requirement Tester: Results</title>
	<meta name="generator" content="BBEdit 9.6" />
	<link rel="Stylesheet" href="https://blackboard.otago.ac.nz/bbcswebdav/courses/INFO321_S2DNS_2013/db_styles.css" type="text/css" />
</head>
<body>

<h1>INFO 214 Assignment 1 Minimum Requirement Tester: Results</h1>

<p>Results may take a few seconds to appear…</p>

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
	$serviceID = "isorcl-400";
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	$outputMode = 'HTML';
	$verbosity = 2;
	$runMode = 'student';
	
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
