<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_name = $_SESSION['username'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT post.*, user.user_name FROM post JOIN user ON post.user_id = user.user_id ORDER BY post.post_id DESC";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Home page</title>
        <style>
            .post {
                cursor: pointer;
                padding: 10px;
                border: 1px solid #ccc;
                margin-bottom: 10px;
                transition: background-color 0.3s ease;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="topnav">
                <button><a class="profile" href="/RecipeBook/Recipe-Book/php/profile.php">Profile</a></button>
            </div>
        </header>

        <h1>Hello <?php echo "$user_name" ?>, welcome to your home feed!!</h1>
        <button><a href="/RecipeBook/Recipe-Book/php/logout.php">Log out</a></button>
        
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()){

                    echo "<div class='post' onclick='viewPost(" . $row['post_id'] . ")'>";
                    echo "<h3>Title:" . htmlspecialchars($row['post_title']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";

                    if (($row['post_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                    } else {
                        echo "No image available";
                    }
                    if ($row['post_edited_date'] != $row['post_posted_date']) {
                        echo "<p><b>Post edited on</b>: " . htmlspecialchars($row['post_edited_date']) . "</p>";
                    } else {
                        echo "<p><b>Posted on</b>: " . htmlspecialchars($row['post_posted_date']) . "</p>";
                    }
                    echo "<p><b>Posted by</b>: " . htmlspecialchars($row['user_name']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>There are no recipes to show you, Sorry T_T</p>";
            }
            $conn->close();
        ?>
    </body>
    <script>
        function viewPost(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/view_post.php?post_id=" + post_id;
        }
    </script>
</html>