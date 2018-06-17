<?php
	session_start();
	// include 'session.php';
	// $userID = $_SESSION['login_id'];
	// setcookie("userID", 1, time() + 1800, "/");
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
  background:white url("image/backg3.png") no-repeat left top;
}

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
    background-color: #333;
    color: #228b22;
}
.navbar button{
  position: top;
  border-radius: 30%;
}

.search {background-color: #abb868;} 
.dash{background-color: #6c804b;} 
.log {background-color: #940016;} 


button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border-radius:30%;
    cursor: pointer;
    width: 100%;
    font-size:16px;
}


button:hover {
    opacity: 0.8;
}


a:link {
    color: green;
}

/* visited link */
a:visited {
    color: green;
}
</style>
</head>
<body>

<div class="header">
</div>

<div class = "navbar">
  <table><tr style="color: green;">
    <td width="66%"><img src="image/find.gif" alt="find" style="width:300px;height:60px;border:0"></td>
    <td><button class="button search" onclick="location.href='./profile_search.php'" style="width:auto;">Search Students</button><td>
    <td><button class="button dash" onclick="location.href='./dashboard.php'" style="width:auto;">Dashboard</button><td>
     <td><button class="button log" onclick="location.href='index.php'" style="width:auto;">Logout</button><td>
  </tr></table>

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
				<td><button class="button button-block" name="submit" style="width:auto;">Search!</button></td>
			</tr>
			<input type="checkbox" name="checkboxes[]" value="fname"/>
			<label for="fname">first name</label>
			<input type="checkbox" name="checkboxes[]" value="lname"/>
			<label for="lname">last name</label>
			<input type="checkbox" name="checkboxes[]" value="school"/>
			<label for="school">school</label>
			<p>If no checkboxes are selected all attributes will be returned</p>
		</table>
	</form>
</div>

</body>
</html>

<?php
$success = True;
$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
$userID = $_COOKIE['userID'];

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

function generateResults() {
	$select_query = "SELECT ";
	$school_only = false;
	foreach($_GET['checkboxes'] as $values){
		switch ($values) {
			case "fname":
				$select_query .= " FNAME,";
				break;
			case "lname":
				$select_query .= " LNAME,";
				break;
			case "school":
				$select_query .= " SNAME";
				break;
		}
	}

	if(empty($_GET['checkboxes'])){
		$select_query = "SELECT *";
	}else if(sizeof($_GET['checkboxes']) == 1 && $_GET['checkboxes'][0] == "school")
		$school_only = true;
	else{
		$select_query = trim($select_query, ",");
	}

	$fname = $_GET['fname'];
	$lname = $_GET['lname'];
	$sname = $_GET['school'];

	$result = NULL;
	$queryStr = NULL;

	// Query on fname, lname, school -- very simplistic
	if ($school_only) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE isEmployer=0 GROUP BY SNAME";
	} else if (empty($lname) && empty($fname) && empty($sname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE isEmployer=0";
	} else if (empty($lname) && empty($fname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE UPPER(sname)=UPPER('{$sname}') AND isEmployer=0";
	} else if (empty($lname) && empty($sname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE UPPER(fname)=UPPER('{$fname}') AND isEmployer=0";
	} else if (empty($sname) && empty($fname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE UPPER(lname)=UPPER('{$lname}') AND isEmployer=0";
	} else if (empty($fname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE UPPER(lname)=UPPER('{$lname}') AND UPPER(sname)=UPPER('{$sname}') AND isEmployer=0";
	} else if (empty($sname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE UPPER(fname)=UPPER('{$fname}') AND UPPER(lname)=UPPER('{$lname}') AND isEmployer=0";
	} else if (empty($lname)) {
		$queryStr = $select_query." FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE UPPER(fname)=UPPER('{$fname}') AND UPPER(sname)=UPPER('{$sname}') AND isEmployer=0";
	} else {	// Query on just fname
		$queryStr = $select_query." FROM Students natural join Accounts natural join Schools WHERE UPPER(fname)=UPPER('{$fname}') AND UPPER(lname)=UPPER('{$lname}') AND UPPER(sname)=UPPER('{$sname}') AND isEmployer=0";
	}

	$result = executePlainSQL($queryStr);
	printResult($result);
	OCILogoff($db_conn);
}

// Check form submitted
if (isset($_GET['submit'])) {
	generateResults();
}

?>
