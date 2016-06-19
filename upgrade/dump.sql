-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2015 at 03:18 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `socialmedia`
--

-- --------------------------------------------------------

--
-- Table structure for table `sn_activation`
--

CREATE TABLE IF NOT EXISTS `sn_activation` (
`id` int(11) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_admins`
--

CREATE TABLE IF NOT EXISTS `sn_admins` (
`id` int(11) NOT NULL,
  `AdminName` varchar(225) NOT NULL,
  `AdminPassword` varchar(225) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sn_admins`
--
-- --------------------------------------------------------

--
-- Table structure for table `sn_chat`
--

CREATE TABLE IF NOT EXISTS `sn_chat` (
`id` int(11) NOT NULL,
  `reply` text,
  `image` varchar(250) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `ConversationDeleted` tinyint(3) NOT NULL,
  `action_deleted_user_id` int(11) NOT NULL,
  `Date` int(11) NOT NULL,
  `LastVuTime` int(11) NOT NULL,
  `ConversationsID` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sn_comments`
--

CREATE TABLE IF NOT EXISTS `sn_comments` (
`id` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `requestId` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `Edited` int(11) NOT NULL,
  `Date` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_config`
--

CREATE TABLE IF NOT EXISTS `sn_config` (
`id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `value` longtext NOT NULL,
  `for` varchar(225) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-------------------------------------------------------

--
-- Table structure for table `sn_conversation`
--

CREATE TABLE IF NOT EXISTS `sn_conversation` (
`id` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `requestId` int(11) NOT NULL,
  `Deleted` tinyint(3) NOT NULL,
  `action_user_id` int(11) NOT NULL,
  `Date` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sn_feedback`
--

CREATE TABLE IF NOT EXISTS `sn_feedback` (
`id` int(11) NOT NULL,
  `fromUser` varchar(100) NOT NULL,
  `Rating` varchar(250) NOT NULL,
  `Message` varchar(250) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sn_feeds`
--

CREATE TABLE IF NOT EXISTS `sn_feeds` (
`id` int(11) NOT NULL,
  `FeedFile` varchar(225) NOT NULL,
  `FeedType` tinyint(3) NOT NULL,
  `FeedVideoThumbnail` varchar(100) NOT NULL,
  `FeedStatus` varchar(225) DEFAULT NULL,
  `holderID` int(11) NOT NULL,
  `Place` varchar(200) DEFAULT NULL,
  `Link` varchar(200) DEFAULT NULL,
  `Youtube` varchar(110) DEFAULT NULL,
  `Privacy` int(11) NOT NULL,
  `Date` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sn_friends`
--

CREATE TABLE IF NOT EXISTS `sn_friends` (
`id` int(10) unsigned NOT NULL,
  `providerId` int(10) unsigned NOT NULL DEFAULT '0',
  `requestId` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `action_user_id` int(11) unsigned NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='providerId is the Id of the users who wish to be friend with';

-- --------------------------------------------------------

--
-- Table structure for table `sn_images`
--

CREATE TABLE IF NOT EXISTS `sn_images` (
`id` int(11) NOT NULL,
  `Image_original_name` varchar(225) NOT NULL,
  `Image_new_name` varchar(225) NOT NULL,
  `Image_type` int(11) NOT NULL,
  `Image_path` varchar(225) NOT NULL,
  `Image_hash` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sn_likes`
--

CREATE TABLE IF NOT EXISTS `sn_likes` (
`id` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `requestId` int(11) NOT NULL,
  `Date` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_links`
--

CREATE TABLE IF NOT EXISTS `sn_links` (
`id` int(11) NOT NULL,
  `link` varchar(200) NOT NULL,
  `image` text,
  `desc` text,
  `title` varchar(200) DEFAULT NULL,
  `hash` varchar(100) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'other'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_places`
--

CREATE TABLE IF NOT EXISTS `sn_places` (
`id` int(11) NOT NULL,
  `longitude` varchar(100) NOT NULL,
  `latitude` varchar(100) NOT NULL,
  `place_name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_reports`
--

CREATE TABLE IF NOT EXISTS `sn_reports` (
`id` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `Description` text,
  `Reason` text NOT NULL,
  `requestId` int(11) NOT NULL,
  `Type` int(11) NOT NULL,
  `Date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_rest_password`
--

CREATE TABLE IF NOT EXISTS `sn_rest_password` (
`id` int(11) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_sessions`
--

CREATE TABLE IF NOT EXISTS `sn_sessions` (
`id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `token` varchar(225) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sn_users`
--

CREATE TABLE IF NOT EXISTS `sn_users` (
`id` int(11) NOT NULL,
  `UserName` varchar(225) NOT NULL,
  `UserEmail` varchar(225) NOT NULL,
  `UserPassword` varchar(225) NOT NULL,
  `UserStatus` varchar(100) NOT NULL,
  `UserState` text NOT NULL,
  `UserImage` varchar(100) DEFAULT NULL,
  `UserCover` varchar(200) DEFAULT NULL,
  `FullName` varchar(225) NOT NULL,
  `UserJob` varchar(225) NOT NULL,
  `UserAddress` varchar(225) DEFAULT NULL,
  `Date` int(11) NOT NULL,
  `active` int(11) DEFAULT '1',
  `reg_id` text,
  `isActivated` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE sn_users
ADD isActivated int(11) NOT NULL DEFAULT '0';
-- --------------------------------------------------------

--
-- Table structure for table `sn_videos`
--

CREATE TABLE IF NOT EXISTS `sn_videos` (
`id` int(11) NOT NULL,
  `Video_original_name` varchar(225) NOT NULL,
  `Video_new_name` varchar(225) NOT NULL,
  `Video_path` varchar(225) NOT NULL,
  `Video_hash` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--
