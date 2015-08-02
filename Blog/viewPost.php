<!DOCTYPE html>

<?php
include "checkDatabase.php";

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
        ?>

        <html>
        <head>
            <title><?php echo "Post: " . $title; ?></title>
        </head>
        </html>

        <h2><?php echo $title; ?></h2>
        <h4><?php echo "Posted by " . $author . " on " . $date; ?></h4>
        <br>
        <br>
        <?php echo $contents;
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