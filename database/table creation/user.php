<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE TABLE user(
            user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_name VARCHAR(30) NOT NULL UNIQUE,
            user_email VARCHAR(50) NOT NULL UNIQUE,
            user_password VARCHAR(20) NOT NULL,
            user_profile_picture LONGBLOB,
            user_reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

    if ($conn->query($sql) === TRUE) {
        echo "User table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();

?>
