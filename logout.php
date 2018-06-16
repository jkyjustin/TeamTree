<?php
	unset($_COOKIE['userID']);
	setcookie("userID", 1, time() - 3600);
	header('Location: ./index.php');
?>