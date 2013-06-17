-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 04 月 03 日 16:38
-- 服务器版本: 5.0.51
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `heaven`
--

-- --------------------------------------------------------

--
-- 表的结构 `demo`
--

CREATE TABLE IF NOT EXISTS `demo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(45) character set latin1 NOT NULL,
  `pass` varchar(45) character set latin1 NOT NULL,
  `email` varchar(45) character set latin1 NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=9 ;

--
-- 导出表中的数据 `demo`
--

INSERT INTO `demo` (`id`, `username`, `pass`, `email`) VALUES
(1, 'test66', '123123', '123@123.com'),
(2, '456', '3333', '333@qq.com'),
(5, '789', 'asdf', 'asf@asdf.com'),
(6, 'qweqwe', 'qweqwe', 'qwe@aqwd.com'),
(7, 'wrewrewer', 'wewer', 'wre@qq.com'),
(8, 'asdasd', 'asdasds', '123@123.com');

-- --------------------------------------------------------

--
-- 表的结构 `hello_admin`
--

CREATE TABLE IF NOT EXISTS `hello_admin` (
  `id` int(11) NOT NULL auto_increment,
  `adminname` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `ctime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=2 ;

--
-- 导出表中的数据 `hello_admin`
--

INSERT INTO `hello_admin` (`id`, `adminname`, `password`, `ctime`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 37);

-- --------------------------------------------------------

--
-- 表的结构 `hello_category`
--

CREATE TABLE IF NOT EXISTS `hello_category` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `category_name` varchar(50) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cate_id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=11 ;

--
-- 导出表中的数据 `hello_category`
--

INSERT INTO `hello_category` (`id`, `parent_id`, `category_name`, `ctime`) VALUES
(1, 0, '一级类别1', 0),
(2, 1, '二级类别1', 0),
(3, 1, '二级类别2', 0),
(4, 1, '二级类别3', 0),
(5, 2, '三级类别21', 0),
(6, 2, '三级类别22', 0),
(7, 2, '三级类别23', 0),
(8, 3, 'rfwesdfsd', 0),
(9, 4, '54534w43', 0),
(10, 5, '66666', 0);

-- --------------------------------------------------------

--
-- 表的结构 `hello_content`
--

CREATE TABLE IF NOT EXISTS `hello_content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `thumb` varchar(200) default NULL,
  `ctime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `hello_content`
--

