<?php
require_once("sec.php");

// check tha POST parameters
$u = $_POST['username'];
$p = $_POST['password'];

//var_dump(isUser($u, $p));
//die();

// Check if user is OK
if(isset($u) && isset($p) && isUser($u, $p)) {
	// set the session
	sec_session_start();
	$_SESSION['username'] = $u;
	$_SESSION['login_string'] = hash('sha512', "123456" +$u);
    // Lägger till sessionsvariabler --------
    $_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];

	header("Location: mess.php");
}
else {
	// To bad
	header('HTTP/1.1 401 Unauthorized');
	die("Unauthorized user!");
}