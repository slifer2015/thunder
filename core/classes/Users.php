<?php

/**
 *
 */
class Users
{
    public $_GB, $Relation;

    function __construct($_GB, $Relation)
    {
        $this->_GB = $_GB;
        $this->_Relation = $Relation;
    }

    /**
     * Function to login
     * @param $username
     * @param $userPassword
     */
    public function userLogin($username, $userPassword)
    {

        $username = $this->_GB->_DB->escapeString($username);
        $userPassword = md5($userPassword);
        $State = "Deactivate";
        $result = $this->_GB->_DB->select('users', '*', "`UserName` = '{$username}' AND `UserPassword` = '{$userPassword}'");
        $fetch = $this->_GB->_DB->fetchAssoc($result);
        if ($this->_GB->_DB->numRows($result) > 0) {
            if ($fetch['UserState'] == $State) {
                $this->ActivateAccount($fetch['id']);
            }
            if ($fetch['UserStatus'] = "offline") {
                $this->updateUserStatusOnline($fetch['id']);
            }
            $token = md5(time() . uniqid() . $username);
            if ($fetch['isActivated'] != 1) {
                $array = array(
                    'success' => false,
                    'userID' => null,
                    'token' => null,
                    'message' => 'Account need activation'
                );
                $this->_GB->JsonResponseMessage($array);
            } else {
                $this->_GB->SetSession('userID', $fetch['id']);
                //header('Location: home.php');
                $array = array(
                    'userID' => $fetch['id'],
                    'token' => $token,
                    'date' => time()
                );

                $insert = $this->_GB->_DB->insert('sessions', $array);
                if ($insert) {

                    $array = array(
                        'success' => true,
                        'userID' => $fetch['id'],
                        'token' => $token,
                        'message' => 'Sign In successfully'
                    );

                }

                $this->_GB->JsonResponseMessage($array);
            }

        } else {
            $array = array(
                'success' => false,
                'userID' => null,
                'token' => null,
                'message' => 'User Name Or Password Invalid '
            );
            $this->_GB->JsonResponseMessage($array);


        }


    }

    /**
     * Function to register
     * @param $UserName
     * @param $Email
     * @param $Password
     * @param $FullName
     * @param $UserJob
     * @param $UserAddress
     */
    public
    function userRegister($UserName, $Email, $Password, $FullName, $UserJob, $UserAddress)
    {

        $username = $this->_GB->_DB->escapeString($UserName);
        $UserEmail = $this->_GB->_DB->escapeString($Email);
        $UserPassword = md5($Password);
        if (strlen(trim($UserName)) <= 4) {
            // failed to insert row
            $array = array(
                'success' => false,
                'message' => 'Username too short'
            );
            $this->_GB->JsonResponseMessage($array);

        } else if (filter_var($Email, FILTER_VALIDATE_EMAIL) === false) {

            // failed to insert row
            $array = array(
                'success' => false,
                'message' => 'Invalid E-mail'
            );
            $this->_GB->JsonResponseMessage($array);

        } else if (strlen(trim($Password)) <= 5) {
            // failed to insert row
            $array = array(
                'success' => false,
                'message' => 'password too short'
            );
            $this->_GB->JsonResponseMessage($array);

        } else if ($this->UserExist($UserName)) {
            // failed to insert row
            $array = array(
                'success' => false,
                'message' => 'Username is already exist try another one'
            );
            $this->_GB->JsonResponseMessage($array);

        } else if ($this->EmailExist($Email)) {
            // failed to insert row
            $array = array(
                'success' => false,
                'message' => 'UserEmail is already exist try another one'
            );
            $this->_GB->JsonResponseMessage($array);

        } else {
            if (isset($_FILES['UserImage'])) {
                $imageID = $this->_GB->uploadImage($_FILES['UserImage']);
            } else {
                $imageID = null;
            }
            $state = "Active";
            $status = "offline";
            $isActivated = $this->_GB->GetConfig('emailactivation','users') != 0 ? 0 : 1;
            $array = array(
                'UserName' => $username,
                'UserEmail' => $UserEmail,
                'UserPassword' => $UserPassword,
                'FullName' => $FullName,
                'UserState' => $state,
                'UserStatus' => $status,
                'UserJob' => $UserJob,
                'UserAddress' => $UserAddress,
                'UserImage' => $imageID,
                'Date' => time(),
                'isActivated' => $isActivated
            );
            $result = $this->_GB->_DB->insert('users', $array);
            $id = $this->_GB->_DB->last_Id();
            // check if row inserted or not
            if ($result) {

                // successfully inserted into database
                if ($this->_GB->GetConfig('emailactivation','users') != 0) {
                    $this->activateMail($id, $UserEmail);
                    $array = array(
                        'success' => true,
                        'message' => 'user successfully created. check your email for activation'
                    );
                    $this->_GB->JsonResponseMessage($array);
                } else {
                    $array = array(
                        'success' => true,
                        'message' => 'user successfully created.'
                    );
                    $this->_GB->JsonResponseMessage($array);
                }


            } else {
                // failed to insert row
                $array = array(
                    'success' => false,
                    'message' => 'Oops! An error occurred.'
                );
                $this->_GB->JsonResponseMessage($array);

            }

        }


    }

    /**
     * Function to  check if th user is already exist.
     * @param $UserName
     * @return bool
     */
    public
    function UserExist($UserName)
    {

        $query = $this->_GB->_DB->select('users', '`id`', "`UserName` = '{$UserName}' ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
        $this->_GB->_DB->free();
    }

    /**
     *  Function to check if the email is already exist.
     * @param $Email
     * @return bool
     */
    public
    function EmailExist($Email)
    {
        $query = $this->_GB->_DB->select('users', '`id`', "`UserEmail` = '" . $Email . "'");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
        $this->_GB->_DB->free();
    }


    /**
     * Function to  get the user id
     * @param $token
     * @return int
     */
    public
    function getUserID($token)
    {
        $token = $this->_GB->_DB->escapeString($token);
        $query = $this->_GB->_DB->select('sessions', '*', "`token`= '{$token}'");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            return $fetch['userID'];
        } else {
            return 0;
        }
    }

    /**
     * Function to  Update profile  of User
     * @param $array
     * @param $imageID
     * @param $coverID
     * @param $userID
     */
    public
    function updateProfile($array, $imageID, $coverID, $userID)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->_GB->_DB->escapeString(trim($value));
        }
        $query = $this->_GB->_DB->select('users', '`UserName`', "`id` = {$userID}");
        $fetch = $this->_GB->_DB->fetchAssoc($query);
        if ($fetch['UserName'] != $array['UserName'] && $this->UserExist($array['UserName'])) {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'UserName is already exists'));
        } else if (filter_var($array['UserEmail'], FILTER_VALIDATE_EMAIL) === false) {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'Invalid E-mail'));
        } else {
            $fields = "`UserName` = '" . $array['UserName'] . "'";
            $fields .= ",`FullName` = '" . $array['FullName'] . "'";
            $fields .= ",`UserJob` = '" . $array['UserJob'] . "'";
            $fields .= ",`UserAddress` = '" . $array['UserAddress'] . "'";
            $fields .= ",`UserEmail` = '" . $array['UserEmail'] . "'";
            if (!empty($array['UserPassword'])) {
                $fields .= ",`UserPassword` = '" . md5($array['UserPassword']) . "'";
            }
            if ($imageID != null) {
                $fields .= ",`UserImage` = '{$imageID}'";
            }
            if ($coverID != null) {
                $fields .= ",`UserCover` = '{$coverID}'";
            }
            $update = $this->_GB->_DB->update('users', $fields, "`id` = {$userID}");
            if ($update) {
                $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your profile is updated successfully'));
            } else {
                $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your profile'));
            }
        }

    }

    /**
     * Function to get user details
     * @param $id
     * @param $userId
     * @return mixed
     */
    public
    function getUserDetails($id, $userId)
    {
        $id = (int)$id;
        $userId = (int)$userId;
        if ($id != 0 && $id != $userId) {
            if ($this->_Relation->CheckStatusProvider($id, $userId, $this->_Relation->Accepted) || $this->_Relation->CheckStatusProvider($userId, $id, $this->_Relation->Accepted)) {
                $query = $this->_GB->_DB->select('users', '*', "`id` = {$id}");
                $fetch = $this->_GB->_DB->fetchAssoc($query);
                if (empty($fetch['FullName'])) {
                    $fetch['FullName'] = $fetch['UserName'];
                }
                $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
                $fetch['FriendsCounter'] = $this->_GB->_DB->CountRows('friends', "(((`requestId` = {$userId}  AND `providerId` = {$id}) OR (`requestId` = {$id}  AND `providerId` = {$userId} ))AND `status`={$this->_Relation->Accepted})");
                $fetch['FeedsCounter'] = $this->_GB->_DB->CountRows('feeds', " `holderID` = {$id} ");
                $fetch['Mine'] = false;
                $fetch['Pending'] = false;
                $fetch['PendingRequest'] = false;
                $fetch['Friends'] = true;
                $fetch['Blocked'] = false;
                $fetch['BlockedRequest'] = false;
                $fetch['UserImage'] = $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['UserCover'] = $this->_GB->getSafeImage($fetch['UserCover']);
                return $fetch;
            } else {
                $query = $this->_GB->_DB->select('users', '*', "`id` = {$id}");
                $fetch = $this->_GB->_DB->fetchAssoc($query);
                if (empty($fetch['FullName'])) {
                    $fetch['FullName'] = $fetch['UserName'];
                }
                $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
                $fetch['FriendsCounter'] = $this->_GB->_DB->CountRows('friends', "(((`requestId` = {$userId}  AND `providerId` = {$id}) OR (`requestId` = {$id}  AND `providerId` = {$userId} ))AND `status`={$this->_Relation->Accepted})");
                $fetch['FeedsCounter'] = $this->_GB->_DB->CountRows('feeds', " `holderID` = {$id} ");
                $fetch['Mine'] = false;
                $fetch['PendingRequest'] = $this->_Relation->CheckStatusProvider($id, $userId, $this->_Relation->Pending);
                $fetch['Pending'] = $this->_Relation->CheckStatusProvider($userId, $id, $this->_Relation->Pending);
                $fetch['BlockedRequest'] = $this->_Relation->CheckStatusProvider($id, $userId, $this->_Relation->Blocked);
                $fetch['Blocked'] = $this->_Relation->CheckStatusProvider($userId, $id, $this->_Relation->Blocked);
                $fetch['Friends'] = false;
                $fetch['UserImage'] = $this->_GB->getSafeImage($fetch['UserImage']);
                $fetch['UserCover'] = $this->_GB->getSafeImage($fetch['UserCover']);
                return $fetch;
            }
        } else {
            $query = $this->_GB->_DB->select('users', '*', "`id` = {$userId}");
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['Date'] = $this->_GB->TimeAgo($fetch['Date']);
            $fetch['FriendsCounter'] = $this->_GB->_DB->CountRows('friends', "`requestId` = {$userId} OR `providerId` = {$userId}  AND `status`={$this->_Relation->Accepted}");
            $fetch['FeedsCounter'] = $this->_GB->_DB->CountRows('feeds', " `holderID` = {$id} ");
            $fetch['Mine'] = true;
            $fetch['Pending'] = false;
            $fetch['PendingRequest'] = false;
            $fetch['Friends'] = false;
            $fetch['UserImage'] = $this->_GB->getSafeImage($fetch['UserImage']);
            $fetch['UserCover'] = $this->_GB->getSafeImage($fetch['UserCover']);
            return $fetch;
        }
    }

    /**
     * Function to update the register id  GCM of a specific user
     * @param $userID
     * @param $regID
     */
    public
    function updateRegID($userID, $regID)
    {
        $regID = $this->_GB->_DB->escapeString($regID);
        $update = $this->_GB->_DB->update('users', "`reg_id` = '{$regID}'", "`id` = '$userID'");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'updated successfully'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'something went wrong'));
        }
    }

    /**
     * Function to get username by his id
     * @param $id
     * @return mixed
     */
    public function getUserNameByID($id)
    {
        $query = $this->_GB->_DB->select('users', '`UserName`', "`id` = {$id}");
        $fetch = $this->_GB->_DB->fetchAssoc($query);
        return $fetch['UserName'];
    }

    /**
     * Function to get user id by his name
     * @param $name
     * @return mixed
     */
    public function getIDByName($name)
    {
        $name = $this->_GB->_DB->escapeString($name);
        $query = $this->_GB->_DB->select('users', '`id`', "`FullName`  LIKE '%$name%' OR `UserName` LIKE '%$name%'");
        $fetch = $this->_GB->_DB->fetchAssoc($query);
        $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
        return $fetch;
    }
    /****************************
     * functions for admins
     ****************************/

    /**
     * Function for admin login
     * @param $username
     * @param $password
     */
    public
    function adminLogin($username, $password)
    {
        $username = trim($this->_GB->_DB->escapeString($username));
        $password = trim($password);
        $adminPassword = md5($password);

        $query = $this->_GB->_DB->select('admins', '*', "`AdminName` = '{$username}' AND `AdminPassword` = '{$adminPassword}'");
        $fetch = $this->_GB->_DB->fetchAssoc($query);
        if (empty($username) || empty($password)) {
            echo $this->_GB->ShowError('All fields required');
        } else if ($this->_GB->_DB->numRows($query) <= 0) {
            echo $this->_GB->ShowError('Login failed please try again');
        } else {
            $this->_GB->SetSession('admin', $fetch['id']);
            $this->_GB->SetSession('AdminName', $fetch['AdminName']);
            header("Refresh: 1; url=index.php?cmd=index");
            echo $this->_GB->ShowError('Logged in successfully.', 'yes');
        }
        $this->_GB->_DB->free($query);
    }

    /**
     * Function to check if the admins is already exist in database
     * @param $username
     * @return bool
     */
    public
    function AdminExists($username)
    {
        $query = $this->_GB->_DB->select('admins', '`id`', "`AdminName` = '" . $username . "'");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Function to get admin details
     * @param $id
     * @return mixed
     */
    public
    function getAdminDetails($id)
    {
        $id = (int)$id;
        if ($id != 0) {
            $query = $this->_GB->_DB->select('admins', '*', "`id` = {$id}");
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            return $fetch;
        } else {
            /*$query = $this->select('admins', '*', "`id` = {$id}");
            $fetch = $this->fetchAssoc($query);
            return $fetch;*/
        }
    }

    /****************************
     * functions for chat
     ***************************/

    /**
     * Function to get list conversations
     * @param $userId
     * @param $limit
     */
    public function getListConversations($userId, $limit)
    {
        $query = "SELECT C.id AS ConversationID,
                  C.Date AS MessageDate,
                  R.action_deleted_user_id,
                  C.action_user_id,
                  U.UserStatus,
                  U.id AS RecipientID ,
                  U.UserName AS RecipientUserName,
                  U.FullName AS  RecipientFullName,
                  U.UserImage AS  RecipientImage

                FROM prefix_users U,prefix_conversation C, prefix_chat R
                WHERE
                CASE
                WHEN C.providerId = {$userId}
                THEN C.requestId = U.id
                WHEN C.requestId = {$userId}
                THEN C.providerId= U.id
                END
                AND
                C.id=R.ConversationsID
                AND
              (C.providerId ={$userId}
              OR C.requestId ={$userId})
              AND U.UserState = 'Active' AND  (C.action_user_id != {$userId} OR R.action_deleted_user_id != {$userId})
              GROUP BY C.id  ORDER BY C.id DESC LIMIT {$limit} ";
        $query = $this->_GB->_DB->MySQL_Query($query);

        if ($this->_GB->_DB->numRows($query) != 0) {
            $conversations = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['action_user_id'] = (empty($fetch['action_user_id'])) ? null : $fetch['action_user_id'];
                $fetch['action_deleted_user_id'] = (empty($fetch['action_deleted_user_id'])) ? null : $fetch['action_deleted_user_id'];
                $fetch['LastMessage'] = $this->getConversationLastMessage($fetch['ConversationID'], $userId, $fetch['RecipientID']);
                $fetch['MessageDate'] = $this->getConversationLastMessageDate($fetch['ConversationID']);
                $fetch['RecipientImage'] = $this->_GB->getSafeImage($fetch['RecipientImage']);
                $fetch['Status'] = $this->getConversationLastMessageStatus($fetch['RecipientID'], $fetch['ConversationID']);
                $fetch['UnreadMessageCounter'] = $this->_GB->_DB->CountRows('chat', " `status` = 0 AND `UserID` = '{$fetch['RecipientID']}' AND `ConversationsID` = {$fetch['ConversationID']}");
                if ($fetch['LastMessage'] === null || $fetch['MessageDate'] === null) {
                    continue;
                }
                $conversations[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($conversations);
        } else {
            $this->_GB->JsonResponseMessage(array('conversations' => null));
        }
    }

    /**
     * Function to get date of last message
     * @param $conversationId
     * @return null
     */
    public function getConversationLastMessageDate($conversationId)
    {
        $query = $this->_GB->_DB->select('chat', '`Date`', "`ConversationsID` = {$conversationId}", '`Date` DESC', '1');
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['Date'] = $this->_GB->Date($fetch['Date']);
            return $fetch['Date'];
        } else {
            return null;
        }
    }

    /**
     * Function to get status of last message
     * @param $RecipientID
     * @param $conversationId
     * @return null
     */
    public function getConversationLastMessageStatus($RecipientID, $conversationId)
    {
        $query = $this->_GB->_DB->select('chat', '`status`', "`UserID` = '{$RecipientID}' AND `ConversationsID` = {$conversationId}", '`Date` DESC', '1');
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            return $fetch['status'];
        } else {
            return null;
        }
    }

    /**
     * function to get the last message
     * @param $conversationId
     * @param $userId
     * @param $RecipientID
     * @return null
     */
    public function getConversationLastMessage($conversationId, $userId, $RecipientID)
    {
        $query = "SELECT R.reply,R.image
                  FROM prefix_chat R
                  WHERE  R.ConversationsID = {$conversationId} AND R.action_deleted_user_id != {$userId} AND (R.UserID = {$userId} OR R.UserID = {$RecipientID})
                  ORDER BY R.Date DESC LIMIT 1";
        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            if ($fetch['image'] != null) {
                return "Image";
            }
            return $fetch['reply'];
        } else {
            return null;
        }
    }

    /**
     * Function to search in conversations
     * @param $string
     * @param $userId
     */
    public function SearchConversation($string, $userId)
    {
        $string = $this->_GB->_DB->escapeString(trim($string));

        $querySQL = "SELECT C.id AS ConversationID,
                  C.Date AS MessageDate,
                  R.action_deleted_user_id,
                  C.action_user_id,
                  U.UserStatus,
                  U.id AS RecipientID ,
                  U.UserName AS RecipientUserName,
                  U.FullName AS  RecipientFullName,
                  U.UserImage AS  RecipientImage
               FROM prefix_users U,prefix_conversation C, prefix_chat R
                WHERE
                CASE
                WHEN C.providerId = {$userId}
                THEN C.requestId = U.id
                WHEN C.requestId = {$userId}
                THEN C.providerId= U.id
                END
                AND
                C.id=R.ConversationsID
                AND
              (C.providerId ={$userId}
              OR C.requestId ={$userId})
              AND (U.UserName LIKE '%" . $string . "%'
               OR U.FullName LIKE '%" . $string . "%')
              AND U.UserState = 'Active' AND  (C.action_user_id != {$userId} OR R.action_deleted_user_id != {$userId})
              GROUP BY C.id  ORDER BY C.id";

        $query = $this->_GB->_DB->MySQL_Query($querySQL);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $conversations = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                $fetch['LastMessage'] = $this->getConversationLastMessage($fetch['ConversationID'], $userId, $fetch['RecipientID']);
                $fetch['MessageDate'] = $this->getConversationLastMessageDate($fetch['ConversationID']);
                $fetch['RecipientImage'] = $this->_GB->getSafeImage($fetch['RecipientImage']);
                $fetch['Status'] = $this->getConversationLastMessageStatus($fetch['RecipientID'], $fetch['ConversationID']);
                $fetch['UnreadMessageCounter'] = $this->_GB->_DB->CountRows('chat', " `status` = 0 AND `UserID` = '{$fetch['RecipientID']}' AND `ConversationsID` = {$fetch['ConversationID']}");
                if ($fetch['LastMessage'] === null || $fetch['MessageDate'] === null) {
                    continue;
                }
                $conversations[] = $fetch;
            }
            $this->_GB->JsonResponseMessage($conversations);
        } else {
            $this->_GB->JsonResponseMessage(null);
        }
    }

    /**
     * Function to delete conversations
     * @param $userId
     * @param $array
     */
    public function DeleteConversation($userId, $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->_GB->_DB->escapeString(trim($value));
        }
        $Deleted = 1;
        if ($userId != $array['RecipientID']) {
            $conversationInfo = $this->getConversations($userId, $array['ConversationID']);
            //  $this->_GB->JsonResponseMessage($conversationInfo);
            if ($conversationInfo['Deleted'] != $Deleted && $conversationInfo['action_user_id'] == 0) {
                $fields = "`Deleted` = '" . $Deleted . "'";
                $fields .= ",`action_user_id` = '" . $userId . "'";
                $update = $this->_GB->_DB->update('conversation', $fields, "(`providerId` = '{$userId}' OR `requestId` = '{$userId}')  AND `id`= '{$conversationInfo['ConversationID']}'");

                if ($update) {
                    $fields = "`ConversationDeleted` = '" . $Deleted . "'";
                    $fields .= ",`action_deleted_user_id` = '" . $userId . "'";
                    $this->_GB->_DB->update('chat', $fields, "(`UserID` = '{$userId}' OR `UserID` = '{$array['RecipientID']}')  AND `ConversationsID`= '{$array['ConversationID']}'");
                    $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this conversation has been removed successfully1'));
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong1'));
                }
            } else if ($conversationInfo['Deleted'] == $Deleted && $conversationInfo['action_user_id'] == $array['RecipientID']) {
                $delete = $this->_GB->_DB->delete('chat', "(`UserID` = '{$userId}' OR `UserID` = '{$array['RecipientID']}')  AND `ConversationsID`= '{$array['ConversationID']}'");
                if ($delete) {
                    $this->_GB->_DB->delete('conversation', "(`providerId` = '{$userId}' OR `requestId` = '{$userId}')  AND `id`= '{$conversationInfo['ConversationID']}'");
                    $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this conversation has been removed successfully'));
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
                }
            }
        }

    }

    /**
     * Function to fetch conversation and delete it
     * @param $userId
     * @param $conversationId
     * @return null
     */
    public function getConversations($userId, $conversationId)
    {


        $query = "SELECT C.id AS ConversationID,
                  C.Date AS MessageDate,
                  C.Deleted,
                  C.action_user_id,
                  U.UserStatus,
                  U.id AS RecipientID ,
                  U.UserName AS RecipientUserName
                   ,U.FullName AS  RecipientFullName,
                   U.UserImage AS  RecipientImage
               FROM prefix_users U,prefix_conversation C, prefix_chat R
                WHERE
                CASE
                WHEN C.providerId = {$userId}
                THEN C.requestId = U.id
                WHEN C.requestId = {$userId}
                THEN C.providerId= U.id
                END
                AND
                C.id=R.ConversationsID
                AND
              (C.providerId ={$userId}
              OR C.requestId ={$userId})
              AND U.UserState = 'Active' AND C.id = {$conversationId}
        ";
        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['LastMessage'] = $this->getConversationLastMessage($fetch['ConversationID'], $userId, $fetch['RecipientID']);
            $fetch['MessageDate'] = $this->getConversationLastMessageDate($fetch['ConversationID']);
            $fetch['RecipientImage'] = $this->_GB->getSafeImage($fetch['RecipientImage']);
            $fetch['Status'] = $this->getConversationLastMessageStatus($fetch['RecipientID'], $fetch['ConversationID']);
            $fetch['UnreadMessageCounter'] = $this->_GB->_DB->CountRows('chat', " `status` = 0 AND `UserID` = '{$fetch['RecipientID']}' AND `ConversationsID` = {$fetch['ConversationID']}");

            return $fetch;
        } else {
            return null;
        }
    }

    /**
     * Function to get list messages
     * @param $userId
     * @param $recipientId
     * @param $conversation_id
     */
    public function getMessages($userId, $recipientId, $conversation_id)
    {
        $conversation_id = (int)$conversation_id;
        $status = 1;
        if ($conversation_id == 0) {
            $query = "SELECT id
                      FROM prefix_conversation
                      WHERE (providerId={$userId}
                       AND requestId={$recipientId})
                       OR(providerId={$recipientId}
                        AND requestId={$userId})";
            $query = $this->_GB->_DB->MySQL_Query($query);
            if ($this->_GB->_DB->numRows($query) != 0) {
                $fetch = $this->_GB->_DB->fetchAssoc($query);
                $conversation_id = $fetch['id'];
            }
            $this->_GB->_DB->update('chat', "`status` = {$status} ", "`UserID`={$recipientId} AND `ConversationsID`={$conversation_id} ");
            $query = "SELECT R.id,R.Date,R.reply,R.image,
                      R.ConversationsID AS conversationID,
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
                  AND (R.UserID ={$userId}
                  OR R.UserID = {$recipientId})
                  AND R.ConversationsID = {$conversation_id}
                   AND C.id = {$conversation_id} AND R.action_deleted_user_id != {$userId}
                  ORDER BY R.Date ASC";
            $query = $this->_GB->_DB->MySQL_Query($query);
            $messages = array();
            if ($this->_GB->_DB->numRows($query)) {
                while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                    $fetch['Date'] = $this->_GB->Date($fetch['Date']);
                    $fetch['image'] = $this->_GB->getSafeImage($fetch['image']);
                    $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
                    $messages[] = $fetch;
                }
                $this->_GB->JsonResponseMessage($messages);
            } else {
                $this->_GB->JsonResponseMessage(array('chat' => null));
            }
        } else {
            $this->_GB->_DB->update('chat', "`status` = {$status}", "`UserID`={$recipientId} AND  `ConversationsID`={$conversation_id} ");
            $query = "SELECT R.id,R.Date,R.reply,R.image,
                      R.ConversationsID AS conversationID,
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
                  AND (R.UserID ={$userId}
                  OR R.UserID = {$recipientId})
                  AND R.ConversationsID = {$conversation_id}
                   AND C.id = {$conversation_id} AND R.action_deleted_user_id != {$userId}
                  ORDER BY R.Date ASC";
            $query = $this->_GB->_DB->MySQL_Query($query);
            $messages = array();
            if ($this->_GB->_DB->numRows($query)) {
                while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {
                    $fetch['Date'] = $this->_GB->Date($fetch['Date']);
                    $fetch['image'] = $this->_GB->getSafeImage($fetch['image']);
                    $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
                    $messages[] = $fetch;
                }
                $this->_GB->JsonResponseMessage($messages);
            } else {
                $this->_GB->JsonResponseMessage(array('chat' => null));
            }
        }

    }

    /**
     * Function helper to get a specific Message to delete it
     * @param $MessageId
     * @param $userId
     * @param $recipientID
     * @param $conversation_id
     * @return null
     */
    public function getMessageHelper($MessageId, $userId, $recipientID, $conversation_id)
    {
        $query = "SELECT R.id,R.Date,R.reply,
                        R.ConversationsID AS conversationID,
                        R.action_deleted_user_id,
                        R.ConversationDeleted,
                        R.status AS Status,
                        U.id AS holderID,
                        R.action_deleted_user_id,
                        U.FullName AS holderFullName,
						U.UserName AS holderUsername,
						U.UserImage AS holderImage
				 FROM prefix_chat R

                  LEFT JOIN prefix_users AS U
                  ON U.id = R.UserID

                  LEFT JOIN prefix_conversation C
                  ON C.id = R.ConversationsID
                  WHERE U.id = R.UserID
                  AND R.id = {$MessageId}
                  AND (R.UserID ={$userId}
                  OR R.UserID = {$recipientID})
                  AND( (C.providerId = {$userId} OR C.providerId = {$recipientID}) OR (C.requestId = {$userId} OR C.requestId = {$recipientID}))
                  AND R.ConversationsID = {$conversation_id}
                   AND C.id = {$conversation_id}
        ";
        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['Date'] = $this->_GB->Date($fetch['Date']);
            $fetch['for'] = 'chat';
            $fetch['recipientID'] = $recipientID;
            $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
            return $fetch;
        } else {
            return null;
        }
    }

    /**
     * Function to delete a specific message
     * @param $userId
     * @param $array
     */
    public function DeleteMessage($userId, $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->_GB->_DB->escapeString(trim($value));
        }
        $Deleted = 1;
        $HolderID = (int)$array['HolderID'];
        $messageID = (int)$array['MessageID'];
        $conversationID = (int)$array['ConversationID'];
        if ($userId != $HolderID) {
            $MessageInfo = $this->getMessageHelper($messageID, $userId, $HolderID, $conversationID);
            if ($MessageInfo['ConversationDeleted'] != $Deleted && $MessageInfo['action_deleted_user_id'] == 0) {
                $fields = "`ConversationDeleted` = '" . $Deleted . "'";
                $fields .= ",`action_deleted_user_id` = '" . $userId . "'";
                $update = $this->_GB->_DB->update('chat', $fields, "`UserID` = '{$MessageInfo['holderID']}'  AND `id`= '{$MessageInfo['id']}' AND `ConversationsID`= '{$MessageInfo['conversationID']}'");
                if ($update) {
                    $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this message has been removed successfully1'));
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong1'));
                }
            } else if ($MessageInfo['ConversationDeleted'] == $Deleted && $MessageInfo['action_deleted_user_id'] == $HolderID) {
                $delete = $this->_GB->_DB->delete('chat', "`UserID` = '{$MessageInfo['holderID']}'  AND `id`='{$MessageInfo['id']}' AND  `ConversationsID`= '{$MessageInfo['conversationID']}'");
                if ($delete) {
                    $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this message has been removed successfully11'));
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
                }
            } else {
                $this->_GB->JsonResponseMessage(array(array('done' => false, 'message' => "you can't remove this message")));
            }
        } else {
            $Deleted = 1;
            $MessageInfo = $this->getMessageHelper($messageID, $userId, $HolderID, $conversationID);
            if ($MessageInfo['ConversationDeleted'] != $Deleted && $MessageInfo['action_deleted_user_id'] == 0) {
                $fields = "`ConversationDeleted` = '" . $Deleted . "'";
                $fields .= ",`action_deleted_user_id` = '" . $userId . "'";
                $update = $this->_GB->_DB->update('chat', $fields, "`UserID` = '{$MessageInfo['holderID']}'  AND `id`= '{$MessageInfo['id']}' AND `ConversationsID`= '{$MessageInfo['conversationID']}'");

                if ($update) {
                    $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this message has been removed successfully2'));
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong1'));
                }
            } else if ($MessageInfo['ConversationDeleted'] == $Deleted && $MessageInfo['action_deleted_user_id'] != $HolderID) {
                $delete = $this->_GB->_DB->delete('chat', "`UserID` = '{$MessageInfo['holderID']}'  AND `id`='{$MessageInfo['id']}' AND  `ConversationsID`= '{$MessageInfo['conversationID']}'");
                if ($delete) {
                    $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'this message has been removed successfully21'));
                } else {
                    $this->_GB->JsonResponseMessage(array('done' => false, 'message' => 'try again, something went wrong'));
                }
            } else {
                $this->_GB->JsonResponseMessage(array(array('done' => false, 'message' => "you can't remove this message")));
            }
        }

    }

    /**
     * Function to send a new message
     * @param $userId
     * @param $array
     */
    public
    function addMessage($userId, $imageID, $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->_GB->_DB->escapeString(trim($value));
        }
        if ($userId != $array['RecipientID']) {
            if ($array['ConversationID'] == 0) {
                $query = "SELECT id
                      FROM prefix_conversation
                      WHERE (providerId={$userId} AND requestId={$array['RecipientID']} ) OR (providerId={$array['RecipientID']}  AND requestId={$userId})";
                $query = $this->_GB->_DB->MySQL_Query($query);
                if ($this->_GB->_DB->numRows($query) != 0) {
                    $fetch = $this->_GB->_DB->fetchAssoc($query);
                    $array['ConversationID'] = $fetch['id'];
                } else {
                    $data = array(
                        'providerId' => $userId,
                        'requestId' => $array['RecipientID'],
                        'Deleted' => 0,
                        'action_user_id' => 0,
                        'Date' => time());

                    $insert = $this->_GB->_DB->insert('conversation', $data);
                    if ($insert) {
                        $array['ConversationID'] = $this->_GB->_DB->last_Id();
                    }
                }
                $arrayData = array(
                    'UserID' => $userId,
                    'reply' => $array['reply'],
                    'Date' => time(),
                    'status' => 0,
                    'action_deleted_user_id' => 0,
                    'ConversationDeleted' => 0,
                    'ConversationsID' => $array['ConversationID']
                );
                if ($imageID != null) {
                    $arrayData['image'] = $imageID;
                }
                $insert = $this->_GB->_DB->insert('chat', $arrayData);
                if ($insert) {
                    $arrayMessageData = $this->getMessage($this->_GB->_DB->last_Id(), $userId, $array['RecipientID'], $array['ConversationID']);
                    $getUser = $this->_GB->_DB->select('users', '`reg_id`', '`id`=' . $array['RecipientID']);
                    $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                    if ($fetchUser['reg_id'] != null) {
                        $regIDs = array($fetchUser['reg_id']);
                        $this->_GB->sendMessageThroughGCM($regIDs, $arrayMessageData);

                    }
                    $this->_GB->JsonResponseMessage($arrayMessageData);
                } else {
                    $this->_GB->JsonResponseMessage(array('MessageData1' => null));
                }
            } else {
                $arrayData = array(
                    'UserID' => $userId,
                    'reply' => $array['reply'],
                    'Date' => time(),
                    'status' => 0,
                    'action_deleted_user_id' => 0,
                    'ConversationDeleted' => 0,
                    'ConversationsID' => $array['ConversationID']
                );
                if ($imageID != null) {
                    $arrayData['image'] = $imageID;
                }
                $insert = $this->_GB->_DB->insert('chat', $arrayData);
                if ($insert) {
                    $arrayMessageData = $this->getMessage($this->_GB->_DB->last_Id(), $userId, $array['RecipientID'], $array['ConversationID']);
                    $getUser = $this->_GB->_DB->select('users', '`reg_id`', '`id`=' . $array['RecipientID']);
                    $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                    if ($fetchUser['reg_id'] != null) {
                        $regIDs = array($fetchUser['reg_id']);
                        $this->_GB->sendMessageThroughGCM($regIDs, $arrayMessageData);

                    }
                    $this->_GB->JsonResponseMessage($arrayMessageData);
                } else {
                    // failed to insert row
                    $array = array(
                        'success' => false,
                        'message' => 'Oops! An error occurred.'
                    );
                    $this->_GB->JsonResponseMessage($array);
                }
            }

        }
    }

    /**
     * Function to get a specific message
     * @param $MessageId
     * @param $userId
     * @param $recipientID
     * @param $conversation_id
     * @return null
     */
    public function getMessage($MessageId, $userId, $recipientID, $conversation_id)
    {
        $query = "SELECT R.id,R.Date,R.reply,R.image,
                        R.ConversationsID AS conversationID,
                        R.action_deleted_user_id,
                        R.ConversationDeleted,
                        R.status AS Status,
                        U.id AS holderID,
                        R.action_deleted_user_id,
                        U.FullName AS holderFullName,
						U.UserName AS holderUsername,
						U.UserImage AS holderImage
				 FROM prefix_chat R

                  LEFT JOIN prefix_users AS U
                  ON U.id = R.UserID

                  LEFT JOIN prefix_conversation C
                  ON C.id = R.ConversationsID
                  WHERE U.id = R.UserID
                  AND (R.UserID ={$userId}
                  OR R.UserID = {$recipientID})
                  AND R.ConversationsID = {$conversation_id}
                   AND C.id = {$conversation_id}
                  AND R.id = {$MessageId} AND R.action_deleted_user_id != {$userId} ";
        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['Date'] = $this->_GB->Date($fetch['Date']);
            $fetch['for'] = 'chat';
            $fetch['recipientID'] = $recipientID;
            $fetch['conversationID'] = $conversation_id;
            $fetch['holderImage'] = $this->_GB->getSafeImage($fetch['holderImage']);
            $fetch['success'] = true;
            $fetch['message'] = 'your message successfully sent.';
            return $fetch;
        } else {
            return null;
        }
    }

    /**
     * Function to check if the  conversation is exist
     * @param $provider
     * @param $request
     * @return bool
     */
    public
    function ConversationsExist($provider, $request)
    {
        $querySQL = "SELECT id
                      FROM prefix_conversation
                      WHERE (providerId={$provider}
                      AND requestId={$request})
                      OR (providerId={$request}
                      AND requestId={$provider})";

        $result = $this->_GB->_DB->MySQL_Query($querySQL);
        $rows = $this->_GB->_DB->numRows($result);
        if ($rows != 0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Function to change user status to offline
     * @param $userID
     */
    public
    function updateUserStatusOffline($userID)
    {
        $offline = "offline";
        $update = $this->_GB->_DB->update('users', "`UserStatus` = '{$offline}'", "`id`= '{$userID}'");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your Offline'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your Status'));
        }

    }

    /**
     * Function to change user status to online
     * @param $userID
     */
    public
    function updateUserStatusOnline($userID)
    {
        $online = "online";
        $update = $this->_GB->_DB->update('users', "`UserStatus` = '{$online}'", "`id`= '{$userID}'");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your Online'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your Status'));
        }
    }

    /**
     * Function give the ability for user to deactivate his account
     * @param $userID
     */
    public
    function DeactivateAccount($userID)
    {
        $deactivate = "Deactivate";
        $offline = "offline";
        $this->_GB->_DB->update('users', "`UserStatus` = '{$offline}'", "`id`= '{$userID}'");
        $this->_GB->_DB->update('users', "`UserState` = '{$deactivate}'", "`id`= '{$userID}'");
    }

    /**
     * Function to activate account
     * @param $userID
     */
    public
    function ActivateAccount($userID)
    {
        $activate = "Active";
        $update = $this->_GB->_DB->update('users', "`UserState` = '{$activate}'", "`id`= '{$userID}'");
        if ($update) {
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your State is updated successfully'));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your State'));
        }
    }

    /**
     * Function to change the status of message
     * @param $userId
     * @param $array
     */
    public function StatusMessage($userId, $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->_GB->_DB->escapeString(trim($value));
        }
        if ($userId != $array['RecipientID']) {
            $status = 1;
            $Conversation_id = $array['ConversationID'];
            $recipientID = $array['RecipientID'];
            // $fields = "`status` = '" . $status . "'";
            //$fields .= ",`LastVuTime` = '" . time() . "'";
            $update = $this->_GB->_DB->update('chat', "`status` = {$status} ", "`UserID`={$recipientID} AND `ConversationsID`={$Conversation_id} ");
            if ($update) {
                //  $this->_GB->JsonResponseMessage(array('done' => true, 'message' => 'Your Message is read successfully'));
                $getUser = $this->_GB->_DB->select('users', '`reg_id`', '`id`=' . $recipientID);
                $fetchUser = $this->_GB->_DB->fetchAssoc($getUser);
                $getStatus = $this->_GB->_DB->select('chat', '*', "`UserID` = {$userId} AND `ConversationsID`= {$Conversation_id}");
                $fetchStatus = $this->_GB->_DB->fetchAssoc($getStatus);
                if ($fetchUser['reg_id'] != null) {
                    $regIDs = array($fetchUser['reg_id']);
                    $msg = array(
                        'for' => 'statusMessage',
                        'userID' => $recipientID,
                        'Status' => $fetchStatus['status']
                    );
                    $this->_GB->sendMessageThroughGCM($regIDs, $msg);
                }
            } else {
                // $this->_GB->JsonResponseMessage(array('done' => false, 'message' => ' Failed to update your Status message'));
            }
        }
    }


    /**
     * Function to rest the password
     * @param $userID
     * @param $email
     */
    public function activateMail($userID, $email)
    {
        $hash = md5(time() . $email . $userID . microtime(true));
        $array = array('hash' => $hash,
            'userid' => $userID,
            'date' => time()
        );
        $subject = $this->_GB->GetConfig('site_name', 'site') . " Activate Your Account";
        $message = "Please follow this link to activate your account password<br>";
        $message .= '<a href="' . $this->_GB->GetConfig('url', 'site') . 'home.php?activate=' . $hash . '">Activate</a>';
        $mail = mail($email, $subject, $message);
        if ($mail) {
            $this->_GB->_DB->insert('activation', $array);
            return true;
        } else {
            return false;
        }

    }

    public function passwordRestMail($userID, $email)
    {
        $hash = md5(time() . $email . $userID . microtime(true));
        $array = array('hash' => $hash,
            'userid' => $userID,
            'date' => time()
        );
        $subject = $this->_GB->GetConfig('site_name', 'site') . " Rest Your Account Password";
        $message = "Please follow this link to rest your account password<br>";
        $message .= '<a href="' . $this->_GB->GetConfig('url', 'site') . 'home.php?password=' . $hash . '">Rest</a>';
        $mail = mail($email, $subject, $message);
        if ($mail) {
            $this->_GB->_DB->insert('rest_password', $array);
            $this->_GB->JsonResponseMessage(array('done' => true, 'message' => "check out your email"));
        } else {
            $this->_GB->JsonResponseMessage(array('done' => false, 'message' => "please try again later"));
        }

    }
}