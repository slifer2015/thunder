<?php

/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 10/26/2015
 * Time: 7:38 PM
 */
class Posts
{
    public $_GB, $Relation;
    public $ImageType, $VideoType;

    function __construct($_GB, $Relation)
    {
        $this->_GB = $_GB;
        $this->_Relation = $Relation;
        $this->ImageType = 1;
        $this->VideoType = 2;
    }

    /**
     * Function to publish something
     * @param $array
     * @param $userID
     * @param $imageID
     * @param $VideoThumbnailID
     * @param $VideoID
     */
    public function publishStatus($array, $userID, $imageID, $VideoThumbnailID, $VideoID)
    {
        $statusData = array(
            'holderID' => $userID,
            'Date' => time(),
        );

        if ($VideoID != null && $VideoThumbnailID != null) {
            $statusData['FeedType'] = $this->VideoType;
            $statusData['FeedFile'] = $VideoID;
            $statusData['FeedVideoThumbnail'] = $VideoThumbnailID;
        }


        if ($imageID != null) {
            $statusData['FeedFile'] = $imageID;
            $statusData['FeedType'] = $this->ImageType;
        }
        if (isset($array['status']) && !empty($array['status'])) {
            $statusData['FeedStatus'] = $this->_GB->_DB->escapeString($array['status']);
        }
        //
        //
        if (isset($array['privacy'])) {
            $statusData['Privacy'] = ($array['privacy'] == 'public') ? 1 : 0;
        } else {
            $statusData['Privacy'] = 0;
        }

        if (isset($array['link']) && $this->_GB->isURL($array['link'])) {
            $link = $this->_GB->getLinkHash($array['link']);
            if ($link != null) {
                $statusData['Link'] = $link;
            }

        }

        if (isset($array['place']) && !empty($array['place'])) {
            $statusData['Place'] = $this->_GB->_DB->escapeString($array['place']);
        }
        $insert = $this->_GB->_DB->insert('feeds', $statusData);
        if ($insert) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'post has been added successfully'));
            $FeedId = $this->_GB->_DB->last_Id();
            if ($array['status'] != null) {
                $name = substr($array['status'], 1);
                $RecipientID = $this->_GB->getIDByName($name);
                if ($RecipientID != 0 && $RecipientID != $userID) {
                    $getUser = $this->_GB->_DB->select('users', '*', '`id`=' . $RecipientID);
                    $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                    $Username = $this->_GB->getUserNameByID($userID);
                    if ($fetchUser['reg_id'] != null) {
                        $regIDs = array($fetchUser['reg_id']);
                        $arrayRequest = array(
                            'for' => 'NewMention',
                            'recipientID' => $RecipientID,
                            'UserName' => $Username,
                            'UserImage' => $this->_GB->getSafeImage($fetchUser['UserImage']),
                            'userID' => $userID,
                            'FeedID' => $FeedId
                        );
                        $this->_GB->sendMessageThroughGCM($regIDs, $arrayRequest);
                    }
                }


            }

        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'Something went wrong please try again'));
        }
    }


    /**
     * Function to fetch a specific Post
     * @param $UserID
     * @param $feedId
     * @param $userId
     */
    public function getSingleFeed($UserID, $feedId, $userId)
    {
        $query = $this->_GB->_DB->select('feeds', '*', "`id`={$feedId} AND `holderID` = {$userId}");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
            $holder = $this->getFeedHolder($fetch['holderID']);
            $fetch['holderFullName'] = $holder['FullName'];
            $fetch['holderUserName'] = $holder['UserName'];
            $fetch['holderImage'] = $this->_GB->getSafeImage($holder['UserImage']);
            $fetch['Liked'] = $this->_GB->isLikeIt($UserID, $fetch['id']);
            $fetch['Likes'] = $this->_GB->_DB->CountRows('likes', '`requestId` = ' . $fetch['id']);
            $fetch['Comments'] = $this->_GB->_DB->CountRows('comments', '`requestId` = ' . $fetch['id']);
            $fetch['VideoURL'] = $this->_GB->getSafeVideo($fetch['FeedFile']);
            $fetch['ImageURL'] = $this->_GB->getSafeImage($fetch['FeedFile']);
            $fetch['FeedVideoThumbnail'] = $this->_GB->getSafeImage($fetch['FeedVideoThumbnail']);
            $this->_GB->JsonResponseMessage($fetch);
        } else {
            $this->_GB->JsonResponseMessage(null);
        }
    }


    /**
     * Function to get User Posts
     * @param $userID
     * @param $requestId
     * @param $limit
     */
    public function getUserPosts($userID, $requestId, $limit)
    {

        if ($this->_Relation->CheckStatusFriends($requestId, $this->_Relation->Accepted)) {
            $query = $this->_GB->_DB->select('feeds', '*', "`holderID` = {$requestId}", '`id` DESC', $limit);
        } else {
            $query = $this->_GB->_DB->select('feeds', '*', "`holderID` = {$requestId} AND `Privacy` = 1", '`id` DESC', $limit);
        }

        if ($this->_GB->_DB->numRows($query) != 0) {
            $feeds = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
                $holder = $this->getFeedHolder($fetch['holderID']);
                $fetch['holderFullName'] = $holder['FullName'];
                $fetch['holderUserName'] = $holder['UserName'];
                $fetch['holderImage'] = $this->_GB->getSafeImage($holder['UserImage']);
                $fetch['Liked'] = $this->_GB->isLikeIt($userID, $fetch['id']);
                $fetch['Link'] = $this->_GB->getLink($fetch['Link']);
                $fetch['Likes'] = $this->_GB->_DB->CountRows('likes', '`requestId` = ' . $fetch['id']);
                $fetch['Comments'] = $this->_GB->_DB->CountRows('comments', '`requestId` = ' . $fetch['id']);
                $fetch['VideoURL'] = $this->_GB->getSafeVideo($fetch['FeedFile']);
                $fetch['ImageURL'] = $this->_GB->getSafeImage($fetch['FeedFile']);
                $fetch['FeedVideoThumbnail'] = $this->_GB->getSafeImage($fetch['FeedVideoThumbnail']);
                $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
                $feeds[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($feeds);
        } else {
            $this->_GB->JsonResponseMessage(array(
                'posts' => null
            ));
        }
    }

    /**
     * @param $requestId
     * @param $limit
     */
    public function getUserGalleryPosts($requestId, $limit)
    {

        if ($this->_Relation->CheckStatusFriends($requestId, $this->_Relation->Accepted)) {
            $query = $this->_GB->_DB->select('feeds', '*', "`holderID` = {$requestId} AND (`FeedType` = 1 OR `FeedType` = 2)", '`id` DESC', $limit);
        } else {
            $query = $this->_GB->_DB->select('feeds', '*', "`holderID` = {$requestId} AND `Privacy` = 1 AND (`FeedType` = 1 OR `FeedType` = 2) ", '`id` DESC', $limit);
        }

        if ($this->_GB->_DB->numRows($query) != 0) {
            $feeds = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['Likes'] = $this->_GB->_DB->CountRows('likes', '`requestId` = ' . $fetch['id']);
                $fetch['Comments'] = $this->_GB->_DB->CountRows('comments', '`requestId` = ' . $fetch['id']);
                $fetch['VideoURL'] = $this->_GB->getSafeVideo($fetch['FeedFile']);
                $fetch['ImageURL'] = $this->_GB->getSafeImage($fetch['FeedFile']);
                $fetch['FeedVideoThumbnail'] = $this->_GB->getSafeImage($fetch['FeedVideoThumbnail']);
                $feeds[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($feeds);
        } else {
            $this->_GB->JsonResponseMessage(array(
                'posts' => null
            ));
        }
    }

    /**
     * Function to fetch user by id. who share a post
     * @param $id
     * @return mixed
     */
    public function getFeedHolder($id)
    {
        $query = $this->_GB->_DB->select('users', '*', "`id` = $id");
        return $this->_GB->_DB->fetchAssoc($query);
    }

    /**
     * Function to like a post
     * @param $userID
     * @param $FeedId
     */
    public function LikePost($userID, $FeedId)
    {
        $FeedId = $this->_GB->_DB->escapeString($FeedId);
        $query = $this->_GB->_DB->select('feeds', '*', "`id` = {$FeedId}");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            if ($fetch['holderID'] != $userID) {
                if ($this->_Relation->CheckStatusProvider($userID, $fetch['holderID'], $this->_Relation->Accepted) || $this->_Relation->CheckStatusProvider($fetch['holderID'], $userID, $this->_Relation->Accepted)) {
                    if ($this->_GB->isLikeIt($userID, $fetch['id']) != true) {
                        $like = $this->_GB->_DB->insert('likes', array('providerId' => $userID, 'requestId' => $fetch['id'], 'Date' => time()));
                        if ($like) {
                            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'your like it '));
                            $UserId = $this->_GB->getUserIDByFeedID($FeedId);
                            if ($UserId != $userID) {
                                $getUser = $this->_GB->_DB->select('users', '*', '`id`=' . $UserId);
                                $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                                $getUserInfo = $this->_GB->_DB->select('users', '*', '`id`=' . $userID);
                                $fetchUserInfo = $this->_GB->_DB->fetchAssoc($getUserInfo);
                                $Username = $this->_GB->getUserNameByID($userID);
                                if ($fetchUser['reg_id'] != null) {
                                    $regIDs = array($fetchUser['reg_id']);
                                    $arrayRequest = array(
                                        'for' => 'NewLike',
                                        'recipientID' => $UserId,
                                        'FeedID' => $FeedId,
                                        'UserImage' => $this->_GB->getSafeImage($fetchUserInfo['UserImage']),
                                        'UserName' => $Username
                                    );
                                    $this->_GB->sendMessageThroughGCM($regIDs, $arrayRequest);
                                }
                            }

                        } else {
                            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
                        }
                    } else {
                        $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'already liked'));
                    }

                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'You are not a friends'));
                }
            } else {
                if ($this->_GB->isLikeIt($userID, $fetch['id']) != true) {
                    $like = $this->_GB->_DB->insert('likes', array('providerId' => $userID, 'requestId' => $fetch['id'], 'Date' => time()));
                    if ($like) {
                        $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'your like it '));
                    } else {
                        $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
                    }
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'already liked'));
                }
            }
        }
    }

    /**
     * Function to unlike a post
     * @param $userID
     * @param $FeedId
     */
    public
    function unLikePost($userID, $FeedId)
    {
        $FeedId = $this->_GB->_DB->escapeString($FeedId);
        $delete = $this->_GB->_DB->delete('likes', "`providerId` = {$userID} AND `requestId` = {$FeedId}");
        if ($delete) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'your Unlike it'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
        }
    }

    /**
     * Function to comment a post
     * @param $Comment
     * @param $FeedID
     * @param $userID
     */
    public function AddComment($Comment, $FeedID, $userID)
    {
        $Comment = $this->_GB->_DB->escapeString($Comment);
        $FeedID = $this->_GB->_DB->escapeString($FeedID);
        $array = array(
            'providerId' => $userID,
            'requestId' => $FeedID,
            'Comment' => $Comment,
            'Date' => time()
        );
        $insert = $this->_GB->_DB->insert('comments', $array);
        if ($insert) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'you add a comment successfully'));
            $UserId = $this->_GB->getUserIDByFeedID($FeedID);
            if ($UserId != $userID) {
                $getUser = $this->_GB->_DB->select('users', '*', '`id`=' . $UserId);
                $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                $getUserInfo = $this->_GB->_DB->select('users', '*', '`id`=' . $userID);
                $fetchUserInfo = $this->_GB->_DB->fetchAssoc($getUserInfo);
                $Username = $this->_GB->getUserNameByID($userID);
                if ($fetchUser['reg_id'] != null) {
                    $regIDs = array($fetchUser['reg_id']);
                    $arrayRequest = array(
                        'for' => 'NewComment',
                        'recipientID' => $UserId,
                        'FeedID' => $FeedID,
                        'UserImage' => $this->_GB->getSafeImage($fetchUserInfo['UserImage']),
                        'UserName' => $Username
                    );
                    $this->_GB->sendMessageThroughGCM($regIDs, $arrayRequest);
                }
            }

        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => "There's an error try again"));
        }
    }

    /**
     * Function to get comments of a specific post
     * @param $FeedID
     */
    public function getComments($FeedID)
    {
        $query = $this->_GB->_DB->select('comments', '*', '`requestId` = ' . $FeedID, '`id` ASC');
        if ($this->_GB->_DB->numRows($query) != 0) {
            $comments = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $holder = $this->getFeedHolder($fetch['providerId']);
                $fetch['holderUserName'] = $holder['UserName'];
                $fetch['holderID'] = $holder['id'];
                $fetch['holderFullName'] = $holder['FullName'];
                $fetch['holderImage'] = $this->_GB->getSafeImage($holder['UserImage']);
                $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
                $fetch['FeedID'] = $fetch['requestId'];
                unset($fetch['providerId']);
                $comments[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($comments);

        } else {
            $this->_GB->JsonResponseMessage(array('comments' => null));
        }
    }

    /**
     * Function to delete a comment
     * @param $commentId
     * @param $userId
     */
    public function DeleteComment($commentId, $userId)
    {
        $commentId = $this->_GB->_DB->escapeString($commentId);
        $userId = $this->_GB->_DB->escapeString($userId);
        $delete = $this->_GB->_DB->delete('comments', "`providerId` = {$userId} AND `id` = {$commentId}");
        if ($delete) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this comment has been removed successfully'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
        }

    }

    /**
     * Function to add new reports of comments
     * @param $Description
     * @param $Reason
     * @param $CommentId
     * @param $userID
     */
    public function insertNewReport($Description, $Reason, $CommentId, $userID)
    {
        $userID = (int)$userID;
        $CommentId = $this->_GB->_DB->escapeString($CommentId);
        $Reason = $this->_GB->_DB->escapeString($Reason);
        $Description = $this->_GB->_DB->escapeString($Description);
        $type = "2";
        $array = array(
            'providerId' => $userID,
            'Description' => $Description,
            'Reason' => $Reason,
            'requestId' => $CommentId,
            'Type' => $type,
            'Date' => time()
        );
        $result = $this->_GB->_DB->insert('reports', $array);
        // check if row inserted or not
        if ($result) {
            // successfully inserted into database
            $array = array(
                'done' => true,
                'message' => 'Your Report is successfully sent thanks .'
            );
            $this->_GB->JsonResponseMessage($array);

        } else {
            // failed to insert row
            $array = array(
                'done' => false,
                'message' => 'Oops! An error occurred.'
            );
            $this->_GB->JsonResponseMessage($array);

        }
    }

    /**
     * Function to get the most liked posts
     * @param $userID
     * @param $limit
     */
    public function getLikedFeeds($userID, $limit)
    {
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
						GROUP BY FS.id ORDER BY FS.id DESC LIMIT {$limit}";

        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $feeds = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
                $fetch['Liked'] = $this->_GB->isLikeIt($userID, $fetch['id']);
                $fetch['Link'] = $this->_GB->getLink($fetch['Link']);
                $fetch['Likes'] = $this->_GB->_DB->CountRows('likes', '`requestId` = ' . $fetch['id']);
                $fetch['Comments'] = $this->_GB->_DB->CountRows('comments', '`requestId` = ' . $fetch['id']);
                $fetch['VideoURL'] = $this->_GB->getSafeVideo($fetch['FeedFile']);
                $fetch['ImageURL'] = $this->_GB->getSafeImage($fetch['FeedFile']);
                $fetch['FeedVideoThumbnail'] = $this->_GB->getSafeImage($fetch['FeedVideoThumbnail']);
                $fetch['FeedVideoThumbnail'] = $this->_GB->getSafeImage($fetch['FeedVideoThumbnail']);
                $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
                $feeds[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($feeds);
        } else {
            $this->_GB->JsonResponseMessage(array('feeds' => null));
        }
    }

    /**
     * Function to edit a comment
     * @param $comment
     * @param $CommentId
     * @param $userID
     */
    public function EditComment($comment, $CommentId, $userID)
    {
        $fields = "`Comment` = '" . $comment . "'";
        $fields .= ",`Date` = '" . time() . "'";
        $fields .= ",`Edited` = '" . 1 . "'";
        $update = $this->_GB->_DB->update('comments', $fields, "`id`= '{$CommentId}' AND `providerId`={$userID}");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your Comment is updated successfully'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your Comment'));
        }

    }

    /**
     * Function to delete a specific post
     * @param $FeedId
     * @param $userId
     */
    public function DeleteFeed($FeedId, $userId)
    {
        $FeedId = $this->_GB->_DB->escapeString($FeedId);
        $userId = $this->_GB->_DB->escapeString($userId);
        $delete = $this->_GB->_DB->delete('feeds', "`holderID` = {$userId} AND `id` = {$FeedId}");
        if ($delete) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this feed has been removed successfully'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
        }
    }

    /**
     * Function to edit a specific post
     * @param $status
     * @param $FeedId
     * @param $userID
     */
    public function EditFeed($status, $FeedId, $userID)
    {
        $fields = "`FeedStatus` = '" . $status . "'";
        $fields .= ",`Date` = '" . time() . "'";
        $update = $this->_GB->_DB->update('feeds', $fields, "`id`= '{$FeedId}' AND `holderID`={$userID}");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your post is updated successfully'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your Post'));
        }

    }

    /**
     * Function to add new reports of posts
     * @param $Description
     * @param $Reason
     * @param $FeedId
     * @param $userID
     */
    public function insertNewReportFeed($Description, $Reason, $FeedId, $userID)
    {
        $userID = (int)$userID;
        $FeedId = $this->_GB->_DB->escapeString($FeedId);
        $Reason = $this->_GB->_DB->escapeString($Reason);
        $Description = $this->_GB->_DB->escapeString($Description);
        $type = "1";
        $array = array(
            'providerId' => $userID,
            'Description' => $Description,
            'Reason' => $Reason,
            'requestId' => $FeedId,
            'Type' => $type,
            'Date' => time()
        );
        $result = $this->_GB->_DB->insert('reports', $array);
        // check if row inserted or not
        if ($result) {
            // successfully inserted into database
            $array = array(
                'done' => true,
                'message' => 'Your Report is successfully sent thanks .'
            );
            $this->_GB->JsonResponseMessage($array);

        } else {
            // failed to insert row
            $array = array(
                'done' => false,
                'message' => 'Oops! An error occurred.'
            );
            $this->_GB->JsonResponseMessage($array);

        }

    }

    /**
     * Function to upload new Thumbnail for videos
     * @param $image
     * @param $userId
     */
    public function NewVideoThumbnailUpload($image, $userId)
    {

        if (isset($_POST['status'])) {
            $status = $this->_GB->_DB->escapeString($_POST['status']);
        } else {
            $status = null;
        }
        if (isset($image)) {
            $ImageID = $this->_GB->uploadVideoThumbnail($image);
        } else {
            $ImageID = null;
        }
        $Type = $this->ImageType;
        $array = array(
            'FeedStatus' => $status,
            'Date' => time(),
            'FeedType' => $Type,
            'holderID' => $userId,
            'FeedFile' => $ImageID
        );
        $result = $this->_GB->_DB->insert('feeds', $array);
        // check if row inserted or not
        if ($result) {
            // successfully inserted into database
            $array = array(
                'success' => true,
                'message' => 'your post successfully created.'
            );
            $this->_GB->JsonResponseMessage($array);

        } else {
            // failed to insert row
            $array = array(
                'success' => false,
                'message' => 'Oops! An error occurred.'
            );
            $this->_GB->JsonResponseMessage($array);
        }
    }


    /**
     * Function to get all posts
     * @param $userID
     * @param $querySQL
     * @param $limit
     */
    public function getPosts($userID, $querySQL, $limit)
    {
        $query = $querySQL . " LIMIT {$limit}";

        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $posts = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
                $fetch['Liked'] = $this->_GB->isLikeIt($userID, $fetch['id']);
                $fetch['Link'] = $this->_GB->getLink($fetch['Link']);
                $fetch['Likes'] = $this->_GB->_DB->CountRows('likes', '`requestId` = ' . $fetch['id']);
                $fetch['Comments'] = $this->_GB->_DB->CountRows('comments', '`requestId` = ' . $fetch['id']);
                $fetch['VideoURL'] = $this->_GB->getSafeVideo($fetch['FeedFile']);
                $fetch['ImageURL'] = $this->_GB->getSafeImage($fetch['FeedFile']);
                $fetch['FeedVideoThumbnail'] = $this->_GB->getSafeImage($fetch['FeedVideoThumbnail']);
                $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
                $posts[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($posts);
        } else {
            $this->_GB->JsonResponseMessage(array());
        }
    }
}