<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 9/28/2015
 * Time: 12:22 AM
 */

include 'init.php';
include 'header.php';
if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
?>
<?php
if (isset($_GET['cmd']) && $_GET['cmd'] == 'users') {
    ?>

    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> کاربران <i class="fa fa-users"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید کاربران را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl"> کاربران</h3>

                    <div class="panel-actions">
                        <button data-expand="#panel-1" title="نمایش" class="btn-panel">
                            <i class="fa fa-expand"></i>
                        </button>
                        <button data-collapse="#panel-1" title="بازکردن" class="btn-panel">
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="table-responsive table-responsive-datatables">
                        <table class="table datatable table-striped table-bordered rtl">
                            <thead>
                            <tr>
                                <th style="text-align:center;">ID</th>
                                <th style="text-align:center;">Username</th>
                                <th style="text-align:center;">Full Name</th>
                                <th style="text-align:center;">Email</th>
                                <th style="text-align:center;">Job</th>
                                <th style="text-align:center;">Address</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Image</th>
                                <th style="text-align:center;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $_DB->CountRows('users');
                            $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                            $_PAG = new Pagination($page,
                                $rows
                                , 20,
                                'users.php?cmd=users&page=#i#');
                            $query = $_DB->select('users', '*', '', '`id` DESC', $_PAG->limit);
                            while ($fetch = $_DB->fetchAssoc($query)) {
                                echo '<tr>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $fetch['id'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo' <a href="users.php?viewUser='. $fetch['id'].'"target="_blank">';
                                echo $fetch['UserName'];
                                echo '</a>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                echo $fetch['FullName'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                echo $fetch['UserEmail'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                echo $fetch['UserJob'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;" >';;
                                echo $fetch['UserAddress'];
                                echo '</td>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle;text-align:center;" >';;
                                echo $fetch['UserStatus'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle;">';;
                                $userImage = $fetch['UserImage'];
                                if ($userImage != null) {
                                    ?>
                                    <center><img class="thumbnail "
                                                 src="../<?php echo $_GB->getSafeImage($userImage) ?>"
                                                 style="height:100px;width: 100px; "></center>
                                    <?php
                                }
                                echo '</td>';
                                echo '<td style="vertical-align: middle;">';;
                                $id = $fetch['id'];
                                echo '<center> <a href="?cmd=removeUser&id=' . $id . '" onclick="return checkDelete()"  <span class="glyphicon glyphicon-remove"></span>Delete</a></center>  ';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!--/content-body -->
    </div>

<?php
} else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'removeUser') {
    $id = $_DB->escapeString($_GET['id']);
    $delete = $_DB->delete('users', '`id` = ' . $id);
    if ($delete) {
        echo $_GB->ShowError('The user Deleted successfully', 'yes');
        echo $_GB->MetaRefresh('users.php?cmd=users', 1);
    } else {
        echo $_GB->ShowError('Failed to delete this user try again');
        echo $_GB->MetaRefresh('users.php?cmd=users', 1);
    }
}if (isset($_GET['viewUser'])) {
    $id = (int)$_GET['viewUser'];
    $query = $_DB->select('users','*',"`id` = {$id}");
    if($_DB->numRows($query) != 0){
        $user = $_DB->fetchObject($query);
        ?>
        <div class="card-panel">
            <div class="red-text text-darken-2"><a href="?removeUser=<?php echo $user->id; ?>" onclick="return checkDelete()">Delete User</a></div>
        </div>
        <div class="card">
            <div class="row author-card valign-wrapper">
                <div class="col s2 center-align">
                    <img src="../<?php echo $_GB->getSafeImage($user->UserImage)?>" alt=""
                         class="circle responsive-img user-image"  width="100" height="100">
                </div>
                <div class="col s10 name-date-col">
                    <span class="author-name"><b> <?php echo $user->UserName?></b></span><br>
                    <span class="post-date"> <?php echo $user->UserEmail?></span>
                </div>
            </div>
        </div>

    <?php
    }else{
        echo $_GB->ShowError("User does not exists");
    }
    include 'footer.php';
    exit;
}
?>


<?php
echo $_PAG->urls;
include 'footer.php';
?>