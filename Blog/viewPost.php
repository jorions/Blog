<!DOCTYPE html>

<?php
include "checkLogin.php";
include "checkDatabase.php";
include "checkLogout.php";
include "checkIndex.php";
include "checkProfile.php";

$post = $db->prepare("SELECT title, author, date, contents FROM posts WHERE id=?");
$post->bind_param("i", $_GET["id"]);
$post->execute();
$post->bind_result($title, $author, $date, $contents);
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
<?php echo $contents; ?>