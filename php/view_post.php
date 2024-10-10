<?php
session_start();

if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
    header("Location: /RecipeBook/Recipe-Book/html/login.html");
    exit();
}

if (!isset($_GET['post_id'])) {
    echo "Post not found.";
    exit();
}

$post_id = $_GET['post_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM post WHERE post_id = $post_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Post not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
</head>

<body>
    <h1><?php echo htmlspecialchars($row['post_title']); ?></h1>
    <p><?php echo htmlspecialchars($row['post_text']); ?></p>

    <?php
    $_SESSION['post_to_be_deleted'] = $row['post_id'];

    if (($row['post_image'])) {
        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
    } else {
        echo "No image available";
    }
    echo "<p><b>Ingrediants</b>:" . htmlspecialchars($row['post_ingredients']) . "</p>";
    echo "<p><b>Instructions</b>:" . htmlspecialchars($row['post_instructions']) . "</p>";
    echo "<p><b>Keywords</b>:" . htmlspecialchars($row['post_keywords']) . "</p>";
    echo "<p><b>Category</b>:" . htmlspecialchars($row['post_category']) . "</p>";
    if ($row['post_edited_date'] != $row['post_posted_date']) {
        echo "<p><b>Post edited on</b>: " . htmlspecialchars($row['post_edited_date']) . "</p>";
    } else {
        echo "<p><b>Posted on</b>: " . htmlspecialchars($row['post_posted_date']) . "</p>";
    }
    echo "<button onclick='edit(" . $row['post_id'] . ")'>Edit post</button>";
    echo "<button onclick='confirm_box(" . $row['post_id'] . ")'>Delete post</button>";
    ?>

</body>
<script>
    function confirm_box(post_id) {
        var ans = confirm("Are you sure you want to delete this post?");
        if (ans == true) {
            window.location.href = "/RecipeBook/Recipe-Book/php/delete_post.php?post_id=" + post_id;
        }
    }

    function edit(post_id) {
        window.location.href = "/RecipeBook/Recipe-Book/php/edit_post.php?post_id=" + post_id;
    }

    function viewPost(post_id) {
        window.location.href = "/RecipeBook/Recipe-Book/php/view_post.php?post_id=" + post_id;
    }
</script>

</html>

<?php $conn->close(); ?>