<!DOCTYPE html>

<?php
include "checkDatabase.php";
?>

<html>
<head>
    <title>All Blogs</title>
</head>
</html>

<?php

//if post was just uploaded to this page provide message letting you know that your blog was posted
if(isset($_SESSION["newPostCreated"])) {
    echo "<h3 style='color:blue'>Your post '$_SESSION[newBlogTitle]' was successfully uploaded!</h3>";
    unset($_SESSION["newPostCreated"]);
}

?>

<h2>All Blogs</h2>

<br>

<table cellpadding="10" style="text-align: left">
    <tr>
        <th><strong>Title</strong></th>
        <th>Author</th>
        <th>Date</th>
        <th>Post</th>
        <th>View</th>
    </tr>
    <?php
    $allPosts = $db->query("SELECT * FROM posts");
    if($allPosts) {
        foreach($allPosts as $row) { ?>
            <tr>
                <td><?php echo $row["title"]; ?></td>
                <td><a href="viewUser.php?name=<?php echo $row["author"]; ?>"><?php echo $row["author"]; ?></a></td>
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