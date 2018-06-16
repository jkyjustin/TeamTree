<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
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
}

.navbar button{
  position: top;
  border-radius: 30%;
}

/* Style for navbar */
.body {
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
    <td width="68%"><h2>Dashboard</h2></td>
    <td align="right"><h2><a href="./profile_search.php">Search Students</a></h2></td>
    <td align="right"><h2><a href="./dashboard.php">Dashboard</a></h2></td>
    <td><form action="logout.php" method="GET"><input type="submit" value="Logout"></form></td>
  </tr></table>
</div>

<?php echo generateSchoolTable();?>

</body>
</html>


<?php
//Listener on post requests
if(isset($_POST['1'])) {
  echo generateLowHighAvgTable(1);
} else if(isset($_POST['2'])) {
  echo generateLowHighAvgTable(2);
}

function generateSchoolTable() {
  $res = executePlainSQL("SELECT DISTINCT(SNAME), SCHOOLID FROM SCHOOLS");
  $html = '<table border="1">';
  while ($row = OCI_Fetch_Array($res, OCI_BOTH)) {
    $html .= '<tr><td>'.$row['SNAME'].'</td>';
    $html .= '<form method="post"><td><input type="submit" name="'.$row['SCHOOLID'].'" value="MIN/MAX/AVG"/> </td>';
    $html .= '</form></tr>';
  }
  return $html;
}

function generateLowHighAvgTable($id) {
  $sql_statement = "SELECT r.SCORE FROM REVIEWS r WHERE r.SCHOOLID = '{$id}' AND r.SCORE NOT IN ";
  $high_query = executePlainSQL($sql_statement."(SELECT r1.SCORE FROM REVIEWS r1, REVIEWS r2 WHERE r1.SCORE < r2.SCORE)");
  $low_query = executePlainSQL($sql_statement."(SELECT r1.SCORE FROM REVIEWS r1, REVIEWS r2 WHERE r1.SCORE > r2.SCORE)");
  $avg_query = executePlainSQL("SELECT AVG(r.SCORE) FROM REVIEWS r WHERE r.SCHOOLID = '{$id}'");

  $high = OCI_Fetch_Array($high_query, OCI_BOTH);
  $low = OCI_Fetch_Array($low_query, OCI_BOTH);
  $avg = OCI_Fetch_Array($avg_query, OCI_BOTH);

  $table = '<table border="1"><td><p>Low = '.$low[0];
  $table .= '</p></td><td><p>High = '.$high[0];
  $table .= '</p></td><td><p>Avg = '.$avg[0] .'</p></td>';

  return $table;
}

function executePlainSQL($cmdstr) {
  $db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
  $userID = 1;
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
	} else {}
	return $statement;
}
?>