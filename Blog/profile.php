<!DOCTYPE html>

<?php
include "checkDatabase.php";
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
//bind_param changing the state of an object so that we can run the execute() method. The order of the variables binded
//here are tied to the same order of the columns pulled in using the above prepare() statement
$userPosts->bind_result($title, $author, $date, $contents, $id);

//changes the state of the userPosts object so that we can run the num_rows method
$userPosts->store_result();

$numRows = $userPosts->num_rows;

if($numRows > 0) {
?>

    <table style="text-align: left">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Date</th>
            <th>Post</th>
            <th>View Post</th>
            <th>Delete</th>
        </tr>
        <?php
            //fetch() cannot be used without the bind_result() function used above
            //it simultaneously...
            //  pulls in a row of information from the userPosts object that was prepared by bind_result()
            //  stores its current position in the list of rows
            //  returns true (or 1?) while it still has information to return
            //so this loop permits us to call the variables defined in bind_result(), 1 row at a time, and
            //tells the loop to stop once there are no more rows to grab
            while($userPosts->fetch()) { ?>
                <tr>
                    <td><?php echo $title; ?></td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $date; ?></td>
                    <td>
                        <?php
                        //if the post is longer than 60 characters return the first 50 characters
                        if(strlen($contents) > 60)
                            echo substr($contents, 0, 50) . "...";
                        //else return the whole post
                        else
                            echo $contents;
                        ?>
                    </td>
                    <!--Link to view the post-->
                    <td><a href="viewPost.php?id=<?php echo $id; ?>">View</a></td>
                    <!--Link to delete the post-->
                    <td>DELETE POST</td>
                </tr> <?php
            }
        ?>
    </table>

<?php } ?>