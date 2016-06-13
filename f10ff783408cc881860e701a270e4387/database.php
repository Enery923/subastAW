<?php
	$database = 'mysql:host=localhost;dbname=f10ff783408cc881860e701a270e4387;charset=utf8';
	$userDatabase = 'f10ff783408cc881';
	$passwordDatabase = 'sekret';
	try {
		$dbh = new PDO($database, $userDatabase, $passwordDatabase);
	} catch(PDOException $e) {
		die("Error: {$e->getMessage()}");
	}
?>
