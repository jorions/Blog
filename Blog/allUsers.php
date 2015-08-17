<!DOCTYPE html>

<?php
include "checkDatabase.php";
?>

<html>
<head>
    <title>List of All Users</title>
</head>
<body>
<h2>All Users</h2>

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