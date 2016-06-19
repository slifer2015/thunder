<?php
include 'init.php';
include 'header.php';

if (isset($_GET['activate'])) {
    $hash = $_DB->escapeString($_GET['activate']);
    $query = $_DB->select('activation', '`userid`', "`hash` = '{$hash}'");
    if ($_DB->numRows($query)) {
        $fetch = $_DB->fetchAssoc($query);
        $update = $_DB->update('users', '`isActivated` = 1', "`id` = " . $fetch['userid']);
        if ($update) {
            echo '<div class="container"><div class="card"><blockquote>
     Your account has been activated successfully
    </blockquote></div></div>';
        }
    } else {
        echo '<div class="container"><div class="card"><blockquote>
      Invalid Activation Key
    </blockquote></div></div>';
    }
    include 'footer.php';
    exit();
} else if (isset($_GET['password'])) {

    $hash = $_DB->escapeString($_GET['password']);
    $query = $_DB->select('rest_password', '`userid`', "`hash` = '{$hash}'");
    if ($_DB->numRows($query) != 0) {
        $fetch = $_DB->fetchAssoc($query);
        if (isset($_POST['password'])) {
            if ($_POST['repassword'] == $_POST['password']) {
                $pass = md5($_POST['password']);
                $update = $_DB->update("users", "`UserPassword` = '{$pass}'", '`id` = ' . $fetch['userid']);
                if ($update) {
                    $_DB->delete('rest_password', "`hash` = '{$hash}'");
                    echo '<div class="container"><div class="card"><blockquote>
      Your password has been updated
    </blockquote></div></div>';
                }
            } else {
                echo '<div class="container"><div class="card"><blockquote>
      Passwords are not the same please check ur password
    </blockquote></div></div>';
            }
        } else {
            ?>
            <div class="container">
                <div class="card publish-form-container">
                    <form id="loginForm" class="col s12" method="POST" action="home.php?password=<?php echo $hash; ?>">

                        <div class="input-field">
                            <input id="username-field" type="password" name="password">
                            <label for="username-field">Password...</label>
                        </div>
                        <div class="input-field">
                            <input id="password-field" type="password" name="repassword">
                            <label for="password-field">Repeat Password...</label>
                        </div>
                        <div>
                            <button class="btn waves-effect waves-light pull-right" type="submit">Login
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Post Card //-->
            </div>
        <?php
        }
    } else {
        echo '<div class="container"><div class="card"><blockquote>
      Invalid Link
    </blockquote></div></div>';
    }
    include 'footer.php';
    exit();
}
if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
}
?>

<?php
$userID = $_SESSION['userID'];
$query = $_DB->select('friends', '*', "`status` = $Relation->Accepted ");
$rows = $_DB->numRows($query);
if ($rows != 0) {
    $querySQL = "SELECT FS.*,
						U.FullName AS holderFullName,
						U.UserName AS holderUserName,
						U.UserImage AS holderImage

						FROM prefix_feeds FS

						LEFT JOIN prefix_friends AS F
						ON F.status = {$Relation->Accepted}

						LEFT JOIN prefix_users AS U
						ON U.id = FS.holderID

						WHERE ((FS.holderID = {$userID}
						 OR FS.holderID = F.providerId
						 OR FS.holderID = F.requestId )
						  AND ((F.providerId ={$userID}
						   OR F.requestId = {$userID})
						   AND F.status = {$Relation->Accepted}))
						   AND U.UserState = 'Active'
						GROUP BY FS.id ORDER BY FS.id DESC
					";
} else {
    $querySQL = "SELECT FS.*,
						U.FullName AS holderFullName,
						U.UserName AS holderUserName,
						U.UserImage AS holderImage

						FROM prefix_feeds FS

						LEFT JOIN prefix_users AS U
						ON U.id = FS.holderID

						WHERE FS.holderID = {$userID}
						GROUP BY FS.id ORDER BY FS.id DESC
					";
}
$query = $_DB->MySQL_Query($querySQL);
$postsTotalCount = $_DB->numRows($query);
$page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
$_PAG = new Pagination($page,
    $postsTotalCount
    , 10,
    'api.php?page=#i#');
/*
*/
?>

    <div class="container2">
        <div class="card publish-form-container">
            <form class="col s12" method="POST" enctype="multipart/form-data" action="">
                <div class="center">
                    <button class="btn-large waves-effect waves-light" onclick="getFile();
            return false;">Choose Image
                        <i class="mdi-action-backup right"></i>
                    </button>
                    <input type="file" name="image" id="imageFile" style='height: 0px;width:0px; overflow:hidden;'>
                </div>
                <div class="input-field">
                    <input id="status-field" type="text" name="status">
                    <label for="status-field">Say Something...</label>
                </div>
                <div>
                    <label>Privacy</label><br>
                    <input name="public" type="radio" id="public"/>
                    <label for="public">Public</label>
                    <input name="private" type="radio" id="private"/>
                    <label for="private">Private</label>
                    <button class="btn waves-effect waves-light pull-right" type="submit">Submit
                        <i class="mdi-content-send right"></i>
                    </button>
                </div>
            </form>
        </div>
        <!-- Post Card //-->
        <div id="postsContainer">

        </div>
        <div id="loadingSpinner" class="center-align">
            <div class="preloader-wrapper small active">
                <div class="spinner-layer spinner-green-only">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- container//-->
<?php
include 'footer.php';
?>