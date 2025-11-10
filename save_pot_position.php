<?php
session_start(); // Start the session
header('Content-Type: application/json');

// Replace with your connection details
$servername = "localhost";
$username = "magismo_newera";
$password = "z65qdc3xmfq8";
$dbname = "magismo_school";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

// Check connection
if ($conn->connect_error) {
    // If there is a connection error, send a JSON response with the error
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
}

/////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // It's good practice to check if the expected POST variables are set
    if (isset($_POST['username'], $_POST['left'], $_POST['top'])) {
        $username = $_POST['username'];
        $left = $_POST['left'];
        $top = $_POST['top'];

        // Prepare SQL statement to avoid SQL injection
        $sql = "INSERT INTO pot_positions (username, pot_left, pot_top) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE pot_left = VALUES(pot_left), pot_top = VALUES(pot_top)";

        $stmt = $conn->prepare($sql);
        
        // Check if the statement was prepared successfully
        if ($stmt === false) {
            die("Error preparing the statement: " . $conn->error);
        }

        $stmt->bind_param("sss", $username, $left, $top);
        
        if ($stmt->execute()) {
            echo "Position saved successfully.";
        } else {
            echo "Error executing the statement: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Required parameters are missing.";
    }
} else {
    // If not a POST request
    echo "Invalid request method.";
}
?>

