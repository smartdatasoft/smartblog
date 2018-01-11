<?php
/**
 * 2017 thirty bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 *  @author    thirty bees <modules@thirtybees.com>
 *  @copyright 2017 thirty bees
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!Module::isInstalled('smartblog')) {
    return;
}

foreach (scandir(__DIR__) as $module) {
    if (in_array($module, ['.', '..']) || !is_dir(__DIR__.'/'.$module)) {
        continue;
    }

    if (!Module::isInstalled($module) || !file_exists(_PS_MODULE_DIR_."$module/$module.php")) {
        if (!file_exists(_PS_MODULE_DIR_."$module/$module.php")) {
            Tools::deleteDirectory(_PS_MODULE_DIR_.$module, true);
            Tools::recurseCopy(__DIR__."/$module/", _PS_MODULE_DIR_.$module, false);
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(_PS_MODULE_DIR_."$module/$module.php");
        }

        // Wait a moment on slow servers
        $wait = 10;
        while (!file_exists(_PS_MODULE_DIR_."$module/$module.php") && $wait > 0) {
            $wait--;
            // Wait half a second
            usleep(500000);
        }
        require_once _PS_MODULE_DIR_."$module/$module.php";

        /** @var Module $mod */
        $mod = new $module();
        $mod->install();
    }
}
