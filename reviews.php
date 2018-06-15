<?php
	// session_start();
	// var_dump(session_id());
	// phpinfo();
	// $_SESSION['reeID'] = $_GET['revieweeID'];
	// echo $_SESSION['revieweeID'];
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
</style>
</head>
<body>

<div class="header">
</div>

<div class = "navbar">
<!--   <button onclick="document.getElementById('id01').style.display='block'"
  style="width:auto;">Login</button> -->

  <h2><?php echo getName(); ?></h2>
</div>

<div class = "body">
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
</div>
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

	function getName() {
		global $profileID;
		$query = "SELECT fname, lname FROM Accounts WHERE acctID={$_GET['revieweeID']}";
		$result = executePlainSQL($query);
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		$fname = $row['FNAME'];
		$lname = $row['LNAME'];
		return $fname . ' ' .$lname;
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