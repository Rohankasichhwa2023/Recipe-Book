<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $user_name = $_POST['username'];
        $user_email = $_POST['email'];
        $user_password = $_POST['password'];
        $user_password2 = $_POST['password2'];

        if (empty($user_name) || empty($user_email) || empty($user_password) || empty($user_password2)) {
            echo "<script>
                    alert ('All fields are required!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
        } else {
            $sql = "INSERT INTO user (user_name, user_email, user_password) VALUES ('$user_name', ' $user_email', '$user_password')";

            if ($conn->query($sql) === TRUE) {
                header("Location: /RecipeBook/Recipe-Book/html/login.html");
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
    $conn->close();
?>
