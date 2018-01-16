<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_0_3($object){
    $smartblog = new smartblog();
    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'smart_blog_category` ADD `level_depth` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `id_smart_blog_category`;');

    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT  *  FROM ' . _DB_PREFIX_ . 'smart_blog_category ');
    $result_update = Db::getInstance()->execute('  UPDATE   ' . _DB_PREFIX_ . 'smart_blog_category SET `level_depth` = 1 WHERE   `id_parent` = 0 ') ;
    $level_depth = 1;
    foreach ($results as $result) {
        $id_parent = $result['id_parent'];
        $id_smart_blog_category = $result['id_smart_blog_category'];
        $level_depth = $result['level_depth'];
        if($id_parent >0)  {
            $results_depth = Db::getInstance()->executeS('SELECT  *  FROM ' . _DB_PREFIX_ . 'smart_blog_category where id_smart_blog_category =  ' .$id_parent );
            $rs =$results_depth[0];
            if((int) $rs['level_depth']<0)
                $rs['level_depth'] = 1;
            $level_depth = ((int) $rs['level_depth']) +1;
            $result_update = Db::getInstance()->execute('UPDATE   ' . _DB_PREFIX_ . 'smart_blog_category SET `level_depth` = '.$level_depth.' WHERE   `id_smart_blog_category` =  ' . $id_smart_blog_category);
        }
    }

    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'smart_blog_post_category` change id_smart_blog_post_category id_smart_blog_post INT(11) NOT NULL ;');

    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'smart_blog_post_category` ADD PRIMARY KEY( `id_smart_blog_post`, `id_smart_blog_category`)');

    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'smart_blog_post_category`(id_smart_blog_post,id_smart_blog_category) select id_smart_blog_post,id_category from `'._DB_PREFIX_.'smart_blog_post`;');

    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'smart_blog_category_lang` ADD `name` VARCHAR( 128 ) NOT NULL AFTER `id_lang`;');

    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'smart_blog_category_lang` SET `name` = `meta_title`;');

    $test = Db::getInstance()->executeS("SHOW COLUMNS from `" . _DB_PREFIX_ . "smart_blog_post` WHERE FIELD LIKE 'associations'", true, false);
    if (!count($test)) {
        Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . 'smart_blog_post` ADD `associations` TEXT NOT NULL AFTER `comment_status`');
    }

    $smartblog->registerHook('displaySmartBlogLeft');
    $smartblog->registerHook('displaySmartBlogRight');
    $smartblog->registerHook('displaySmartBeforePost');
    $smartblog->registerHook('displaySmartAfterPost');

    $id_tab = (int) Tab::getIdFromClassName('AdminSmartBlogAjax');
    if ($id_tab) {
        $tab = new Tab($id_tab);
        $tab->active = 1;
        $tab->class_name = 'AdminSmartBlogAjax';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'SmartBlogAjax';
        }
        $tab->id_parent = -1;
        $tab->module = $object->name;
        $tab->save();
    }

    $tables = array(
        _DB_PREFIX_ . 'smart_blog_category_shop' => 'id_smart_blog_category_shop',
        _DB_PREFIX_ . 'smart_blog_comment_shop' => 'id_smart_blog_comment_shop',
        _DB_PREFIX_ . 'smart_blog_post_shop' => 'id_smart_blog_post_shop'
    );
    $sqltorun = '';
    foreach ($tables as $table => $col) {
        $test = Db::getInstance()->executeS("SHOW COLUMNS from `{$table}` WHERE FIELD LIKE '{$col}'", true, false);
        if (count($test)) {
            $sqltorun .= "ALTER TABLE `{$table}` DROP COLUMN `{$col}`;\n";
        }
    }
    if (!empty($sqltorun))
        Db::getInstance()->execute($sqltorun);

    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    
    $sql = 'CREATE TABLE IF NOT EXISTS `smart_blog_post_shop_temp` SELECT DISTINCT(`id_shop`), id_smart_blog_post FROM `'._DB_PREFIX_.'smart_blog_post_shop` WHERE id_smart_blog_post IN (SELECT DISTINCT(`id_smart_blog_post`) FROM `'._DB_PREFIX_.'smart_blog_post_shop`)';
    $db->execute($sql);
    
    $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'smart_blog_post_shop`';
    $db->execute($sql);
    
    $sql = 'ALTER TABLE `smart_blog_post_shop_temp` ADD PRIMARY KEY (`id_smart_blog_post`, `id_shop`), ADD KEY `id_shop` (`id_shop`)';
    $db->execute($sql);
    
    $sql = 'ALTER TABLE `smart_blog_post_shop_temp` RENAME AS `'._DB_PREFIX_.'smart_blog_post_shop`';
    $db->execute($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_media` (
      `id_media` int(11) NOT NULL auto_increment,
      `id_post` int(11) DEFAULT NULL,
      `id_parent` int(11) DEFAULT NULL,
      `position` int(11) DEFAULT NULL,
      `media_path` varchar(45) DEFAULT NULL,
      `media_name` varchar(45) DEFAULT NULL,
      `media_description` varchar(45) DEFAULT NULL,
      PRIMARY KEY (`id_media`)
    )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    Db::getInstance()->execute($sql);

    return true;
}
