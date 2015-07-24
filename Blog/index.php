<!DOCTYPE html>

<?php
include "checkLogin.php";
include "checkDatabase.php";
include "checkLogout.php";
?>

<html>
    <head>
        <title><?php echo $_SESSION["user"]; ?>'s Profile</title>
    </head>
</html>

<?php
echo "<h2>Here are all of the blogs that have been posted</h2>";
?>