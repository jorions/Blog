<!DOCTYPE html>

<?php
include "checkLogin.php";
include "checkDatabase.php";
include "checkLogout.php";
?>

<html>
    <head>
        <title>All of our blogs</title>
    </head>
</html>

<?php
if(isset($_POST["createPost"])) {
    header("location: createPost.php");
}
if(isset($_SESSION["blogTitle"])) {
    echo "<h3 style='color:blue'>Your post $_SESSION[blogTitle] was successfuly uploaded!</h3>";
    unset($_SESSION["blogTitle"]);
    unset($_SESSION["blogContents"]);
}

?>

<h2>Here are all of the blogs that have been posted</h2>

<form action=index.php method="POST">
    <input type="submit" name="createPost" value="Click here to create a blog post">
</form>

<br>

<table>
    <tr>
        <th><strong>Post Title</strong></th>
        <th>Post Author</th>
        <th>Post Date</th>
    </tr>
    <?php
    $allPosts = $db->query("SELECT * FROM posts");
    if($allPosts) {
        foreach($allPosts as $row) { ?>
            <tr>
                <td><?php echo $row["title"]; ?></td>
                <td><?php echo $row["author"]; ?></td>
                <td><?php echo $row["date"]; ?></td>
            </tr> <?php
        }
    } else {
        echo $db->error;
    }
    ?>
</table>