<?php

class smartblogModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($id_category = Tools::getValue('id_category') && Tools::getValue('id_category') != null) {
            $this->context->smarty->assign(SmartBlogCategory::GetMetaByCategory(Tools::getValue('id_category')));
        }
        if ($id_post = Tools::getValue('id_post') && Tools::getValue('id_post') != null) {
            $this->context->smarty->assign(SmartBlogPost::GetPostMetaByPost(Tools::getValue('id_post')));
        }
        if (Tools::getValue('id_category') == null && Tools::getValue('id_post') == null) {
            $meta['meta_title'] = Configuration::get('smartblogmetatitle');
            $meta['meta_description'] = Configuration::get('smartblogmetadescrip');
            $meta['meta_keywords'] = Configuration::get('smartblogmetakeyword');
            $this->context->smarty->assign($meta);
        }
        if (Configuration::get('smartshowcolumn') == 0) {
            $this->context->smarty->assign(
                array(
                    'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                    'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight'),
                )
            );
        } elseif (Configuration::get('smartshowcolumn') == 1) {
            $this->context->smarty->assign(
                array(
                    'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                )
            );
        } elseif (Configuration::get('smartshowcolumn') == 2) {

            $this->context->smarty->assign(
                array(
                    'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight'),
                )
            );
        } elseif (Configuration::get('smartshowcolumn') == 3) {
            $this->context->smarty->assign(array());
        } else {
            $this->context->smarty->assign(
                array(
                    'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                    'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight'),
                )
            );
        }
    }
}