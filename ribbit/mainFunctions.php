<?php

//pull in database
$db = new mysqli("ribbit.ccoefbt2lfct.us-east-1.rds.amazonaws.com", "student", "austincoding", "ribbit");



//connection error handling
if($db->connect_errno) {
    echo "Uh oh - failed to connect to MySQL...<br />";
    echo $db->connect_error;
    exit();
}



//initiate session variables
session_start();



//get name of current page for use on other pages
$currentPage = $_SERVER["PHP_SELF"];
?>


<!--link css-->
<link href="css/styles.css" type="text/css" rel="stylesheet" />



<?php
//check if logout button was pressed
if(isset($_POST["goToLogout"])) {
    header("location: login.php");
}



//check if view all users button was pressed
if(isset($_POST["goToAllUsers"])) {
    header("location: viewAllUsers.php");
}
?>


<!--top of page items-->
<table>
    <tr>
        <!--logo and navigation-->
        <td>
            <a href="index.php"><img src="images/logo.png" /></a>
            <form action="mainFunctions.php" method="POST">
                <input type="submit" name="goToLogout" value="logout">
                <input type="submit" name="goToAllUsers" value="all users">
            </form>
        </td>

        <!--gap between logo and search-->
        <td width="50"></td>

        <!--search bar-->
        <td>
            <form action="searchResults.php" method="GET">
                <input type="text" name="query" placeholder="search ribbit" />
                <input type="submit" name="goToSearch" value="search ribbits" />
                <br />
                everywhere <input type="radio" name="searchParam" value="everywhere" checked/>
                profiles <input type="radio" name="searchParam" value="usernames" />
                ribbits <input type="radio" name="searchParam" value="ribbits" />
            </form>
        </td>

        <!--gap between search and ribbit-->
        <td width="50"></td>

        <!--create ribbit-->
        <td>
            <form action="index.php" method="POST">
                <textarea name="ribbit" cols="30" rows="3" placeholder="share your ribbit"></textarea>
                <input type="submit" name="submitRibbit" value="ribbit!" />
            </form>
        </td>
    </tr>
</table>
<hr />



<?php
//pull list of all ribbits for index.php
if($currentPage == "/ribbit/index.php") {

    echo "<h1>all ribbits</h1>";

    //get list of all ribbits
    $ribbits = $db->query("SELECT * FROM ribbit ORDER BY created DESC");

    //iterate through each ribbit
    foreach($ribbits as $row) {

        //get profile name and image of current ribbit
        $getProfile = $db->prepare("SELECT first_name, profile_pic_url FROM profile WHERE id=?");
        $getProfile->bind_param("i", $row["user_id"]);
        $getProfile->execute();
        $getProfile->bind_result($profile, $picture);
        $getProfile->fetch();
        $getProfile->close();

        //get username of current ribbit
        $getUsername = $db->prepare("SELECT username FROM user WHERE id=?");
        $getUsername->bind_param("i", $row["user_id"]);
        $getUsername->execute();
        $getUsername->bind_result($username);
        $getUsername->fetch();
        $getUsername->close();

        //echo ribbit
        echo "<img src='$picture' /> $profile <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span><br />";
        echo $row["content"] . "<br /><br />";
    }
    $ribbits->close();
}



//view specific user's ribbits for viewUser.php
if($currentPage == "/ribbit/viewUser.php") {

    //if the name variable is set in the URL
    if (isset($_GET["id"])) {

        //get profile name and image of user
        $getProfile = $db->prepare("SELECT first_name, profile_pic_url FROM profile WHERE user_id=?");
        $getProfile->bind_param("i", $_GET["id"]);
        $getProfile->execute();
        $getProfile->bind_result($profile, $picture);
        $getProfile->fetch();
        $getProfile->close();

        //get username of user
        $getUsername = $db->prepare("SELECT username FROM user WHERE id=?");
        $getUsername->bind_param("i", $_GET["id"]);
        $getUsername->execute();
        $getUsername->bind_result($username);
        $getUsername->fetch();
        $getUsername->close();

        //get list of user's ribbits
        $ribbits = $db->prepare("SELECT content FROM ribbit WHERE user_id=? ORDER BY created DESC");
        $ribbits->bind_param("i", $_GET["id"]);
        $ribbits->execute();
        $ribbits->bind_result($content);

        //if a username has been returned output ribbits, otherwise provide prompt
        if($username != "") {
            echo "<h1>$username's ribbits</h1>";

            //iterate through each ribbit
            while ($ribbits->fetch()) {

                //echo ribbit
                echo "<img src='$picture' /> $profile <span class='username'><a href='viewUser.php?id=$_GET[id]'>@$username</a></span><br />";
                echo "$content<br /><br />";
            }
            $ribbits->close();

        //if no username has been returned list all users
        } else {
            $ribbits->close();
            echo "<h1>no user selected - pick a user</h1>";

            //get profiles of all users
            $allProfiles = $db->query("SELECT * FROM profile");

            //iterate through users
            foreach ($allProfiles as $row) {

                //get username of current user
                $getUsername = $db->prepare("SELECT username FROM user WHERE id=?");
                $getUsername->bind_param("i", $row["user_id"]);
                $getUsername->execute();
                $getUsername->bind_result($username);
                $getUsername->fetch();
                $getUsername->close();

                //get # of ribbits
                $getRibbits = $db->prepare("SELECT * FROM ribbit WHERE user_id=?");
                $getRibbits->bind_param("i", $row["user_id"]);
                $getRibbits->execute();
                $getRibbits->store_result();
                $numRibbits = $getRibbits->num_rows;
                $getRibbits->close();

                //echo current user (based on # of ribbits)
                if($numRibbits == 1) {
                    echo "<img src='$row[profile_pic_url]' /> $row[first_name] <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span> - $numRibbits ribbit<br /><br />";
                } else {
                    echo "<img src='$row[profile_pic_url]' /> $row[first_name] <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span> - $numRibbits ribbits<br /><br />";
                }
            }
        }

    //else if no id specified
    } else {
        echo "<h1>no user selected - pick a user</h1>";

        //get profiles of all users
        $allProfiles = $db->query("SELECT * FROM profile");

        //iterate through users
        foreach ($allProfiles as $row) {

            //get username of current user
            $getUsername = $db->prepare("SELECT username FROM user WHERE id=?");
            $getUsername->bind_param("i", $row["user_id"]);
            $getUsername->execute();
            $getUsername->bind_result($username);
            $getUsername->fetch();
            $getUsername->close();

            //get # of ribbits
            $getRibbits = $db->prepare("SELECT * FROM ribbit WHERE user_id=?");
            $getRibbits->bind_param("i", $row["user_id"]);
            $getRibbits->execute();
            $getRibbits->store_result();
            $numRibbits = $getRibbits->num_rows;
            $getRibbits->close();

            //echo current user (based on # of ribbits)
            if ($numRibbits == 1) {
                echo "<img src='$row[profile_pic_url]' /> $row[first_name] <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span> - $numRibbits ribbit<br /><br />";
            } else {
                echo "<img src='$row[profile_pic_url]' /> $row[first_name] <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span> - $numRibbits ribbits<br /><br />";
            }
        }
    }
}



//list all users on viewAllUsers.php
if($currentPage == "/ribbit/viewAllUsers.php") {
    echo "<h1>all users</h1>";

    //get profiles of all users
    $allProfiles = $db->query("SELECT * FROM profile");

    //iterate through users
    foreach ($allProfiles as $row) {

        //get username of current user
        $getUsername = $db->prepare("SELECT username FROM user WHERE id=?");
        $getUsername->bind_param("i", $row["user_id"]);
        $getUsername->execute();
        $getUsername->bind_result($username);
        $getUsername->fetch();
        $getUsername->close();

        //get # of ribbits
        $getRibbits = $db->prepare("SELECT * FROM ribbit WHERE user_id=?");
        $getRibbits->bind_param("i", $row["user_id"]);
        $getRibbits->execute();
        $getRibbits->store_result();
        $numRibbits = $getRibbits->num_rows;
        $getRibbits->close();

        //echo current user (based on # of ribbits)
        if ($numRibbits == 1) {
            echo "<img src='$row[profile_pic_url]' /> $row[first_name] <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span> - $numRibbits ribbit<br /><br />";
        } else {
            echo "<img src='$row[profile_pic_url]' /> $row[first_name] <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span> - $numRibbits ribbits<br /><br />";
        }
    }
}



//list search results on searchResults.php
if($currentPage == "/ribbit/searchResults.php") {

    //if there is a search query
    if(isset($_GET["query"]) && isset($_GET["searchParam"])) {

        //if the query is not empty
        if($_GET["query"] != "") {

            //setting up initial variables
            $searchUsernames = "";
            $searchRibbits = "";
            $numProfileRows = 0;
            $numRibbitRows = 0;

            //if "everywhere" radio button was clicked, set all variables to TRUE
            if ($_GET["searchParam"] == "everywhere") {
                $searchUsernames = "TRUE";
                $searchRibbits = "TRUE";
            }

            //if "usernames" radio button was clicked, set username variable to TRUE
            if ($_GET["searchParam"] == "usernames") {
                $searchUsernames = "TRUE";
            }

            //if "ribbits" radio button was clicked, set ribbits variable to TRUE
            if ($_GET["searchParam"] == "ribbits") {
                $searchRibbits = "TRUE";
            }

            //if username variable is true
            if ($searchUsernames == "TRUE") {

                //prepare profile search (using JOIN syntax 1, as opposed to separate select statements used elsewhere)
                $profileSearch = $db->prepare("SELECT pr.user_id, pr.first_name, pr.profile_pic_url, us.username FROM profile pr JOIN user us WHERE pr.user_id = us.id AND us.username LIKE ?");
                $search = "%" . $_GET["query"] . "%";
                $profileSearch->bind_param("s", $search);
                $profileSearch->execute();
                $profileSearch->bind_result($id, $profile, $picture, $username);
                $profileSearch->store_result();
                $numProfileRows = $profileSearch->num_rows;

                //if there are any profile matches
                if ($numProfileRows > 0) {
                    echo "<h1>usernames containing '$_GET[query]'</h1>";

                    //echo profiles
                    while ($profileSearch->fetch()) {
                        echo "<img src='$picture' /> $profile <span class='username'><a href='viewUser.php?id=$id'>@$username</a></span><br />";
                    }
                }
                $profileSearch->close();
            }

            if ($searchRibbits == "TRUE") {

                //prepare ribbit search (using JOIN syntax 2 (more info here: http://stackoverflow.com/questions/9853586/sql-join-multiple-tables))
                $ribbitSearch = $db->prepare("SELECT profile.user_id, profile.first_name, profile.profile_pic_url, user.username, ribbit.content FROM profile JOIN user ON profile.user_id = user.id JOIN ribbit ON ribbit.user_id = user.id WHERE ribbit.content LIKE ?");
                $search = "%" . $_GET["query"] . "%";
                $ribbitSearch->bind_param("s", $search);
                $ribbitSearch->execute();
                $ribbitSearch->bind_result($id, $profile, $picture, $username, $content);
                $ribbitSearch->store_result();
                $numRibbitRows = $ribbitSearch->num_rows;

                //if there are any ribbit matches
                if ($numRibbitRows > 0) {
                    echo "<h1>ribbits containing '$_GET[query]'</h1>";

                    //echo ribbits
                    while ($ribbitSearch->fetch()) {
                        echo "<img src='$picture' /> $profile <span class='username'><a href='viewUser.php?id=$id'>@$username</a></span><br />";
                        echo $content . "<br /><br />";
                    }
                }

                $ribbitSearch->close();
            }

            //if there are no returned search results
            if($numProfileRows == 0 && $numRibbitRows == 0) {
                echo "<h1>no results containing '$_GET[query]'";
            }

        //else if no query was entered
        } else {
            echo "<h1>no search entered</h1>";
        }
    }
}


/*
QUESTION - WHY DOES IT KEEP NOT WORKING WHEN I TRY TO MAKE THIS A SEPARATE FUNCTION?
//view all users function
function listAllUsers() {

    //get profiles of all users
    $allProfiles = $db->query("SELECT * FROM profile");

    //iterate through users
    foreach($allProfiles as $row) {

        //get username of current user
        $getUsername = $db->prepare("SELECT username FROM user WHERE id=?");
        $getUsername->bind_param("i", $row["user_id"]);
        $getUsername->execute();
        $getUsername->bind_result($username);
        $getUsername->fetch();
        $getUsername->close();

        //echo all users
        echo "<img src='$row[profile_pic_url]' /> $profile <span class='username'><a href='viewUser.php?id=$row[user_id]'>@$username</a></span><br />";
    }
}

*/