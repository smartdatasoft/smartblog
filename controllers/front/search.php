<?php

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class smartblogsearchModuleFrontController extends smartblogModuleFrontController
{
    public $ssl = false;

    public function init(){
            parent::init();
    }
        
    public function initContent(){
           
           parent::initContent();
           $rer  =  SmartBlogPost::tagsPost('asd');
           
            $blogcomment = new Blogcomment();
                $result = '';
                $keyword = Tools::getValue('smartsearch');
                Hook::exec('actionsbsearch', array('keyword' => Tools::getValue('smartsearch')));
                $id_lang = (int)$this->context->language->id;
                $title_category = '';
                $posts_per_page = Configuration::get('smartpostperpage');
                $limit_start = 0;
                $limit = $posts_per_page;
                
                if((boolean)Tools::getValue('page')){
                $c = (int)Tools::getValue('page');
                    $limit_start = $posts_per_page * ($c - 1);
            }
                
                    $keyword = Tools::getValue('smartsearch');
                    $id_lang = (int)$this->context->language->id;
                $result = SmartBlogPost::SmartBlogSearchPost($keyword,$id_lang,$limit_start,$limit);
                
                $total = SmartBlogPost::SmartBlogSearchPostCount($keyword,$id_lang);
                $totalpages = ceil($total/$posts_per_page);
                $i = 0;
            if(!empty($result)){
                foreach($result as $item){
                    $to[$i] = $blogcomment->getToltalComment($item['id_post']);
                   $i++;
                }
                $j = 0;
                foreach($to as $item){
                    if($item == ''){
                        $result[$j]['totalcomment'] = 0;
                    }else{
                        $result[$j]['totalcomment'] = $item;
                    }
                    $j++;
                }
            }

            $this->context->smarty->assign( array(
                                            'postcategory'=>$result,
                                            'title_category'=>$title_category,
                                            'smartshowauthorstyle'=>Configuration::get('smartshowauthorstyle'),
                                            'limit'=>isset($limit) ? $limit : 0,
                                            'limit_start'=>isset($limit_start) ? $limit_start : 0,
                                            'c'=>isset($c) ? $c : 1,
                                            'total'=>$total,
                                            'smartshowviewed' => Configuration::get('smartshowviewed'),
                                            'smartcustomcss' => Configuration::get('smartcustomcss'),
                                            'smartshownoimg' => Configuration::get('smartshownoimg'),
                                            'smartshowauthor'=>Configuration::get('smartshowauthor'),
                                            'smartblogliststyle' => Configuration::get('smartblogliststyle'),
                                            'post_per_page'=>$posts_per_page,
                                            'smartsearch'=>Tools::getValue('smartsearch'),
                                            'pagenums' => $totalpages - 1,
                                            'totalpages' =>$totalpages
                                            ));

       $template_name  = 'searchresult.tpl';

            $this->setTemplate($template_name);
    }
}
