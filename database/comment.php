<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE Comment(
        comment_id INT(6) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED,
        post_id INT(6) UNSIGNED,
        comment_text TEXT NOT NULL,
        commented_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES User(user_id),
        FOREIGN KEY (post_id) REFERENCES Post(post_id)
    )";

if ($conn->query($sql) === TRUE) {
    echo "Comment table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
