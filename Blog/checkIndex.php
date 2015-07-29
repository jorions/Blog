<?php
if(isset($_POST["goToIndex"])) {
    header("location: index.php");
}
?>

<form action="checkIndex.php" method="POST">
    <input type="submit" name="goToIndex" value="Click here to view all blogs">
</form>
<br>