<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php
        if(isset($_GET["id"])) {
            echo "<title>view ribbits</title>";
        } else {
            echo "<title>no user selected</title>";
        }
    ?>
</head>
<body>
    <?php
        include "mainFunctions.php";
    ?>
</body>
</html>