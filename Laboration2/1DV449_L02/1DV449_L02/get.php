<?php

// get the specific message
/*
function getMessages() {
	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	
	$q = "SELECT * FROM messages";
	
	$result;
	$stm;	
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}
	
	if($result)
		return $result;
	else
	 	return false;
} */

function getMessages($largestId){
	$db = null;
	$maxTime = time() + 20;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}

	while(time() < $maxTime) {
		$q = "SELECT * FROM messages WHERE serial > :largestId";
		$result = "";
		$stm = "";

		try {
			$stm = $db->prepare($q);
			$stm->bindParam(':largestId', $largestId, PDO::PARAM_INT);
			$stm->execute();
			$result = $stm->fetchAll();
		}
		catch(PDOException $e) {
			echo("Error creating query: " .$e->getMessage());
			return false;
		}

		if($result)
			return $result;
		else
			sleep(1);
	}
	return false;
}

// Just for testing purposes
// $resultArr = getMessages(72);
// var_dump($resultArr);