<?php

/**
Just som simple scripts for session handling
*/
function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params(3600, $cookieParams["path"], $cookieParams["domain"], $secure, false);
        $httponly = true; // This stops javascript being able to access the session id.
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
}

function checkUser() {
	if(!session_id()) {
		sec_session_start();
	}

	if(!isset($_SESSION["username"])) {
        header('HTTP/1.1 401 Unauthorized');
        die();
    }

	$user = getUser($_SESSION["username"]);
	$un = $user[0]["username"];

	if(isset($_SESSION['login_string']) && ($_SERVER['HTTP_USER_AGENT'] == $_SESSION['browser']) && ($_SERVER['REMOTE_ADDR'] == $_SESSION['ip'])) {
		if($_SESSION['login_string'] !== hash('sha512', "123456" + $un)) {
			header('HTTP/1.1 401 Unauthorized');
            echo 'HTTP/1.1 401 Unauthorized';
            die();
		}
	}
	else {
		header('HTTP/1.1 401 Unauthorized');
        echo 'HTTP/1.1 401 Unauthorized';
        die();
	}
	return true;
}

function isUser($u, $p) {
	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	$q = "SELECT id, password FROM users WHERE username = :user"; // Ändrat till parametriserad fråga och lagt till lösenordskolumnen i frågan  "AND password = :pwd"

	$result;
	$stm;
    $userIsAuthenticated;   // lagt till variabel som returneras på slutet

	try {
		$stm = $db->prepare($q);
        $stm->bindParam(':user', $u);       // Lagt till bindparam
 		$stm->execute();
		$result = $stm->fetchAll();
        if($result) {
            if(password_verify($p, $result[0]["password"])) // Kollar om lösenordet stämmer med det hashade i databasen
                $userIsAuthenticated = true;
            else
                $userIsAuthenticated = false;
		} else {
            $userIsAuthenticated = false;
        }
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}
	return $userIsAuthenticated;
}

function getUser($user) {
	$db = null;
    $result;
    $stm;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	$q = "SELECT * FROM users WHERE username = :user"; // Ändrat till parametriserad fråga

	try {
		$stm = $db->prepare($q);
        $stm->bindParam(':user', $user);        //Lagt till bindparam
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}

	return $result;
}

function logout() {

	if(!session_id()) {
		sec_session_start();
	}
	session_end();
	header('Location: index.php');
}

