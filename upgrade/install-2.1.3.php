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

function upgrade_module_2_1_3($object)
{
    
    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    
    $sql = 'CREATE TABLE IF NOT EXISTS `smart_blog_post_shop_temp` SELECT DISTINCT(`id_shop`), id_smart_blog_post FROM `'._DB_PREFIX_.'smart_blog_post_shop` WHERE id_smart_blog_post IN (SELECT DISTINCT(`id_smart_blog_post`) FROM `'._DB_PREFIX_.'smart_blog_post_shop`)';
    $db->execute($sql);
    
    $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'smart_blog_post_shop`';
    $db->execute($sql);
    
    $sql = 'ALTER TABLE `smart_blog_post_shop_temp` ADD PRIMARY KEY (`id_smart_blog_post`, `id_shop`), ADD KEY `id_shop` (`id_shop`)';
    $db->execute($sql);
    
    $sql = 'ALTER TABLE `smart_blog_post_shop_temp` RENAME AS `'._DB_PREFIX_.'smart_blog_post_shop`';
    $db->execute($sql);
    
    return true;
}
