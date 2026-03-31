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
        <title>Tutorial Details</title>
    </head>
    <body>
        <canvas class="back" id="canvas"></canvas>
        <?php include '../base/sidebar.php'; ?>
        <div class="con">
            <button class="circle-btn" onclick="openNav()">☰</button>  
            <?php
            if (isset($_GET['tutorial'])) {
                $index = $_GET['tutorial'];

                $sql = "SELECT * FROM tutorials WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $index);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $tutorial = $result->fetch_assoc();
                    echo '<div class="tutorial">';
                    echo '<button class="home" onclick="window.location.href = `index.php`;">Home</button>';
                    echo '<h2>' . htmlspecialchars($tutorial['title']) . '</h2>';
                    echo '<p>' . htmlspecialchars($tutorial['description']) . '</p>';
                    echo parseContent($tutorial['content']);
                    if (isset($_SESSION['username']) && $_SESSION['username'] === $tutorial['author']) {
                        echo '<a href="edit.php?tutorial=' . $index . '">Edit</a>';
                    } else {
                        echo "<p>Written by " . htmlspecialchars($tutorial['author']) . "</p>";
                    }
                    echo '</div>';
                } else {
                    echo '<p>Tutorial not found.</p>';
                }

                $stmt->close();
            } else {
                echo '<p>No tutorial specified.</p>';
            }

            function parseContent($content) {
                $lines = explode("\n", $content);
                $parsedContent = '';
                $inCodeBlock = false;
                foreach ($lines as $line) {
                    if (trim($line) == '//!') {
                        $inCodeBlock = true;
                        $parsedContent .= '<div class="code"><button class="copy-button" onclick="copyCode(this)">Copy Code</button><pre><code>';
                    } elseif (trim($line) == '!\\') {
                        $inCodeBlock = false;
                        $parsedContent .= '</code></pre></div>';
                    } else {
                        if ($inCodeBlock) {
                            $parsedContent .= htmlspecialchars($line) . "\n";
                        } else {
                            $parsedContent .= htmlspecialchars($line) . '<br>';
                        }
                    }
                }
                if ($inCodeBlock) {
                    $parsedContent .= '</code></pre></div>';
                }
                return $parsedContent;
            }
            ?>
            <script>
                function copyCode(button) {
                    const codeDiv = button.parentElement;
                    const code = codeDiv.querySelector('code').innerText;
                    navigator.clipboard.writeText(code).then(() => {
                        button.textContent = 'Copied!';
                        setTimeout(() => {
                            button.textContent = 'Copy Code';
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy code: ', err);
                    });
                }
            </script>
        </div>
    </body>
    <script src="https://theme.house-778.theorangecow.org/background.js"></script>
    <script src="https://house-778.theorangecow.org/base/main.js"></script>
    <script src="https://house-778.theorangecow.org/base/sidebar.js"></script>
</html>

<?php
$conn->close();
?>
