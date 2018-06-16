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

  <h2>Register a Student Account</h2>
</div>

<div class = "body">
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
    <input type="submit" name="submit" value="Submit">  
  </form>
</div>
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
      echo "<a href=./index.php>Go back to index</a>";
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
