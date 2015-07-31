<form action="Day16.php" method="POST">
    Enter a number!<input type="text" name="size" value="">
    <input type="submit" name="submit" value="Submit">
</form>

<?php
if(isset($_POST["submit"])) {
    if($_POST["size"] != "" && $_POST["size"] > 0) {
        createShape($_POST["size"]);
    }
}

function createShape($size) { ?>
<pre>
<?php
for($row = 0; $row < $size; $row++) {
    for($col = 0; $col <= $row; $col++) {
        echo "*";
    }
    echo "\n";
}
?>
</pre>

<?php
}
?>