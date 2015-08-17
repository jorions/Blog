<!DOCTYPE html>

<?php
include "checkDatabase.php";
?>

<html>
<head>
    <title>Update Your Profile</title>
</head>
<body>
<h2>Update Your Profile</h2>

<?php
//if update button is pressed
if(isset($_POST["submitProfileUpdate"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //if the username or password are blank
    if($username == "") {
        echo "<h3 style='color:blue'>Please enter a username</h3>";
    }
    if($password == "") {
        echo "<h3 style='color:blue'>Please enter a password</h3>";
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
            echo "<h3 style='color:blue'>That username already exists</h3>";
            //else if there is no returned value, then the username doesn't exist, so permit it to be used
        } else {
            //update user login
            $update = $db->prepare("UPDATE user_logins SET username=?, password=? WHERE username=?");
            $update->bind_param("sss", $username, $password, $_SESSION["user"]);
            $update->execute();
            $update->close();

            //update blog posts
            $update = $db->prepare("UPDATE posts SET author=? WHERE author=?");
            $update->bind_param("ss", $username, $_SESSION["user"]);
            $update->execute();
            $update->close();

            //set SESSION variables and return to Profile
            $_SESSION["user"] = $username;
            $_SESSION["password"] = $password;
            $_SESSION["newProfileInfo"] = "TRUE";
            header("location: profile.php");
        }
        //else if username and password are entered but contain illegal characters
    } else if ($username !="" && $password != "" && (!preg_match("#^[a-zA-Z0-9]+$#", $username) || !preg_match("#^[a-zA-Z0-9]+$#", $password))) {
        echo "<h3 style='color:blue'>Usernames and passwords can only contain letters and numbers</h3>";
    }
}
?>


<form action="updateProfile.php" method="POST">
    Username:<br />
    <input type="text" name="username" maxlength="25" value="<?php echo $_SESSION["user"]; ?>">
    <br />
    <br />
    Password:<br />
    <input type="text" name="password" maxlength="25" value="<?php echo $_SESSION["password"]; ?>">
    <br />
    <br />
    <input type="submit" name="submitProfileUpdate" value="Update Profile">
</form>
</body>
</html>