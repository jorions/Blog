<?php

//pull in database
$db = new mysqli("localhost", "root", "root", "personal_blog");

//connection error handling
if($db->connect_errno) {
    echo "Oh no! Failed to connnect to MySQL<br>";
    echo $db->connect_error;
    exit();
}

//initiate session variables
session_start();

//get name of current page for use on other pages
$currentPage = $_SERVER["PHP_SELF"];

//if not on the login, noLogin, or newUser page, check if user is logged in
if($currentPage != "/Blog/login.php" && $currentPage != "/Blog/noLogin.php" && $currentPage != "/Blog/newUser.php") {
    if(!isset($_SESSION["user"])) {
        header("location: noLogin.php");
    }
}

//check if logout button is pressed
if(isset($_POST["logout"])) {
    session_destroy();
    header("location: login.php");
}

//check if createPost button is pressed
if(isset($_POST["createPost"])) {
    header("location: createPost.php");
}

//check if index button is pressed
if(isset($_POST["goToIndex"])) {
    header("location: index.php");
}

//check if users button is pressed
if(isset($_POST["goToUsers"])) {
    header("location: allUsers.php");
}

//check if profile button is pressed
if(isset($_POST["goToProfile"])) {
    header("location: profile.php");
}

//if not on the login, noLogin, or newUser pages show buttons
if($currentPage != "/Blog/login.php" && $currentPage != "/Blog/noLogin.php" && $currentPage != "/Blog/newUser.php") {
    ?>
    <form action="checkDatabase.php" method="POST">
        <input type="submit" name="logout" value="Log out">
        <input type="submit" name="createPost" value="Create post">
        <input type="submit" name="goToIndex" value="All blogs">
        <input type="submit" name="goToUsers" value="All users">
        <input type="submit" name="goToProfile" value="Your profile">
        <br>
    </form>
    <hr />
    <form action="searchResults.php" method="GET">
        <input type="search" name="query" placeholder="Enter search">
        <input type="submit" name="goToSearch" value="Search">
        <br />
        <br />
        <!--Creating the radio buttons all with the same name prevents multiple radio buttons from being checked at once -->
        Everwhere <input type="radio" name="searchParam" value="everywhere" checked>
        Authors <input type="radio" name="searchParam" value="authors">
        Titles <input type="radio" name="searchParam" value="titles">
        Contents <input type="radio" name="searchParam" value="contents">
    </form>
    <hr />
    <?php
}
?>