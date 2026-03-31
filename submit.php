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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $content = $_POST['content'];
    $username = $_SESSION['username'];

    $sql = "INSERT INTO tutorials (title, description, category, content, author) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $title, $description, $category, $content, $username);

    if ($stmt->execute()) {
        echo '<p>Tutorial submitted successfully!</p>';
    } else {
        echo '<p>Error submitting tutorial: ' . $conn->error . '</p>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://house-778.theorangecow.org/base/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
        <link rel="icon" href="https://house-778.theorangecow.org/base/icon.ico" type="image/x-icon">
        <title>Submit Tutorial</title>
    </head>
    <body>
        <canvas class="back" id="canvas"></canvas>
        <?php include '../base/sidebar.php'; ?>
        <div class="con">
            <button class="circle-btn" onclick="openNav()">☰</button> 
            <div class="form-container">
                <button class="home" onclick="window.location.href = 'index.php'">Home</button>
                <h2>Submit a New Tutorial</h2>
                <form action="submit.php" method="post">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" placeholder="How to add a chat to your website" required>
                    
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="html, js, css">html, js, css</option>
                        <option value="html">html</option>
                        <option value="js">js</option>
                        <option value="css">css</option>
                        <option value="python">Python</option>
                        <option value="php">php</option>
                    </select>
                    
                    <label for="description">Description</label>
                    <textarea class="description" id="description" name="description" placeholder="This is a perfect chat to add to your static website" required></textarea>
                    
                    <label for="content">Content</label>
                    <textarea class="content" id="content" name="content" placeholder="This is where the content would go if I could be bothered to write it" required></textarea>
                    <button type="button" onclick="insertCodeMarkers()">Insert Code</button><br>
                    
                    <button type="submit">Submit</button>
                </form>
            </div>
            <script>
                function insertCodeMarkers() {
                    const textarea = document.getElementById('content');
                    const codeSnippet = "\n//!\nYour code here\n!\\\n";
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    const before = text.substring(0, start);
                    const after = text.substring(end, text.length);
                    textarea.value = before + codeSnippet + after;
                    textarea.selectionStart = textarea.selectionEnd = start + codeSnippet.length;
                    textarea.focus();
                }
            </script>
        </div>
    </body>
    <script src="https://theme.house-778.theorangecow.org/background.js"></script>
    <script src="https://house-778.theorangecow.org/base/main.js"></script>
    <script src="https://house-778.theorangecow.org/base/sidebar.js"></script>
</html>
