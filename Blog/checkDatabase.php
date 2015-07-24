<?php

//pull in database
$db = new mysqli("localhost", "root", "root", "personal_blog");

//connection error handling
if($db->connect_errno) {
    echo "Oh no! Failed to connnect to MySQL<br>";
    echo $db->connect_error;
    exit();
}

?>