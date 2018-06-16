<html>
<style>
body {  
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
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

/* Style the top navigation bar */
.navbar {
    overflow: hidden;
    padding-top: 0px;
    background-color: #333;
    color: #228b22;
}

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
      <td width="68%"><h2>Register a Student Account</h2></td>
      <td align="right"><h2><a href="./profile_search.php">Search Students</a></h2></td>
      <td align="right"><h2><a href="./dashboard.php">Dashboard</a></h2></td>
      <td><form action="logout.php" method="GET"><input type="submit" value="Logout"></form></td>
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
    Company: <input type="text" name="company" value="<?php echo $company;?>">
    <span class="error">* <?php echo $companyErr;?></span>
    <br><br>
    <input type="submit" name="submit" value="Submit">  
  </form>

</body>
</html>

<?php
$firstnameErr = $lastname = $emailErr = $passwordErr = $companyErr = "";
$firstname = $lastname = $email = $password = $company = $result = $companyId = "";

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
    
  if (empty($_POST["company"])) {
    $companyErr = "company is required";
  } else {
    if (get_query_count("SELECT * FROM Companies WHERE UPPER(name) = UPPER('{$_POST["company"]}')") < 1) {
      $companyErr = "company does not exist";
    } else {
      $result = executePlainSQL("SELECT companyID FROM Companies WHERE UPPER(name) = UPPER('{$_POST["company"]}')");
      $rslt2 = OCI_Fetch_Array($result, OCI_BOTH);
      $companyId = $rslt2["COMPANYID"];
      $company = test_input($_POST["company"]);
    }
  }

  if (empty($_POST["password"])) {
    $passwordErr = "Pasword is required";
  } else {
    $password = test_input($_POST["password"]);
  }

  if ($passwordErr == "" && $emailErr == "" && $companyErr == "" && $firstnameErr == "" && $lastnameErr !== "") {
    $queryStr = "SELECT * FROM Accounts WHERE UPPER(fname) = UPPER('{$firstname}') AND UPPER(lname) = UPPER('{$lastname}') AND UPPER(email) = UPPER('{$email}')";

    if(get_query_count($queryStr) > 0){
      echo "This combination of first name, last name and email are taken";
    } else {
      $account_id = generateAcctId();
      executePlainSQL("INSERT INTO Accounts VALUES({$account_id}, '{$firstname}', '{$lastname}', '{$email}', '{$password}', 0)");
      executePLainSQL("INSERT INTO Employers VALUES ({$account_id}, {$companyId})");
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
