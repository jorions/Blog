<!DOCTYPE html>

<?php
include "checkLogin.php";
include "checkDatabase.php";
include "checkLogout.php";
?>

<html>
    <head>
        <title><?php echo $_SESSION["user"]; ?>'s Newest Blog Post</title>
    </head>
</html>

<?php
if(isset($_POST["submit"])) {
    $_SESSION["blogTitle"] = $_POST["title"];
    $_SESSION["blogContents"] = $_POST["contents"];

    if($_POST["title"] != "" && $_POST["contents"] != "") {
        //add post to database
        $insert = $db->prepare("INSERT INTO posts (title, author, date, contents) VALUES (?, ?, ?, ?)");
        $date = date("d F Y");
        $insert->bind_param("ssss", $_SESSION["blogTitle"], $_SESSION["user"], $date, $_SESSION["blogContents"]);
        $insert->execute();

        //redirect to blog list
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
    Enter your title here<br>
    <input type="text" name="title" value="<?php echo $_SESSION["blogTitle"]; ?>" style="width: 500px">
    <br>
    <br>
    Enter your blog post here<br>
    <input type="text" name="contents" value="<?php echo $_SESSION["blogContents"]; ?>" style="width: 600px; height: 400px">
    <br>
    <input type="submit" name="submit" value="Click here to submit your post">
</form>