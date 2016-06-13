<?php
	$database = 'mysql:host=localhost;dbname=subastaDB;charset=utf8';
	$userDatabase = 'mysql';
	$passwordDatabase = 'sekret';
	try {
		$dbh = new PDO($database, $userDatabase, $passwordDatabase);
	} catch(PDOException $e) {
		die("Error: {$e->getMessage()}");
	}
?>
