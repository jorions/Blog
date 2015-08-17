<!DOCTYPE html>

<html>
<head>
    <title>Search Results</title>
</head>
</html>


<?php
include "checkDatabase.php";

echo "<h2>Search Results</h2>";

//if there is a search query
if(isset($_GET["query"]) && isset($_GET["searchParam"])) {
    //if "everywhere" radio button was clicked, prepare SELECT statement
    if($_GET["searchParam"] == "everywhere") {
        $query = $db->prepare("SELECT title, author, date, contents, id FROM posts WHERE title LIKE ? OR author LIKE ? or contents LIKE ?");
        $search = "%" . $_GET["query"] . "%";
        $query->bind_param("sss", $search, $search, $search);
    }
    //if "titles" radio button was clicked, prepare SELECT statement
    if($_GET["searchParam"] == "titles") {
        $query = $db->prepare("SELECT title, author, date, contents, id FROM posts WHERE title LIKE ?");
        $search = "%" . $_GET["query"] . "%";
        $query->bind_param("s", $search);
    }
    //if "authors" radio button was clicked, prepare SELECT statement
    if($_GET["searchParam"] == "authors") {
        $query = $db->prepare("SELECT title, author, date, contents, id FROM posts WHERE author LIKE ?");
        $search = "%" . $_GET["query"] . "%";
        $query->bind_param("s", $search);
    }
    //if "contents" radio button was clicked, prepare SELECT statement
    if($_GET["searchParam"] == "contents") {
        $query = $db->prepare("SELECT title, author, date, contents, id FROM posts WHERE contents LIKE ?");
        $search = "%" . $_GET["query"] . "%";
        $query->bind_param("s", $search);
    }
    $query->execute();
    $query->bind_result($title, $author, $date, $contents, $id);
    $query->store_result();
    $numRows = $query->num_rows;

    //if there are results from SELECT statement, return table
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
                        <td><a href="viewUser.php?name=<?php echo $author; ?>"><?php echo $author; ?></a></td>
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
    //else if no results from search
    } else {
        if($_GET["searchParam"] == "everywhere") {
            echo "<h3>No blog titles, authors, or contents contain <em>'" . $_GET["query"] . "'</em></h3>";
        } else {
            echo "<h3>No blog " . $_GET["searchParam"] . " contain <em>'" . $_GET["query"] . "'</em></h3>";
        }
    }
            ?>
        </table>
    <?php
//else if there is no query
} else {
    echo "<h3>No query entered!</h3>";
}
?>