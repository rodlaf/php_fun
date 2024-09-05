<!DOCTYPE html>
<html>
<head>
    <title>PHP Fun</title>
    <style>
        table {
            border-collapse: collapse;
        }

        td {
            padding: 10px;
            text-align: center;
            width: 60px;
            height: 60px;
        }
    </style>
</head>
<body>
    <h1>PHP Fun</h1>

    <?php
    $servername = "localhost";
    $username = "root";
    $dbname = "php_fun_db";

    // Check if the database exists, create it if not
    try {
        $conn = new PDO("mysql:host=$servername", $username);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        $conn->exec($sql);
        $conn->exec("USE $dbname");
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit; // Stop executing the code if connection fails
    }

    // Set the number of rows and columns
    $numRows = 15;
    $numColumns = 15;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit; // Stop executing the code if connection fails
    }

    // Check if the table exists, create it if not
    $tableName = "button_grid";
    $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            value INT(6) NOT NULL
    )";

    try {
        $conn->exec($sql);
    } catch(PDOException $e) {
        echo "Error creating table: " . $e->getMessage();
        exit; // Stop executing the code if table creation fails
    }

    // Function to retrieve the current value of a button
    function getButtonValue($conn, $buttonId) {
        global $tableName; // Add this line to access the $tableName variable inside the function
        try {
            $stmt = $conn->prepare("SELECT value FROM $tableName WHERE id = :id");
            $stmt->bindParam(':id', $buttonId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['value'] : 0;
        } catch(PDOException $e) {
            echo "Error retrieving button value: " . $e->getMessage();
            exit; // Stop executing the code if an error occurs
        }
    }

    // Function to update the value of a button
    function updateButtonValue($conn, $buttonId, $newValue) {
        global $tableName; // Add this line to access the $tableName variable inside the function
        try {
            $stmt = $conn->prepare("UPDATE $tableName SET value = :value WHERE id = :id");
            $stmt->bindParam(':value', $newValue);
            $stmt->bindParam(':id', $buttonId);
            $stmt->execute();
        } catch(PDOException $e) {
            echo "Error updating button value: " . $e->getMessage();
            exit; // Stop executing the code if an error occurs
        }
    }

    // Function to insert a new button row
    function insertButtonRow($conn, $buttonId) {
        global $tableName; // Add this line to access the $tableName variable inside the function
        try {
            $stmt = $conn->prepare("INSERT INTO $tableName (id, value) VALUES (:id, 0)");
            $stmt->bindParam(':id', $buttonId);
            $stmt->execute();
        } catch(PDOException $e) {
            echo "Error inserting button row: " . $e->getMessage();
            exit; // Stop executing the code if an error occurs
        }
    }

    // Ensure that the required rows exist in the table
    for ($i = 0; $i < $numRows; $i++) {
        for ($j = 0; $j < $numColumns; $j++) {
            $buttonId = $i * $numColumns + $j + 1;
            $buttonValue = getButtonValue($conn, $buttonId);
            if ($buttonValue === 0) {
                insertButtonRow($conn, $buttonId);
            }
        }
    }

    // Handle button click
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $buttonId = $_POST['buttonId'];
        $currentValue = getButtonValue($conn, $buttonId);
        $newValue = $currentValue + 1;
        updateButtonValue($conn, $buttonId, $newValue);
    }

    // Handle clear button click
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear'])) {
        try {
            $conn->exec("TRUNCATE TABLE $tableName");
        } catch(PDOException $e) {
            echo "Error clearing table: " . $e->getMessage();
            exit; // Stop executing the code if an error occurs
        }
    }

    // Display the button grid
    echo '<table>';
    for ($i = 0; $i < $numRows; $i++) {
        echo '<tr>';
        for ($j = 0; $j < $numColumns; $j++) {
            $buttonId = $i * $numColumns + $j + 1;
            $buttonValue = getButtonValue($conn, $buttonId);
            echo '<td>';
            echo '<form method="post">';
            echo "<button type='submit' name='buttonId' value='$buttonId'>$buttonValue</button>";
            echo '</form>';
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';

    // Display the clear button
    echo '<form method="post">';
    echo '<button type="submit" name="clear">Clear Table</button>';
    echo '</form>';
    ?>

</body>
</html>