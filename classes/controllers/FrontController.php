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

class smartblogModuleFrontController extends ModuleFrontController
{

    public $ssl = false;

    public function initContent()
    {
        $meta = array();

        parent::initContent();
        $hide_column_left = 0;
        $hide_column_right = 0;
        if (isset($this->php_self) && is_object(Context::getContext()->theme)) {
            $colums = Context::getContext()->theme->hasColumns(Context::getContext()->controller->page_name);
            if ($colums) {
                $hide_column_left = isset($colums['left_column']) && !empty($colums['left_column']) ? 0 : 1;
                $hide_column_right = isset($colums['right_column']) && !empty($colums['right_column']) ? 0 : 1;
            }
        }

        $this->context->smarty->assign($meta);
        if (Configuration::get('smartshowcolumn') == 0) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                'hide_right_column' => '',
                'hide_left_column' => '',
                'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
            ));
        } elseif (Configuration::get('smartshowcolumn') == 1) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                'hide_right_column' => '1',
                'hide_left_column' => '',
                'HOOK_RIGHT_COLUMN' => ''
            ));
        } elseif (Configuration::get('smartshowcolumn') == 2) {

            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => '',
                'hide_right_column' => '',
                'hide_left_column' => '1',
                'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
            ));
        } elseif (Configuration::get('smartshowcolumn') == 3) {

            $this->context->smarty->assign(array(
                'hide_right_column' => $hide_column_right,
                'hide_left_column' => $hide_column_left,
            ));
        } else {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                'hide_right_column' => '',
                'hide_left_column' => '',
                'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
            ));
        }
    }
   
}
