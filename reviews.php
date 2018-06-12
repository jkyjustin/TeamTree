<html>
	<body>

		<form action="reviews.php" method="post" id="reviewform">
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
				<td colspan="2" align="center"><input type="submit" value="Submit Review"/></td>
			</tr>
		</table>
		</form>

	</body>
</html>

<?php
	// db login
	$success = True; //keep track of errors so it redirects the page only if there are no errors
	$db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");

	// $reviewerId = 1;	// NEED TO DYNAMICALLY GENERATE THESE.. with sessionID maybe
	// $revieweeId = 2;
	// $schoolId = 1;		// Same as ^
	// $reviewId = 1; // NEED TO AUTOINCREMENT THIS IN DB
	// $courseNo = ;
	// $dept = ;
	// $score = ;
	// $assignmentDesc = ;
	// $assignmentContent = ;
	// $numLikes = 0;
	// $numDislikes = 0;

	// if courseNo + dept + schoolID doesn't exist in schools, insert one
	// Then make review obj
?>