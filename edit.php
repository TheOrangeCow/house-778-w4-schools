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

if (!isset($_GET['tutorial'])) {
    echo 'No tutorial specified.';
    exit();
}

$index = $_GET['tutorial'];

$sql = "SELECT * FROM tutorials WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $index);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo 'Tutorial not found.';
    exit();
}

$tutorial = $result->fetch_assoc();

if ($_SESSION['username'] !== $tutorial['author']) {
    echo 'You are not authorized to edit this tutorial.';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $content = $_POST['content'];

    $updateSql = "UPDATE tutorials SET title = ?, description = ?, category = ?, content = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('ssssi', $title, $description, $category, $content, $index);
    
    if ($updateStmt->execute()) {
        header('Location: tutorial.php?tutorial=' . $index);
        exit();
    } else {
        echo 'Error updating tutorial: ' . $conn->error;
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
        <title>Edit Tutorial</title>
    </head>
    <body>
        <canvas class="back" id="canvas"></canvas>
        <?php include '../base/sidebar.php'; ?>
        <div class="con">
            <button class="circle-btn" onclick="openNav()">☰</button>  
            <div class="form-container">
                <h2>Edit Tutorial</h2>
                <form action="edit.php?tutorial=<?= $index ?>" method="post">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($tutorial['title']) ?>" required>
                    
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="html, js, css" <?= $tutorial['category'] == 'html, js, css' ? 'selected' : '' ?>>html, js, css</option>
                        <option value="html" <?= $tutorial['category'] == 'html' ? 'selected' : '' ?>>html</option>
                        <option value="js" <?= $tutorial['category'] == 'js' ? 'selected' : '' ?>>js</option>
                        <option value="css" <?= $tutorial['category'] == 'css' ? 'selected' : '' ?>>css</option>
                        <option value="python" <?= $tutorial['category'] == 'python' ? 'selected' : '' ?>>Python</option>
                        <option value="php" <?= $tutorial['category'] == 'php' ? 'selected' : '' ?>>php</option>
                    </select>

                    <label for="description">Description</label>
                    <textarea id="description" class="description" name="description" required><?= htmlspecialchars($tutorial['description']) ?></textarea>
                    
                    <label for="content">Content</label>
                    <textarea id="content" class="content" name="content" required><?= htmlspecialchars($tutorial['content']) ?></textarea>
                    <button type="button" onclick="insertCodeMarkers()">Insert Code</button><br>
                    
                    <button type="submit">Update</button>
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
