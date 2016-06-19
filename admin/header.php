<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ادمین</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="شبکه مجازی">

    <meta http-equiv="x-pjax-version" content="v173">

    <link rel="stylesheet" type="text/css" href="libs/assets/css/print.css" media="print">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!-- fav and touch icons -->
    <link rel="shortcut icon" href="libs/images/fav.png">
    <link rel="shortcut icon" href="libs/images/fav.png">

    <!-- build:css styles/vendor.css -->
    <!-- bower:css -->
    <link rel="stylesheet" href="libs/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="libs/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="libs/assets/css/animate.min.css">
    <link rel="stylesheet" href="libs/assets/css/hover-min.css">
    <!-- endbower -->
    <!-- endbuild -->

    <!-- build:css(.tmp) styles/main.css -->
    <link id="style-components" href="libs/assets/css/loaders.css" rel="stylesheet">
    <link id="style-components" href="libs/assets/css/bootstrap-theme.css" rel="stylesheet">
    <link id="style-components" href="libs/assets/css/dependencies.css" rel="stylesheet">
    <link id="style-components" href="libs/assets/css/summernote.css" rel="stylesheet">
    <link id="style-base" href="libs/assets/css/style.css" rel="stylesheet">
    <link id="style-responsive" href="libs/assets/css/stilearn-responsive.css" rel="stylesheet">
    <link id="style-helper" href="libs/assets/css/helper.css" rel="stylesheet">
    <link id="style-sample" href="libs/assets/css/pages-style.css" rel="stylesheet">
    <link id="style-sample" href="libs/assets/css/bootstrap-tagsinput.css" rel="stylesheet">
    <!-- endbuild -->

    <link rel="stylesheet" href="libs/assets/css/jquery.tablesorter.pager.css">

    <!-- persian datePicker style -->
    <link href="libs/assets/css/persianDatepicker-default.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js"></script>
    <![endif]-->

    <script src="libs/assets/js/jquery.js"></script>

</head>

<body class="animated fadeIn">
    <div class="spinnerContainer">
        <!-- loading -->
        <div class="spinner">
            <div class="spinner-container container1">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
            </div>
            <div class="spinner-container container2">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
            </div>
            <div class="spinner-container container3">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
            </div>
        </div>
    </div>

    <!-- section header -->
    <header class="header fixed">

        <!-- header-profile -->
        <div class="header-profile pull-left">
            <div class="profile-nav">
                <span class="profile-username text-16">حساب کاربری</span>
                <a  class="dropdown-toggle" data-toggle="dropdown">
                    <span class="fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu animated fadeInDown pull-right" role="menu">

                    <?php if ($_GB->GetSession('admin') != false) {
                        $userID = $_GB->GetSession('admin');
                        ?>

                        <li>
                            <?php
                            $userID = $_GB->GetSession('admin');
                            $query = $_DB->select('admins', '*', '`id`=' . $userID);
                            $fetch = $_DB->fetchAssoc($query);
                            $username = $fetch['AdminName'];
                            ?>
                            <a href="profile.php?cmd=details&id="<?php echo $userID; ?> class="text-16"><i class="fa fa-check-circle-o"></i> <?php echo $username ?> </a>
                            <?php
                            ?>
                        </li>

                        <li><a href="profile.php?cmd=details&id="<?php echo $userID; ?> class="text-16"><i class="fa fa-user"></i> پروفایل</a></li>
                        <li><a href="logout.php" class="text-16"><i class="fa fa-power-off"></i> خروج از حساب</a></li>

                        <?php
                    } else {
                        $userID = 0;
                        ?>
                        <?php
                    }
                    ?>

                </ul>
            </div>
            <div class="profile-picture">
                <img alt="me" src="libs/assets/statics/adminPics/user.jpg" >
            </div>
        </div><!-- header-profile -->

        <div class="pull-right logoHolder">
            <img src="libs/assets/img/logo.png" alt="شبکه مجازی">
        </div>
    </header><!--/header-->

    <!-- content section -->
    <section class="section">

        <aside class="side-left">
            <ul class="sidebar">
                <li>
                    <a href="index.php?cmd=index">
                        <i class="sidebar-icon fa fa-home"></i>
                        <span class="sidebar-text">خانه</span>
                    </a>
                </li><!--/sidebar-item-->
                <li>
                    <a href="users.php?cmd=users">
                        <i class="sidebar-icon fa fa-users"></i>
                        <span class="sidebar-text">کاربران</span>
                    </a>
                </li>
                <li>
                    <a href="messages.php?cmd=messages">
                        <i class="sidebar-icon fa fa-paper-plane-o"></i>
                        <span class="sidebar-text">پیام ها</span>
                    </a>
                </li>
                <li>
                    <a href="comments.php?cmd=comments">
                        <i class="sidebar-icon fa fa-comments"></i>
                        <span class="sidebar-text">کامنت ها</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php?cmd=reports">
                        <i class="sidebar-icon fa fa-bar-chart"></i>
                        <span class="sidebar-text">گزارشات</span>
                    </a>
                </li>
                <li>
                    <a href="feedback.php?cmd=feedback">
                        <i class="sidebar-icon fa fa-exchange"></i>
                        <span class="sidebar-text">پشتیبانی و سوالات</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="sidebar-icon fa fa-cogs"></i>
                        <span class="sidebar-text">تنظیمات</span>
                    </a>
                </li>
            </ul>
        </aside>
