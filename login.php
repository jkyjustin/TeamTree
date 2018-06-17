<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
</head>

<style>

body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
  background:white url("image/backg3.png") no-repeat left top;
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
    padding-top: 4px;
    background-color: #333;
}

.form {
  text-align:center;
  width: 300px;
  margin: 0 auto;

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

img.avatar {
    width: 50%;
    border-radius: 50%;
}

</style>


<body>
<div class="header">
</div>

<div class = "navbar">
<a href="index.php">
  <img src="image/home.png" alt="HOME" style="width:42px;height:42px;border:0">
</a>

</div>
<div class="bod">
  <div class="form">
      
      
      <div class="tab-content">

         <div id="login">   
          <h1>Login</h1>
        
          <img src="image/t1.png" alt="Tree" class="avatar">
     
          
          <form action="" method="post" autocomplete="off">
          
          <label for="uname"><b>Username</b></label>
          <input type="text" placeholder="Enter E-mail" name="uname" required>

          <label for="psw"><b>Password</b></label>
          <input type="password" placeholder="Enter Password" name="psw" required>

          <button class="button button-block" name="login" style="width:auto;">Login</button>
          
          </form>

        </div>
     
        
      </div><!-- tab-content -->
      
</div> <!-- /form -->
</div>
</body>
</html>

 

<?php 

  $db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");

  function executePlainSQL($cmdstr) 
  {
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) 
    {
      echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
      $e = OCI_Error($db_conn);
      echo htmlentities($e['message']);
      $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) 
    {
      echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
      $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
      echo htmlentities($e['message']);
      $success = False;
    } else {

    }
    return $statement;
  }


  if($_SERVER['REQUEST_METHOD'] == 'POST') 
  {
      // username and password sent from form 
        $user = $_POST['uname'];
        $password = $_POST['psw'];
        
        $queryStr = "SELECT COUNT(*) FROM Accounts WHERE email='{$user}' AND password='{$password}'";

        $result = executePlainSQL($queryStr);
        $row = OCI_Fetch_Array($result, OCI_BOTH);

        $count = $row[0];

        //echo $count;
        
        // If result matched $user and $password, table row must be 1 row
        if($count == 1) 
        {

          $queryString = "SELECT acctid FROM Accounts WHERE email='{$user}' AND password='{$password}'";
          $res = executePlainSQL($queryString);
          $userRow = OCI_Fetch_Array($res, OCI_BOTH);

          $userid = $userRow['ACCTID'];

          $_SESSION['login_id'] = $userid;
          $_SESSION['password'] = $password;

          setcookie("userID", $userid, time() + 3600, "/");

          //$profileLink = "profile.php?acctID=" . $userRow["ACCTID"] . "&userID=" . $userid";
          //echo $profileLink;
          //$profileLink = "<a href=profile.php?acctID=" . $row["ACCTID"] . "&userID=" . $userID . "> View Profile </a>";
           
           header("location: dashboard.php");

        
      }else 
        {
          echo "<p align='center'> <font color=red  size='3pt'>Your Login username or Password is invalid</font> </p>";
        }
     }
      

?>



