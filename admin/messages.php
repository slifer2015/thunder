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

if (isset($_GET['cmd']) && $_GET['cmd'] == 'messages') {
    ?>

    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> پیام ها <i class="fa fa-paper-plane-o"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید پیام های کاربران را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl"> پیام های کاربران</h3>

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
                                <th style="text-align:center;">Message</th>
                                <th style="text-align:center;">Provider</th>
                                <th style="text-align:center;">Request</th>
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = "SELECT R.id,R.Date,R.reply,R.image,
                      R.ConversationsID AS conversationID,
                      C.requestId,
                      C.providerId,
                      R.status AS Status,
                      R.action_deleted_user_id,
                        U.id AS holderID,
                        U.FullName AS holderFullName,
						U.UserName AS holderUsername,
						U.UserImage AS holderImage
                  FROM prefix_chat R

                  LEFT JOIN prefix_users AS U
                  ON U.id = R.UserID

                  LEFT JOIN prefix_conversation C
                  ON C.id = R.ConversationsID
                  WHERE U.id = R.UserID
                  ORDER BY R.Date DESC";
                            $query = $_DB->MySQL_Query($query);

                            $rows = $_DB->numRows($query);
                            $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                            $_PAG = new Pagination($page,
                                $rows
                                , 20,
                                'messages.php?cmd=messages&page=#i#');
                            $query = "SELECT R.id,R.Date,R.reply,R.image,
                      R.ConversationsID AS conversationID,
                      R.UserID,
                      C.requestId,
                      C.providerId,
                      R.status AS Status,
                      R.action_deleted_user_id,
                        U.id AS holderID,
                        U.FullName AS holderFullName,
						U.UserName AS holderUsername,
						U.UserImage AS holderImage
                  FROM prefix_chat R

                  LEFT JOIN prefix_users AS U
                  ON U.id = R.UserID

                  LEFT JOIN prefix_conversation C
                  ON C.id = R.ConversationsID
                  WHERE U.id = R.UserID
                  ORDER BY R.Date DESC LIMIT $_PAG->limit";
                            $query = $_DB->MySQL_Query($query);

                            if ($_DB->numRows($query) != 0) {
                                while ($fetch = $_DB->fetchAssoc($query)) {
                                    $fetch['Date'] = $_GB->Date($fetch['Date']);
                                    $providerName = $Users->getUserNameByID($fetch['providerId']);
                                    $requestName = $Users->getUserNameByID($fetch['requestId']);
                                    echo '<tr>';
                                    echo '<td style="vertical-align: middle;  text-align:center;">';
                                    echo $fetch['id'];
                                    echo '</td>';
                                    echo '<td style="vertical-align: middle;  text-align:center;">';
                                    if ($fetch['reply'] != null) {
                                        echo '<p style="height: auto; width: 150px; "> ' . $fetch['reply'] . '</p>';
                                    }
                                    if ($fetch['image'] != null) {
                                        ?>
                                        <center><img class="thumbnail"
                                                     src="../image/home/<?php echo $fetch['image'] ?>"
                                                     style="height:100px;width: 100px; "></center>
                                    <?php }
                                    echo '</td>';
                                    echo '<td style="vertical-align: middle; text-align:center; color:#0005ff">';;
                                    echo ' <a href="users.php?viewUser=' . $fetch['providerId'] . '"target="_blank">';
                                    echo $providerName;
                                    echo '</a>';
                                    echo '</td>';
                                    echo '<td style="vertical-align: middle;text-align:center;  color:#0005ff">';;
                                    echo ' <a href="users.php?viewUser=' . $fetch['requestId'] . '"target="_blank">';
                                    echo $requestName;
                                    echo '</a>';
                                    echo '</td>';
                                    echo '<td style="vertical-align: middle; text-align:center; ">';;
                                    echo $fetch['Date'];
                                    echo '</td>';
                                    echo '<td style="vertical-align: middle;">';;
                                    $id = $fetch['id'];
                                    $UserId = $fetch['UserID'];
                                    $ConversationId = $fetch['conversationID'];
                                    echo '<center><a href="?cmd=removeMessage&id=' . $id . '&UserID=' . $UserId . '&ConversationID=' . $ConversationId . '" onclick="return checkDelete()"  style="vertical-align: center;"><span class="glyphicon glyphicon-remove"></span>Remove</a> </center>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
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
} else if (isset($_GET['cmd'], $_GET['id'], $_GET['UserID'], $_GET['ConversationID']) && $_GET['cmd'] == 'removeMessage') {
    $id = $_DB->escapeString($_GET['id']);
    $UserId = $_DB->escapeString($_GET['UserID']);
    $ConversationId = $_DB->escapeString($_GET['ConversationID']);
    $delete = $_DB->delete('chat', "`UserID` = '{$UserId}'  AND `id`='{$id}' AND  `ConversationsID`= '{$ConversationId}'");
    if ($delete) {
        echo $_GB->ShowError('The message Deleted successfully', 'yes');
        echo $_GB->MetaRefresh('messages.php?cmd=messages', 1);
    } else {
        echo $_GB->ShowError('Failed to delete this message try again');
        echo $_GB->MetaRefresh('messages.php?cmd=messages', 1);
    }


}
?>
<?php
echo $_PAG->urls;
include 'footer.php';
?>