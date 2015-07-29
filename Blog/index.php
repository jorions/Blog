<!DOCTYPE html>

<?php
include "checkLogin.php";
include "checkDatabase.php";
include "checkLogout.php";
include "checkProfile.php";
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

//if post was just uploaded to this page provide message letting you know that your blog was posted
if(isset($_SESSION["blogTitle"])) {
    if($_SESSION["blogTitle"] != "") { //necessary because if you move from createPost to here without creating a post $_SESSION["blogTitle"] is set to ""
        echo "<h3 style='color:blue'>Your post $_SESSION[blogTitle] was successfuly uploaded!</h3>";
        unset($_SESSION["blogTitle"]);
        unset($_SESSION["blogContents"]);
    }
}

?>

<h2>Here are all of the blogs that have been posted</h2>

<form action=index.php method="POST">
    <input type="submit" name="createPost" value="Click here to create a blog post">
</form>

<br>

<table style="text-align: left">
    <tr>
        <th><strong>Title</strong></th>
        <th>Author</th>
        <th>Date</th>
        <th>Post</th>
        <th>View Post</th>
    </tr>
    <?php
    $allPosts = $db->query("SELECT * FROM posts");
    if($allPosts) {
        foreach($allPosts as $row) { ?>
            <tr>
                <td><?php echo $row["title"]; ?></td>
                <td><?php echo $row["author"]; ?></td>
                <td><?php echo $row["date"]; ?></td>
                <td>
                    <?php
                    if(strlen($row["contents"]) > 60)
                        echo substr($row["contents"], 0, 50) . "...";
                    else
                        echo $row["contents"];
                    ?>
                </td>
                <td><a href="viewPost.php?id=<?php echo $row["id"]; ?>">View</a></td>
            </tr> <?php
        }
    } else {
        echo $db->error;
    }
    ?>
</table>