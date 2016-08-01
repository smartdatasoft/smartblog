<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_category` (
  `id_smart_blog_category` int(11) NOT NULL auto_increment,
  `id_parent` varchar(45) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `desc_limit` varchar(45) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_category`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_category_lang` (
  `id_smart_blog_category` int(11) NOT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `meta_title` varchar(150) DEFAULT NULL,
  `meta_keyword` varchar(200) DEFAULT NULL,
  `meta_description` varchar(350) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `link_rewrite` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_category`,`id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_category_shop` (
  `id_smart_blog_category_shop`  int(11) NOT NULL auto_increment,
  `id_smart_blog_category` int(11) NOT NULL,
  `id_shop` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_category_shop`,`id_smart_blog_category`,`id_shop`)
)ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_comment_shop` (
  `id_smart_blog_comment_shop`  int(11) NOT NULL auto_increment,
  `id_smart_blog_comment` int(11) NOT NULL,
  `id_shop` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_comment_shop`,`id_smart_blog_comment`,`id_shop`)
)ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_comment`(
  `id_smart_blog_comment` int(11) NOT NULL auto_increment,
  `id_parent` int(11) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_post` int(11) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `email` varchar(90) DEFAULT NULL,
  `website` varchar(128) DEFAULT NULL,
  `content` text,
  `active` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id_smart_blog_comment`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_media` (
  `id_media` int(11) NOT NULL auto_increment,
  `id_post` int(11) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `media_path` varchar(45) DEFAULT NULL,
  `media_name` varchar(45) DEFAULT NULL,
  `media_description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_media`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post` (
  `id_smart_blog_post` int(11) NOT NULL auto_increment,
  `id_author` int(11) DEFAULT NULL,
  `id_category` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified`  datetime DEFAULT NULL,
  `viewed` int(11) DEFAULT NULL,
  `is_featured` int(11) DEFAULT NULL,
  `comment_status` int(11) DEFAULT NULL,
  `post_type` varchar(45) DEFAULT NULL,
  `image` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_post`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post_lang` (
  `id_smart_blog_post` int(11) NOT NULL,
  `id_lang` varchar(45) DEFAULT NULL,
  `meta_title` varchar(150) DEFAULT NULL,
  `meta_keyword` varchar(200) DEFAULT NULL,
  `meta_description` varchar(450) DEFAULT NULL,
  `short_description` varchar(450) DEFAULT NULL,
  `content` text,
  `link_rewrite` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_post`,`id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post_shop` (
  `id_smart_blog_post_shop` int(11) NOT NULL auto_increment,
  `id_smart_blog_post` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_smart_blog_post_shop`,`id_smart_blog_post`,`id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post_category` (
  `id_smart_blog_post_category` int(11) NOT NULL,
  `id_smart_blog_category` int(11) DEFAULT NULL
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post_related` (
  `id_post` int(11) NOT NULL,
  `related_post_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_post`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_product_related` (
  `id_post` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_post`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_tag` (
  `id_tag` int(11) NOT NULL auto_increment,
  `id_lang` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_tag`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post_tag` (
  `id_tag` int(11) NOT NULL,
  `id_post` int(11) DEFAULT NULL
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_imagetype`(
  `id_smart_blog_imagetype` int(11) NOT NULL auto_increment,
  `type_name` varchar(45) DEFAULT NULL,
  `width` varchar(45) DEFAULT NULL,
  `height` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_smart_blog_imagetype`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}
