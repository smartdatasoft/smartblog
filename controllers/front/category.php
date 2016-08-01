<?php

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');

class smartblogCategoryModuleFrontController extends smartblogModuleFrontController
{
    public $ssl = true;
    public $smartblogCategory;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $category_status = '';
        $totalpages = '';
        $cat_image = 'no';
        $categoryinfo = '';
        $title_category = '';
        $cat_link_rewrite = '';
        $blogcomment = new SmartBlogComment();
        $SmartBlogPost = new SmartBlogPost();
        $BlogCategory = new SmartBlogCategory();
        $BlogPostCategory = new SmartBlogPostCategory();
        $id_category = Tools::getValue('id_category');
        $posts_per_page = Configuration::get('smartpostperpage');
        $limit_start = 0;
        $limit = $posts_per_page;
        if (!$id_category = Tools::getValue('id_category')) {
            $total = $SmartBlogPost->getToltal($this->context->language->id);
        } else {
            $total = $SmartBlogPost->getToltalByCategory($this->context->language->id, $id_category);
            Hook::exec('actionsbcat', array('id_category' => Tools::getValue('id_category')));
        }
        if ($total != '' || $total != 0) {
            $totalpages = ceil($total / $posts_per_page);
        }
        if ((boolean) Tools::getValue('page')) {
            $c = Tools::getValue('page');
            $limit_start = $posts_per_page * ($c - 1);
        }
        if (!$id_category = Tools::getValue('id_category')) {
            $allNews = $SmartBlogPost->getAllPost($this->context->language->id, $limit_start, $limit);
        } else {
            if (file_exists(_PS_MODULE_DIR_.'smartblog/images/category/'.$id_category.'.jpg')) {
                $cat_image = $id_category;
            } else {
                $cat_image = 'no';
            }
            $categoryinfo = $BlogCategory->getCategoryName($id_category);
            $title_category = $categoryinfo[0]['meta_title'];
            $category_status = $categoryinfo[0]['active'];
            $cat_link_rewrite = $categoryinfo[0]['link_rewrite'];
            if ($category_status == 1) {
                $allNews = $BlogPostCategory->getToltalByCategory(
                    $this->context->language->id,
                    $id_category,
                    $limit_start,
                    $limit
                );
            } elseif ($category_status == 0) {
                $allNews = '';
            }
        }
        $i = 0;
        if (!empty($allNews)) {
            foreach ($allNews as $item) {
                $to[$i] = $blogcomment->getTotalPosts($item['id_post']);
                $i++;
            }
            $j = 0;
            foreach ($to as $item) {
                if ($item == '') {
                    $allNews[$j]['totalcomment'] = 0;
                } else {
                    $allNews[$j]['totalcomment'] = $item;
                }
                $j++;
            }
        }

        $this->context->smarty->assign(
            array(
                'postcategory' => $allNews,
                'category_status' => $category_status,
                'title_category' => $title_category,
                'cat_link_rewrite' => $cat_link_rewrite,
                'id_category' => $id_category,
                'cat_image' => $cat_image,
                'categoryinfo' => $categoryinfo,
                'smartshowauthorstyle' => Configuration::get('smartshowauthorstyle'),
                'smartshowauthor' => Configuration::get('smartshowauthor'),
                'limit' => isset($limit) ? $limit : 0,
                'limit_start' => isset($limit_start) ? $limit_start : 0,
                'c' => isset($c) ? $c : 1,
                'total' => $total,
                'smartblogliststyle' => Configuration::get('smartblogliststyle'),
                'smartcustomcss' => Configuration::get('smartcustomcss'),
                'smartshownoimg' => Configuration::get('smartshownoimg'),
                'smartdisablecatimg' => Configuration::get('smartdisablecatimg'),
                'smartshowviewed' => Configuration::get('smartshowviewed'),
                'post_per_page' => $posts_per_page,
                'pagenums' => $totalpages - 1,
                'totalpages' => $totalpages,
            )
        );

        $template_name = 'postcategory.tpl';

        $this->setTemplate($template_name);
    }
}