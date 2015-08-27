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

<header>
    <div class="wrapper">
        <a href="index.php"><img src="images/logo.png"></a>
        <span>Twitter Clone</span>
    </div>
        <table>
            <tr>
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
                <td width="40px"></td>
                <td>
                    <form action="mainFunctions.php" method="POST">
                        <input id="btnLogOut" type="submit" name="goToLogout" value="logout">
                        <input id="btnAllUsers" type="submit" name="goToAllUsers" value="all users">
                    </form>
                </td>
            </tr>
        </table>
    </div>
</header>
<div id="content">


<?php
//pull list of all ribbits for index.php
if($currentPage == "/ribbit/index.php") {

    //get list of all ribbits
    $ribbits = $db->query("SELECT * FROM ribbit ORDER BY created DESC");

    echo "<div class='wrapper'>";
        echo "<div id='ribbits' class='panel left'>";
            echo "<h1>public ribbits</h1>";
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
                listRibbit($picture, $row["user_id"], $profile, $username, date("M d", strtotime($row["created"])), $row["content"]);
            }
            $ribbits->close();
        echo "</div>";
    echo "</div>";
echo "</div>";
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
        $ribbits = $db->prepare("SELECT content, created FROM ribbit WHERE user_id=? ORDER BY created DESC");
        $ribbits->bind_param("i", $_GET["id"]);
        $ribbits->execute();
        $ribbits->bind_result($content, $time);

        //if a username has been returned output ribbits, otherwise provide prompt
        if($username != "") {
            echo "<div class='wrapper'>";
                echo "<div id='ribbits' class='panel left'>";
                    echo "<h1>$username's ribbits</h1>";

                    //iterate through each ribbit
                    while ($ribbits->fetch()) {

                        //echo ribbit
                        listRibbit ($picture, $_GET["id"], $profile, $username, date("M d", strtotime($time)), $content);
                    }
                    $ribbits->close();
                echo "</div>";
            echo "</div>";
        echo "</div>";

        //if no username has been returned list all users
        } else {
            $ribbits->close();

            echo "<div class='wrapper'>";
                echo "<div id='ribbits' class='panel left'>";
                    echo "<h1>no user selected - pick a user</h1>";

                    //get profiles of all users
                    listAllUsers($db);

                echo "</div>";
            echo "</div>";
        echo "</div>";
        }

    //else if no id specified
    } else {
        echo "<div class='wrapper'>";
            echo "<div id='ribbits' class='panel left'>";
                echo "<h1>no user selected - pick a user</h1>";

                //get profiles of all users
                listAllUsers($db);

            echo "</div>";
        echo "</div>";
    echo "</div>";
    }
}



//list all users on viewAllUsers.php
if($currentPage == "/ribbit/viewAllUsers.php") {
    echo "<div class='wrapper'>";
        echo "<div id='ribbits' class='panel left'>";
            echo "<h1>all users</h1>";

            //get profiles of all users
            listAllUsers($db);

        echo "</div>";
    echo "</div>";
echo "</div>";
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
                    echo "<div class='wrapper'>";
                        echo "<div id='ribbits' class='panel left'>";
                            echo "<h1>usernames containing '$_GET[query]'</h1>";

                            //echo profiles
                            while ($profileSearch->fetch()) {
                                echo "<div class='userWrapper'>";
                                    echo "<img class='avatar' src='$picture'>";
                                    echo "<a href='viewUser.php?id=$id'><span class='name'>$profile</span></a> @$username";
                                    echo "<p> </p>";
                                echo "</div>";
                            }
                        echo "</div>";
                    echo "</div>";
                }
                $profileSearch->close();
            }

            if ($searchRibbits == "TRUE") {

                //prepare ribbit search (using JOIN syntax 2 (more info here: http://stackoverflow.com/questions/9853586/sql-join-multiple-tables))
                $ribbitSearch = $db->prepare("SELECT profile.user_id, profile.first_name, profile.profile_pic_url, user.username, ribbit.created, ribbit.content FROM profile JOIN user ON profile.user_id = user.id JOIN ribbit ON ribbit.user_id = user.id WHERE ribbit.content LIKE ? ORDER BY ribbit.created DESC");
                $search = "%" . $_GET["query"] . "%";
                $ribbitSearch->bind_param("s", $search);
                $ribbitSearch->execute();
                $ribbitSearch->bind_result($id, $profile, $picture, $username, $time, $content);
                $ribbitSearch->store_result();
                $numRibbitRows = $ribbitSearch->num_rows;

                //if there are any ribbit matches
                if ($numRibbitRows > 0) {
                    echo "<div class='wrapper'>";
                        echo "<div id='ribbits' class='panel left'>";
                            echo "<h1>ribbits containing '$_GET[query]'</h1>";

                            //echo ribbits
                            while ($ribbitSearch->fetch()) {
                                listRibbit($picture, $id, $profile, $username, date("M d", strtotime($time)), $content);
                            }

                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
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

        //echo the closing div for the main content of the page
        echo "</div>";
    }
}

?>

<footer>
    <div class="wrapper">
        Ribbit - A Twitter Clone
    </div>
</footer>






<?php
function listRibbit ($picture, $id, $profile, $username, $time, $content) {
    echo "<div class='ribbitWrapper'>";
        echo "<img class='avatar' src='$picture'>";
        echo "<a href='viewUser.php?id=$id'><span class='name'>$profile</span></a> @$username <span class='time'>$time</span>";
        echo "<p>$content</p>";
    echo "</div>";
}

function listAllUsers ($db) {

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

        //echo current user
        echo "<div class='userWrapper'>";
            echo "<img class='avatar' src='$row[profile_pic_url]'>";
            if($numRibbits == 1) {
                echo "<a href='viewUser.php?id=$row[user_id]'><span class='name'>$row[first_name]</span></a> @$username <span class='count'>$numRibbits ribbit</span>";
            } else {
                echo "<a href='viewUser.php?id=$row[user_id]'><span class='name'>$row[first_name]</span></a> @$username <span class='count'>$numRibbits ribbits</span>";
            }
            echo "<p> </p>";
        echo "</div>";
    }
}