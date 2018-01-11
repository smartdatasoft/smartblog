<?php

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