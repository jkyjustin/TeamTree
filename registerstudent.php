<html>
<style>
body {  
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
  background:white url("image/backg3.png") no-repeat left top;
}

table {
  border-spacing: 7px;
}
.error {color: #FF0000;}
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

/* Style the top navigation bar */
.navbar {
    overflow: hidden;
    background-color: #333;
    color: #228b22;
}

.search {background-color: #abb868;} 
.dash{background-color: #6c804b;} 
.log {background-color: #940016;} 

a:link {
    color: green;
}

/* visited link */
a:visited {
    color: green;
}
</style>
<body>

  <div class="header">
  </div>

  <div class = "navbar">
    <table><tr style="color: green;">
      <td width="66%"><h2>Student Account Registration</h2></td>
      <td><button class="button search" onclick="location.href='./profile_search.php'" style="width:auto;">Search Students</button><td>
      <td><button class="button dash" onclick="location.href='./dashboard.php'" style="width:auto;">Dashboard</button><td>
      <td><button class="button log" onclick="location.href='index.php'" style="width:auto;">Logout</button><td>
    </tr></table>
  </div>

  <p><span class="error">* required field</span></p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
    First Name: <input type="text" name="firstname" value="<?php echo $firstname;?>">
    <span class="error">* <?php echo $firstnameErr;?></span>
    <br><br>
    Last Name: <input type="text" name="lastname" value="<?php echo $lastname;?>">
    <span class="error">* <?php echo $lastnameErr;?></span>
    <br><br>
    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error">* <?php echo $emailErr;?></span>
    <br><br>
    Password: <input type="text" name="password" value="<?php echo $password;?>">
    <span class="error">* <?php echo $passwordErr;?></span>
    <br><br>
    School: <input type="text" name="school" value="<?php echo $school;?>">
    <span class="error">* <?php echo $schoolErr;?></span>
    <br><br>
    <td><button class="button button-block" name="submit" style="width:auto;">Submit</button></td>
  </form>

</body>
</html>

<?php
$firstnameErr = $lastname = $emailErr = $passwordErr = $schoolErr = "";
$firstname = $lastname = $email = $password = $school = $result = $schoolId = "";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["firstname"])) {
    $firstnameErr = "First name is required";
  } else {
    $firstname = test_input($_POST["firstname"]);
    if (!preg_match("/[a-zA-Z]/",$firstname)) {
      $firstnameErr = "Only letters allowed"; 
    }
  }

  if (empty($_POST["lastname"])) {
    $lastnameErr = "Last name is required";
  } else {
    $lastname = test_input($_POST["lastname"]);
    if (!preg_match("/[a-zA-Z]/",$lastname)) {
      $lastnameErr = "Only letters allowed"; 
    }
  }
  
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format"; 
    }
  }
    
  if (empty($_POST["school"])) {
    $schoolErr = "School is required";
  } else {
    if (get_query_count("SELECT * FROM Schools WHERE UPPER(sname) = UPPER('{$_POST["school"]}')") < 1) {
      $schoolErr = "School does not exist";
    } else {
      $result = executePlainSQL("SELECT schoolID FROM Schools WHERE UPPER(sname) = UPPER('{$_POST["school"]}')");
      $rslt2 = OCI_Fetch_Array($result, OCI_BOTH);
      $schoolId = $rslt2["SCHOOLID"];
      $school = test_input($_POST["school"]);
    }
  }

  if (empty($_POST["password"])) {
    $passwordErr = "Pasword is required";
  } else {
    $password = test_input($_POST["password"]);
  }

  if ($passwordErr == "" && $emailErr == "" && $schoolErr == "" && $firstnameErr == "" && $lastnameErr !== "") {
    $queryStr = "SELECT * FROM Accounts WHERE UPPER(fname) = UPPER('{$firstname}') AND UPPER(lname) = UPPER('{$lastname}') AND UPPER(email) = UPPER('{$email}')";

    if(get_query_count($queryStr) > 0){
      echo "This combination of first name, last name and email are taken";
    } else {
      $account_id = generateAcctId();
      executePlainSQL("INSERT INTO Accounts VALUES({$account_id}, '{$firstname}', '{$lastname}', '{$email}', '{$password}', 0)");
      executePLainSQL("INSERT INTO Students VALUES ({$account_id}, '{$schoolId}')");
      OCI_commit($db_conn);
      header('Location: ./index.php');
    }
  }
}

function generateAcctId() {
  $query = "SELECT MAX(ACCTID) FROM ACCOUNTS";
  $result = executePlainSQL($query);
  $num = OCI_Fetch_Array($result);
  return $num[0] + 1;
}

function get_query_count($input) {
  $result = executePlainSQL($input);
  while($res = oci_fetch_array($result)){
    $data[] = $res;
  }
  return count($data);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
