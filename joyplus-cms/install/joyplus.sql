-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- 主机: 10.6.7.149
-- 生成日期: 2013 年 07 月 18 日 12:11
-- 服务器版本: 5.5.24-log
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ijoyplus`
--

-- --------------------------------------------------------

--
-- 表的结构 `mac_cj_vod_projects`
--

CREATE TABLE IF NOT EXISTS `mac_cj_vod_projects` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(128) DEFAULT NULL,
  `p_coding` varchar(64) DEFAULT NULL,
  `p_playtype` varchar(11) DEFAULT NULL,
  `p_pagetype` int(11) NOT NULL DEFAULT '0',
  `p_url` varchar(255) DEFAULT NULL,
  `p_pagebatchurl` varchar(255) DEFAULT NULL,
  `p_manualurl` varchar(255) DEFAULT NULL,
  `p_pagebatchid1` varchar(128) DEFAULT NULL,
  `p_pagebatchid2` varchar(128) DEFAULT NULL,
  `p_script` int(11) NOT NULL DEFAULT '0',
  `p_showtype` int(11) NOT NULL DEFAULT '0',
  `p_collecorder` int(11) NOT NULL DEFAULT '0',
  `p_savefiles` int(11) NOT NULL DEFAULT '0',
  `p_intolib` int(11) NOT NULL DEFAULT '0',
  `p_ontime` int(11) NOT NULL DEFAULT '0',
  `p_listcodestart` text,
  `p_listcodeend` text,
  `p_classtype` int(11) NOT NULL DEFAULT '0',
  `p_collect_type` int(11) NOT NULL DEFAULT '0',
  `p_time` datetime DEFAULT NULL,
  `p_listlinkstart` text,
  `p_listlinkend` text,
  `p_starringtype` int(11) NOT NULL DEFAULT '0',
  `p_starringstart` text,
  `p_starringend` text,
  `p_titletype` int(11) NOT NULL DEFAULT '0',
  `p_titlestart` text,
  `p_titleend` text,
  `p_pictype` int(11) NOT NULL DEFAULT '0',
  `p_picstart` text,
  `p_picend` text,
  `p_timestart` text,
  `p_timeend` text,
  `p_areastart` text,
  `p_areaend` text,
  `p_typestart` text,
  `p_typeend` text,
  `p_contentstart` text,
  `p_contentend` text,
  `p_playcodetype` int(11) NOT NULL DEFAULT '0',
  `p_playcodestart` text,
  `p_playcodeend` text,
  `p_playurlstart` text,
  `p_playurlend` text,
  `p_playlinktype` int(11) NOT NULL DEFAULT '0',
  `p_playlinkstart` text,
  `p_playlinkend` text,
  `p_playspecialtype` int(11) NOT NULL DEFAULT '0',
  `p_playspecialrrul` text,
  `p_playspecialrerul` text,
  `p_server` varchar(128) DEFAULT NULL,
  `p_hitsstart` int(11) NOT NULL DEFAULT '0',
  `p_hitsend` int(11) NOT NULL DEFAULT '0',
  `p_lzstart` text,
  `p_lzend` text,
  `p_colleclinkorder` int(11) NOT NULL DEFAULT '0',
  `p_lzcodetype` int(11) NOT NULL DEFAULT '0',
  `p_lzcodestart` text,
  `p_lzcodeend` text,
  `p_languagestart` text,
  `p_languageend` text,
  `p_remarksstart` text,
  `p_remarksend` text,
  `p_directedstart` text,
  `p_directedend` text,
  `p_setnametype` int(11) NOT NULL DEFAULT '0',
  `p_setnamestart` text,
  `p_setnameend` text,
  `p_playcodeApiUrl` varchar(300) NOT NULL DEFAULT '""',
  `p_playcodeApiUrltype` int(1) NOT NULL DEFAULT '0',
  `p_playcodeApiUrlParamstart` varchar(300) NOT NULL DEFAULT '""',
  `p_playcodeApiUrlParamend` varchar(300) NOT NULL DEFAULT '""',
  `p_videocodeApiUrl` varchar(255) DEFAULT NULL,
  `p_videocodeApiUrlParamstart` text,
  `p_videocodeApiUrlParamend` text,
  `p_videourlstart` text,
  `p_videourlend` text,
  `p_videocodeType` int(1) DEFAULT '0',
  PRIMARY KEY (`p_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1856 ;

--
-- 转存表中的数据 `mac_cj_vod_projects`
--

INSERT INTO `mac_cj_vod_projects` (`p_id`, `p_name`, `p_coding`, `p_playtype`, `p_pagetype`, `p_url`, `p_pagebatchurl`, `p_manualurl`, `p_pagebatchid1`, `p_pagebatchid2`, `p_script`, `p_showtype`, `p_collecorder`, `p_savefiles`, `p_intolib`, `p_ontime`, `p_listcodestart`, `p_listcodeend`, `p_classtype`, `p_collect_type`, `p_time`, `p_listlinkstart`, `p_listlinkend`, `p_starringtype`, `p_starringstart`, `p_starringend`, `p_titletype`, `p_titlestart`, `p_titleend`, `p_pictype`, `p_picstart`, `p_picend`, `p_timestart`, `p_timeend`, `p_areastart`, `p_areaend`, `p_typestart`, `p_typeend`, `p_contentstart`, `p_contentend`, `p_playcodetype`, `p_playcodestart`, `p_playcodeend`, `p_playurlstart`, `p_playurlend`, `p_playlinktype`, `p_playlinkstart`, `p_playlinkend`, `p_playspecialtype`, `p_playspecialrrul`, `p_playspecialrerul`, `p_server`, `p_hitsstart`, `p_hitsend`, `p_lzstart`, `p_lzend`, `p_colleclinkorder`, `p_lzcodetype`, `p_lzcodestart`, `p_lzcodeend`, `p_languagestart`, `p_languageend`, `p_remarksstart`, `p_remarksend`, `p_directedstart`, `p_directedend`, `p_setnametype`, `p_setnamestart`, `p_setnameend`, `p_playcodeApiUrl`, `p_playcodeApiUrltype`, `p_playcodeApiUrlParamstart`, `p_playcodeApiUrlParamend`, `p_videocodeApiUrl`, `p_videocodeApiUrlParamstart`, `p_videocodeApiUrlParamend`, `p_videourlstart`, `p_videourlend`, `p_videocodeType`) VALUES

(177, '百度电影', 'GB2312', 'baidu', 1, '', 'http://video.baidu.com/commonapi/movie2level/?callback=jQuery19109205652033106855_1368604219634&filter=false&type=&area=&actor=&start=&complete=&order=hot&pn={ID}&rating=&_=1368604219636', '', '13', '13', 8191, 0, 1, 0, 0, 0, '<div class="bd">', '<div class="ft">', 0, 1, '2013-07-18 02:03:02', '<dt class="v-title"><a href="', '"', 0, '主演：</span>', '</li>', 0, '<div class="title-wrapper clearfix">', '<span', 0, '<img src="', '"', '<span class="year">', '</span>', '地区：</span>', '</li>', '<ul class="aside-highlight">', '</ul>', '简介：</span>', '</span>', 0, '', '', '时长：</span>', '分钟</li>', 0, '"site_url":', '"', 0, '', '', '0', 0, 0, '', '', 0, 0, '', '', '语言：</span>', '</li>', '', '', '导演：</span>', '</li>', 0, '', '', 'size="""""""""""""""""""""""""""""""""""', 0, '""', '""', '', '', '', '', '', 0),
(179, '百度综艺', 'GB2312', 'baidu', 1, '', 'http://video.baidu.com/commonapi/tvshow2level/?callback=jQuery19101723327927027405_1369964574718&filter=false&type=&area=&actor=&start=&complete=&order=hot&pn={ID}&rating=&prop=&_=1369964574721', '', '1', '1', 8191, 0, 1, 0, 0, 0, '<div class="bd">', '<div class="ft">', 0, 3, '2013-07-18 07:50:29', '<dt class="v-title"><a href="', '"', 0, '', '', 0, '<div class="title-wrapper clearfix">', '<span class="update-info">', 0, '<img src="', '"', '"years":["', '"]', '地区：</span>', '</li>', '', '', '简介：</span>', '</span>', 0, '', '', '"url_prefix":"', '"', 0, '', '', 0, '', '', '0', 0, 0, '<b class="newest">', '</b>', 0, 0, '更新至', '"', '<?echo $p_languagestart?>', '<?echo $p_languageend?>', '<b class="newest">', '</b>', '', '', 0, '', '', 'size="""""""""""""""', 0, '""', '""', '', '', '', '', '', 0),

(178, '百度电视剧', 'GB2312', 'baidu', 1, '', 'http://video.baidu.com/commonapi/tvplay2level/?callback=jQuery19105037843275615373_1368593570491&filter=true&type=&area=&actor=&start=&complete=&order=pubtime&pn={ID}&rating=', '', '5', '5', 8191, 0, 1, 0, 0, 0, '<div class="bd">', '<div class="ft">', 0, 2, '2013-07-17 23:07:02', '<dt class="v-title"><a href="', '"', 0, '主演：</span>', '</li>', 0, '<div class="title-wrapper clearfix">', '<span class="update-info">', 0, '<img src="', '"', '<span class="year">', '</span>', '地区：</span>', '</li>', '<ul class="aside-highlight">', '</ul>', '简介：</span>', '</span>', 1, '', '', '时长：</span>', '分钟</li>', 0, '', '', 0, '', '', '0', 0, 0, 'episode: ''', '''', 0, 0, '更新至', '"', '语言：</span>', '</li>', '共', '集', '导演：</span>', '</li>', 0, '', '', ' size=', 0, '""', '""', '', '', '', '', '', 0),
(180, '百度动漫', 'GB2312', 'baidu', 1, '', 'http://video.baidu.com/commonapi/comic2level/?callback=jQuery191025315059880813306_1369966105776&filter=true&type=&area=&actor=&start=&complete=&order=hot&pn={ID}&rating=&prop=&_=1369966105778', '', '1', '1', 8191, 0, 1, 0, 0, 0, '<div class="bd">', '<div class="ft">', 0, 131, '2013-07-18 01:00:02', '<dt class="v-title"><a href="', '"', 0, '主演：</span>', '</li>', 0, '<div class="title-wrapper clearfix">', '<span class="update-info">', 0, '<img src="', '"', '"al_date": "', '"', '地区：</span>', '</li>', '', '', '简介：</span>', '</span>', 1, '"sites":', ', "max_episode":', '"url_prefix": "', '"', 0, '', '', 0, '', '', '0', 0, 0, '<b class="newest">', '</b>', 0, 0, '"max_episode": "', '"', '<?echo $p_languagestart?>', '<?echo $p_languageend?>', '', '', '导演：</span>', '</li>', 0, '', '', 'size=""""""""""""""""""""', 0, '""', '""', '', '', '', '', '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
