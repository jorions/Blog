<?php
include "checkLogin.php";
include "checkDatabase.php";

if(isset($_POST["submit"])) {
    session_destroy();
    header("location: login.php");
}

echo "Congratulations <strong>$_SESSION[user]</strong> - you made it to the home page!";
echo "<br><br>";
echo "<a href='index.php'>Click here</a> to go to the homepage.";
echo "<br><br>";
?>

<form action="profile.php" method="POST">
    <input type="submit" name="submit" value="Click here to log out">
</form>
