<?php
include "../base/chech.php"; 
include "../base/chech2.php"; 
include "../base/main.php";
session_start(); 

if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}

$host = "127.0.0.1:3306";
$user = getenv('db_user');
$pass = getenv('db_pass');
$db = "w4schools";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tutorials";
$result = $conn->query($sql);

$tutorials = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tutorials[] = $row;
    }
}

$conn->close();

$categories = [];
foreach ($tutorials as $tutorial) {
    $categories[] = $tutorial['category'];
}
$categories = array_unique($categories);

header('Content-Type: application/json');
echo json_encode([
    'tutorials' => $tutorials,
    'categories' => $categories
]);
?>
