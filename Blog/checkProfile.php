<?php
if(isset($_POST["goToProfile"])) {
    header("location: profile.php");
}
?>

<form action="checkProfile.php" method="POST">
    <input type="submit" name="goToProfile" value="Click here to view your profile">
</form>
<br>