<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
}

/* Full-width input fields */
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
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
  padding: 16px;
  font-size: 16px;
}
.dropbtn {
    position: top;
    border-radius: 30%;
    background-color: #3498DB;
    color: white;
    padding: 16px;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

.dropbtn:hover, .dropbtn:focus {
    background-color: #2980B9;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f1f1f1;
    min-width: 160px;
    overflow: auto;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}


.dropdown a:hover {background-color: #ddd;}

.show {display: block;}


/* Style for navbar */
.body {
  background:white url("image/backg3.png") no-repeat left top;
  width: 100%;
  padding: 300px;
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
  <button onclick="document.getElementById('id01').style.display='block'"
  style="width:auto;">Login</button>

  <button onclick="dropMenu()" class="dropbtn" style="width:auto;">Register</button>
  <div id="drop" class="dropdown-content">
  <a href="#Student_registrationpage">Student</a>
  <a href="#Employer_registrationpage">Employer</a>

</div>

<div class = "body">
</div>

<div id="id01" class="modal">

  <form class="modal-content animate" action="" method="post">

    <div class="imgcontainer">
      <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
      <img src="image/t1.png" alt="Tree" class="avatar">
    </div>

    <div class="container">
      <label for="uname"><b>Username</b></label>
      <input type="text" placeholder="Enter Username" name="uname" required>

      <label for="psw"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="psw" required>

      <button type="submit" name="submit" >Login</button>
      <label>
        <input type="checkbox" checked="checked" name="remember"> Remember me
      </label>
    </div>

    <div class="container" style="background-color:#f1f1f1">
      <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
      <span class="psw">Forgot <a href="#">password?</a></span>
    </div>
  </form>
</div>

<script>

function dropMenu(){
    document.getElementById("drop").classList.toggle("show");
}

window.onclick = function(event)
{
    // Get the modal
    var modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    if (event.target == modal)
    {
        modal.style.display = "none";
    }

    // Close the dropdown if the user clicks outside of it
    else if (!event.target.matches('.dropbtn'))
    {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var i;
      for (i = 0; i < dropdowns.length; i++)
      {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show'))
        {
          openDropdown.classList.remove('show');
        }
      }
    }
}
</script>

</body>
</html>


<?php
   //include("session.php");        //include this on profile page 
   session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") 
   {
      // username and password sent from form 
      $user = $_POST['uname'];
      $password = $_POST['psw'];
      
      $queryStr = "SELECT COUNT(*) FROM Accounts WHERE email='{$user}' AND password='{$pass}'";
      $result = executePlainSQL($queryStr);
      $row = OCI_Fetch_Array($result, OCI_BOTH);
  
      $count = $row[1];
      
      // If result matched $user and $password, table row must be 1 row
      if($count == 1) 
      {
        $queryString = "SELECT acctid FROM Accounts WHERE email='{$user}' AND password='{$password}'";
        $res = executePlainSQL($queryString);
        $userRow = OCI_Fetch_Array($res, OCI_BOTH);

        $userid = $userRow['ACCTID'];
        session_register("userid");
        $_SESSION['login_id'] = $userid;
         
        header("location: profile.php");
      }else 
      {
         $error = "Your Login Name or Password is invalid";
      }
   }
?>

