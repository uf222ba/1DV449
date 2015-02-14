<?php

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

	session_write_close();

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
