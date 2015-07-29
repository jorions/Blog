<!DOCTYPE html>

<h2>Login</h2>
<form action="login.php" method="POST">
    Enter your username: <input type="text" name="username" value=""><br>
    Enter your password: <input type="text" name="password" value=""><br>
    <br>
    <input type="submit" name="submit" value="Login" style="width: 300px"><br>
    <br><br>
    If you dont have an account <a href="newUser.php">click here</a>
</form>

<br><br>

<?php
session_start(); //required here because this page does not use "include checkLogin.php";

include "checkDatabase.php";

if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //login error handling
    if($username == "") {
        echo "<em>Please enter a username</em><br>";
    }
    if($password == "") {
        echo "<em>Please enter a password</em><br>";
    }

    //login (with security)
    if($username !="" && $password != "" && preg_match("#^[a-zA-Z0-9]+$#", $username) && preg_match("#^[a-zA-Z0-9]+$#", $password)) {
        //check username and password
        $check = $db->prepare("SELECT * FROM user_logins WHERE username=? AND password=?");
        $check->bind_param("ss", $username, $password);
        $check->execute();
        $check->store_result(); //stores value of executed statement
        $rows = $check->num_rows;
        if($rows == 1) {
            $_SESSION["user"] = $username;
            header("location: profile.php");
        } else {
            echo "<em>Invalid username or password</em>";
        }
    } else if ($username !="" && $password != "" && (!preg_match("#^[a-zA-Z0-9]+$#", $username) || !preg_match("#^[a-zA-Z0-9]+$#", $password))) {
        echo "<em>Usernames and passwords can only contain letters and numbers</em>";
    } else if ($username !="" && $password != "") {
        echo "<em>Incorrect username or password</em>";
    }
}

?>