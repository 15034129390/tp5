-- phpMyAdmin SQL Dump
-- version 2.11.2-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 06 月 17 日 04:02
-- 服务器版本: 5.0.41
-- PHP 版本: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `lrfbeyond_demo`
--

-- --------------------------------------------------------

--
-- 表的结构 `sortlist`
--

CREATE TABLE IF NOT EXISTS `sortlist` (
  `id` int(11) NOT NULL auto_increment,
  `sort` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 导出表中的数据 `sortlist`
--

INSERT INTO `sortlist` (`id`, `sort`) VALUES
(1, '1,2,6,5,3,4,7,8,9,10,11,12');
