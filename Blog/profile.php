<!DOCTYPE html>

<?php
include "checkLogin.php";
include "checkDatabase.php";
include "checkLogout.php";
include "checkIndex.php";
?>

<html>
    <head>
        <title><?php echo $_SESSION["user"]; ?>'s Profile</title>
    </head>
</html>

<h2><strong><?php echo $_SESSION["user"]; ?></strong>'s Profile Page</h2>
<br>
<h3>All of <strong><?php echo $_SESSION["user"]; ?></strong>'s posts</h3>

<?php
$userPosts = $db->prepare("SELECT title, author, date, contents, id FROM posts WHERE author = ?");
$userPosts->bind_param("s", $_SESSION["user"]);
$userPosts->execute();

//changes the state of the userPosts object so that we can run the fetch() method. can be though of similarly to
//bind_param changing the state of an object so that we can run the execute() method.
$userPosts->bind_result($title, $author, $date, $contents, $id);

//changes the state of the userPosts object so that we can run the num_rows method
$userPosts->store_result();

$numRows = $userPosts->num_rows;

if($numRows > 0) {
?>

    <table style="text-align: left">
        <tr>
            <th><strong>Title</strong></th>
            <th>Author</th>
            <th>Date</th>
            <th>Post</th>
            <th>View Post</th>
            <th>Delete</th>
        </tr>
        <?php
            while($userPosts->fetch()) { ?>
                <tr>
                    <td><?php echo $title; ?></td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $date; ?></td>
                    <td>
                        <?php
                        if(strlen($contents) > 60)
                            echo substr($contents, 0, 50) . "...";
                        else
                            echo $contents;
                        ?>
                    </td>
                    <td><a href="viewPost.php?id=<?php echo $id; ?>">View</a></td>
                    <td>DELETE POST</td>
                </tr> <?php
            }
        ?>
    </table>

<?php } ?>