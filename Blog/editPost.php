<!DOCTYPE html>

<html>
<head>
    <title>Edit Post</title>
</head>
</html>


<?php
include "checkDatabase.php";

//if a post to edit has been clicked, prepare and execute a SELECT statement based on the id
if(isset($_GET["id"])) {
    //check for both author and id for security purposes
    $post = $db->prepare("SELECT title, date, contents FROM posts WHERE id = ? AND author = ?");
    $post->bind_param("is", $_GET["id"], $_SESSION["user"]);
    $post->execute();
    $post->bind_result($title, $date, $contents);
    $post->store_result();
    $rows = $post->num_rows;

    //if the SELECT statement returns a row, fetch data from bind_result and print out blog update form
    if($rows > 0) {
        $post->fetch();
        echo "<h2>Update your blog post </h2>";

        //if submit button is pressed...
        if (isset($_POST["submit"])) {

            //...set SESSION variables to POST variables to have persistent values upon form submission...
            $_SESSION["blogTitle"] = $_POST["title"];
            $_SESSION["blogContents"] = $_POST["contents"];

            //...AND if blog title and contents have values, update the blog entry then redirect to the profile page
            if ($_POST["title"] != "" && $_POST["contents"] != "") {
                //update post in database
                $update = $db->prepare("UPDATE posts SET title = ?, contents = ? WHERE id = ?");
                $update->bind_param("ssi", $_SESSION["blogTitle"], $_SESSION["blogContents"], $_GET["id"]);
                $update->execute();

                //redirect to profile page
                $_SESSION["postEdited"] = "TRUE";
                header("location: profile.php");

            //else if either title or contents are blank
            } else {
                echo "<p style='color:blue'><em>You must have a title and contents.</em></p><br>";
            }

        //else if submit button wasn't pressed, initialize SESSION variables to equal fetched MySQL data
        } else {
            $_SESSION["blogTitle"] = $title;
            $_SESSION["blogContents"] = $contents;
        }
        ?>
        <!--NOTE that the form action contains id= so upon form submission the id is persistent in the URL -->
        <!--QUESTION: Does the action mean where the submit button redirects to, or just where it sends data to? -->
        <form action="editPost.php?id=<?php echo $_GET["id"]; ?>" method="POST">
            <em>Post originally made on <?php echo $date; ?></em><br><br>
            <strong>Title:</strong><br>
            <input type="text" name="title" maxlength="50" value="<?php echo $_SESSION["blogTitle"]; ?>" style="width: 500px">
            <br>
            <br>
            <strong>Post:</strong><br>
            <textarea name="contents" cols="70" rows="20"><?php echo $_SESSION["blogContents"]; ?></textarea>
            <br>
            <input type="submit" name="submit" value="Submit post">
        </form>
        <?php

    //else if the SELECT statement does not return a row
    } else {
            echo "<h2>That isn't your post to edit! For shame!</h2>";
    }

//else if the GET id is not set
} else {
    echo "<h2>No post selected to edit</h2>";
}
?>