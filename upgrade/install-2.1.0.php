<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_1_0($object)
{

    Configuration::updateGlobalValue('smartdataformat', 'm/d/Y H:i:s');

    upgrade_addquickaccess();

    $smartblog = new smartblog();
    $smartblog->registerHook('actionsbdeletecat');
    $smartblog->registerHook('actionsbnewcat');
    $smartblog->registerHook('actionsbupdatecat');
    $smartblog->registerHook('actionsbtogglecat');
    $smartblog->registerHook('actionsbdeletepost');
    $smartblog->registerHook('actionsbnewpost');
    $smartblog->registerHook('actionsbupdatepost');
    $smartblog->registerHook('actionsbtogglepost');
    $smartblog->registerHook('actionHtaccessCreate');
    $smartblog->registerHook('displaySmartBlogLeft');
    $smartblog->registerHook('displaySmartBlogRight');
    $smartblog->registerHook('displaySmartBeforePost');
    $smartblog->registerHook('displaySmartAfterPost');

    Configuration::updateGlobalValue('smartshowviewed', '1');
    Configuration::updateGlobalValue('smartcaptchaoption', '1');
    Configuration::updateGlobalValue('smartdisablecatimg', '1');


    //----------------------------------------------------------------------------------
    Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'smart_blog_product_related`');

    //----------------------------------------------------------------------------------
    Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . 'smart_blog_post_category` CHANGE `id_smart_blog_post_category` `id_smart_blog_post` INT(11) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'smart_blog_post_category` ADD PRIMARY KEY( `id_smart_blog_post`, `id_smart_blog_category`)');

    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT  id_smart_blog_post	, id_category  FROM ' . _DB_PREFIX_ . 'smart_blog_post ');


    $res = null;
    foreach ($results as $result) {

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post_category` (`id_smart_blog_post`, `id_smart_blog_category`) VALUES (' + $result['id_smart_blog_post'] + ', ' + $result['id_category'] + ')';
        //  $res .= Db::getInstance()->execute($sql);
    }

    //-------------------------------------------------------------------------------------
    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT  id_smart_blog_post	, id_category  FROM ' . _DB_PREFIX_ . 'smart_blog_post ');


    foreach ($results as $result) {

        Db::getInstance()->insert('smart_blog_post_category', array(
            'id_smart_blog_post' => (int) $result['id_smart_blog_post'],
            'id_smart_blog_category' => (int) $result['id_category']
        ));
    }
    //--------------------------------------------------------------------------------------
    $id_tab = (int) Tab::getIdFromClassName('AdminSmartBlogAjax');

    if (!$id_tab) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminSmartBlogAjax';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'SmartBlogAjax';
        }
        $tab->id_parent = -1;
        $tab->module = $object->name;

        $tab->add();
    }
    //--------------------------------------------------------------------------------------

    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_gallary_images` (
      `id_smart_blog_gallary_images` INT(11) NOT NULL AUTO_INCREMENT,
      `id_smart_blog_post` int(11) NOT NULL,
      `position` int(11) NOT NULL,
       PRIMARY KEY (`id_smart_blog_gallary_images`,`id_smart_blog_post`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    Db::getInstance()->execute($sql);


    $sql1 = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_gallary_images_lang` (
        `id_smart_blog_gallary_images` INT(11) NOT NULL,
        `id_lang` int(11) NOT NULL,
        `legend` varchar(256) NOT NULL,
        PRIMARY KEY (`id_smart_blog_gallary_images`,`id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

    Db::getInstance()->execute($sql1);

    // Changes for shop table
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
    //--------------------------------------------------------------------------------------
    $test = Db::getInstance()->executeS("SHOW COLUMNS from `" . _DB_PREFIX_ . "smart_blog_post` WHERE FIELD LIKE 'associations'", true, false);
    if (!count($test)) {
        Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . 'smart_blog_post` ADD `associations` TEXT NOT NULL AFTER `comment_status`');
    }
    //--------------------------------------------------------------------------------------
    Configuration::updateGlobalValue('smartenableguestcomment', '1');
    //--------------------------------------------------------------------------------------
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_blog_post_meta` (  
  `id_smart_blog_post_meta` int(11) NOT NULL auto_increment,
  `id_smart_blog_post` int(11) NOT NULL,  
  `meta_key` VARCHAR(50) NOT NULL,  
  `meta_value` LONGTEXT,  
    PRIMARY KEY (`id_smart_blog_post_meta`, `id_smart_blog_post`),
    KEY `id_smart_blog_post` (`id_smart_blog_post`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
    //--------------------------------------------------------------------------------------
    Configuration::updateGlobalValue('smartblogurlpattern', '2');
    Configuration::updateGlobalValue('smartshowhomepost', 4);
        Configuration::updateGlobalValue('smartshowrelatedproduct', 5);
        Configuration::updateGlobalValue('smartshowrelatedproductpost', 5);
        Configuration::updateGlobalValue('smart_update_period', 'hourly');
        Configuration::updateGlobalValue('smart_update_frequency', '1');
        Configuration::updateGlobalValue('smartshowrelatedpost', 3);
        Configuration::updateGlobalValue('sort_category_by', 'id_desc');
        Configuration::updateGlobalValue('latestnews_sort_by', 'id_desc');
    //--------------------------------------------------------------------------------------

    return true;
}

function upgrade_addquickaccess()
{
    $link = new Link();
    $qa = new QuickAccess();
    $qa->link = $link->getAdminLink('AdminModules') . '&configure=smartblog';
    $languages = Language::getLanguages(false);
    foreach ($languages as $language) {
        $qa->name[$language['id_lang']] = 'Smart Blog Setting';
    }
    $qa->new_window = '0'; 
    if ($qa->save()) {
        Configuration::updateValue('smartblog_quick_access', $qa->id);
    }
    return true;
}
