<?php
    session_start();


    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_name = $_SESSION['username'];
    $search = '';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $search = $_POST['search'];
        $_SESSION['last_search'] = $search;

        header("Location: /Recipebook/Recipe-Book/php/search_functionality/search_post.php?search=" . urlencode($search));
        exit(); 
    }

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $_SESSION['last_search'] = $search;
    } else {
        $search = $_SESSION['last_search'] ?? '';
    }

    // Update post_like_count on post table
    $update_sql = "
        UPDATE post 
        SET post_like_count = (
            SELECT COUNT(*) 
            FROM Likes 
            WHERE Likes.post_id = post.post_id
        )
    ";
    $conn->query($update_sql);
    $sql = "SELECT post.*, user.user_name, user.user_profile_picture
            FROM post 
            JOIN user ON post.user_id = user.user_id 
            WHERE (post.post_title LIKE '%$search%' 
                OR user.user_name LIKE '%$search%')";

    // Split the search terms by commas and trim whitespace
    $searchTerms = array_map('trim', explode(',', $search));

    // Initialize arrays for conditions
    $categoryConditions = [];
    $keywordConditions = [];

    // Loop through each term to create conditions for both category and keywords
    foreach ($searchTerms as $term) {
        // Create dynamic conditions for both categories and keywords
        $categoryConditions[] = "post.post_category LIKE '%$term%'";
        $keywordConditions[] = "post.post_keywords LIKE '%$term%'";
    }

    // Add category conditions to the SQL query
    if (!empty($categoryConditions)) {
        $sql .= " OR (" . implode(' OR ', $categoryConditions) . ")";
    }

    // Add keyword conditions to the SQL query
    if (!empty($keywordConditions)) {
        $sql .= " OR (" . implode(' OR ', $keywordConditions) . ")";
    }

    $sql .= " ORDER BY post.post_id DESC";

    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo1.png" type="image/png">
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/styles.css">
    </head>
    <body>
        <nav class="navbar">
            <button class="home-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'">Home</button>            
        </nav>
        <br><br><br><br><br><br>
        <div class="search-bar">
            <form name="search" method="post" action="/RecipeBook/Recipe-Book/php/search_functionality/search_post.php">
                <img src="/RecipeBook/Recipe-Book/buttons/search_icon.png" height="30px"/>
                <input class="search_bar" type="text" id="search" name="search" placeholder="Search Recipe" style="margin-left:20px"/>
                <input type="submit" value="Search"/>
            </form> 
        </div>
        <h2>Results for <?php echo "$search" ?>:</h2>
        <br/>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $postId = $row['post_id'];
                    echo "<div class='container' onmouseover='onHover(this)' onmouseout='noHover(this)'>";
                    echo "<div class='post-title'>";
                        echo "<h3 style='font-size:25px;'>" . htmlspecialchars($row['post_title']) . "</h3>";
                    echo "</div>";
                    echo "<div class='post' onclick='view_post(" . $row['post_id'] . ")'>";

                    if ($row['post_edited_date'] != $row['post_posted_date']) {
                        // Post has been edited
                        echo "<div style='display: flex; align-items: center;'>";
                        if ($row['user_profile_picture']) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        } else {
                            echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        }
                        echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> edited on <b>" . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                        echo "</div>"; 
                    } else {
                        // Post has not been edited
                        echo "<div style='display: flex; align-items: center;'>";
                        if ($row['user_profile_picture']) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        } else {
                            echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        }
                        echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> posted on <b>" . htmlspecialchars($row['post_posted_date']) . "</b></p>";
                        echo "</div>"; 
                    }

                    echo "<p>Category : " . htmlspecialchars($row['post_category']) . "</p>";

                    if (($row['post_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                    } else {
                        echo "No image available";
                    }

                    echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
                    echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";
                    echo "<img class='like-btn'  data-post-id='" . $postId . "' src='/RecipeBook/Recipe-Book/buttons/like_button_black_outlined.png' height='30px' width='30px' title='Likes'/><span id='like-count-" . $postId . "'>" . htmlspecialchars($row['post_like_count']) . "</span>&nbsp;&nbsp;&nbsp;";
                    echo "<img class='comment-btn' data-post-id='" . $postId . "' src='/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png' height='30px' width='30px' title='Comment'/>&nbsp;&nbsp;&nbsp;";
                    echo "<img class='fav-btn' data-post-id='" . $row['post_id'] . "' src='/RecipeBook/Recipe-Book/buttons/fav_button_black_outlined.png' height='30px' width='30px' title='Add to favourites'/>";
                    
                    echo "</div>";
                    echo "</div>";
                    echo "<br/>";
                }
            } else {
                echo "<p>No matches for $search, Sorry T_T </p>";
            }
            $conn->close();
        ?>
        <!-- pop up box for comments -->
        <div id="commentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Comments</h2>
                <div id="commentList"></div>
                <textarea id="commentText" placeholder="Add your comment..."></textarea><br/><br/>
                <button id="submitComment">Submit Comment</button>
            </div>
        </div>
    </body>
    <script>
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/view_post.php?post_id=" + post_id;
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

        function onHover(container) {
            const favIcon = container.querySelector('.fav-btn');
            const likeIcon = container.querySelector('.like-btn');
            const commentIcon = container.querySelector('.comment-btn');

            favIcon.src = "/RecipeBook/Recipe-Book/buttons/fav_button_yellow_outlined.png";
            favIcon.style.height="35px";
            favIcon.style.width="35px";
            likeIcon.src = "/RecipeBook/Recipe-Book/buttons/like_button_yellow_outlined.png";
            likeIcon.style.height="35px";
            likeIcon.style.width="35px";
            commentIcon.src = "/RecipeBook/Recipe-Book/buttons/comment_button_yellow_outlined.png";
            commentIcon.style.height="35px";
            commentIcon.style.width="35px";
        }

        function noHover(container) {
            const favIcon = container.querySelector('.fav-btn');
            const likeIcon = container.querySelector('.like-btn');
            const commentIcon = container.querySelector('.comment-btn');

            favIcon.src = "/RecipeBook/Recipe-Book/buttons/fav_button_black_outlined.png";
            favIcon.style.height="30px";
            favIcon.style.width="30px";
            likeIcon.src = "/RecipeBook/Recipe-Book/buttons/like_button_black_outlined.png";
            likeIcon.style.height="30px";
            likeIcon.style.width="30px";
            commentIcon.src = "/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png";
            commentIcon.style.height="30px";
            commentIcon.style.width="30px";
        }

         //ajax for like button
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/recipebook/Recipe-Book/php/likes_functionality/like_post.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            const likeCountSpan = document.getElementById('like-count-' + postId);
                            likeCountSpan.innerText = response.newLikeCount;
                        } else {
                            alert(response.message);
                        }
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });

        //ajax for favourite button
        document.querySelectorAll('.fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/favourite_functionality/add_favourite.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Post added to your favourites!');
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });
        // ajax and js for comments section
        let commentPollingInterval; // Variable to hold the interval ID

        function openModal(postId) {
            // Fetch existing comments
            fetchComments(postId);
            
            // Start polling for new comments
            commentPollingInterval = setInterval(() => {
                fetchComments(postId);
            }, 3000); // Fetch new comments every 3 seconds
            
            // Display the modal
            document.getElementById('commentModal').style.display = 'block';
            
            // Set the postId in the button
            document.getElementById('submitComment').setAttribute('data-post-id', postId);
        }

        function closeModal() {
            document.getElementById('commentModal').style.display = 'none';
            
            // Stop polling when the modal is closed
            clearInterval(commentPollingInterval);
        }

        function fetchComments(postId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '/recipebook/Recipe-Book/php/comment_functionality/fetch_comments.php?post_id=' + postId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('commentList').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Submit comment
        document.getElementById('submitComment').addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentText = document.getElementById('commentText').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/recipebook/Recipe-Book/php/comment_functionality/add_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('commentText').value = ''; // Clear the text area
                    fetchComments(postId); // Refresh comments
                }
            };
            xhr.send('post_id=' + postId + '&comment_text=' + encodeURIComponent(commentText));
        });

        // Handle comment button clicks
        document.querySelectorAll('.comment-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent post click
                const postId = this.getAttribute('data-post-id');
                openModal(postId);
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('commentModal');
            if (event.target == modal) {
                closeModal();
            }
        };
        // Close modal on 'x' click
        document.querySelector('.close').addEventListener('click', closeModal);

        //deleting comment
        function deleteComment(commentId) {
            if (confirm("Are you sure you want to delete this comment?")){
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/Recipebook/Recipe-Book/php/comment_functionality/delete_comment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                    }
                };
                xhr.send('comment_id=' + commentId);
            }
            
        }

    </script>
</html>
