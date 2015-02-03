<?php
require_once("sec.php");
sec_session_start();    // Starta sessionen
checkUser();            // Kolla att användaren, webbläsare och IP-adress är ok
/**
* Called from AJAX to add stuff to DB
*/
function addToDB($message, $user) {
	$db = null;
    $stm = "";
    $result = "";

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Something went wrong -> " .$e->getMessage());
	}
	
	$q = "INSERT INTO messages (message, name, timestamp) VALUES(:message, :user, time())";

	try {
        $stm = $db->prepare($q);
        $stm->bindParam(':user', $user);
        $stm->bindParam(':message', $message);
		if(!$stm->execute()) {
        }
	}
	catch(PDOException $e) {
        die($e);
    }
/*
	$q = "SELECT * FROM users WHERE username = :user";

	try {
		$stm = $db->prepare($q);
        $stm->bindParam(':user', $user);
		$stm->execute();
		$result = $stm->fetchAll();
		if(!$result) {
			return "Could not find the user";
		}
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}
	// Send the message back to the client
	echo "Message saved by user: " .json_encode($result);
	*/
}

// Just for testing purposes
//$result = addToDB("Meddelande", "user");
//var_dump($result);

