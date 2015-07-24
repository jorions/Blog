<h2>Create a new account</h2>

<form action="new_user.php" method="POST">
    Enter a username: <input type="text" name="username" value=""><br>
    Enter a password: <input type="text" name="password" value=""><br>
    <br>
    <input type="submit" name="submit" value="Create Account" style="width: 250px"><br>
</form>

<br><br>

<?php
session_start();

include "checkDatabase.php";

if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if($username == "") {
        echo "<em>Please enter a username</em><br>";
    }
    if($password == "") {
        echo "<em>Please enter a password</em><br>";
    }

    //login (with security)
    if($username !="" && $password != "" && preg_match("#^[a-zA-Z0-9]+$#", $username) && preg_match("#^[a-zA-Z0-9]+$#", $password)) {
        //check if username and password already exists
        $check = $db->prepare("SELECT * FROM user_logins WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result(); //stores value of executed statement
        $rows = $check->num_rows;
        if($rows == 1) {
            echo "<em>That username already exists</em>";
        } else {
            $insert = $db->prepare("INSERT INTO user_logins (username, password) VALUES (?,?)");
            $insert->bind_param("ss", $username, $password);
            $insert->execute();
            $_SESSION["user"] = $username;
            header("location: profile.php");
        }
    } else if ($username !="" && $password != "" && (!preg_match("#^[a-zA-Z0-9]+$#", $username) || !preg_match("#^[a-zA-Z0-9]+$#", $password))) {
        echo "<em>Usernames and passwords can only contain letters and numbers</em>";
    }
}