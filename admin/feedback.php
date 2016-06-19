<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 10/3/2015
 * Time: 1:59 AM
 */
include 'init.php';
include 'header.php';
if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
?>
<?php
if (isset($_GET['cmd']) && $_GET['cmd'] == 'feedback') {
    ?>

    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> پشتیبانی و سوالات <i class="fa fa-exchange"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید پشتیبانی و سوالات را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl"> پشتیبانی و سوالات </h3>

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
                                <th style="text-align:center;"> From</th>
                                <th style="text-align:center;"> Rating Star</th>
                                <th style="text-align:center;"> Message</th>
                                <th style="text-align:center;"> Date</th>
                                <th style="text-align:center;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $_DB->CountRows('feedback');
                            $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                            $_PAG = new Pagination($page,
                                $rows
                                , 20,
                                'feedback.php?cmd=feedback&page=#i#');
                            $query = $_DB->select('feedback', '*', '', '`id` DESC', $_PAG->limit);
                            $userId = $_GB->getIDByName($fetch['fromUser']);
                            while ($fetch = $_DB->fetchAssoc($query)) {
                                echo '<tr>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $fetch['id'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo' <a href="users.php?viewUser='. $userId['id'].'"target="_blank">';
                                echo $fetch['fromUser'];
                                echo '</a>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $fetch['Rating'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;>';
                                echo '<p style="height: auto; width: 150px;  "> ' . $fetch['Message'] . '</p>';

                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $fetch['Date'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                $Id = $fetch['id'];
                                echo '<center> <a href="?cmd=removeFeedBack&id=' . $Id . '"  onclick="return checkDelete()" ><span class="glyphicon glyphicon-remove"></span>Remove</a></center>  ';
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
} else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'removeFeedBack') {
    $Id = $_DB->escapeString($_GET['id']);
    $delete = $_DB->delete('feedback', '`id` = ' . $Id);
    if ($delete) {
        echo $_GB->ShowError('the feedback is Deleted successfully', 'yes');
        echo $_GB->MetaRefresh('feedback.php?cmd=feedback', 1);
    } else {
        echo $_GB->ShowError('Failed to delete this feedback try again');
    }
} ?>
<?php
echo $_PAG->urls;
include 'footer.php';
?>
