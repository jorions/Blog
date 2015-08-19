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
    $_SESSION["blogTitle"] = $_POST["title"];
    $_SESSION["blogContents"] = $_POST["contents"];

    if($_POST["title"] != "" && $_POST["contents"] != "") {
        //add post to database
        $insert = $db->prepare("INSERT INTO posts (title, author, date, contents) VALUES (?, ?, ?, ?)");
        $date = date("M j, Y");
        $insert->bind_param("ssss", $_SESSION["blogTitle"], $_SESSION["user"], $date, $_SESSION["blogContents"]);
        $insert->execute();

        //redirect to blog list
        $_SESSION["newPostCreated"] = "TRUE";
        header("location: index.php");
    } else {
        echo "<h3 style='color:blue'>Please enter both a title and blog post</h3>";
    }
} else {
    $_SESSION["blogTitle"] = "";
    $_SESSION["blogContents"] = "";
}
?>

<h2>Create a new blog post </h2>

<form action="createPost.php" method="POST">
    <input type="text" name="title" maxlength="50" placeholder="Title" value="<?php echo $_SESSION["blogTitle"]; ?>" style="width: 400px">
    <br>
    <br>
    <textarea name="contents" cols="70" rows="20" placeholder="Post"><?php echo $_SESSION["blogContents"]; ?></textarea>
    <br>
    <input type="submit" name="submit" value="Submit post">
</form>