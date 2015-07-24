<?php
if(isset($_POST["logout"])) {
    session_destroy();
    header("location: login.php");
}
?>

<form action="checkLogout.php" method="POST">
    <input type="submit" name="logout" value="Click here to log out">
</form>
