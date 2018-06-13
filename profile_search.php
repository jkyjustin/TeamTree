<html>
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
</html>

<?php

$success = True;
$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");

// Why isn't this working?
// $query1 = "SELECT * FROM ACCOUNTS";
// $rslt = executePlainSQL($query1);
// OCI_commit($db_conn);
// printResult($rslt);

// Fetch each row in an associative array
// print '<table border="1">';
// while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
//    print '<tr>';
//    foreach ($row as $item) {
//        print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
//    }
//    print '</tr>';
// }
// print '</table>';


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
	echo "<br>Search results:<br>";
	echo "<table>";
	echo "<tr><th>First Name</th><th>Last Name</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["FNAME"] . "</td><td>" . $row["LNAME"] . "</td></tr>";
	}
	echo "</table>";
}

// Check form submitted
if (isset($_GET['submit'])) {
	$fname = $_GET['fname'];
	$lname = $_GET['lname'];
	$school = $_GET['school'];

	$result = NULL;

	// Query on fname, lname, school -- very simplistic
	if (!empty($lname)) {
		$queryStr = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools C WHERE fname='{$fname}' AND A.lname='{$lname}' AND sName='{$school}'";

		$result = executePlainSQL($queryStr);

	} else {	// Query on just fname
		$queryStr = "SELECT * FROM Students natural join Accounts natural join Schools WHERE fname='{$fname}' AND sname='{$school}'";
		echo($queryStr);
		$result = executePlainSQL($queryStr);
	}

	printResult($result);
}

?>
