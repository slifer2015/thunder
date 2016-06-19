<?php

include 'init.php';
include 'header.php';
if ($_GB->GetSession('admin') == false) {
    header("location:login.php");
}
?>
<?php

if (isset($_GET['cmd']) && $_GET['cmd'] == 'index') {
    ?>


    <div class="content">
        <!-- content-control -->
        <div class="content-control">
            <!--control-nav-->
            <ul class="control-nav pull-right">
                <li><a class="rtl text-24"> خانه <i class="fa fa-home"></i></a></li>
            </ul><!--/control-nav-->
        </div><!-- /content-control -->

        <div class="content-body">
            <!-- APP CONTENT
            ================================================== -->
            <div class="callout callout-info">
                <p class="rtl">در این بخش شما می توانید پست های کاربران را مشاهده نمایید.</p>
            </div>

            <div class="row xsmallSpace"></div>

            <div id="panel-1" class="panel panel-default border-blue">
                <div class="panel-heading bg-blue">
                    <h3 class="panel-title rtl">پست های کاربران</h3>

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
                                <th style="text-align:center;">FeedFile</th>
                                <th style="text-align:center;">FeedType</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Provider</th>
                                <th style="text-align:center;">Privacy</th>
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $_DB->CountRows('feeds');
                            $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                            $_PAG = new Pagination($page,
                                $rows
                                , 20,
                                'home.php?cmd=index&page=#i#');
                            $query = $_DB->select('feeds', '*', '', '`id` DESC', $_PAG->limit);
                            while ($fetch = $_DB->fetchAssoc($query)) {
                                $fetch['Date'] = $_GB->Date($fetch['Date']);
                                $holderName = $Users->getUserNameByID($fetch['holderID']);
                                $feedFile = $fetch['FeedFile'];
                                $feedType = $fetch['FeedType'];
                                if ($feedType == "1") {
                                    $type = "Image";
                                } else if ($feedType == "2") {
                                    $type = "Video";
                                } else if ($fetch['Place'] != null) {
                                    $type = "Place";
                                } else if ($fetch['Link'] != null) {
                                    $querLink = $_DB->select('links', '*', "`hash` = '{$fetch['Link']}'");
                                    $fetchLink = $_DB->fetchAssoc($querLink);
                                    if ($fetchLink['type'] != 'youtube') {
                                        $type = "Link";
                                    } else {
                                        $type = "Youtube";
                                    }
                                } else {
                                    $type = "Other";
                                }
                                echo '<tr>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo $fetch['id'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle;text-align:center; align= middle ; color:#0005ff">';;
                                if ($feedType == "1") {
                                    ?>
                                    <center><img class="thumbnail"
                                                 src="../<?php echo $_GB->getSafeImage($feedFile) ?>"
                                                 style="height:100px;width: 100px; "></center>
                                    <?php
                                } else if ($feedType == "2") {
                                    $VideoThumbnail = $fetch['FeedVideoThumbnail'];
                                    if ($VideoThumbnail != null) {
                                        ?>
                                        <center><img class="thumbnail"
                                                     src="../<?php echo $_GB->getSafeImage($VideoThumbnail) ?>"
                                                     style="height:100px;width: 100px; "></center>
                                        <?php
                                    }

                                } else if ($fetch['Place'] != null) {
                                    echo '<p style="height: auto;  width: 150px;"> ' . $fetch['Place'] . '</p>';
                                } else if ($feedType = "Link") {
                                    echo '<a target="_blank" href="' . $fetchLink['link'] . '">' . $fetchLink['title'] . '</a>';
                                } else if ($feedType = "Youtube") {
                                    echo '<a target="_blank" href="https://youtu.be/' . $fetchLink['link'] . '">' . $fetchLink['title'] . '</a>';
                                }
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;color:#0005ff">';;
                                echo $type;
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';
                                echo '<p style="height: auto;  width: 150px;"> ' . $fetch['FeedStatus'] . '</p>';
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                echo $holderName;
                                echo '</td>';
                                echo '<td  style="vertical-align: middle; text-align:center;">';
                                if ($fetch['Privacy'] == 1) {
                                    echo 'Public';
                                } else {
                                    echo 'Private';
                                }
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                echo $fetch['Date'];
                                echo '</td>';
                                echo '<td style="vertical-align: middle; text-align:center;">';;
                                $id = $fetch['id'];
                                echo '<center><a href="?cmd=removeFeed&id=' . $id . '" onclick="return checkDelete()"  ><span class="glyphicon glyphicon-remove"></span>Delete Post</a> </center>';
                                echo '<center><a href="?cmd=viewPost&id=' . $id . '"  ><span class="glyphicon glyphicon-remove"></span>View Post</a> </center>';
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
} else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'removeFeed') {
    $id = $_DB->escapeString($_GET['id']);
    $delete = $_DB->delete('feeds', "`id`='{$id}'");
    if ($delete) {
        echo $_GB->ShowError('This Feed Deleted successfully', 'yes');
        echo $_GB->MetaRefresh('home.php?cmd=index', 1);
    } else {
        echo $_GB->ShowError('Failed to delete this Feed try again');
        echo $_GB->MetaRefresh('home.php?cmd=index', 1);
    }


} else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'viewPost') {
    $id = (int)$_GET['id'];
    $querysql = "SELECT P.*,

						COUNT(L.providerId) AS likes,
						U.FullName AS holderFullName,
						U.UserName AS holderUserName,
						U.UserImage AS holderUserImage
						FROM prefix_feeds P


						LEFT JOIN prefix_users AS U
						ON U.id = P.holderID

						LEFT JOIN prefix_likes AS L
						ON L.providerId = P.id

						WHERE P.id = {$id}
						GROUP BY P.id ORDER BY P.id DESC
					";
    $query = $_DB->MySQL_Query($querysql);
    if ($_DB->numRows($query) != 0) {
        $post = $_DB->fetchObject($query);
        $post->Link = $_GB->getLink($post->Link);
       // $post->Liked =
        $post->Date = $_GB->TimeAgo($post->Date);
        ?>
        <div class="card-panel">
            <div class="red-text text-darken-2"><a href="?cmd=removeFeed=<?php echo $post->id; ?>"
                                                   onclick="return checkDelete()">Delete Post</a></div>
        </div>
        <div class="card">
            <a href="users.php?viewUser=<?php echo $post->holderID ?>" target="_blank">
                <div class="row author-card valign-wrapper">
                    <div class="col s2 center-align">
                        <img src="../<?php echo $_GB->getSafeImage($post->holderUserImage) ?>" alt=""
                             class="circle responsive-img user-image" width="100" height="100" ">
                    </div>
                    <div class="col s10 name-date-col">
                        <span class="author-name"><b> <?php echo $post->holderUserName ?></b></span><br>
                        <span class="post-date"> <?php echo $post->Date ?></span>
                    </div>
                </div>
            </a>
            <?php if ($post->FeedFile != null) {
                if ($post->FeedType == "1") {
                    ?>
                    <div class="card-image">
                        <img class="materialboxed" src="../<?php echo $_GB->getSafeImage($post->FeedFile) ?>">

                    </div>
                <?php
                } else if ($post->FeedType == "2") {
                    ?>
                    <div class="card-content">
                        <video style="width: 100%" controls>
                            <source src="../<?php echo $_GB->getSafeVideo($post->FeedFile) ?>" type="video/mp4">
                        </video>
                    </div>
                <?php
                } ?>

            <?php
            }
            if ($post->FeedStatus != null) { ?>
                <div class="card-content">
                    <p><?php echo $post->FeedStatus ?></p>
                </div>
            <?php }
            if ($post->Place != null) { ?>
                <div class="center-align" style="color:#2196F3">
                    <p><i class="small mdi-maps-place"></i><?php echo $post->Place ?></p>
                </div>
            <?php }
            if ($post->Link != null) {
                if ($post->Link['type'] == 'youtube') { ?>
                    <div class="video-container">';
                        <iframe width="853" height="480"
                                src="https://www.youtube.com/embed/<?php echo $post->Link['link'] ?>?rel=0"
                                frameborder="0" allowfullscreen></iframe>

                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col s12 m6">
                            <div class="card blue lighten-1">
                                <div class="card-content white-text" style="padding: 0px; ">
                                    <span class="card-title"> <?php echo $post->Link['title'] ?></span>

                                    <div class="card-content"><p> <?php echo $post->Link['desc'] ?></p></div>
                                    <p><img src="<?php echo $post->Link['image'] ?>"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
            } ?>
        </div><!-- card//-->

    <?php
    } else {
        echo $_GB->ShowError("Post does not exists");
    }
    include 'footer.php';
    exit;
}
?>
<?php
echo $_PAG->urls;
include 'footer.php';
?>