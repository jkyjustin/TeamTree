<?php

   $db_conn = OCILogon("ora_q7b7", "a68143064", "dbhost.ugrad.cs.ubc.ca:1522/ug");
   session_start();

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
   
    $user_id = $_SESSION['login_id'];
   
    $queryStr = "SELECT * FROM Accounts WHERE acctid='{$user_id}'";
    $result = executePlainSQL($queryStr);
    $row = OCI_Fetch_Array($result, OCI_BOTH);
   
    $login_fname = $row['FNAME'];
    $login_lname = $row['LNAME'];
    $login_type = $row['ISEMPLOYER'];

   	// if(!isset($_SESSION['login_id']))
   	// {
    //   header('location: index.php');
   	// }
?>