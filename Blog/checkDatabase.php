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

//if not on the index page, check if user is logged in
if($currentPage != "/Blog/login.php") {
    if(!isset($_SESSION["user"])) {
        header("location: noLogin.php");
    }
}

//check if logout button is pressed
if(isset($_POST["logout"])) {
    session_destroy();
    header("location: login.php");
}

//check if index button is pressed
if(isset($_POST["goToIndex"])) {
    header("location: index.php");
}

//check if profile button is pressed
if(isset($_POST["goToProfile"])) {
    header("location: profile.php");
}

//if not on the login, noLogin, or newUser pages, at least show logout button and search form
if($currentPage != "/Blog/login.php" && $currentPage != "/Blog/noLogin.php" && $currentPage != "/Blog/newUser.php") {
    ?>
    <form action="checkDatabase.php" method="POST">
        <input type="submit" name="logout" value="Log out">
        <?php
        //if not on the index page, show index button
        if($currentPage != "/Blog/index.php") { ?>
            <input type="submit" name="goToIndex" value="View all blogs">
        <?php
        }
        //if not on the profile page, show profile button
        if($currentPage != "/Blog/profile.php") { ?>
            <input type="submit" name="goToProfile" value="View your profile">
            <br>
        <?php
        } ?>
    </form>
    <br>
    <form action="searchResults.php" method="GET">
        <input type="text" name="query" value="Enter search">
        <input type="submit" name="goToSearch" value="Search">
    </form>
    <br>
    <?php
}
?>