<?php
	session_start();
	$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
?>

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
	  <td width="68%"><h2><?php echo getName(); ?></h2></td>
	  <td align="right"><h2><a href="./profile_search.php">Search Students</a></h2></td>
	  <td align="right"><h2><a href="./dashboard.php">Dashboard</a></h2></td>
	  <td><form action="logout.php" method="GET"><input type="submit" value="Logout"></form></td>
	</tr></table>
</div>

<!-- <div class = "body">
	can put some stuff in here
</div> -->
</body>
</html>

<?php
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

function getSchoolId($schoolName) {
	$query = "SELECT schoolID FROM Schools WHERE sname='{$schoolName}'";
	$result = executePlainSQL($query);
	$row = OCI_Fetch_Array($result, OCI_BOTH);

	return $row[0];
}

function getName() {
	global $profileID;
	$query = "SELECT fname, lname FROM Accounts WHERE acctID={$_GET['acctID']}";
	$result = executePlainSQL($query);
	$row = OCI_Fetch_Array($result, OCI_BOTH);
	$fname = $row['FNAME'];
	$lname = $row['LNAME'];
	return $fname . ' ' .$lname;
}

$result = NULL;
$success = True;
$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");

if (isset($_GET['unendorse'])) {
	$profileID = $_GET['acctID'];
	$userID = $_GET['userID'];
	$query = "DELETE FROM Endorsements WHERE employerID={$userID} AND studentID={$profileID}";
	$result = executePlainSQL($query);
	OCICommit($db_conn);
}

if (isset($_GET['endorse'])) {
	$profileID = $_GET['acctID'];
	$userID = $_GET['userID'];
	$query = "INSERT INTO Endorsements VALUES({$userID}, {$profileID})";
	$result = executePlainSQL($query);
	OCICommit($db_conn);
}

if (isset($_GET['updateSchool'])) {
	$profileID = $_GET['acctID'];
	$userID = $_GET['userID'];
	$newSchoolName = $_GET['newSchoolName'];
	$newSchoolID = getSchoolId($newSchoolName);
	if ($newSchoolID <= 0) {
		exit("School does not exist");
	}

	$query = "UPDATE Students SET schoolID={$newSchoolID} WHERE acctID={$profileID}";
	$result = executePlainSQL($query);
	OCICommit($db_conn);
}

$profileID = $_GET['acctID'];
$userID = $_GET['userID'];
// $userID = 4;

$query = "SELECT AVG(SCORE) FROM REVIEWS WHERE REVIEWEEID={$profileID}";
$result = executePlainSQL($query);
$row = OCI_Fetch_Array($result, OCI_BOTH);
$avgScore = $row[0];

// Query logged in user accountID and see if it is employer or student, then pick what to show
$query = "SELECT isEmployer FROM Accounts WHERE acctID={$userID}";
$result = executePlainSQL($query);
$row = OCI_Fetch_Array($result, OCI_BOTH);
if ($row['ISEMPLOYER']==0) {
	$query = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE acctID ={$profileID}";
	$result = executePlainSQL($query);
	printResult($result, $profileID);
} else {
	$query = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE acctID ={$profileID}";
	$result = executePlainSQL($query);
	printResultForEmployers($result, $profileID, $userID);
}

function printResult($result, $profileID){
	global $userID;
	global $avgScore;
	while($row = OCI_Fetch_Array($result, OCI_BOTH)) {

		echo "Average Score: " . $avgScore . "<br>" . $row["SNAME"] . $row["NAME"]. "<br><br>";
		if ($userID == $profileID) {
			echo '<form action="profile.php" method="GET"><input type="hidden" name="acctID" value=' . $profileID . '><input type="hidden" name="userID" value=' . $userID . '><input type="text" name="newSchoolName"><input type="submit" value="Update School" name="updateSchool"></form>';
		}
		
		//Information for student profiles
		if (!is_null($row["SNAME"])){
			//ENDORSEMENTS
			$endo = executePlainSQL("SELECT * FROM Endorsements End, Employers Emp, Companies Cmp WHERE End.studentID={$profileID} AND End.employerID=Emp.acctID AND Emp.companyID=Cmp.companyID");
			echo "Endorsed by:";
			while ($e = OCI_Fetch_Array($endo, OCI_BOTH)){
				echo "<br>";
				echo $e["NAME"];
			}
			echo "<br><br>";
			
			//REVIEWS		
			echo "<table><tr><th>Reviews</th><th><a href=./reviews.php?revieweeID={$profileID}&reviewerID={$userID}>Post a review!</a></th></tr>";
			$receive = executePlainSQL("SELECT DISTINCT reviewID,reviewerID,courseNo,dept,sname,score,assignmentDesc,content,numLikes,numDislikes,datetime FROM Students NATURAL JOIN Accounts NATURAL JOIN Reviews NATURAL JOIN Schools WHERE revieweeID={$profileID}");
			
			while ($r = OCI_Fetch_Array($receive, OCI_BOTH)){
				$unprocesssedDate = $r["DATETIME"];
				$processedDate = substr($unprocesssedDate, 0, 14);

				echo "<tr><td>" . $r["DEPT"]. " ". $r ["COURSENO"] . " at " . $r["SNAME"];
				echo "<br>Score: " . $r["SCORE"] . " Given by ";
				
				//find reviewer name
				$otherID = $r["REVIEWERID"];
				$other = executePlainSQL("SELECT fname,lname FROM Accounts WHERE acctID ={$otherID}"); 
				$o = OCI_Fetch_Array($other, OCI_BOTH);
				$fullName = $o["FNAME"] . " " . $o["LNAME"];
				echo "<a href=./profile.php?acctID={$otherID}&userID={$userID}>{$fullName}</a>" . "<br>"; //prints nothing
				
				echo "Assignment Description: " . $r["ASSIGNMENTDESC"] . "<br>";
				echo "Content: " . $r["CONTENT"] . "<br>";
				echo "Posted at " . $processedDate;
				echo "<br><br></tr></td>";
			}
			echo "</table>";
		}
	}
}


function printResultForEmployers($result, $profileID, $userID) {
	global $avgScore;
	while($row = OCI_Fetch_Array($result, OCI_BOTH)) {

		echo "Average Score: " . $avgScore . "<br>" . $row["SNAME"] . $row["NAME"]. "<br><br>";
		
		//Information for student profiles
		if (!is_null($row["SNAME"])){
			//ENDORSEMENTS
			$endo = executePlainSQL("SELECT * FROM Endorsements End, Employers Emp, Companies Cmp WHERE End.studentID={$profileID} AND End.employerID=Emp.acctID AND Emp.companyID=Cmp.companyID");
			
			$endorsable = 1;

			echo "<table><tr><th>Endorsed by:</th></tr>";
			while ($e = OCI_Fetch_Array($endo, OCI_BOTH)){
				if ($e['EMPLOYERID'] != $userID) {
					echo "<tr><td>" . $e["NAME"] . "</td></tr>";
				} else {
					echo '<tr><td>' . $e["NAME"] . '</td><td> <form action="profile.php" method="GET"><input type="hidden" name="acctID" value=' . $profileID . '><input type="hidden" name="userID" value=' . $userID . '><input type="submit" value="Unendorse" name="unendorse"></form> </td></tr>';
					$endorsable = 0;
				}
			
			}
			echo "</table><br>";
			if ($endorsable) {
				echo '<form action="profile.php" method="GET"><input type="hidden" name="acctID" value=' . $profileID . '><input type="hidden" name="userID" value=' . $userID . '><input type="submit" value="Endorse!" name="endorse"></form>';
			}
			echo "<br><br>";

			//REVIEWS		
			echo "<table><tr><th>Reviews</th></tr>";
			$receive = executePlainSQL("SELECT DISTINCT reviewID,reviewerID,courseNo,dept,sname,score,assignmentDesc,content,numLikes,numDislikes,datetime FROM Students NATURAL JOIN Accounts NATURAL JOIN Reviews NATURAL JOIN Schools WHERE revieweeID={$profileID}");
			
			while ($r = OCI_Fetch_Array($receive, OCI_BOTH)){
				$unprocesssedDate = $r["DATETIME"];
				$processedDate = substr($unprocesssedDate, 0, 14);

				echo "<tr><td>" . $r["DEPT"]. " ". $r ["COURSENO"] . " at " . $r["SNAME"];
				echo "<br>Score: " . $r["SCORE"] . " Given by ";
				
				//find reviewer name
				$otherID = $r["REVIEWERID"];
				$other = executePlainSQL("SELECT fname,lname FROM Accounts WHERE acctID ={$otherID}"); 
				$o = OCI_Fetch_Array($other, OCI_BOTH);
				$fullName = $o["FNAME"] . " " . $o["LNAME"];
				echo "<a href=./profile.php?acctID={$otherID}&userID={$userID}>{$fullName}</a>" . "<br>";
				
				echo "Assignment Description: " . $r["ASSIGNMENTDESC"] . "<br>";
				echo "Content: " . $r["CONTENT"] . "<br>";
				echo "Posted at " . $processedDate;
				echo "<br><br></tr></td>";
			}
			echo "</table>";
		}
	}
}
?>
