<html>
<head>
	<title> My Profile </title>
</head>
</html>
<?php
$success = True;
$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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


//$var = $_GET['acctID'];
$var = 1;
$result = NULL;

$query = "SELECT * FROM Students NATURAL JOIN Accounts NATURAL JOIN Schools WHERE acctID ='{$var}'";
$result = executePlainSQL($query);
printResult($result, $var);

//echo OCI_num_rows($result);
//if (is_null($result)) {
	$query = "SELECT * FROM Employers NATURAL JOIN Accounts NATURAL JOIN Companies WHERE acctID ='{$var}'";
	$result = executePlainSQL($query);
//}
printResult($result);

function printResult($result, $var){
	while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo $row["FNAME"] . " " . $row["LNAME"] . "<br>" . $row["SNAME"] . $row["NAME"]. "<br><br>";
		
		//Information for student profiles
		if (!is_null($row["SNAME"])){
			echo "Your Reviews <br>";
			
			echo "<table><tr><th>Received</th></tr>";
			$receive = executePlainSQL("SELECT DISTINCT reviewID,reviewerID,courseNo,dept,sname,score,assignmentDesc,content,numLikes,numDislikes FROM Students NATURAL JOIN Accounts NATURAL JOIN Reviews NATURAL JOIN Schools WHERE revieweeID ='{$var}'");
			
			while ($r = OCI_Fetch_Array($receive, OCI_BOTH)){				
				echo "<tr><td>" . $r["DEPT"]. " ". $r ["COURSENO"] . ", " . $r["SNAME"] . "<br>Given by ";
				
				//find reviewer name
				$otherID = $r["REVIEWERID"];
				echo $otherID;
				$other = executePlainSQL("SELECT fname,lname FROM Accounts WHERE acctID ='{$otherID}'"); 
				while ($o = OCI_Fetch_Array($other, OCI_BOTH));
				echo $o["FNAME"] . " " . $o["LNAME"] . "<br>"; //prints nothing
				
				echo "Assignment: " . $r["ASSIGNMENTDESC"] . " Score: " . $r["SCORE"] . "<br>";
				echo $r["CONTENT"] . "<br> Likes: " . $r["NUMLIKES"] . " Dislikes: " . $r["NUMDISLIKES"];
				echo "</tr></td>";
			}
			echo "</table>";
			
			echo "<table><tr><th>Given</th></tr>";
			$given = executePlainSQL("SELECT DISTINCT reviewID,revieweeID,courseNo,dept,sname,score,assignmentDesc,content,numLikes,numDislikes FROM Students NATURAL JOIN Accounts NATURAL JOIN Reviews NATURAL JOIN Schools WHERE reviewerID ='{$var}'");
			
			while ($g = OCI_Fetch_Array($given, OCI_BOTH)){				
				echo "<tr><td>" . $g["DEPT"]. " ". $g ["COURSENO"] . ", " . $g["SNAME"] . "<br>Given by ";
				
				//find reviewee name
				$otherID = $g["REVIEWEEID"];
				echo $otherID;
				$other = executePlainSQL("SELECT fname,lname FROM Accounts WHERE acctID ='{$otherID}'"); 
				while ($o = OCI_Fetch_Array($other, OCI_BOTH));
				echo $o["FNAME"] . " " . $o["LNAME"] . "<br>"; //prints nothing
				
				echo "Assignment: " . $g["ASSIGNMENTDESC"] . " Score: " . $g["SCORE"] . "<br>";
				echo $g["CONTENT"] . "<br> Likes: " . $g["NUMLIKES"] . " Dislikes: " . $g["NUMDISLIKES"];
				echo "</tr></td>";
			}
			echo "</table>";
			
		}
		//information for employer profiles
		else if (!is_null($row["NAME"])){
			echo endorse;
		}
	}
}
?>
