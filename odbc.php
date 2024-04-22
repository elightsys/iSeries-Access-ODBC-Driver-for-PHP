<?php
	// Example odbc.php
	try {
		$conn = new PDO('odbc:myDBdev'); // The name matches what's in the ODBC.ini file
		$stmt = $conn->prepare("SELECT * FROM myTable WHERE id = :id");
		$stmt->execute(['id' => 123]);
		while ($row = $stmt->fetch()) {
			print_r($row);
			echo PHP_EOL;  // Improved line break for clarity
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
?>
