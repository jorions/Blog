<!DOCTYPE html>

<?php
include "checkDatabase.php";

//if a comment has been made insert it into the database and post a message
if(isset($_POST["submitComment"])) {
    if($_POST["comment"] != "") {
        $insert = $db->prepare("INSERT INTO post_comments (post_id, comment_author, comment, comment_date) VALUES (?,?,?,?)");
        $date = date("M j, Y");
        $insert->bind_param("isss", $_GET["id"], $_SESSION["user"], $_POST["comment"], $date);
        $insert->execute();
        $insert->close();
        echo "<h3 style='color:blue'>You made a comment!</h3>";
    } else {
        echo "<p style='color:blue'><em>Your comment must have contents.</em></p><br>";
    }
}

//if a post to view has been clicked, prepare and execute a SELECT statement based on the id
if(isset($_GET["id"])) {
    $post = $db->prepare("SELECT title, author, date, contents FROM posts WHERE id=?");
    $post->bind_param("i", $_GET["id"]);
    $post->execute();
    $post->bind_result($title, $author, $date, $contents);
    $post->store_result();
    $rows = $post->num_rows;
    if($rows > 0) {
        $post->fetch();
        $post->close();
        ?>

        <html>
        <head>
            <title><?php echo "Post: " . $title; ?></title>
        </head>
        </html>

        <h2><?php echo $title; ?></h2>
        <h4><?php echo "Posted by <a href='viewUser.php?name=$author'>$author</a> on $date"; ?></h4>
        <br>
        <br>
        <?php echo str_replace("\n","<br>",$contents);
        ?>

        <!--create comment section-->
        <br />
        <br />
        <br />
        <hr />
        <form action="viewPost.php?id=<?php echo $_GET["id"]; ?>" method="POST">
            <textarea name="comment" cols="70" rows="5" placeholder="Leave a comment..."></textarea>
            <br />
            <input type="submit" name="submitComment" value="Comment" />
        </form>
        <br />
        <br />

        <?php
        //select all comments that share a post ID with the current post
        $allComments = $db->prepare("SELECT comment_author, comment, comment_date FROM post_comments WHERE post_id=?");
        $allComments->bind_param("i", $_GET["id"]);
        $allComments->execute();
        $allComments->bind_result($commentAuthor, $comment, $commentDate);
        $allComments->store_result();
        $rows = $allComments->num_rows;
        //if there are comments, display them in a table
        if($rows > 0) { ?>
            <table cellpadding="10" style="text-align: left">
                <?php
                while ($allComments->fetch()) { ?>
                    <tr bgcolor="#87cefa">
                        <td><?php echo $commentAuthor; ?></td>
                        <td><i><?php echo $commentDate; ?></i></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?php echo str_replace("\n","<br />",$comment); ?></td>
                    </tr>
                <?php
                } ?>
            </table>
        <?php
        }
    } else { ?>

        <html>
        <head>
            <title>No Post Selected</title>
        </head>
        </html>
        <?php
        echo "<h2>No post selected!</h2>";
    }
} else {
    ?>
    <html>
    <head>
        <title>No Post Selected</title>
    </head>
    </html>

    <?php echo "<h2>No post selected!</h2>";
}
?>