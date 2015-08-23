<!DOCTYPE html>

<html>
<head>
    <?php
    if(isset($_GET["name"])) {
        echo"<title>Posts by $_GET[name]</title>";
    } else {
        echo "<title>No User by That Name</title>";
    }
    ?>
</head>
<body>
    <?php
    include "checkDatabase.php";

    //check if username is set
    if(isset($_GET["name"])) {
        echo "<h2>$_GET[name]'s Posts</h2>";

        $userPosts = $db->prepare("SELECT title, date, contents, id FROM posts WHERE author = ?");
        $userPosts->bind_param("s", $_GET["name"]);
        $userPosts->execute();
        $userPosts->bind_result($title, $date, $contents, $id);
        $userPosts->store_result();
        $numRows = $userPosts->num_rows;

        if($numRows > 0) {
            ?>

            <table cellpadding="10" style="text-align: left">
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Post</th>
                    <th>View</th>
                </tr>
                <?php
                while ($userPosts->fetch()) { ?>
                    <tr>
                        <td><?php echo $title; ?></td>
                        <td><?php echo $date; ?></td>
                        <td>
                            <?php
                            //if the post is longer than 60 characters return the first 50 characters
                            if (strlen($contents) > 60)
                                echo substr($contents, 0, 50) . "...";
                            //else return the whole post
                            else
                                echo $contents;
                            ?>
                        </td>
                        <!--Link to view the post-->
                        <td><a href="viewPost.php?id=<?php echo $id; ?>">View</a></td>
                    </tr> <?php
                }
                ?>
            </table>
        <?php
        }
    //else if username isn't set
    } else { ?>
        <h2>No user selected to view. Choose from the list below:</h2>
        <table cellpadding="10" style="text-align: left">
            <tr>
                <th>Author</th>
                <th># of Posts</th>
            </tr>
            <?php
            $allUsers = $db->query("SELECT * FROM user_logins");
            if($allUsers) {
                foreach($allUsers as $row) { ?>
                    <tr>
                        <td><a href="viewUser.php?name=<?php echo $row["username"]; ?>"><?php echo $row["username"]; ?></a></td>
                        <td>
                            <?php
                            //get number of posts from current user
                            $selectPosts = $db->prepare("SELECT * FROM posts WHERE author=?");
                            $selectPosts->bind_param("s", $row["username"]);
                            $selectPosts->execute();
                            $selectPosts->store_result();
                            $numPosts = $selectPosts->num_rows;
                            $selectPosts->close();
                            echo $numPosts;
                            ?>
                        </td>
                    </tr> <?php
                }
            } else {
                echo $db->error;
            }
            ?>
        </table>

        <?php
    } ?>
</body>
</html>