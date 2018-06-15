<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {	
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
}

/* Full-width input fields */
/*input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}*/
table {
  border-spacing: 7px;
}
/* Style the header */
.header {
    background-color: white;
    padding: 60px;
    text-align: center;
    background-repeat: no-repeat;
    background-size: 300px 130px, 600px 600px;
    background-image: url("image/logo.png"), url("image/backg.jpg");
    background-position: left, right;
    background:cover;
}

/* Style the top navigation bar */
.navbar {
    overflow: hidden;
    padding-top: 0px;
    background-color: #333;
    color: #228b22;
}

.navbar button{
  position: top;
  border-radius: 30%;
}
.navbar img{
}

/* Style for navbar */
.abc {
  width: 100%;
  padding: 300px;
  background-image: url("image/backg3.png");
  background-repeat: no-repeat;
  background-position: left top;
  background-blend-mode: lighten;
}

/* Set a style for all buttons */
button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

button:hover {
    opacity: 0.8;
}

/* Extra styles for the cancel button */
.cancelbtn {
    width: auto;
    padding: 10px 18px;
    background-color: #f44336;
}

/* Center the image and position the close button */
.imgcontainer {
    text-align: center;
    margin: 24px 0 12px 0;
    position: relative;
}

img.avatar {
    width: 15%;
    border-radius: 50%;
}

.container {
    padding: 16px;
}

span.psw {
    float: right;
    padding-top: 16px;
}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    padding-top: 60px;
}

/* Modal Content/Box */
.modal-content {
    background-color: #fefefe;
    margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
    border: 1px solid #888;
    width: 50%; /* Could be more or less, depending on screen size */
}

/* The Close Button (x) */
.close {
    position: absolute;
    right: 25px;
    top: 0;
    color: #000;
    font-size: 35px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: red;
    cursor: pointer;
}

/* Add Zoom Animation */
.animate {
    -webkit-animation: animatezoom 0.6s;
    animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
    from {-webkit-transform: scale(0)}
    to {-webkit-transform: scale(1)}
}

@keyframes animatezoom {
    from {transform: scale(0)}
    to {transform: scale(1)}
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
    span.psw {
       display: block;
       float: none;
    }
    .cancelbtn {
       width: 100%;
    }
}
</style>
</head>
<body>

<div class="header">
</div>

<div class = "navbar">
<!--   <button onclick="document.getElementById('id01').style.display='block'"
  style="width:auto;">Login</button> -->
  <h2> Find a Classmate!</h2>
</div>

<div class = "body">
	<form action="profile_search.php" method="GET">
		<table>
			<tr>
				<td>First Name:</td>
				<td><input type="text" name="fname"></td>
			</tr>
			<tr>
				<td>Last Name:</td>
				<td><input type="text" name="lname"></td>
			</tr>
			<tr>
				<td>School:</td>
				<td><input type="text" name="school"></td>
			</tr>
			<tr>
				<td><input type ="submit" name="submit" value="Search!" ></td>
			</tr>
		</table>
	</form>
</div>

</body>
</html>

<!-- <html>
<head>
	<title> Find a classmate! </title>
</head>
<body>
	<h2> Find a Classmate! </h2>
	<form action="profile_search.php" method="GET">
		<table>
			<tr>
				<td>First Name:</td>
				<td><input type="text" name="fname"></td>
			</tr>
			<tr>
				<td>Last Name*:</td>
				<td><input type="text" name="lname"></td>
			</tr>
			<tr>
				<td>School:</td>
				<td><input type="text" name="school"></td>
			</tr>
			<tr>
				<td><input type ="submit" name="submit" value="Search!" ></td>
			</tr>
		</table>
	</form>
	<p> * Optional </p>
</body>
</html> -->

<?php

$success = True;
$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
$userID = 3;
// $userID = $_GET['userID']; // This should come from login

function executePlainSQL($cmdstr) {
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;
}

function printResult($result) { //prints results from a select statement
	global $userID;
	echo '<div class = "body"><br>Search results:<br>';
	echo "<table>";
	echo "<tr><th>First Name</th><th>Last Name</th><th>School</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		$profileLink = "<a href=profile.php?acctID=" . $row["ACCTID"] . "&userID=" . $userID . "> View Profile </a>";
		echo "<tr><td>" . $row["FNAME"] . "</td><td>" . $row["LNAME"] . "</td><td>" . $row["SNAME"] . "</td><td>" . $profileLink . "</td></tr>";
	}
	echo "</table></div>";
}

// Check form submitted
if (isset($_GET['submit'])) {
	$fname = $_GET['fname'];
	$lname = $_GET['lname'];
	$sname = $_GET['school'];

	$result = NULL;
	$queryStr = NULL;

	// Query on fname, lname, school -- very simplistic
	if (empty($lname) && empty($fname) && empty($sname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE isEmployer=0";
	} else if (empty($lname) && empty($fname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE sname='{$sname}' AND isEmployer=0";
	} else if (empty($lname) && empty($sname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE fname='{$fname}' AND isEmployer=0";
	} else if (empty($sname) && empty($fname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE lname='{$lname}' AND isEmployer=0";
	} else if (empty($fname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE lname='{$lname}' AND sname='{$sname}' AND isEmployer=0";
	} else if (empty($sname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE fname='{$fname}' AND lname='{$lname}' AND isEmployer=0";
	} else if (empty($lname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE fname='{$fname}' AND sname='{$sname}' AND isEmployer=0";
	} else {	// Query on just fname
		$queryStr = "SELECT * FROM Students natural join Accounts natural join Schools WHERE fname='{$fname}' AND lname='{$lname}' AND sname='{$sname}' AND isEmployer=0";
	}

	$result = executePlainSQL($queryStr);
	printResult($result);
	OCILogoff($db_conn);
}

?>
