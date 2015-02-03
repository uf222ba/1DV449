<?php
require_once("get.php");
require_once("post.php");

/*
* It's here all the ajax calls goes
*/
if(isset($_GET['function']) || isset($_POST['function'])) {

	if($_GET['function'] == 'logout') {
		logout();
    } 
    elseif($_POST['function'] == 'add') {
			$name = $_POST["name"];
			$message = $_POST["message"];
			addToDB($message, $name);
			//header("Location: test/debug.php");

    }
    elseif($_GET['function'] == 'getMessages') {
  	   	$serial = 0;
		if(isset($_GET['serial']))
			$serial = $_GET['serial'];
		echo(json_encode(getMessages($serial)));
    }
}