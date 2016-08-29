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

function upgrade_module_2_0_1($object)
{
    upgrade_addquickaccess();
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'smart_blog_post` ADD is_featured int(11) DEFAULT NULL');
    $smartblog = new smartblog();
    $smartblog->registerHook('displayBackOfficeHeader');
    $smartblog->SmartHookInsert();
    Configuration::updateGlobalValue('smartshowviewed', '1');
    Configuration::updateGlobalValue('smartcaptchaoption', '1');
    Configuration::updateGlobalValue('smartdisablecatimg', '1');
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
