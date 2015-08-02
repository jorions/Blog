<!DOCTYPE html>

<html>
<head>
    <title>No Login</title>
</head>
</html>

<?php
include "checkDatabase.php";

echo "<h2>You are not logged in!</h2>";
echo "Either <a href='newUser.php'>create a new account</a> or if you already have an account, <a href='login.php'>login</a>.";
?>