<?php
	session_start();
?>
<html>
<head>
	<title>Profile</title>
</head>
</html>

<?php
$success = True;
$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
$profileID = $_GET['acctID'];

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

$query = "SELECT AVG(SCORE) FROM REVIEWS WHERE REVIEWEEID={$profileID}";
$result = executePlainSQL($query);
$row = OCI_Fetch_Array($result, OCI_BOTH);
$avgScore = $row[0];

$query = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE acctID ={$profileID}";
$result = executePlainSQL($query);
printResult($result, $profileID);

$query = "SELECT * FROM Employers NATURAL JOIN Accounts NATURAL JOIN Companies WHERE acctID ={$profileID}";
$result = executePlainSQL($query);

printResult($result);

function printResult($result, $profileID){
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
			echo "<table><tr><th>Reviews</th><th><a href=./reviews.php?revieweeID={$profileID}>Post a review!</a></th></tr>";
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
				echo "<a href=./profile.php?acctID={$otherID}>{$fullName}</a>" . "<br>"; //prints nothing
				
				echo "Assignment Description: " . $r["ASSIGNMENTDESC"] . "<br>";
				// echo $r["CONTENT"] . "<br> Likes: " . $r["NUMLIKES"] . " Dislikes: " . $r["NUMDISLIKES"];
				// echo "<br>";
				echo "Posted at " . $processedDate;
				echo "<br><br></tr></td>";
			}
			echo "</table>";
			
			// echo "<table><tr><th>Given</th></tr>";
			// $given = executePlainSQL("SELECT DISTINCT reviewID,revieweeID,courseNo,dept,sname,score,assignmentDesc,content,numLikes,numDislikes,datetime FROM Students NATURAL JOIN Accounts NATURAL JOIN Reviews NATURAL JOIN Schools WHERE reviewerID ='{$profileID}'");
			
			// while ($g = OCI_Fetch_Array($given, OCI_BOTH)){				
			// 	echo "<tr><td>" . $g["DEPT"]. " ". $g ["COURSENO"] . ", " . $g["SNAME"] . "<br>Given by ";
				
			// 	//find reviewee name
			// 	$otherID = $g["REVIEWEEID"];
			// 	echo $otherID;
			// 	$other = executePlainSQL("SELECT fname,lname FROM Accounts WHERE acctID ='{$otherID}'"); 
			// 	while ($o = OCI_Fetch_Array($other, OCI_BOTH));
			// 	echo $o["FNAME"] . " " . $o["LNAME"] . "<br>"; //prints nothing
				
			// 	echo "Assignment: " . $g["ASSIGNMENTDESC"] . " Score: " . $g["SCORE"] . "<br>";
			// 	echo $g["CONTENT"] . "<br> Likes: " . $g["NUMLIKES"] . " Dislikes: " . $g["NUMDISLIKES"] . " " . $g["DATETIME"];
			// 	echo "</tr></td>";
			// }
			// echo "</table>";
			
		}
		//information for employer profiles
		else if (!is_null($row["NAME"])){
			$cid = $row["COMPANYID"];
			$endo = executePlainSQL("SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Endorsements NATURAL JOIN Companies WHERE companyID ='{$cid}'");
			echo "<table><tr><th>Endorsing:</th></tr>";
			while ($e = OCI_Fetch_Array($endo, OCI_BOTH)){
				$profileLink = "<a href=profile.php?acctID=" . $e["ACCTID"] . "> View Profile </a>";
				echo "<tr><td>" . $e["FNAME"] . " " . $e["LNAME"] . " " .  $profileLink . "</td></tr>";
			}
			echo "</table>";
		}
	}
}
?>
