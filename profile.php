<?php
	session_start();
?>
<html>
<head>
	<title>Profile</title>
</head>
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

function printResultForEmployers($result, $profileID, $userID) {
	global $avgScore;
	while($row = OCI_Fetch_Array($result, OCI_BOTH)) {

		echo $row["FNAME"] . " " . $row["LNAME"] . " - Average Score: " . $avgScore . "<br>" . $row["SNAME"] . $row["NAME"]. "<br><br>";
		
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
				echo "Posted at " . $processedDate;
				echo "<br><br></tr></td>";
			}
			echo "</table>";
		}
	}
}

function printResult($result, $profileID){
	global $userID;
	global $avgScore;
	while($row = OCI_Fetch_Array($result, OCI_BOTH)) {

		echo $row["FNAME"] . " " . $row["LNAME"] . " - Average Score: " . $avgScore . "<br>" . $row["SNAME"] . $row["NAME"]. "<br><br>";
		
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
				echo "Posted at " . $processedDate;
				echo "<br><br></tr></td>";
			}
			echo "</table>";
		}
	}
}
?>
