<?php
require_once("get.php");
require_once("sec.php");
require_once("post.php");
require_once("Token.php");

/*
* It's here all the ajax calls goes
*/

if(isset($_GET['function']) || isset($_POST['function'])) {

	if($_GET['function'] == 'logout') {
		logout();
    } 
    elseif($_POST['function'] == 'add') {
		if (isset($_POST['token']) && Token::check($_POST['token']) && isset($_POST["name"]) && isset($_POST["message"])) {

            // Namn
			$name = sanitizeInput($_POST["name"]);
			if(strlen($name) > 150)
				$name = substr($name, 0, 149);

            // Meddelande
			$message = sanitizeInput($_POST["message"]);

            // Strängarna måste vara minst ett tecken långa för att förhindra att tomma meddelande läggs till i databasen
            if(strlen($name) > 0 && strlen($message) > 0)
                addToDB($message, $name);
            else
                header("Location: mess.php");

			session_write_close();
		} else {
			header("Location: mess.php");
		}

		//Sanitize input
		///Kolla att de postade variablerna isset
    }
    elseif($_GET['function'] == 'getMessages') {
  	   	$serial = 0;
		if(isset($_GET['serial']))
			$serial = $_GET['serial'];
		echo(json_encode(getMessages($serial)));
		//session_write_close();
    }
}
// Function for cleaning user input data
function sanitizeInput($data) {
	$data = trim($data);
	$data = strip_tags($data);
	//$data = stripslashes($data);
	return $data;
}