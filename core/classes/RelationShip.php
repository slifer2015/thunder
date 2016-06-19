<?php

/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 10/14/2015
 * Time: 1:36 AM
 */
class RelationShip
{
    private $_GB;

    public $Pending, $Accepted, $Declined, $Blocked;

    function  __construct($_GB)
    {
        $this->_GB = $_GB;
        $this->Pending = 0;
        $this->Accepted = 1;
        $this->Blocked = 2;
    }

    /**
     * Function to send a new request
     * @param $userId
     * @param $id
     */
    public function SentRequestFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        $query = $this->_GB->_DB->select('users', '*', "`id` = {$request}");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            if ($fetch['id'] != $userId) {
                if (($this->CheckStatusProvider($userId, $fetch['id'], $this->Accepted) == true || $this->CheckStatusRequest($userId, $fetch['id'], $this->Accepted) == true)) {
                    $this->_GB->JsonResponseMessage(array('message' => $fetch['UserName'] . '  is already friend with you'));
                } else if (($this->CheckStatusProvider($userId, $fetch['id'], $this->Accepted) != true || $this->CheckStatusRequest($userId, $fetch['id'], $this->Accepted) != true)) {
                    if ($this->CheckStatusProvider($userId, $fetch['id'], $this->Pending) == true || $this->CheckStatusRequest($userId, $fetch['id'], $this->Pending) == true) {
                        $this->_GB->JsonResponseMessage(array('message' => '  there is a Pending request sent a request from ' . $fetch['UserName'] . ' Or you already sent to him'));
                    } else if ($this->CheckStatusProvider($userId, $fetch['id'], $this->Pending) != true || $this->CheckStatusRequest($userId, $fetch['id'], $this->Pending) != true) {
                        if ($this->CheckStatusProvider($userId, $fetch['id'], $this->Blocked) == true) {
                            $this->_GB->JsonResponseMessage(array('message' => $fetch['UserName'] . '  has been blocked you'));

                        } else if ($this->CheckStatusProvider($userId, $fetch['id'], $this->Blocked) != true) {
                            $send = $this->_GB->_DB->insert('friends', array('providerId' => $userId, 'requestId' => $fetch['id'], 'status' => $this->Pending, 'action_user_id' => $userId, 'date' => time(),));
                            if ($send) {
                                $this->_GB->JsonResponseMessage(array('message' => 'Your request is sent successfully'));
                                $getUser = $this->_GB->_DB->select('users', '*', '`id`=' . $fetch['id']);
                                $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                                $getUserInfo = $this->_GB->_DB->select('users', '*', '`id`=' . $userId);
                                $fetchUserInfo = $this->_GB->_DB->fetchAssoc($getUserInfo);
                                $Username = $this->_GB->getUserNameByID($userId);
                                if ($fetchUser['reg_id'] != null) {
                                    $regIDs = array($fetchUser['reg_id']);
                                    $arrayRequest = array(
                                        'for' => 'NewRequest',
                                        'recipientID' => $fetch['id'],
                                        'UserImage' => $this->_GB->getSafeImage($fetchUserInfo['UserImage']),
                                        'UserName' => $Username
                                    );
                                    $this->_GB->sendMessageThroughGCM($regIDs, $arrayRequest);
                                }
                            } else {
                                $this->_GB->JsonResponseMessage(array('message' => ' Failed to sent to this person'));
                            }
                        }
                    }
                } else {
                    $this->_GB->JsonResponseMessage(array('message' => ' Error Try Again '));
                }
            }

        }
    }

    /**
     * Function to cancel a request that user sent
     * @param $userId
     * @param $id
     */
    function CancelRequestFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        $delete = $this->_GB->_DB->delete('friends', "`providerId` = {$userId} AND `requestId` = {$request} AND `status` = $this->Pending ");
        if ($delete) {
            $this->_GB->JsonResponseMessage(array('message' => 'You are cancel this request'));
        } else {
            $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
        }

    }

    /**
     * Function to accept new request
     * @param $userId
     * @param $id
     */
    public
    function AcceptRequestFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        $fields = "`status` = '{$this->Accepted}'";
        $fields .= ",`action_user_id` = '{$userId}'";
        $update = $this->_GB->_DB->update('friends', $fields, "`providerId` = {$request} AND `requestId` = {$userId}");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('message' => 'You are accepted this request'));
            $getUser = $this->_GB->_DB->select('users', '*', '`id`=' . $request);
            $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
            $getUserInfo = $this->_GB->_DB->select('users', '*', '`id`=' . $userId);
            $fetchUserInfo = $this->_GB->_DB->fetchAssoc($getUserInfo);
            $Username = $this->_GB->getUserNameByID($userId);
            if ($fetchUser['reg_id'] != null) {
                $regIDs = array($fetchUser['reg_id']);
                $msg = array(
                    'for' => 'RequestAccept',
                    'userID' => $request,
                    'UserImage' => $this->_GB->getSafeImage($fetchUserInfo['UserImage']),
                    'providerID' => $userId,
                    'UserName' => $Username
                );
                $this->_GB->sendMessageThroughGCM($regIDs, $msg);

            }
        } else {
            $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
        }
    }

    /**
     * Function to decline the new request
     * @param $userId
     * @param $id
     */
    public
    function DeclineRequestFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        $delete = $this->_GB->_DB->delete('friends', "`providerId` = {$request} AND `requestId` = {$userId} AND `status` = $this->Pending ");
        if ($delete) {
            $this->_GB->JsonResponseMessage(array('message' => 'You are declined this request'));
        } else {
            $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
        }

    }

    /**
     * Function to remove friend from you list friends
     * @param $userId
     * @param $id
     */
    public
    function UnFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        if ($this->CheckStatusProvider($userId, $request, $this->Accepted) == true) {
            $delete = $this->_GB->_DB->delete('friends', "`providerId` = {$userId} AND `requestId` = {$request} AND `status` = {$this->Accepted} AND `action_user_id` = {$request} ");
            if ($delete) {
                $this->_GB->JsonResponseMessage(array('message' => 'You are removed this person successfully'));
            } else {
                $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
            }
        } else {
            $delete = $this->_GB->_DB->delete('friends', "`providerId` = {$request} AND `requestId` = {$userId} AND `status` = {$this->Accepted} AND `action_user_id` = {$userId} ");
            if ($delete) {
                $this->_GB->JsonResponseMessage(array('message' => 'You are removed this person successfully'));
            } else {
                $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
            }


        }

    }

    /**
     * Function to block a friend
     * @param $userId
     * @param $id
     */
    public
    function BlockRequestFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        if ($this->CheckStatusProvider($userId, $request, $this->Blocked) != true || $this->CheckStatusProvider($request, $userId, $this->Blocked) != true) {
            if ($this->CheckStatusProvider($userId, $request, $this->Accepted) == true) {
                $fields = "`status` = {$this->Blocked}";
                $fields .= ",`action_user_id` = {$userId}";
                $update = $this->_GB->_DB->update('friends', $fields, "`providerId` = {$userId} AND `requestId` = {$request} AND `status` = {$this->Accepted} AND `action_user_id` = {$request}");
                if ($update) {
                    $this->_GB->JsonResponseMessage(array('message' => 'You are Blocked this person successfully1'));
                } else {
                    $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong 1'));
                }
            } else {
                $fields = "`status` = {$this->Blocked}";
                $fields .= ",`action_user_id` = {$userId}";
                $update = $this->_GB->_DB->update('friends', $fields, "`providerId` = {$request} AND `requestId` = {$userId} AND `status` = {$this->Accepted} AND `action_user_id` = {$userId} ");
                if ($update) {
                    $this->_GB->JsonResponseMessage(array('message' => 'You are Blocked this person successfully2'));
                } else {
                    $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong 2'));
                }
            }
        } else {
            $this->_GB->JsonResponseMessage(array('message' => 'this person has been already blocked you'));
        }
    }

    /**
     * Function to unblock friend who has been blocked
     * @param $userId
     * @param $id
     */
    public
    function UnBlockRequestFriend($userId, $id)
    {
        $request = $this->_GB->_DB->escapeString($id);
        if ($this->CheckStatusRequest($userId, $request, $this->Blocked) == true) {
            $fields = "`status` = {$this->Accepted}";
            $fields .= ",`action_user_id` = {$userId}";
            $update = $this->_GB->_DB->update('friends', $fields, "`providerId` = {$request} AND `requestId` = {$userId} AND `status` = {$this->Blocked} AND `action_user_id` = {$userId}");
            if ($update) {
                $this->_GB->JsonResponseMessage(array('message' => 'You are UnBlocked this person successfully1'));
            } else {
                $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
            }
        } else if ($this->CheckStatusProvider($userId, $request, $this->Blocked) == true) {
            $fields = "`status` = {$this->Accepted}";
            $fields .= ",`action_user_id` = {$request}";
            $update = $this->_GB->_DB->update('friends', $fields, "`providerId` = {$userId} AND `requestId` = {$request} AND `status` = {$this->Blocked}  AND `action_user_id` = {$userId}");
            if ($update) {
                $this->_GB->JsonResponseMessage(array('message' => 'You are UnBlocked this person successfully2'));
            } else {
                $this->_GB->JsonResponseMessage(array('message' => 'Something went wrong'));
            }
        }

    }

    /**
     * Function to get list friends
     * @param $userId
     * @param $limit
     */
    public
    function GetFriendsList($userId, $limit)
    {
        $querySQL = "SELECT U.*,F.*,F.id AS friendId,U.id AS id
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userId},F.requestId,F.providerId)
                            WHERE ((F.providerId = {$userId} AND F.status = {$this->Accepted} )
                            OR   (F.requestId = {$userId} AND F.status = {$this->Accepted})) AND U.UserState = 'Active'
                            GROUP BY U.id ORDER BY U.id  DESC LIMIT {$limit}";


        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($result) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($result)) {
                $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['UserStatus'] = (empty($fetch['UserStatus'])) ? null : $fetch['UserStatus'];
                $fetch['Friends'] = $this->CheckStatusFriends($userId, $this->Accepted);
                $fetch['Blocked'] = $this->CheckStatusRequest($userId, $fetch['friendId'], $this->Blocked) || $this->CheckStatusProvider($userId, $fetch['friendId'], $this->Blocked);
                unset($fetch['authenticateTime'], $fetch['reg_id'], $fetch['date']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(array('friends' => null));
        }

    }

    /**
     * Function to get list friends for lsit chat
     * @param $userId
     * @param $limit
     */
    public
    function GetFriendsChatList($userId, $limit)
    {
        $querySQL = "SELECT U.*,F.*,F.id AS friendId,U.id AS id
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userId},F.requestId,F.providerId)
                            WHERE ((F.providerId = {$userId} AND F.status = {$this->Accepted} )
                            OR   (F.requestId = {$userId} AND F.status = {$this->Accepted})) AND U.UserState = 'Active'
                            GROUP BY U.id ORDER BY U.UserStatus  DESC LIMIT {$limit}";


        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($result) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($result)) {
                $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['UserStatus'] = (empty($fetch['UserStatus'])) ? null : $fetch['UserStatus'];
                $fetch['LastAuthenticateTime'] = $this->_GB->Date($fetch['authenticateTime']);
                $fetch['Friends'] = $this->CheckStatusFriends($userId, $this->Accepted);
                $fetch['Blocked'] = $this->CheckStatusRequest($userId, $fetch['friendId'], $this->Blocked) || $this->CheckStatusProvider($userId, $fetch['friendId'], $this->Blocked);;
                unset($fetch['reg_id'], $fetch['date']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(array('friends' => null));
        }

    }

    /**
     * Function to get friend for a specific user
     * @param $userID
     * @param $userId
     * @param $limit
     */
    public
    function GetUserFriendsList($userID, $userId, $limit)
    {
        $querySQL = "SELECT U.*,F.*,F.id AS friendId,U.id AS id
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userId},F.requestId,F.providerId)
                            WHERE ((F.providerId = {$userId} AND F.status = {$this->Accepted} )
                            OR   (F.requestId = {$userId} AND F.status = {$this->Accepted})) AND U.UserState = 'Active'
                            GROUP BY U.id ORDER BY U.UserStatus  DESC LIMIT {$limit}";


        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($result) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($result)) {
                $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['UserStatus'] = (empty($fetch['UserStatus'])) ? null : $fetch['UserStatus'];
                $fetch['LastAuthenticateTime'] = $this->_GB->Date($fetch['authenticateTime']);
                $fetch['Friends'] = $this->CheckStatusProvider($userId, $userID, $this->Accepted) || $this->CheckStatusProvider($userID, $userId, $this->Accepted);
                $fetch['Blocked'] = $this->CheckStatusProvider($userId, $fetch['friendId'], $this->Blocked) || $this->CheckStatusProvider($userId, $fetch['friendId'], $this->Blocked);;
                unset($fetch['reg_id'], $fetch['date']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(array('friends' => null));
        }

    }

    /**
     * Function to get sent request list friends
     * @param $userId
     * @param $limit
     */
    function GetSentRequestFriendsList($userId, $limit)
    {
        $querySQL = "SELECT U.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId = {$userId} AND F.requestId != {$userId}
                            WHERE U.id != F.providerId AND  U.id = F.requestId AND  F.status = {$this->Pending}  AND F.action_user_id = {$userId}
						    GROUP BY U.id ORDER BY U.id  DESC LIMIT {$limit}";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($result) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($result)) {
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['Pending'] = $this->CheckStatusProvider($userId, $fetch['id'], $this->Pending);
                unset($fetch['authenticationTime'], $fetch['reg_id'], $fetch['date']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(array('friends' => null));
        }

    }

    /**
     * Function to get new request list friends
     * @param $userId
     * @param $limit
     */
    function GetNewRequestFriendsList($userId, $limit)
    {
        $querySQL = "SELECT  U.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId != {$userId} AND F.requestId = {$userId}
                            WHERE U.id = F.providerId AND  U.id != F.requestId AND F.status = {$this->Pending} AND F.action_user_id != {$userId}
						    GROUP BY U.id ORDER BY U.id  DESC LIMIT {$limit}";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($result) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($result)) {
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['Pending'] = $this->CheckStatusRequest($userId, $fetch['id'], $this->Pending);
                unset($fetch['authenticationTime'], $fetch['reg_id'], $fetch['date']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(array('friends' => null));
        }

    }

    /**
     * Function to get block list friends
     * @param $userId
     * @param $limit
     */
    function getBlockedFriendsList($userId, $limit)
    {

        $querySQL = "SELECT U.*,F.*,F.id AS friendId,U.id AS id
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userId},F.requestId,F.providerId)
                            WHERE ( F.status = {$this->Blocked} AND F.action_user_id = {$userId}  )
                            GROUP BY U.id ORDER BY U.id  DESC LIMIT {$limit}";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($result) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($result)) {
                $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['Blocked'] = $this->CheckStatusRequest($userId, $fetch['id'], $this->Blocked) || $this->CheckStatusProvider($userId, $fetch['id'], $this->Blocked);;
                unset($fetch['authenticationTime'], $fetch['reg_id'], $fetch['date']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(array('friends' => null));
        }

    }

    /**
     * Function to search friends
     * @param $string
     * @param $userId
     */
    public
    function SearchFriends($string, $userId)
    {
        $string = $this->_GB->_DB->escapeString(trim($string));
        if ($string != 'Suggestions') {

            $querySQL = "SELECT U.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId != {$userId} OR F.requestId != {$userId}
                            WHERE  U.id != {$userId} AND (U.UserName LIKE '%" . $string . "%'  OR U.UserEmail LIKE '%" . $string . "%') AND U.UserState = 'Active'
						    GROUP BY U.id ORDER BY U.id ";


        } else {
            $querySQL = "SELECT U.*,F.*,F.id AS friendId,U.id AS id
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userId},F.requestId,F.providerId)
                            WHERE (F.providerId != {$userId})
                            OR   (F.requestId != {$userId} ) AND U.UserState = 'Active'
                             GROUP BY RAND() ORDER BY RAND()  DESC LIMIT 5";
        }
        $query = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $friends = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['UserName'] = (empty($fetch['UserName'])) ? null : $fetch['UserName'];
                $fetch['UserJob'] = (empty($fetch['UserJob'])) ? null : $fetch['UserJob'];
                $fetch['UserImage'] = (empty($fetch['UserImage'])) ? null : $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['Friends'] = $this->CheckStatusProvider($fetch['id'], $userId, $this->Accepted) || $this->CheckStatusProvider($userId, $fetch['id'], $this->Accepted);
                $fetch['PendingRequest'] = $this->CheckStatusProvider($fetch['id'], $userId, $this->Pending);
                $fetch['Pending'] = $this->CheckStatusProvider($userId, $fetch['id'], $this->Pending);
                $fetch['BlockedRequest'] = $this->CheckStatusProvider($fetch['id'], $userId, $this->Blocked);
                $fetch['Blocked'] = $this->CheckStatusProvider($userId, $fetch['id'], $this->Blocked);
                unset($fetch['UserPassword'], $fetch['UserEmail'], $fetch['reg_id'], $fetch['UserCover'], $fetch['UserStatus'], $fetch['UserAddress'], $fetch['date'], $fetch['authenticationTime'], $fetch['status']);
                $friends[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($friends);
        } else {
            $this->_GB->JsonResponseMessage(null);
        }


    }

    /**
     * Function to check friend Status Provider
     * @param $provider
     * @param $request
     * @param $status
     * @return bool
     */
    public
    function CheckStatusProvider($provider, $request, $status)
    {

        $querySQL = "SELECT U.*,F.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId = {$provider} AND F.requestId = {$request}
                            WHERE U.id != F.providerId AND  U.id = F.requestId AND F.status = {$status}
                            GROUP BY U.id ORDER BY U.id
						   ";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        $rows = $this->_GB->_DB->numRows($result);
        if ($rows != 0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Function to check friend Status Requester
     * @param $request
     * @param $provider
     * @param $status
     * @return bool
     */
    public
    function CheckStatusRequest($request, $provider, $status)
    {

        $querySQL = "SELECT U.*,F.*
						     FROM prefix_users U
					     	LEFT JOIN prefix_friends AS F
                            ON F.providerId = {$provider} AND F.requestId = {$request}
                            WHERE U.id = F.providerId AND  U.id != F.requestId AND F.status = {$status}
                            GROUP BY U.id ORDER BY U.id
						   ";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        $rows = $this->_GB->_DB->numRows($result);
        if ($rows != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to check friend Status
     * @param $userId
     * @param $status
     * @return bool
     */
    public
    function CheckStatusFriends($userId, $status)
    {
        $querySQL = "SELECT U.*,F.*,F.id AS friendId,U.id AS id
						     FROM prefix_friends F
					     	LEFT JOIN prefix_users AS U
                            ON U.id = IF(F.providerId = {$userId},F.requestId,F.providerId)
                            WHERE (F.providerId = {$userId} AND F.status = {$status} )
                            OR   (F.requestId = {$userId} AND F.status = {$status})
                            GROUP BY U.id ORDER BY U.id
						   ";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        $rows = $this->_GB->_DB->numRows($result);
        if ($rows != 0) {
            return true;
        } else {
            return false;
        }
    }


}