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
include "checkDatabase.php";

if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //if username or password are blank
    if($username == "") {
        echo "<em>Please enter a username</em><br>";
    }
    if($password == "") {
        echo "<em>Please enter a password</em><br>";
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
            header("location: profile.php");
        //else if there is no returned value, then the password or username is incorrect
        } else {
            echo "<em>Incorrect username or password</em>";
        }
    //else if username and password are entered but contain illegal characters
    } else if ($username !="" && $password != "" && (!preg_match("#^[a-zA-Z0-9]+$#", $username) || !preg_match("#^[a-zA-Z0-9]+$#", $password))) {
        echo "<em>Usernames and passwords can only contain letters and numbers</em>";
    }
}

?>