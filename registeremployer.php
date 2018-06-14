<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

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
      $companyId = $rslt2["companyID"];
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
      $account_id = get_query_count("SELECT * FROM Accounts") + 1;
      $employer_id = get_query_count("SELECT * FROM Employers") + 1;
      executePlainSQL("INSERT INTO Accounts VALUES('{$account_id}', '{$firstname}', '{$lastname}', '{$email}', '{$password}', 0)");
      executePLainSQL("INSERT INTO Employers VALUES ('{$employer_id}', '{$companyId}')");
      OCI_commit($db_conn);
      header("Location:./index.php");
    }
  }
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

<h2>Create Student Account</h2>
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

<?php
echo "<h2>Your Input:</h2>";
echo $firstname;
echo "<br>";
echo $lastname;
echo "<br>";
echo $email;
echo "<br>";
echo $password;
echo "<br>";
echo $company;
echo "<br";
echo $result;
?>

</body>
</html>