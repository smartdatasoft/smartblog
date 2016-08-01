<?php

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');

class smartblogarchiveModuleFrontController extends smartblogModuleFrontController
{
    public $ssl = false;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $blogcomment = new SmartBlogComment();
        $year = Tools::getValue('year');
        $month = Tools::getValue('month');
        $title_category = '';
        $posts_per_page = Configuration::get('smartpostperpage');
        $limit_start = 0;
        $limit = $posts_per_page;
        if ((boolean) Tools::getValue('page')) {
            $c = (int) Tools::getValue('page');
            $limit_start = $posts_per_page * ($c - 1);
        }
        $result = SmartBlogPost::getArchiveResult($month, $year, $limit_start, $limit);
        $total = count($result);
        $totalpages = ceil($total / $posts_per_page);

        $i = 0;
        if (!empty($result)) {
            foreach ($result as $item) {
                $to[$i] = $blogcomment->getTotalPosts($item['id_post']);
                $i++;
            }
            $j = 0;
            foreach ($to as $item) {
                if ($item == '') {
                    $result[$j]['totalcomment'] = 0;
                } else {
                    $result[$j]['totalcomment'] = $item;
                }
                $j++;
            }
        }
        $this->context->smarty->assign(
            array(
                'postcategory' => $result,
                'title_category' => $title_category,
                'smartshowauthorstyle' => Configuration::get('smartshowauthorstyle'),
                'limit' => isset($limit) ? $limit : 0,
                'limit_start' => isset($limit_start) ? $limit_start : 0,
                'c' => isset($c) ? $c : 1,
                'total' => $total,
                'smartshowviewed' => Configuration::get('smartshowviewed'),
                'smartcustomcss' => Configuration::get('smartcustomcss'),
                'smartshownoimg' => Configuration::get('smartshownoimg'),
                'smartshowauthor' => Configuration::get('smartshowauthor'),
                'post_per_page' => $posts_per_page,
                'pagenums' => $totalpages - 1,
                'smartblogliststyle' => Configuration::get('smartblogliststyle'),
                'totalpages' => $totalpages,
            )
        );

        $template_name = 'archivecategory.tpl';
        $this->setTemplate($template_name);
    }
}
