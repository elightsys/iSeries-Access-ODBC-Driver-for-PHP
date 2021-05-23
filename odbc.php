<?php
    // odbc.php
	try {
		$conn = new PDO('odbc:myDBdev');
		// The name is the same as what's in our square brackets in ODBC.ini
		$stmt = $conn->prepare("SELECT * FROM myTable WHERE id = :id");
		$stmt->execute(array('id' => 123));
		while ($row = $stmt->fetch()) {
			print_r($row);
			echo '';
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
?>