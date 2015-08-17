<!DOCTYPE html>

<html>
<head>
    <title>Login</title>
</head>
</html>

<h2>Login</h2>
<form action="login.php" method="POST">
    <input type="text" name="username" placeholder="username" maxlength="25"><br>
    <input type="password" name="password" placeholder="password" maxlength="25"><br>
    <br>
    <input type="submit" name="submit" value="Login" style="width: 130px"><br>
    <br><br>
    If you dont have an account <a href="newUser.php">click here</a>
</form>

<br><br>

<?php
include "checkDatabase.php";

if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //if username or password are blank
    if($username == "") {
        echo "<h3 style='color:blue'>Please enter a username</h3>";
    }
    if($password == "") {
        echo "<h3 style='color:blue'>Please enter a password</h3>";
    }

    //if the username and password have values and no illegal characters, check login database
    if($username !="" && $password != "" && preg_match("#^[a-zA-Z0-9]+$#", $username) && preg_match("#^[a-zA-Z0-9]+$#", $password)) {

        //check username and password against database
        $check = $db->prepare("SELECT * FROM user_logins WHERE username=? AND password=?");
        $check->bind_param("ss", $username, $password);
        $check->execute();
        //stores value of executed statement and enables num_rows method to be used
        $check->store_result();
        $rows = $check->num_rows;
        //if there is a returned row then the password and username are correct, so login
        if($rows == 1) {
            $_SESSION["user"] = $username;
            $_SESSION["password"] = $password;
            header("location: profile.php");
        //else if there is no returned value, then the password or username is incorrect
        } else {
            echo "<h3 style='color:blue'>Incorrect username or password</h3>";
        }
    //else if username and password are entered but contain illegal characters
    } else if ($username !="" && $password != "" && (!preg_match("#^[a-zA-Z0-9]+$#", $username) || !preg_match("#^[a-zA-Z0-9]+$#", $password))) {
        echo "<h3 style='color:blue'>Usernames and passwords can only contain letters and numbers</h3>";
    }
}

?>