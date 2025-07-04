<?php
    session_start();

    if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
        header("Location: /RecipeBook/Recipe-Book/php/home_for_all.php");
        exit();
    }

    $user_name = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "
        SELECT 
            post.*, 
            user.user_name, 
            user.user_profile_picture
        FROM 
            post
        JOIN 
            user ON post.user_id = user.user_id
        WHERE 
            post.post_status = 'disapproved' 
            AND post.user_id = $user_id
        ORDER BY post.post_id DESC
    ";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/pending_post_page.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo.png" type="image/png">
    </head>
    <body>
    
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo.png" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" title="Home feed" style="width: 120px; height: 120px;"/>&nbsp;
                <h1  onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" class="recipebook" title="About Recipebook">Recipebook</h1> 
            </div>
    
            <div class="rightside-bar">
                <img class="home-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" src='/RecipeBook/Recipe-Book/buttons/home_button_black_outlined.png' title="Home feed"  onmouseover="onHoverHome()" onmouseout="noHoverHome()" />
                <img class="favc-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/favourite_functionality/favourite_page.php'" src="/RecipeBook/Recipe-Book/buttons/fav_button_black.png" title="Your favourites" onmouseover="onHoverFavc()" onmouseout="noHoverFavc()"/>
                <img class="pen-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/post_functionality/pending_post_page.php'" src="/RecipeBook/Recipe-Book/buttons/pending_button_yellow.png" title="Your pending posts"/>
                <img class="setting-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/manage_profile/settings.html'" src="/RecipeBook/Recipe-Book/buttons/settings_button_black_lined.png" title="Settings" onmouseover="onHoverSetting()" onmouseout="noHoverSetting()"/>
            </div>
        </nav>
        <div class="heading">
            <h1 style="text-align: center;">Welcome to <span style="color:#ffbf17; cursor:pointer;" title="Your pending posts">your Pending Posts</span></h1><br/>
            <h2 style="text-align: center;">All posts</h2>
        </div>

        <a href="/RecipeBook/Recipe-Book/html/post_functionality/add_post.html"><img class="add_recipe" src="/RecipeBook/Recipe-Book/buttons/add_button.png" title="Add Recipe"></a>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $postId = $row['post_id'];
                    echo "<br/>";
                    echo "<div class='container'>";
                        echo "<div class='post' onclick='view_pending_post($postId)'>";
                            echo "<div class='post-title'>";
                                echo "<h3 style='font-size:30px;'>" . htmlspecialchars($row['post_title']) . "</h3>";
                            echo "</div>";
                            
                            echo "<div class='post-image' style='text-align:center;'>";
                                if (($row['post_image'])) {
                                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' 
                                    alt='Recipe Image' style='max-width: 450px; max-height: 450px; border-radius:8px; cursor: pointer;'/>";
                                } else {
                                    echo "No image available";
                                }
                            echo "</div>";

                            echo "<div class='post-description'>";
                                echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";
                                echo "<p><b>Category : </b>" . htmlspecialchars($row['post_category']) . "</p>";
                            echo "</div>";

                            echo "<div class='post-actions'>";
                                echo "<div style='display: flex; align-items: center;'>"; 
                                    if ($row['user_profile_picture']) {
                                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    } else {
                                        echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    }
                                    echo "<p><b>" . htmlspecialchars($row['user_name']) . "</p></b>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";  
                    echo "<br/>";      
                }
            } else {
                echo "<p style='text-align: center; font-size: 20px;'><b>";
                echo "<img src='/RecipeBook/Recipe-Book/logo/logo.png' title='Recipebook' style='width: 300px; height: 300px; cursor:pointer;'/><br/>";
                echo "You have not posted any recipes yet. <span style='color:#ffbf17; cursor:pointer;' onclick='add_post()'>Click Here</span> to get started with Recipebook!";
                echo "</b></p><br/>";
            }
            $conn->close();
        ?>
        
    </body>
    <script>
        function view_pending_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/view_pending_post.php?post_id=" + post_id;
        }

        function onHoverHome() {
            document.querySelector('.home-btn').src = '/RecipeBook/Recipe-Book/buttons/home_button_yellow_outlined.png';
        }

        function noHoverHome() {
            document.querySelector('.home-btn').src = '/RecipeBook/Recipe-Book/buttons/home_button_black_outlined.png';
        }

        function onHoverFavc(){
            document.querySelector('.favc-btn').src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow.png';
        }

        function noHoverFavc(){
            document.querySelector('.favc-btn').src = '/RecipeBook/Recipe-Book/buttons/fav_button_black.png';
        }

        function onHoverSetting(){
            document.querySelector('.setting-btn').src = '/RecipeBook/Recipe-Book/buttons/settings_button_yellow_lined.png';
            document.querySelector('.setting-btn').style.height="50px";
            document.querySelector('.setting-btn').style.width="50px";
        }

        function noHoverSetting(){
            document.querySelector('.setting-btn').src = '/RecipeBook/Recipe-Book/buttons/settings_button_black_lined.png';
        }

    </script>
</html>