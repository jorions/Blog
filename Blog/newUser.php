<!DOCTYPE html>

<html>
<head>
    <title>Set Up New Account</title>
</head>
</html>

<h2>Create a new account</h2>

<form action="newUser.php" method="POST">
    Enter a username: <input type="text" name="username" value=""><br>
    Enter a password: <input type="text" name="password" value=""><br>
    <br>
    <input type="submit" name="submit" value="Create Account" style="width: 250px"><br>
</form>


<br><br>

<a href="login.php">Click here</a> to return to the login page.

<br><br>

<?php
include "checkDatabase.php";

if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //if the username or password are blank
    if($username == "") {
        echo "<em>Please enter a username</em><br>";
    }
    if($password == "") {
        echo "<em>Please enter a password</em><br>";
    }

    //if the username and password have values and no illegal characters, check login database
    if($username !="" && $password != "" && preg_match("#^[a-zA-Z0-9]+$#", $username) && preg_match("#^[a-zA-Z0-9]+$#", $password)) {

        //check username against database
        $check = $db->prepare("SELECT * FROM user_logins WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        //stores value of executed statement and enables num_rows method to be used
        $check->store_result();
        $rows = $check->num_rows;
        //if there is a returned row then the username already exists, so don't permit it to be used
        if($rows == 1) {
            echo "<em>That username already exists</em>";
        //else if there is no returned value, then the username doesn't exist, so permit it to be used
        } else {
            $insert = $db->prepare("INSERT INTO user_logins (username, password) VALUES (?,?)");
            $insert->bind_param("ss", $username, $password);
            $insert->execute();
            $_SESSION["user"] = $username;
            header("location: profile.php");
        }
    //else if username and password are entered but contain illegal characters
    } else if ($username !="" && $password != "" && (!preg_match("#^[a-zA-Z0-9]+$#", $username) || !preg_match("#^[a-zA-Z0-9]+$#", $password))) {
        echo "<em>Usernames and passwords can only contain letters and numbers</em>";
    }
}