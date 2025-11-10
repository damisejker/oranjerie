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

// [MULTIPOT START] Обновленная логика сохранения позиции с поддержкой pot_id
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, что все необходимые параметры переданы
    if (isset($_POST['pot_id'], $_POST['left'], $_POST['top'], $_POST['username'])) {
        $potId = intval($_POST['pot_id']);
        $username = $_POST['username'];
        $left = $_POST['left'];
        $top = $_POST['top'];

        // Проверяем, что горшок принадлежит этому пользователю
        $sqlCheck = "SELECT `id` FROM `pots` WHERE `id` = ? AND `login` = ?";
        $stmtCheck = $conn->prepare($sqlCheck);

        if ($stmtCheck === false) {
            die("Error preparing check statement: " . $conn->error);
        }

        $stmtCheck->bind_param("is", $potId, $username);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Горшок принадлежит пользователю, обновляем позицию
            $sqlUpdate = "UPDATE `pots` SET `pot_left` = ?, `pot_top` = ? WHERE `id` = ? AND `login` = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);

            if ($stmtUpdate === false) {
                die("Error preparing update statement: " . $conn->error);
            }

            $stmtUpdate->bind_param("ssis", $left, $top, $potId, $username);

            if ($stmtUpdate->execute()) {
                echo "Position saved successfully for pot ID: " . $potId;
            } else {
                echo "Error executing update: " . $stmtUpdate->error;
            }

            $stmtUpdate->close();
        } else {
            echo "Error: Pot does not belong to this user or does not exist.";
        }

        $stmtCheck->close();
    } else {
        echo "Required parameters are missing.";
    }
} else {
    echo "Invalid request method.";
}
// [MULTIPOT END]
?>

