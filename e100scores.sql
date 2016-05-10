-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-05-05 14:30:12
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `e100scores`
--

-- --------------------------------------------------------

--
-- 表的结构 `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_id` int(11) NOT NULL,
  `number` int(11) NOT NULL COMMENT '题号',
  `alias` varchar(63) COLLATE utf8_unicode_ci NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `options` text COLLATE utf8_unicode_ci NOT NULL COMMENT '用json存储，每个包括选项和正确与否',
  `point` int(11) DEFAULT NULL COMMENT '分值',
  `analysis` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_2` (`alias`),
  KEY `bank_id` (`bank_id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `question_bank`
--

CREATE TABLE IF NOT EXISTS `question_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `course_id_2` (`course_id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `answer` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'json存储，包括答案及对错',
  `completed` int(11) NOT NULL DEFAULT '0' COMMENT '已完成题数',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `open_id` (`user_id`,`bank_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `register_time` datetime NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `open_id_2` (`open_id`),
  KEY `open_id` (`open_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
