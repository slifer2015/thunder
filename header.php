
<html>
<head>
    <!--Import materialize.css-->
    <title class="grey-text">SocialNetwork</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="libs/css/materialize.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="libs/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="libs/css/style.css" media="screen,projection"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="admin/style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="admin/css/style.css">
    <!-- For  login-->
    <link rel="stylesheet" href="libs/css/IndexStyle.css">
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="libs/css/normalize.css">
    <link rel="stylesheet" href="libs/css/style.css">
</head>
<body class="bodyAll " >
<nav>
    <div class="nav-wrapper navigationBar">
        <a class="brand-logo  textStyle">Social Network</a>
        <ul id="nav-mobile" class="right side-nav ">
            <?php if ($_GB->GetSession('userID') != false) {
                $userID = $_GB->GetSession('userID');
                $query = $_DB->select('users', '*', '`id`=' . $userID);
                $fetch = $_DB->fetchAssoc($query);
                $username = $fetch['UserName'];
                ?>
            <?php
            }
            ?>

            <?php if ($_GB->GetSession('userID') != false) {
                ?>

                <li><a href="home.php">Home</a></li>
                <li><a href="account.php">Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php
            }
            ?>

        </ul>
        <ul class="right hide-on-med-and-down">
            <?php if ($_GB->GetSession('userID') != false) {
                $userID = $_GB->GetSession('userID');
                $query = $_DB->select('users', '*', '`id`=' . $userID);
                $fetch = $_DB->fetchAssoc($query);
                $username = $fetch['UserName'];
                $userImage = $fetch['UserImage'];
                // session_destroy();
                // header('Location: home.php');
                ?>

                <li>

                    <ul>
                        <li>
                            <?php
                            if ($userImage != null) {
                                ?>
                                <center>
                                    <img class="img-rounded  "
                                         src="image/profile/<?php echo $userImage ?>"
                                         align="bottom"
                                         style="height:45px;width: 45px; margin-top: 10px; margin-bottom: auto ">
                                </center>
                            <?php
                            }
                            ?>
                        </li>
                        <li>
                            <a href="profile.php?cmd=details&id=" style="font-size:large;"><?php echo $username ?></a>

                        </li>


                    </ul>
                </li>

                <li><a href="home.php" style="font-size:large;">Home</a></li>
                <li><a href="findFriends.php" style="font-size:large;"><i class="material-icons">search</i></a></li>
                <li><a href="Messages.php" style="font-size:large;"><i class="material-icons">question_answer</i></a>
                </li>
                <li><a href="favorites.php" style="font-size:large;"><i class="material-icons">grade</i></a></li>
                <li><a href="logout.php" style="font-size:large;">Logout</a></li>
            <?php
            }
            ?>
        </ul>

    </div>

</nav>
