<?php
$sql = array();
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_category`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_category_lang`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_category_shop`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_comment`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_comment_shop`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_media`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_post`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_post_lang`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_post_category`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_post_related`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_post_shop`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_product_related`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_tag`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_post_tag`';
$sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'smart_blog_imagetype`';

foreach ($sql as $s) {
    if (!Db::getInstance()->execute($s)) {
        return false;
    }
}
