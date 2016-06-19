<?php

/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 9/26/2015
 * Time: 11:22 PM
 */
class General
{
    public $_DB, $_Sec;

    function __construct($_DB, $_Sec)
    {
        $this->_DB = $_DB;
        $this->_Sec = $_Sec;
    }

    /**
     * Function to upload new images
     * @param $array
     * @param string $dir
     * @return null|string
     */
    public function uploadImage($array, $dir = './')
    {
        if (!empty($array)) {
            $tmp = $array["tmp_name"];
            $name = $array["name"];
            $new_name = md5(time() . '-' . $name) . '.jpg';
            $day_folder = date('d-m-y', time());
            if (is_dir($dir . 'uploads/' . $day_folder)) {
                $path = $day_folder;
            } else {
                if (mkdir($dir . 'uploads/' . $day_folder)) {
                    $path = $day_folder;
                } else {
                    $path = '';
                }
            }
            if (move_uploaded_file($tmp, $dir . 'uploads/' . $path . '/' . $new_name)) {
                $imgHash = md5($tmp . $new_name . uniqid() . time());
                $imageData = array(
                    'Image_original_name' => $this->_DB->escapeString($name),
                    'Image_new_name' => $new_name,
                    'Image_path' => $path,
                    'Image_hash' => $imgHash,
                    'Image_type' => 0
                );
                $query = $this->_DB->insert('images', $imageData);
                if ($query) {
                    return $imgHash;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }

    /**
     * Function to upload new videos Thumbnail
     * @param $array
     * @param string $dir
     * @return null|string
     */
    public function uploadVideoThumbnail($array, $dir = './')
    {
        if (!empty($array)) {
            $tmp = $array["tmp_name"];
            $name = $array["name"];
            $new_name = md5(time() . '-' . $name) . '.jpg';
            $day_folder = date('d-m-y', time());
            if (is_dir($dir . 'uploads/' . $day_folder)) {
                $path = $day_folder;
            } else {
                if (mkdir($dir . 'uploads/' . $day_folder)) {
                    $path = $day_folder;
                } else {
                    $path = '';
                }
            }
            if (move_uploaded_file($tmp, $dir . 'uploads/' . $path . '/' . $new_name)) {
                $imgHash = md5($tmp . $new_name . uniqid() . time());
                $imageData = array(
                    'Image_original_name' => $this->_DB->escapeString($name),
                    'Image_new_name' => $new_name,
                    'Image_path' => $path,
                    'Image_hash' => $imgHash,
                    'Image_type' => 1
                );
                $query = $this->_DB->insert('images', $imageData);
                if ($query) {
                    return $imgHash;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }

    /**
     * Function to upload new videos
     * @param $array
     * @param string $dir
     * @return null|string
     */
    public function uploadVideos($array, $dir = './')
    {

        if (!empty($array)) {
            $tmp = $array["tmp_name"];
            $name = $array["name"];
            $new_name = md5(time() . '-' . $name) . '.mp4';
            $day_folder = date('d-m-y', time());
            if (is_dir($dir . 'uploads/' . $day_folder)) {
                $path = $day_folder;
            } else {
                if (mkdir($dir . 'uploads/' . $day_folder)) {
                    $path = $day_folder;
                } else {
                    $path = '';
                }
            }
            if (move_uploaded_file($tmp, $dir . 'uploads/' . $path . '/' . $new_name)) {
                $vidHash = md5($tmp . $new_name . uniqid() . time());
                $videoData = array(
                    'Video_original_name' => $this->_DB->escapeString($name),
                    'Video_new_name' => $new_name,
                    'Video_path' => $path,
                    'Video_hash' => $vidHash
                );
                $query = $this->_DB->insert('videos', $videoData);
                if ($query) {
                    return $vidHash;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }

    /**
     * function to get a safe image
     * @param $Hash
     * @return null|string
     */
    public function getSafeImage($Hash)
    {
        $Hash = $this->_DB->escapeString($Hash);
        $query = $this->_DB->select('images', '*', "`Image_hash` = '$Hash'");
        if ($this->_DB->numRows($query) != 0) {
            $fetch = $this->_DB->fetchAssoc($query);
            $path = 'uploads/' . $fetch['Image_path'] . '/' . $fetch['Image_new_name'];
            return $path;
        } else {
            return null;
        }

    }

    /**
     * Function to get a safe video
     * @param $Hash
     * @return null|string
     */
    public function getSafeVideo($Hash)
    {
        $Hash = $this->_DB->escapeString($Hash);
        $query = $this->_DB->select('videos', '*', "`Video_hash` = '$Hash'");
        if ($this->_DB->numRows($query) != 0) {
            $fetch = $this->_DB->fetchAssoc($query);
            $path = 'uploads/' . $fetch['Video_path'] . '/' . $fetch['Video_new_name'];
            return $path;
        } else {
            return null;
        }
    }

    /**
     * Function to refresh pages
     * @param $url
     * @param string $time
     * @return string
     */
    public function MetaRefresh($url, $time = '0')
    {
        return "<meta http-equiv=\"refresh\" content=\"$time;URL='$url'\" /> ";
    }

    /**
     * Function to get the date by time ago
     * @param $ptime
     * @return string
     */
    public function TimeAgo($ptime)
    {
        $etime = time() - $ptime;
        if ($etime < 1) {
            return '0 sec';
        }
        $a = array(12 * 30 * 24 * 60 * 60 => 'y',
            30 * 24 * 60 * 60 => 'm',
            24 * 60 * 60 => 'd',
            60 * 60 => 'h',
            60 => 'min',
            1 => 'sec'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str;
            }
        }
    }

    /**
     * Function to get the date by days
     * @param $date
     * @return bool|string
     */
    public function  Date($date)
    {

        $time_dd = date("d", $date);
        $time_MM = date("M", $date);
        $now = time();
        $c_dd = date("d", $now);
        $c_MM = date("M", $now);
        if ($time_MM == $c_MM) {
            if ($time_dd == $c_dd) {
                //days
                $newFormat = date(' H:i ', $date);
                return $newFormat;
            } else if ($time_dd == $c_dd - 1) {
                //yesterday
                $yesterday = 'Yesterday ';
                $newFormat = date('H:i', $date);
                return $yesterday . '' . $newFormat;
            } else if ($time_dd > $c_dd - 6 && $time_dd < $c_dd - 1) {
                //week
                $newFormat = date('l H:i', $date);
                return $newFormat;
            } else {
                //month
                $newFormat = date('D M H:i ', $date);
                return $newFormat;
            }
        }
        //month
        $newFormat = date('D M Y ', $date);
        return $newFormat;
    }

    /*******************************
     * Other Methods
     *********************************/

    /**
     * Check the json response message
     * @param $array
     */
    public
    function JsonResponseMessage($array)
    {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        if (is_array($array)) {
            echo json_encode($array);
        } else {
            echo $array;
        }
    }

    /**
     * Show Error Message
     * @param $error
     * @param string $error_type
     * @return string
     */
    function ShowError($error, $error_type = 'no')
    {
        switch ($error_type) {
            case 'no':
                $msg = '<div class="card-panel semi-transparent center"><div class="red-text text-darken-2 ">';
                $msg .= $error;
                $msg .= '</div></div>';
                return $msg;
                break;
            case 'yes':
                $msg = '<div class="card-panel semi-transparent center"><div class="teal-text  darken-1">';
                $msg .= $error;
                $msg .= '</div></div>';
                return $msg;
                break;
        }
    }

    /*******************************
     * Method for Sessions
     *********************************/
    /**
     * Function to set sessions
     * @param $key
     * @param $value
     */
    function SetSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Function to get sessions
     * @param $key
     * @return bool
     */
    function GetSession($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    /**
     * Function to unset session
     * @param $key
     * @return bool
     */
    function UnsetSession($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        } else {
            return false;
        }
    }

    /**
     * Function to check if the post is liked by a specific user
     * @param $userID
     * @param $FeedID
     * @return bool
     */
    public
    function isLikeIt($userID, $FeedID)
    {
        $count = $this->_DB->CountRows('likes', "`providerId` = {$userID} AND `requestId` = {$FeedID}");
        if ($count != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to send notification through GCM Service
     * @param $Register_ids
     * @param $data
     * @return mixed
     */
    public function sendMessageThroughGCM($Register_ids, $data)
    {

        $fields = array(
            'registration_ids' => $Register_ids,
            'data' => $data,
        );
        // Update your Google Cloud Messaging API Key
        //define("GOOGLE_API_KEY", "AIzaSyB_qPU4W3980TfV8J47ly5c02DHdVCbYV8");
        $headers = array(
            'Authorization: key=' . $this->GetConfig('googleApiConfig', 'site'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');  //Google cloud messaging GCM-API url
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    /**
     * Function to get username by his id
     * @param $id
     * @return mixed
     */
    public function getUserNameByID($id)
    {
        $query = $this->_DB->select('users', '`UserName`', "`id` = {$id}");
        $fetch = $this->_DB->fetchAssoc($query);
        return $fetch['UserName'];
    }

    /**
     * Function to get user id by his name
     * @param $name
     * @return mixed
     */
    public function getIDByName($name)
    {
        $name = $this->_DB->escapeString($name);
        $query = $this->_DB->select('users', '`id`', "`FullName`  LIKE '%$name%' OR `UserName` LIKE '%$name%'");
        $fetch = $this->_DB->fetchAssoc($query);
        return $fetch['id'];
    }

    /**
     * Function to get User id by post id
     * @param $id
     * @return mixed
     */
    public function getUserIDByFeedID($id)
    {
        $query = $this->_DB->select('feeds', '`holderID`', "`id` = {$id}");
        $fetch = $this->_DB->fetchAssoc($query);
        return $fetch['holderID'];
    }

    /**
     * Function to get Map image
     * @param $placeName
     */
    public function getMapImage($placeName)
    {
        $placeName = $this->_DB->escapeString($placeName);
        $query = $this->_DB->select('places', '*', "`place_name` = '{$placeName}'");
        if ($this->_DB->numRows($query)) {
            $fetch = $this->_DB->fetchAssoc($query);
            $imageURL = "https://maps.googleapis.com/maps/api/staticmap?center=" . $fetch['latitude'] . "," . $fetch['longitude'] . "&zoom=9&sensor=false&markers=" . $fetch['latitude'] . "," . $fetch['longitude'] . "&";
            $this->JsonResponseMessage(array('done' => true, 'message' => $imageURL));
        } else {
            $this->JsonResponseMessage(array('done' => true, 'message' => "https://maps.googleapis.com/maps/api/staticmap?center={$placeName}&zoom=9&sensor=false&markers={$placeName}&"));
        }
    }

    /**
     * Function to a  Link
     * @param $hash
     * @return null
     */
    public function getLink($hash)
    {
        $hash = $this->_DB->escapeString($hash);
        $query = $this->_DB->select('links', '*', "`hash` = '{$hash}'");
        if ($this->_DB->numRows($query) != 0) {
            return $this->_DB->fetchAssoc($query);
        } else {
            return null;
        }
    }

    /**
     * Function to get Address
     * @param $lat
     * @param $lng
     */
    public function getAddress($lat, $lng)
    {

        $longitude = addslashes($lng);
        $latitude = addslashes($lat);

        $query = $this->_DB->select('places', '`place_name`', "`longitude` = '{$longitude}' AND `latitude` = '{$latitude}'");
        if ($this->_DB->numRows($query)) {
            $fetch = $this->_DB->fetchAssoc($query);
            $response = array('status' => true, 'address' => $fetch['place_name']);
        } else {
            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latitude) . ',' . trim($longitude) . '&sensor=false';
            $json = @file_get_contents($url);
            $data = json_decode($json);
            $status = $data->status;
            if ($status == "OK") {
                $place_name = $data->results[0]->formatted_address;
            } else {
                $place_name = null;
            }
            if ($place_name != null) {
                $insert = $this->_DB->insert('places', array('longitude' => $longitude, 'latitude' => $latitude, 'place_name' => $place_name));
                if ($insert) {
                    $response = array('status' => true, 'address' => $place_name);
                } else {
                    $response = array('status' => true, 'address' => $place_name);
                }
            } else {
                $response = array('status' => false, 'address' => '');
            }
        }
        $this->JsonResponseMessage($response);

    }

    /**
     * Function to get Config (disclaimer,site name...etc )
     * @param $name
     * @param $for
     * @return mixed
     */
    public function GetConfig($name, $for)
    {
        $query = $this->_DB->select('config', '`value`', "`name` = '{$name}' AND `for` = '{$for}'");
        $fetch = $this->_DB->fetchAssoc($query);
        return $fetch['value'];
    }

    /**
     * Function to update Config information
     * @param $name
     * @param $value
     * @param $for
     */
    public function UpdateConfig($name, $value, $for)
    {
        $value = $this->_DB->escapeString($value);
        $this->_DB->update('config', "`value` = '{$value}'", "`name` = '{$name}' AND `for` = '{$for}'");
    }
    /**
     * Function to get link hash
     * @param $link
     * @return null
     */
    public function getLinkHash($link)
    {
        $hash = md5(time() . $link);
        $videoID = $this->getVideoIDFromURL($link);
        if ($videoID != false) {
            $video = $this->getVideoInformation($link);
            $linkInfo = array(
                'hash' => $hash,
                'link' => $this->_DB->escapeString($videoID),
                'image' => $this->_DB->escapeString($video['thumbnail_url']),
                'title' => $this->_DB->escapeString($video['title']),
                'desc' => $this->_DB->escapeString($video['author_name']),
                'type' => 'youtube'
            );
        } else {
            $linkInfo = array(
                'hash' => $hash,
                'link' => $this->_DB->escapeString($link),
                'type' => 'other'
            );
            $content = $this->file_get_contents_curl($link);
            $metaTags = $this->getMetaTags($content);

            if (isset($metaTags['image']) && !empty($metaTags['image'])) {
                $linkInfo['image'] = $this->_DB->escapeString($metaTags['image']);
            } else {
                $imageSrc = $this->extractImage($content);
                if ($imageSrc != null) {
                    $linkInfo['image'] = $imageSrc;
                }
            }
            if (isset($metaTags['title'])) {
                $linkInfo['title'] = $this->_DB->escapeString($metaTags['title']);
            }
            if (isset($metaTags['description'])) {
                $linkInfo['desc'] = $this->_DB->escapeString($metaTags['description']);
            }

        }
        $query = $this->_DB->select('links', '`hash`', "`link` = '" . $linkInfo['link'] . "'");
        if ($this->_DB->numRows($query) != 0) {
            $fetch = $this->_DB->fetchAssoc($query);
            return $fetch['hash'];
        } else {
            $insert = $this->_DB->insert('links', $linkInfo);
            if ($insert) {
                return $linkInfo['hash'];
            } else {
                return null;
            }
        }

    }

    /**
     * Function to check if is URL
     * @param $url
     * @return bool
     */
    public function isURL($url)
    {
        if (!preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Function to get Video ID from the URL
     * @param $url
     * @return bool
     */
    public function getVideoIDFromURL($url)
    {
        $pattern =
            '%^# Match any youtube URL
	        (?:https?://)?  # Optional scheme. Either http or https
	        (?:www\.)?      # Optional www subdomain
	        (?:             # Group host alternatives
	          youtu\.be/    # Either youtu.be,
	        | youtube\.com  # or youtube.com
	          (?:           # Group path alternatives
	            /embed/     # Either /embed/
	          | /v/         # or /v/
	          | /watch\?v=  # or /watch\?v=
	          )             # End path alternatives.
	        )               # End host alternatives.
	        ([\w-]{10,12})($|&)  # Allow 10-12 for 11 char youtube id.
	        $%x';
        $result = preg_match($pattern, $url, $matches);
        if (false != $result) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Function to video information
     * @param $link
     * @return mixed
     */
    public function getVideoInformation($link)
    {
        $apiurl = sprintf('http://www.youtube.com/oembed?url=%s&format=json', urlencode($link));
        $content = file_get_contents($apiurl);
        return json_decode($content, true);
    }

    public function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        //$data = curl_exec($ch);
        $data = curl_exec($ch);
        if ($data === FALSE) {
            return null;
        }
        curl_close($ch);

        return $data;
    }

    public function getMetaTags($contents)
    {
        $result = false;
        if (isset($contents)) {
            $list = array(
                "UTF-8",
                "EUC-CN",
                "EUC-JP",
                "EUC-KR",
                'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
                'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
                'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
                'Windows-1251', 'Windows-1252', 'Windows-1254',
            );
            $encoding_check = mb_detect_encoding($contents, $list, true);
            $encoding = ($encoding_check === false) ? "UTF-8" : $encoding_check;
            $metaTags = $this->getMetaTagsEncoding($contents, $encoding);
            $result = $metaTags;
        }
        return $result;
    }

    private function getMetaTagsEncoding($contents, $encoding)
    {
        $result = false;
        $metaTags = array("url" => "", "title" => "", "description" => "", "image" => "");
        if (isset($contents)) {
            $doc = new DOMDocument('1.0', 'utf-8');
            @$doc->loadHTML($contents);
            $metas = $doc->getElementsByTagName('meta');
            for ($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);
                if ($meta->getAttribute('name') == 'description')
                    $metaTags["description"] = $meta->getAttribute('content');
                if ($meta->getAttribute('name') == 'keywords')
                    $metaTags["keywords"] = $meta->getAttribute('content');
                if ($meta->getAttribute('property') == 'og:title')
                    $metaTags["title"] = $meta->getAttribute('content');
                if ($meta->getAttribute('property') == 'og:image')
                    $metaTags["image"] = $meta->getAttribute('content');
                if ($meta->getAttribute('property') == 'og:description')
                    $metaTags["og_description"] = $meta->getAttribute('content');
                if ($meta->getAttribute('property') == 'og:url')
                    $metaTags["url"] = $meta->getAttribute('content');
            }
            if (!empty($metaTags["og_description"])) {
                $metaTags["description"] = $metaTags["og_description"];
                unset($metaTags["og_description"]);
            }
            if (empty($metaTags["title"])) {
                $nodes = $doc->getElementsByTagName('title');
                $metaTags["title"] = $nodes->item(0)->nodeValue;
            }
            $result = $metaTags;
        }
        return $result;
    }


    public function extractImage($text)
    {
        $imageRegex = "/<img(.*?)src=(\"|\')(.+?)(gif|jpg|png|bmp)(.*?)(\"|\')(.*?)(\/)?>(<\/img>)?/";

        $srcRegex = '/src=(\"|\')(.+?)(\"|\')/i';
        $httpRegex = "/https?\:\/\//i";
        $imgSrc = null;
        if (preg_match_all($imageRegex, $text, $matching)) {
            for ($i = 0; $i < count($matching[0]); $i++) {
                preg_match($srcRegex, $matching[0][$i], $imgSrc);
                $imgSrc = str_replace("../", "", $imgSrc[2]);
                $imgSrc = str_replace("./", "", $imgSrc);
                $imgSrc = str_replace(" ", "%20", $imgSrc);
                if (!preg_match($httpRegex, $imgSrc)) {
                    if (strpos($imgSrc, "//") === 0) {
                        if (preg_match($httpRegex, "http:" . $imgSrc)) {
                            $imgSrc = "http:" . $imgSrc;
                        }
                    }
                }
            }

        }
        return $imgSrc;

    }


    public  function datadump($table, $drop = true, $stripapos = true)
    {
        $result     = "# Dump of $table \n";
        $result    .= "# Dump DATE : " . date("d-M-Y") ."\n\n";
        if ( $drop ) {
            if ( $stripapos ) {
                $result    .= "DROP TABLE IF EXISTS $table;\n";
                // dump create table
                $createTableQuery = mysql_query("SHOW CREATE TABLE ".$table.";");
                $createTable      = mysql_fetch_row($createTableQuery);
                $result          .= str_replace('`', '', $createTable[1]).";\n\n\n\n";
            } else {
                $result    .= "DROP TABLE IF EXISTS `$table`;\n";
                // dump create table
                $createTableQuery = mysql_query("SHOW CREATE TABLE ".$table.";");
                $createTable      = mysql_fetch_row($createTableQuery);
                $result          .= $createTable[1].";\n\n\n\n";
            }
        } else {
            $result    .= "TRUNCATE TABLE $table;\n";
        }

        $query      = mysql_query("SELECT * FROM $table");
        $num_fields = @mysql_num_fields($query);
        $numrow     = mysql_num_rows($query);

        $columnsRes = mysql_query("SHOW COLUMNS FROM $table;");
        $columns = array();

        while ( $row = mysql_fetch_assoc($columnsRes) ) {
            $columns[$row['Field']] = $row;
        }

        while ( $row = mysql_fetch_assoc($query) ) {
            $result .= "INSERT INTO ".$table." VALUES(";

            $fields = array();

            foreach ( $row as $field => $data ) {
                if ( strpos(strtolower($columns[$field]['Type']), 'int') !== false
                    || strpos(strtolower($columns[$field]['Type']), 'float') !== false
                    || strpos(strtolower($columns[$field]['Type']), 'tinyint') !== false ) {
                    if ( strlen($data) > 0 ) {
                        $fields[] = $data;
                    } else {
                        if ( strtolower($columns[$field]['Null']) == 'no' ) {
                            $fields[] = 0;
                        } else {
                            $fields[] = "NULL";
                        }
                    }
                } elseif ( strpos(strtolower($columns[$field]['Type']), 'datetime') !== false ) {
                    if ( strlen($data) > 0 ) {
                        $fields[] = "\"".$data."\"" ;
                    } else {
                        if ( strtolower($columns[$field]['Null']) == 'no' ) {
                            $fields[] = '""';
                        } else {
                            $fields[] = "NULL";
                        }
                    }
                } elseif ( strpos(strtolower($columns[$field]['Type']), 'time') !== false ) {
                    if ( strlen($data) > 0 ) {
                        $fields[] = "\"".$data."\"" ;
                    } else {
                        if ( strtolower($columns[$field]['Null']) == 'no' ) {
                            $fields[] = '""';
                        } else {
                            $fields[] = "NULL";
                        }
                    }
                } elseif ( strpos(strtolower($columns[$field]['Type']), 'varchar') !== false
                    || strpos(strtolower($columns[$field]['Type']), 'text') !== false
                    || strpos(strtolower($columns[$field]['Type']), 'longtext') !== false
                    || strpos(strtolower($columns[$field]['Type']), 'mediumtext') !== false ) {
                    $data = addslashes($data);
                    $data = trim(ereg_replace("\n", "\\n", $data));
                    if ( strlen($data) > 0 ) {
                        $fields[] = "\"".$data."\"" ;
                    } else {
                        if ( strtolower($columns[$field]['Null']) == 'no' ) {
                            $fields[] = '""';
                        } else {
                            $fields[] = "NULL";
                        }
                    }
                } else {
                    // $columns[$field]['Type'] will contain the datatype
                    if ( strlen($data) > 0 ) {
                        $fields[] = "\"".$data."\"" ;
                    } else {
                        if ( strtolower($columns[$field]['Null']) == 'no' ) {
                            $fields[] = '""';
                        } else {
                            $fields[] = "NULL";
                        }
                    }
                }
            }
            $result .= implode(',', $fields);
            $result .= ");\n";
        }
        return $result . "\n\n\n";
    }
}