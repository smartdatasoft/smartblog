<?php

class smartblogModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
	    parent::initContent();
            if($id_category = Tools::getvalue('id_category') && Tools::getvalue('id_category') != Null){
                 $this->context->smarty->assign(BlogCategory::GetMetaByCategory(Tools::getvalue('id_category')));
            }
            if($id_post = Tools::getvalue('id_post')  && Tools::getvalue('id_post') != Null){
                 $this->context->smarty->assign(SmartBlogPost::GetPostMetaByPost(Tools::getvalue('id_post')));
            }
            if(Tools::getvalue('id_category') == Null  && Tools::getvalue('id_post') == Null){
              $meta['meta_title'] = Configuration::get('smartblogmetatitle');
              $meta['meta_description'] = Configuration::get('smartblogmetadescrip');
              $meta['meta_keywords'] = Configuration::get('smartblogmetakeyword');
              $this->context->smarty->assign($meta);
            }
              if(Configuration::get('smartshowcolumn') == 0){
                  $this->context->smarty->assign(array(
			    'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
			    'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
			));
              }elseif(Configuration::get('smartshowcolumn') == 1){
                  $this->context->smarty->assign(array(
			    'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft')
			)); 
              }elseif(Configuration::get('smartshowcolumn') == 2){

                   $this->context->smarty->assign(array(
			    'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
			));
              }elseif(Configuration::get('smartshowcolumn') == 3){
                  $this->context->smarty->assign(array());
              }else{
                  $this->context->smarty->assign(array(
			    'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
			    'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
			));   
              } 
        }
}