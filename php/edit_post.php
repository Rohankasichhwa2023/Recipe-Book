<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['post_id'])) {
    $post_id = (int)$_GET['post_id'];
    
    $sql = "SELECT * FROM post WHERE post_id = $post_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No post found for the provided ID.";
        exit();
    }
} else {
    echo "No post ID provided for editing.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>

<body>
    <h1>Edit your Post</h1>
    <form action="/RecipeBook/Recipe-Book/php/update_post.php?post_id=<?php echo $post_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="post_image">Post Image:</label>
        <input type="file" name="post_image" accept="image" id="post_image" required><br><br>

        <label for="post_title">Post Title:</label>
        <input type="text" name="post_title" id="post_title" value="<?php echo htmlspecialchars($row['post_title']); ?>" required><br><br>

        <label for="post_ingredients">Ingredients:</label>
        <textarea name="post_ingredients" id="post_ingredients" required><?php echo htmlspecialchars($row['post_ingredients']); ?></textarea><br><br>

        <label for="post_instructions">Instructions:</label>
        <textarea name="post_instructions" id="post_instructions" required><?php echo htmlspecialchars($row['post_instructions']); ?></textarea><br><br>

        <label for="post_keywords">Keywords:</label>
        <input type="text" name="post_keywords" id="post_keywords" value="<?php echo htmlspecialchars($row['post_keywords']); ?>" required><br><br>

        <label for="post_category">Category:</label>
        <input type="text" name="post_category" id="post_category" value="<?php echo htmlspecialchars($row['post_category']); ?>" required><br><br>

        <label for="post_text">Description :</label>
        <input type="text" name="post_text" id="post_text" value="<?php echo htmlspecialchars($row['post_text']); ?>" required><br><br>

        <input type="submit" value="Update Post">
    </form>
</body>

</html>

<?php
$conn->close();
?>
