<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 10/11/2015
 * Time: 9:22 PM
 */


// include the database connection class
include 'core/classes/DataBase.php';
// include the Users class
include 'core/classes/Users.php';
// include the pagination class
include 'core/classes/Pagination.php';
// include the General class
include 'core/classes/General.php';;
// include the Security class
include 'core/classes/Security.php';
// include the Posts class
include 'core/classes/Posts.php';
// include the config.php file
include 'config.php';
// include the RelationShip class
include 'core/classes/RelationShip.php';

$_DB = new DataBase($_Config);
$_DB->connect();
$_DB->selectDB();
$Security = new Security($_DB);
$_GB = new General($_DB, $Security);
$Relation = new RelationShip($_GB);
$Users = new Users($_GB, $Relation);
$Posts = new Posts($_GB, $Relation);
$cmd = $_GET['cmd'];

if (isset($cmd)) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
        $userID = $Users->getUserID($_SERVER['HTTP_TOKEN']);
    } else {
        $userID = 0;
    }
    switch ($cmd) {
        case 'Login':
            if (isset($_POST['UserName']) && isset($_POST['UserPassword'])) {
                $Users->userLogin($_POST['UserName'], $_POST['UserPassword']);
            } else {
                // failed to insert row
                $array = array(
                    'success' => false,
                    'userID' => null,
                    'token' => null,
                    'message' => 'Oops! some params are missing.'
                );
                $_GB->JsonResponseMessage($array);
            }
            break;

        case 'Register':
            if (isset($_POST['UserName']) && isset($_POST['UserEmail']) && isset($_POST['UserPassword']) && isset($_POST['FullName']) && isset($_POST['UserJob']) && isset($_POST['UserAddress'])) {
                $Users->userRegister($_POST['UserName'], $_POST['UserEmail'], $_POST['UserPassword'], $_POST['FullName'], $_POST['UserJob'], $_POST['UserAddress']);
            } else {
                // failed to insert row
                $array = array(
                    'success' => false,
                    'message' => 'Oops! some params are missing.'
                );
                $_GB->JsonResponseMessage($array);
            }
            break;
        case 'users':
            if ($userID != 0) {
                if ($_GET['id'] != 0) {
                    $userInfo = $Users->getUserDetails($_GET['id'], $userID);
                    $_GB->JsonResponseMessage($userInfo);
                } else {
                    $_GB->JsonResponseMessage(null);
                }

            } else {
                $array = array(
                    'success' => false,
                    'message' => 'something went wrong'
                );
                $_GB->JsonResponseMessage($array);
            }
            break;
        case 'getUserID':
            if ($userID != 0) {
                if ($_GET['mention']) {
                    $id = $Users->getIDByName($_GET['mention']);
                    if ($id != 0) {
                        $_GB->JsonResponseMessage($id);
                    } else {
                        $_GB->JsonResponseMessage(null);
                    }
                }
            } else {
                $array = array(
                    'success' => false,
                    'message' => 'something went wrong'
                );
                $_GB->JsonResponseMessage($array);
            }
            break;
        case 'sentRequestFriend':
          //  $userID = 2;
            if ($userID != 0) {
                $Relation->SentRequestFriend($userID, $_GET['id']);
            }
            break;
        case 'cancelRequestFriend':
            if ($userID != 0) {
                $Relation->CancelRequestFriend($userID, $_GET['id']);
            }
            break;
        case 'acceptRequestFriend':
            if ($userID != 0) {
                $Relation->AcceptRequestFriend($userID, $_GET['id']);
            }
            break;
        case 'UnFriend':
            if ($userID != 0) {
                $Relation->UnFriend($userID, $_GET['id']);
            }
            break;
        case 'DeclineRequestFriend':
            if ($userID != 0) {
                $Relation->DeclineRequestFriend($userID, $_GET['id']);
            }
            break;
        case 'BlockRequestFriend':
            if ($userID != 0) {
                $Relation->BlockRequestFriend($userID, $_GET['id']);
            }
            break;
        case 'UnBlockRequestFriend':
            if ($userID != 0) {
                $Relation->UnBlockRequestFriend($userID, $_GET['id']);
            }
            break;
        case 'getFriendsList':
            if ($userID != 0 && isset($_GET["page"])) {
                $querySQL = "SELECT U.*
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userID},F.requestId,F.providerId)
                            WHERE ((F.providerId = {$userID} AND F.status = $Relation->Accepted )
                            OR  (F.requestId = {$userID} AND F.status = $Relation->Accepted)) AND U.UserState = 'Active'
                            GROUP BY U.id ORDER BY U.id ";

                $query = $_DB->MySQL_Query($querySQL);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Relation->GetFriendsList($userID, $PAG->limit);
                }
            }
            break;
        case 'getFriendsChatList':
            if ($userID != 0 && isset($_GET["page"])) {
                $querySQL = "SELECT U.*
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userID},F.requestId,F.providerId)
                            WHERE ((F.providerId = {$userID} AND F.status = $Relation->Accepted )
                            OR  (F.requestId = {$userID} AND F.status = $Relation->Accepted)) AND U.UserState = 'Active'
                            GROUP BY U.id ORDER BY U.UserStatus ";

                $query = $_DB->MySQL_Query($querySQL);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Relation->GetFriendsChatList($userID, $PAG->limit);
                }
            }

            break;
        case 'getUserFriendsList':
            if ($userID != 0) {
                if (isset($_GET['userId'])) {
                    $user = $Users->getUserDetails($_GET['userId'], $userID);
                    $querySQL = "SELECT U.*
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$user['id']},F.requestId,F.providerId)
                            WHERE ((F.providerId = {$user['id']} AND F.status = $Relation->Accepted )
                            OR  (F.requestId = {$user['id']} AND F.status = $Relation->Accepted)) AND U.UserState = 'Active'
                            GROUP BY U.id ORDER BY U.UserStatus ";

                    $query = $_DB->MySQL_Query($querySQL);
                    $rows = $_DB->numRows($query);
                    $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                    $PAG = new Pagination($page,
                        $rows
                        , 10,
                        'api.php?page=#i#');
                    if ($page > $PAG->pages) {
                        $_GB->JsonResponseMessage(array());
                    } else {
                        $Relation->GetUserFriendsList($userID, $user['id'], $PAG->limit);
                    }
                }
            }
            break;
        case 'SearchFriends':
            if (isset($_POST['string']) && $userID != 0) {
                $Relation->SearchFriends($_POST['string'], $userID);
            }
            break;
        case 'GetSentRequestFriendsList':
            if ($userID != 0) {
                $querySQL = "SELECT U.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId = {$userID} AND F.requestId != {$userID}
                            WHERE U.id != F.providerId AND  U.id = F.requestId AND F.status = $Relation->Pending AND F.action_user_id = {$userID}
						    GROUP BY U.id ORDER BY U.id";

                $query = $_DB->MySQL_Query($querySQL);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Relation->GetSentRequestFriendsList($userID, $PAG->limit);
                }
            }
            break;
        case 'GetNewRequestFriendsList':
            if ($userID != 0) {
                $querySQL = " SELECT U.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId != {$userID} AND F.requestId = {$userID}
                            WHERE U.id = F.providerId AND  U.id != F.requestId AND F.status = $Relation->Pending AND F.action_user_id != {$userID}
						    GROUP BY U.id ORDER BY U.id";

                $query = $_DB->MySQL_Query($querySQL);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Relation->GetNewRequestFriendsList($userID, $PAG->limit);
                }
            }
            break;
        case 'GetBlockedFriendsList':
            if ($userID != 0) {
                $querySQL = "SELECT U.*,F.*
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userID},F.requestId,F.providerId)
                            WHERE ( F.status = {$Relation->Blocked} AND F.action_user_id = {$userID}  )
                            GROUP BY U.id ORDER BY U.id ";

                $query = $_DB->MySQL_Query($querySQL);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Relation->getBlockedFriendsList($userID, $PAG->limit);
                }
            }
            break;
        case 'publish':
           // $userID = 1;
            if ($userID != 0) {
                if (isset($_FILES['video'])) {
                    $VideoID = $_GB->uploadVideos($_FILES['video']);
                } else {
                    $VideoID = null;
                }

                if (isset($_FILES['thumbnail'])) {
                    $VideoThumbnailID = $_GB->uploadVideoThumbnail($_FILES['thumbnail']);
                } else {
                    $VideoThumbnailID = null;
                }
                if (isset($_FILES['image'])) {
                    $imageID = $_GB->uploadImage($_FILES['image']);
                } else {
                    $imageID = null;
                }
                $Posts->publishStatus($_POST, $userID, $imageID, $VideoThumbnailID, $VideoID);
            }
            break;

        case 'Posts':
           // $userID = 1;
            if ($userID != 0) {
                if (isset($_GET['hashtag'])) {
                    $hashtag = $_DB->escapeString($_GET['hashtag']);
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
						   AND FS.FeedStatus LIKE '%#$hashtag%'
						GROUP BY FS.id ORDER BY FS.id DESC
					";
                } else {
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
                }
                $query = $_DB->MySQL_Query($querySQL);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $_PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $_PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Posts->getPosts($userID, $querySQL, $_PAG->limit);
                }
            }
            break;
        case 'UserPosts':
            if ($userID != 0) {
                if (isset($_GET['userId'])) {

                    $user = $Users->getUserDetails($_GET['userId'], $userID);
                    $query = $_DB->select('feeds', '*', "`holderID` = {$user['id']}", '`id` DESC');
                    $rows = $_DB->numRows($query);
                    $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                    $_PAG = new Pagination($page,
                        $rows
                        , 5,
                        'api.php?page=#i#');
                    if ($page > $_PAG->pages) {
                        $_GB->JsonResponseMessage(array());
                    } else {
                        $Posts->getUserPosts($userID, $user['id'], $_PAG->limit);
                    }
                }
            }
            break;
        case 'UserGalleryPosts':
            if ($userID != 0) {
                if (isset($_GET['userId'])) {
                    $user = $Users->getUserDetails($_GET['userId'], $userID);
                    $query = $_DB->select('feeds', '*', "`holderID` = {$user['id']}", '`id` DESC');
                    $rows = $_DB->numRows($query);
                    $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                    $_PAG = new Pagination($page,
                        $rows
                        , 5,
                        'api.php?page=#i#');
                    if ($page > $_PAG->pages) {
                        $_GB->JsonResponseMessage(array());
                    } else {
                        $Posts->getUserGalleryPosts($user['id'], $_PAG->limit);
                    }
                }
            }
            break;
        case 'getFavorites':
            if ($userID != 0) {
                $query = "SELECT FS.*,
						U.FullName AS holderFullName,
						U.Username AS holderUserName,
						U.UserImage AS holderImage
						FROM prefix_feeds FS

						LEFT JOIN prefix_users AS U
						ON U.id = FS.holderID

						LEFT JOIN prefix_likes AS L
						ON L.providerId = {$userID}

						WHERE FS.id = L.requestId AND U.UserState = 'Active'
						GROUP BY FS.id ORDER BY FS.id
					";

                $query = $_DB->MySQL_Query($query);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $_PAG = new Pagination($page,
                    $rows
                    , 10,
                    'api.php?page=#i#');
                if ($page > $_PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Posts->getLikedFeeds($userID, $_PAG->limit);
                }

            }
            break;

        case 'getSingleFeed':
            if ($userID != 0) {
                if (isset($_GET['FeedId'])) {
                    $Posts->getSingleFeed($userID, $_GET['FeedId'], $_GET['userId']);
                }
            }
            break;
        case 'DeleteFeed':
            if ($userID != 0) {
                $Posts->DeleteFeed($_GET['FeedId'], $_GET['userId']);
            }
            break;
        case 'EditFeed':
            if ($userID != 0) {
                if (isset($_POST['status'])) {
                    $Posts->EditFeed($_POST['status'], $_GET['FeedId'], $userID);
                }
            }
            break;

        case 'insertNewReportFeed':
            if ($userID != 0) {
                if (isset($_POST['Reason'])) {
                    $Posts->insertNewReportFeed($_POST['Description'], $_POST['Reason'], $_GET['FeedId'], $userID);
                } else {
                    // failed to insert row
                    $array = array(
                        'done' => false,
                        'message' => 'Oops! some params are missing.'
                    );
                    $_GB->JsonResponseMessage($array);
                }
            } else {
                $array = array(
                    'done' => false,
                    'message' => 'something went wrong'
                );
                $_GB->JsonResponseMessage($array);
            }

            break;

        case 'feedback':
            if ($userID != 0) {
                if (isset($_POST['fromUser']) && isset($_POST['Rating']) && isset($_POST['Message'])) {
                    $array = array(
                        'fromUser' => $_DB->escapeString($_POST['fromUser']),
                        'Rating' => $_DB->escapeString($_POST['Rating']),
                        'Message' => $_DB->escapeString($_POST['Message'])
                    );
                    $result = $_DB->insert('feedback', $array);
                    // check if row inserted or not
                    if ($result) {
                        // successfully inserted into database
                        $array = array(
                            'done' => true,
                            'message' => 'FeedBack is successfully sent.'
                        );
                        $_GB->JsonResponseMessage($array);

                    } else {
                        // failed to insert row
                        $array = array(
                            'done' => false,
                            'message' => 'Oops! An error occurred.'
                        );
                        $_GB->JsonResponseMessage($array);

                    }
                }
            }

            break;
        case 'getListConversations':
            if ($userID != 0) {
                $query = "SELECT C.id AS ConversationID,
                                  C.Date AS MessageDate,
                                   C.action_user_id,
                                  R.action_deleted_user_id,
                                  U.id AS RecipientID ,
                                  U.UserName AS RecipientUserName ,
                                  U.FullName AS  RecipientFullName,
                                  U.UserImage AS  RecipientImage
                          FROM prefix_users U,prefix_conversation C, prefix_chat R
                          WHERE
                          CASE
                          WHEN C.providerId = {$userID}
                          THEN C.requestId = U.id
                          WHEN C.requestId = {$userID}
                          THEN C.providerId= U.id
                           END
                           AND
                           C.id=R.ConversationsID
                           AND
                          (C.providerId ={$userID}
                           OR C.requestId ={$userID})
                           AND U.UserState = 'Active'
                          GROUP BY C.id  ORDER BY C.Date DESC ";
                $query = $_DB->MySQL_Query($query);
                $rows = $_DB->numRows($query);
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $_PAG = new Pagination($page,
                    $rows
                    , 5,
                    'api.php?page=#i#');
                if ($page > $_PAG->pages) {
                    $_GB->JsonResponseMessage(array());
                } else {
                    $Users->getListConversations($userID, $_PAG->limit);
                }

            }
            break;
        case 'SearchConversation':
            if (isset($_POST['string']) && $userID != 0) {
                $Users->SearchConversation($_POST['string'], $userID);
            }
            break;
        case 'DeleteConversation':
            if ($userID != 0) {
                $Users->DeleteConversation($userID, $_POST);
            }
            break;
        case 'getMessages':
            if ($userID != 0 && isset($_GET['conversationID'])) {
                $Users->getMessages($userID, $_GET['recipientID'], $_GET['conversationID']);
            }
            break;
        case 'addMessage':
            if ($userID != 0) {
                if (isset($_FILES['image'])) {
                    $imageID = $_GB->uploadImage($_FILES['image']);
                } else {
                    $imageID = null;
                }
                $Users->addMessage($userID, $imageID, $_POST);
            }
            break;
        case 'StatusMessage':
            if ($userID != 0) {
                $Users->StatusMessage($userID, $_POST);
            }
            break;
        case 'DeleteMessage':
            if ($userID != 0) {
                $Users->DeleteMessage($userID, $_POST);
            }
            break;
        case 'updateProfile':
            if ($userID != 0) {
                if (isset($_FILES['UserImage'])) {
                    $UserImage = $_GB->uploadImage($_FILES['UserImage']);
                } else {
                    $UserImage = null;
                }
                if (isset($_FILES['UserCover'])) {
                    $cover = $_GB->uploadImage($_FILES['UserCover']);
                } else {
                    $cover = null;
                }

                $Users->updateProfile($_POST, $UserImage, $cover, $userID);
            }
            break;
        case 'updateRegID':
            if ($userID != 0) {
                $Users->updateRegID($userID, $_POST['regID']);
            }
            break;
        case 'updateUserStatusOffline':
            if ($userID != 0) {
                $Users->updateUserStatusOffline($userID);
            }
            break;
        case 'updateUserStatusOnline':
            if ($userID != 0) {
                $Users->updateUserStatusOnline($userID);
            }
            break;
        case 'DeactivateAccount':
            if ($userID != 0) {
                $Users->DeactivateAccount($userID);
            }
            break;
        case 'Like':
            if ($userID != 0) {
                if (isset($_GET['feedId'])) {
                    $Posts->LikePost($userID, $_GET['feedId']);
                }
            }
            break;
        case 'UnLike':
            if ($userID != 0) {
                if (isset($_GET['feedId'])) {
                    $Posts->unLikePost($userID, $_GET['feedId']);
                }

            }
            break;
        case 'AddComment':
            if ($userID != 0) {
                if (isset($_POST['comment'])) {
                    $Posts->AddComment($_POST['comment'], $_GET['feedId'], $userID);
                }
            }
            break;
        case 'DeleteComment':
            if ($userID != 0) {
                $Posts->DeleteComment($_GET['CommentId'], $_GET['userId']);
            }
            break;
        case 'EditComment':
            if ($userID != 0) {
                if (isset($_POST['comment'])) {
                    $Posts->EditComment($_POST['comment'], $_GET['CommentId'], $userID);
                }
            }
            break;
        case 'getComments':
            if ($userID != 0) {
                if (isset($_GET['feedId'])) {
                    $Posts->getComments($_GET['feedId']);
                }
            }
            break;

        case 'insertNewReport':
            if ($userID != 0) {
                if (isset($_POST['Reason'])) {
                    $Posts->insertNewReport($_POST['Description'], $_POST['Reason'], $_GET['CommentId'], $userID);
                } else {
                    // failed to insert row
                    $array = array(
                        'done' => false,
                        'message' => 'Oops! some params are missing.'
                    );
                    $_GB->prepareMessage($array);
                }
            } else {
                $array = array(
                    'done' => false,
                    'message' => 'something went wrong'
                );
                $_GB->prepareMessage($array);
            }

            break;

        case 'map':
            if ($userID != 0 && isset($_POST['place_name'])) {
                $_GB->getMapImage($_POST['place_name']);
            }
            break;
        case 'getLink':
            $_GB->getLink($_GET['hash']);
            break;
        case 'place':
            if ($userID != 0) {
                $_GB->getAddress($_GET['lng'], $_GET['lat']);
            }
            break;
        case 'disclaimer':
            $dis = $_GB->getConfig('disclaimer', 'site');
            $_GB->JsonResponseMessage(array('done' => true, 'message' => $dis));
            break;
        case 'restPass':
            if (isset($_POST['email'])) {
                $query = $_DB->select('users', '*', "`UserEmail` = '" . $_DB->escapeString($_POST['email']) . "'");
                if ($_DB->numRows($query) != 0) {
                    $user = $_DB->fetchAssoc($query);
                    $Users->passwordRestMail($user['id'], $user['email']);
                } else {
                    $_GB->JsonResponseMessage(array('done' => false, 'message' => "we didn't found your account"));
                }
            }
            break;
    }
} else {

    $array = array(
        'success' => false,
        'message' => ' Required field(s) is missing'
    );
    $_GB->JsonResponseMessage($array);

}
?>