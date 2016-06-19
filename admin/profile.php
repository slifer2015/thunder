<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 10/3/2015
 * Time: 2:20 AM
 */
include 'init.php';
include 'header.php';

if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
?>
<?php

if (isset($_GET['cmd']) && $_GET['cmd'] == 'details') {
$query = $_DB->select('admins', '*', '`id`=' . $userID);
$fetch = $_DB->fetchAssoc($query);
$username = $fetch['AdminName'];
?>
<div id="profilecenter" class="white center">
    <div id="CenterCol">



    </div>
    <table class="table table-bordered">
        <form method="post" action="" enctype="multipart/form-data">

            <tr>
                <th>UserName :</th>
                <td>
                    <?php echo $username ?>
                </td>
            </tr>
        </form>

    </table>
    <?php echo ' <a href="?cmd=editAdminProfile&id=' . $userID . '" class="btn-floating btn-large waves-effect waves-light red" style="float=right;"><i class="material-icons">edit</i></a>'; ?>

    <?php
    //  }
    } else if (isset($_GET['cmd']) && $_GET['cmd'] == 'editAdminProfile') {
        $AdminId = $_DB->escapeString($userID);
        $fetch = $Users->getAdminDetails($AdminId);
        $username = $fetch['AdminName'];
        $oldPassword = $fetch['AdminPassword'];

        if (isset($_POST['username'])) {

            foreach ($_POST as $key => $value) {
                $_POST[$key] = $_DB->escapeString(trim($value));
            }

            if ($_POST['newPassword'] != $_POST['confirmPassword']) {
                echo $_GB->ShowError(' passwords are not match');
                echo $_GB->MetaRefresh('profile.php?cmd=editAdminProfile&id=' . $userID . '', 1);
            } else if (md5($_POST['oldPassword']) != $oldPassword) {
                echo $_GB->ShowError(' your old password is not correct plse try again');
                echo $_GB->MetaRefresh('profile.php?cmd=editAdminProfile&id=' . $userID . '', 1);
            } else {
                $fields = "`AdminName` = '" . $_POST['username'] . "'";
                if (!empty($_POST['newPassword'])) {
                    $fields .= ",`AdminPassword` = '" . md5($_POST['newPassword']) . "'";
                }
                $update = $_DB->update('admins', $fields, "`id` = {$userID}");
                if ($update) {
                    echo $_GB->ShowError('Your profile is update successfully', 'yes');
                    echo $_GB->MetaRefresh('profile.php?cmd=details&id=' . $userID . '', 1);
                } else {
                    echo $_GB->ShowError('Failed to update your profile try again');
                }
            }
        } else {
            ?>
            <div id="profilecenter" class="white center">
                <table class="table table-bordered">
                    <form method="post" action="" enctype="multipart/form-data">
                        <tr>
                            <th>Name :</th>
                            <td>
                                <textarea name="username" class="form-control"><?php echo $username ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>Old Password :</th>
                            <td>
                                <input type="password" name="oldPassword" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>New Password :</th>
                            <td>
                                <input type="password" name="newPassword" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Retype New Password :</th>
                            <td>
                                <input type="password" name="confirmPassword" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"><input type="submit" class="btn btn-success form-control center"
                                                   value="Save">
                            </td>
                        </tr>
                    </form>
                    <tr>
                        <td colspan="2">
                            <?php echo '<a href="profile.php?cmd=details&id=' . $userID . '">' ?>
                            <input type="button" class="btn btn-block form-control center" value="Cancel">
                        </td>
                    </tr>
                </table>
            </div>

        <?php

        }
    } ?>
    <?php
    include 'footer.php';
    ?>
