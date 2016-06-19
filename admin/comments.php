<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 11/25/2015
 * Time: 2:46 PM
 */
include 'init.php';
include 'header.php';
if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
?>
<?php

if (isset($_GET['cmd']) && $_GET['cmd'] == 'comments') {
    ?>

    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> کامنت ها <i class="fa fa-comments"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید کامنت های کاربران را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl"> کامنت های کاربران</h3>

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
                                <th style="text-align:center;">Provider</th>
                                <th style="text-align:center;">RequestID</th>
                                <th style="text-align:center;">Comment</th>
                                <th style="text-align:center;">Edited</th>
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $_DB->CountRows('comments');
                            $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                            $_PAG = new Pagination($page,
                                $rows
                                , 20,
                                'comments.php?cmd=comments&page=#i#');
                            $query = $_DB->select('comments', '*', '', '`id` DESC', $_PAG->limit);
                            while ($fetch = $_DB->fetchAssoc($query)) {
                                $fetch['Date'] = $_GB->Date($fetch['Date']);
                                $providerName = $Users->getUserNameByID($fetch['providerId']);
                                if($fetch['Edited'] == "1"){
                                    $Edited = "Yes";
                                }else{
                                    $Edited = "No";
                                }
                                echo '<tr>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $fetch['id'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center; color:#0005ff">';;
                                echo' <a href="users.php?viewUser='. $fetch['providerId'].'"target="_blank">';
                                echo $providerName;
                                echo '</a>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center; color:#0005ff">';;
                                echo' <a href="home.php?cmd=viewPost&id='. $fetch['requestId'].'"target="_blank">';
                                echo $fetch['requestId'];
                                echo '</a>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle;  text-align:center;">';
                                echo '<p style="height: auto;  width: 150px;  "> ' . $fetch['Comment'] . '</p>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $Edited;
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                echo $fetch['Date'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                $id = $fetch['id'];
                                echo '<center><a href="?cmd=removeComment&id=' . $id . '" onclick="return checkDelete()"  ><span class="glyphicon glyphicon-remove"></span>Remove</a> </center>';
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
} else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'removeComment') {
    $id = $_DB->escapeString($_GET['id']);
    $delete = $_DB->delete('comments', "`id`='{$id}'");
    if ($delete) {
        echo $_GB->ShowError('The comment Deleted successfully', 'yes');
        echo $_GB->MetaRefresh('comments.php?cmd=comments', 1);
    } else {
        echo $_GB->ShowError('Failed to delete this comment try again');
        echo $_GB->MetaRefresh('comments.php?cmd=comments', 1);
    }


}
?>
<?php
echo $_PAG->urls;
include 'footer.php';
?>