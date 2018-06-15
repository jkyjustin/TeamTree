<?php
	// session_start();
	// var_dump(session_id());
	// phpinfo();
	// $_SESSION['reeID'] = $_GET['revieweeID'];
	// echo $_SESSION['revieweeID'];
?>
<html>
<body>

<form action="reviews.php" method="POST" id="reviewform">
	<table border="0">
		<tr>
			<td>Rating (1 to 5)</td>
			<td align="left"><input type="number" name="score" min="1" max="5" /></td>
		</tr>

		<tr>
			<td>Assignment Description</td>
			<td align="left">
				<textarea rows="4" cols="50" name="assignmentDesc" maxlength="255" form="reviewform">255 char max</textarea>
			</td>
		</tr>

		<tr>
			<td>Review</td>
			<td>
				<textarea rows="4" cols="50" name="content" maxlength="1000" form="reviewform">1000 char max</textarea>
			</td>
		</tr>

		<tr>
			<td>Course Department</td>
			<td align="left"><input type="text" name="dept" size="6" minlength="4" maxlength="4" /></td>
		</tr>

		<tr>
			<td>Course Number</td>
			<td align="left"><input type="number" name="courseNo" min="100" max="999" /></td>
		</tr>

		<tr>
			<td><input type="hidden" name="revieweeToken" value="<?=$_GET['revieweeID'];?>" /></td>
		</tr>

		<tr>
			<td><input type="hidden" name="reviewerToken" value="<?=$_GET['reviewerID'];?>" /></td>
		</tr>
		<tr>

			<td>  </td>
			<td colspan="2" align="left"><input type="submit" name="Submit"/></td>
		</tr>
	</table>
</form>

</body>
</html>

<?php
	// db login
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
		}

		return $statement;
	}

	function getSchoolId($acctID) {
		$queryStr = "SELECT schoolID FROM Accounts NATURAL JOIN Students WHERE acctID={$acctID}";
		$result = executePlainSQL($queryStr);
		
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		return $row['SCHOOLID'];
	}

	function generateReviewId() {
		$queryStr = "SELECT MAX(reviewID) AS MaxID FROM Reviews";
		$result = executePlainSQL($queryStr);
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		return $row['MAXID'] + 1;
	}

	function checkCourseExistsOrCreate($dept, $courseNo, $schoolID) {
		$queryStr = "SELECT COUNT(*) FROM Courses WHERE courseNo={$courseNo} AND dept='{$dept}' AND schoolID={$schoolID}";
		$result = executePlainSQL($queryStr);
		$row = OCI_Fetch_Array($result, OCI_BOTH);

		if ($row[0]==0) {
			$queryStr1 = "INSERT INTO Courses VALUES ({$courseNo}, '{$dept}', {$schoolID})";
			executePlainSQL($queryStr1);
			OCICommit($db_conn);
		}

	}

	if ($db_conn) {
		if (isset($_POST['Submit'])) {
			$reviewerId = $_POST["reviewerToken"];
			$revieweeId = $_POST["revieweeToken"];

			if ($reviewerId == $revieweeId) {
				exit("Cannot post reviews on your own profile.");
			}

			$reviewerSchoolId = getSchoolId($reviewerId);
			$revieweeSchoolId = getSchoolId($revieweeId);	

			if ($reviewerSchoolId != $revieweeSchoolId) {
				exit("Must attend same school to post review.");
			}

			$reviewId = generateReviewId();

			if (empty($_POST["courseNo"])) {
				exit("Please enter a course number.");
			}

			if (empty($_POST["dept"])) {
				exit("Please enter a school department.");
			}

			if (empty($_POST["score"])) {
				exit("Please enter a score.");
			}

			if (empty($_POST["assignmentDesc"])) {
				exit("Please write a description of the assignment.");
			}

			if (empty($_POST["content"])) {
				exit("Please write about how it was like working with your teammate.");
			}

			$courseNo = $_POST["courseNo"];
			$dept = strtoupper($_POST["dept"]);
			$score = $_POST["score"];
			$assignmentDesc = $_POST["assignmentDesc"];
			$content = $_POST["content"];
			$numLikes = 0;
			$numDislikes = 0;

			checkCourseExistsOrCreate($dept, $courseNo, $revieweeSchoolId);

			if ($success) {
				$queryStr = "insert into reviews values (CURRENT_TIMESTAMP, {$reviewId}, {$reviewerId}, {$revieweeId}, {$courseNo}, '{$dept}', '{$revieweeSchoolId}', {$score}, '{$assignmentDesc}', '{$content}', 0, 0)";
					executePlainSQL($queryStr);
					OCICommit($db_conn);
			}	else {
				exit("Failed to post review, please try again.");
			}
		}

		OCILogoff($db_conn);
	} 
	else {	
		echo "cannot connect";
		$e = OCI_Error();
		echo htmlentities($e['message']);
	}
?>