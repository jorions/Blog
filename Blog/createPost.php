<!DOCTYPE html>

<?php
include "checkDatabase.php";
?>

<html>
<head>
    <title><?php echo $_SESSION["user"]; ?>'s Newest Blog Post</title>
</head>
</html>

<?php
if(isset($_POST["submit"])) {
    //set session variables to equal submitted form entries to preserve full entry in case of error
    $_SESSION["newBlogTitle"] = $_POST["title"];
    $_SESSION["newBlogContents"] = $_POST["contents"];

    if($_POST["title"] != "" && $_POST["contents"] != "") {
        //add post to database
        $insert = $db->prepare("INSERT INTO posts (title, author, date, contents) VALUES (?, ?, ?, ?)");
        $date = date("d F Y");
        $insert->bind_param("ssss", $_SESSION["newBlogTitle"], $_SESSION["user"], $date, $_SESSION["newBlogContents"]);
        $insert->execute();

        //redirect to blog list
        header("location: index.php");
    } else {
        echo "<h3 style='color:blue'>Please enter both a title and blog post</h3>";
    }
} else {
    $_SESSION["newBlogTitle"] = "";
    $_SESSION["newBlogContents"] = "";
}
?>

<h2>Create a new blog post </h2>

<form action="createPost.php" method="POST">
    <input type="text" name="title" maxlength="50" placeholder="Title" value="<?php echo $_SESSION["newBlogTitle"]; ?>" style="width: 400px">
    <br>
    <br>
    <textarea name="contents" cols="70" rows="20" placeholder="Post"><?php echo $_SESSION["newBlogContents"]; ?></textarea>
    <br>
    <input type="submit" name="submit" value="Submit post">
</form>