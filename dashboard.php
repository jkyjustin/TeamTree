<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
  background:white url("image/backg3.png") no-repeat left top;
}

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
}

.search {background-color: #abb868;} 

.dash{background-color: #6c804b;} 

.log {background-color: #940016;} 

.navbar button{
  position: top;
  border-radius: 30%;
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
    <td width="67%"><img src="image/dash.gif" alt="dash" style="width:300px;height:60px;border:0"></td>
    <td><button class="button search" onclick="location.href='./profile_search.php'" style="width:auto;">Search Students</button><td>
    <td><button class="button dash" onclick="location.href='./dashboard.php'" style="width:auto;">Dashboard</button><td>
    <td><button class="button log" onclick="location.href='index.php'" style="width:auto;">Logout</button><td>
  </tr></table>
</div>

<form method="post">
<t><b>Find the lowest or highest average of reviews among Schools</b></t><br>
<input type="submit" name="groupMin" value="min"/>
<input type="submit" name="groupMax" value="max"/>
</form>

<form method="post">
<t><b>This will query for all users who ONLY got reviews from user with the inputted id:</b></t><br>
Id: <input type="text" name="AccountId" value="<?php $account_id;?>">
<input type="submit" name="query" value="Divide Query"/>
</form>

<?php echo generateSchoolTable();?>
</body>
</html>


<?php
//Global Variables
$account_id = "";

//Listener on post requests
if(isset($_POST['1'])) {
  echo generateLowHighAvgTable(1);
} else if(isset($_POST['2'])) {
  echo generateLowHighAvgTable(2);
} else if(isset($_POST['query'])) {
  echo generateDivideQuery($_POST['AccountId']);
} else if(isset($_POST['groupMin'])) {
  echo generateSchoolsMin();
} else if(isset($_POST['groupMax'])) {
  echo generateSchoolsMax();
}

function generateSchoolsMax() {
  $query = executePlainSQL("SELECT MAX(AVERAGE) FROM (SELECT SCHOOLID, AVG(SCORE) AS AVERAGE FROM REVIEWS GROUP BY SCHOOLID)");
  $result = OCI_Fetch_Array($query);
  return "<p>Highest school average for reviews = ".round($result[0],2)."</p>";
}

function generateSchoolsMin() {
  $query = executePlainSQL("SELECT MIN(AVERAGE) FROM (SELECT SCHOOLID, AVG(SCORE) AS AVERAGE FROM REVIEWS GROUP BY SCHOOLID)");
  $result = OCI_Fetch_Array($query);
  return "<p>Lowest school average for reviews = ".round($result[0],2)."</p>";
}

function generateSchoolsAvg() {
  return "SELECT SCHOOLID, AVG(SCORE) FROM REVIEWS GROUP BY SCHOOLID";
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
  $table .= '</p></td><td><p>Avg = '.round($avg[0], 2).'</p></td>';

  return $table;
}

function generateDivideQuery($id) {
  $max_query = "SELECT MAX(REVIEWERID) FROM REVIEWS";
  $max_query_result = executePlainSQL($max_query);
  $max_query_result_array = OCI_Fetch_Array($max_query_result);

  if ($id == null) {
    return "<p>REQUIRE USER ID</p>";
  } else if ($id > $max_query_result_array[0] || $id < 1) {
    return "<p>Invalid ID</p>";
  } else {
    $result = "<p>Relational Algebra: PROJECTION fname(Accounts JOIN [PROJECTION revieweeId(Reviews) DIVIDES PROJECTION revieweeId(SELECTION reviewerId= <b>".$id."</b> (Reviews))])</p>";
    $result .= "<b>Names:</b>";
    $query_res = executePlainSQL("SELECT FNAME FROM ACCOUNTS a WHERE NOT EXISTS ((SELECT r.REVIEWEEID FROM REVIEWS r WHERE r.REVIEWERID = {$id}) MINUS (SELECT r1.REVIEWEEID FROM REVIEWS r1 WHERE r1.REVIEWEEID = a.ACCTID))");
  
    while($row = OCI_Fetch_Array($query_res, OCI_BOTH)) {
      $result .= "<p>".$row[0]."</p>";
    }

    return $result;
  }
}

function executePlainSQL($cmdstr) {
  $db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
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