<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 11/25/2015
 * Time: 2:44 PM
 */
include 'init.php';
include 'header.php';
if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
?>
<?php
if (isset($_GET['cmd']) && $_GET['cmd'] == 'reports') {
    ?>

    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> گزارشات <i class="fa fa-chart"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید گزارشات فعالیت کاربران را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl"> گزارشات فعالیت کاربران </h3>

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
                                <th style="text-align:center;"> RequestID</th>
                                <th style="text-align:center;">Reason</th>
                                <th style="text-align:center;">Description</th>
                                <th style="text-align:center;">Type</th>
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $_DB->CountRows('reports');
                            $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                            $_PAG = new Pagination($page,
                                $rows
                                , 20,
                                'reports.php?cmd=reports&page=#i#');
                            $query = $_DB->select('reports', '*', '', '`id` DESC', $_PAG->limit);
                            while ($fetch = $_DB->fetchAssoc($query)) {
                                $fetch['Date'] = $_GB->Date($fetch['Date']);
                                if($fetch['Type'] == "1"){
                                    $type = "Feed Report";
                                }else{
                                    $type = "Comment Report";
                                }
                                $providerName = $Users->getUserNameByID($fetch['providerId']);
                                echo '<tr>';
                                echo '<td style="vertical-align: middle;">';
                                echo $fetch['id'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle;  color:#0005ff">';
                                echo $providerName;
                                echo '</td>';
                                echo '<td style="vertical-align: middle;">';;
                                echo $fetch['requestId'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle;">';;
                                echo $fetch['Reason'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle;">';;
                                echo  '<p style="height: auto;  width: 150px; text-align:center; "> ' . $fetch['Description'] . '</p>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align: justify;" >';;
                                echo $type;
                                echo '</td>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align: justify;" >';;
                                echo $fetch['Date'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle;">';;
                                $id = $fetch['id'];
                                echo '<a href="?cmd=removeReport&id=' . $id . '" onclick="return checkDelete()" ><span class="glyphicon glyphicon-remove"></span>Remove</a> ';
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
} else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'removeReport') {
    $id = $_DB->escapeString($_GET['id']);
    $delete = $_DB->delete('reports', '`id` = ' . $id);
    if ($delete) {
        echo $_GB->ShowError('This report Deleted successfully', 'yes');
        echo $_GB->MetaRefresh('reports.php?cmd=reports', 1);
    } else {
        echo $_GB->ShowError('Failed to delete this report try again');
        echo $_GB->MetaRefresh('reports.php?cmd=reports', 1);
    }
}
?>


<?php
include 'footer.php';
?>