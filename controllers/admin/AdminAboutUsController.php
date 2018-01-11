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

class AdminAboutUsController extends ModuleAdminController
{

    public $asso_type = 'shop';

    public function __construct()
    {
        $this->module = 'smartblog';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->bootstrap = true;

        parent::__construct();
    }

 
    public function initContent()
    {
        $this->content = $this->setPromotion();
        return parent::initContent();
        
    }

    public function setPromotion(){
        $this->context->smarty->assign(array(
            'smartpromotion' => smartblog::getSmartPromotion('about_us')
        ));
        $promotion = $this->context->smarty->fetch(_PS_MODULE_DIR_.'smartblog/views/templates/admin/promotion.tpl');
        return $promotion;
    }

}