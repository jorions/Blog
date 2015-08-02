<!DOCTYPE html>

<?php
include "checkDatabase.php";
?>

<html>
    <head>
        <title><?php echo $_SESSION["user"]; ?>'s Profile</title>
    </head>
</html>

<?php
//if a post was edited, run this code
if(isset($_SESSION["blogTitle"])) {
    //moving straight from createPost to here creates an empty blogTitle SESSION variable, so check for it
    if($_SESSION["blogTitle"] != "") {
        echo "<h3 style='color:blue'>Your post $_SESSION[blogTitle] was successfully updated!</h3>";
    }
    unset($_SESSION["blogTitle"]);
}

//if a post was deleted, run this code
//QUESTION - THIS IS PROBABLY HORRENDOUSLY INSECURE. WHAT IS THE BEST OPTION??
if(isset($_GET["deleteID"])) {
    //QUESTION - WHY DO I HAVE TO EXECUTE AND CLOSE MY STATEMENTS BEFORE MOVING ON TO NEW STATEMENTS? I DON'T HAVE TO DO THIS ON EDITPOST.PHP
    $select = $db->prepare("SELECT title FROM posts WHERE id = ?");
    $select->bind_param("i", $_GET["deleteID"]);
    $select->execute();
    $select->bind_result($deleteTitle);
    $select->fetch();
    $select->close();

    $delete = $db->prepare("DELETE FROM posts WHERE id = ?");
    $delete->bind_param("i", $_GET["deleteID"]);
    $delete->execute();
    $delete->store_result();
    $rows = $delete->num_rows;

    if($rows > 0) {
        echo "<h3 style='color:blue'>Your post $deleteTitle was successfully deleted!</h3>";
    }

    $delete->close();
}
?>

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

    <table cellpadding="10" style="text-align: left">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Date</th>
            <th>Post</th>
            <th>View</th>
            <th>Delete</th>
            <th>Edit</th>
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
                    <td><a href="profile.php?deleteID=<?php echo $id; ?>">Delete</a></td>
                    <!--Link to edit the post-->
                    <td><a href="editPost.php?id=<?php echo $id; ?>">Edit</a></td>
                </tr> <?php
            }
        ?>
    </table>

<?php } ?>