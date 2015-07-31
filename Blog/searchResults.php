<!DOCTYPE html>

<html>
<head>
    <title>Search Results</title>
</head>
</html>


<?php
include "checkDatabase.php";

echo "<h2>Search Results</h2>";

if(isset($_GET["query"])) {
    $query = $db->prepare("SELECT title, author, date, contents, id FROM posts WHERE contents LIKE ?");
    $search = "%" . $_GET["query"] . "%";
    $query->bind_param("s", $search);
    $query->execute();
    $query->bind_result($title, $author, $date, $contents, $id);
    $query->store_result();
    $numRows = $query->num_rows;

    if($numRows > 0) {
    ?>
        <table style="text-align: left">
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Date</th>
                <th>Post</th>
                <th>View Post</th>
            </tr>
            <?php
                while($query->fetch()) { ?>
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
                        <td><a href="viewPost.php?id=<?php echo $id; ?>">View</a></td>
                    </tr>
                <?php
                }
        }
            ?>
        </table>
    <?php
} else {
    echo "<h2>No query entered!</h2>";
}
?>