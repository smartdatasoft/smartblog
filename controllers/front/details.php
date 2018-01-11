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

include_once(dirname(__FILE__) . '/../../classes/controllers/FrontController.php');

class smartblogDetailsModuleFrontController extends smartblogModuleFrontController
{

    public $ssl = false;
    public $_report = '';
    private $_postsObject;
    protected $post;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Tools::getValue('live_edit')) {
            return;
        }

        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
   
        $smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);
        if (Validate::isLoadedObject($this->post) && ($canonicalURL = $smartbloglink->getSmartBlogPostLink($this->post, $this->post->link_rewrite))) {
            parent::canonicalRedirection($canonicalURL);
        } 
    }


    public function init()
    {

        parent::init();
    }

    public function initContent()
    {

        
        $smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');

        //now we will check whihc option we need to url rewrite 
        $id_post = null;
        switch ($smartblogurlpattern) {

            case 1:

                $SmartBlog = new smartblog();
                $slug = Tools::getValue('slug');
                $id_post = $SmartBlog->slug2id($slug);

                break;
            case 2:

                $id_post = pSQL(Tools::getvalue('id_post'));
                break;
            case 3:

                $id_post = pSQL(Tools::getvalue('id_post'));
                break;

            default:
                $id_post = pSQL(Tools::getvalue('id_post'));
        }

        if ($id_post) {

            $this->post = new SmartBlogPost($id_post, true, $this->context->language->id, $this->context->shop->id);

                $meta_title = $this->post->meta_title;
                $meta_description = $this->post->meta_description;
                $meta_keyword = $this->post->meta_keyword;

            if(!$this->post->active) $this->post = array();
            if (new DateTime() >= new DateTime($this->post->created)){
                $published = true;
            } else {
                $this->post = array();
                $published = false;
            }
        }
            
        if (!Validate::isLoadedObject($this->post)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Post not found');
        }
        
        parent::initContent();

      //  $this->canonicalRedirection();

        if (!$this->errors) {
            Hook::exec('actionsbsingle', array('id_post' => $this->post));
            $blogcomment = new Blogcomment();
            $SmartBlogPost = new SmartBlogPost();
            $BlogCategory = new BlogCategory();
            
            $id_lang = $this->context->language->id;
 
            $post = $SmartBlogPost->getPost($id_post, $id_lang);
            
            
            $title_category = array();
            $getPostCategories = $this->getPostCategories($id_post); 
            
            
            $i = 0;
            foreach($getPostCategories as $category){ 
                $title_category[] = $BlogCategory->getNameCategory($getPostCategories[$i]['id_smart_blog_category']); 
                $i++;
            } 
            
            $post['post_img'] = null;//--extra added
            
            $tags = $SmartBlogPost->getProductTags($id_post);
            $comment = $blogcomment->getComment($id_post);
            $countcomment = $blogcomment->getToltalComment($id_post);
            $id_cate = $post['id_category'];
            //$title_category = $BlogCategory->getNameCategory($id_cate);
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $id_post . '.jpg')) {
                $post_img = $id_post; 
            } else {
                $post_img = 'no';
            }

            $posts_previous = SmartBlogPost::getPreviousPostsById($id_lang, $id_post);
            
            $posts_next = SmartBlogPost::getNextPostsById($id_lang, $id_post);
            
             /* Server Params */
            $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
   
            $smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);

            SmartBlogPost::postViewed($id_post);

            //here we can give validation if category page or other page it will show
            $post['date'] =  Smartblog::displayDate($post['created']);

            $this->context->smarty->assign(array(
                'link_rewrite_'=>SmartBlogPost::GetPostSlugById($id_post,$this->context->language->id),
                'displayBackOfficeSmartBlog'=>Hook::exec('displayBackOfficeSmartBlog'),
                'smartbloglink'=> $smartbloglink,
                'baseDir'=>_PS_BASE_URL_.__PS_BASE_URI__,
                'modules_dir'=>_PS_BASE_URL_.__PS_BASE_URI__.'modules/',
                'post' => $post,
                'posts_next' => $posts_next,
                'posts_previous' => $posts_previous,
                'comments' => $comment,
                'enableguestcomment' => Configuration::get('smartenableguestcomment'),
                'is_looged' => $this->context->customer->isLogged(),
                'is_looged_id' => $this->context->customer->id,
                'is_looged_email' => $this->context->customer->email,
                'is_looged_fname' => $this->context->customer->firstname,
                'tags' => $tags,
                //'live_configurator_token' => $this->getLiveConfiguratorToken(),
                //'title_category' => $title_category[0][0]['meta_title'],
                'title_category' => (isset($title_category[0][0]['name']))? $title_category[0][0]['name'] : '',
               'cat_link_rewrite' => (isset($title_category[0][0]['link_rewrite'])) ? $title_category[0][0]['link_rewrite'] : '',
                'meta_title' => $post['meta_title'],
                'post_active' => $post['active'],
                'content' => $post['content'],
                'id_post' => $post['id_post'],
                'smartshowauthorstyle' => Configuration::get('smartshowauthorstyle'),
                'smartshowauthor' => Configuration::get('smartshowauthor'),
                'created' => Smartblog::displayDate($post['created']),
                'firstname' => $post['firstname'],
                'lastname' => $post['lastname'],
                'smartcustomcss' => Configuration::get('smartcustomcss'),
                'smartshownoimg' => Configuration::get('smartshownoimg'),
                'comment_status' => $post['comment_status'],
                'countcomment' => $countcomment,
                'post_img' => $post_img,
                '_report' => $this->_report,
                'id_category' => $post['id_category']
            ));
            $this->context->smarty->assign('HOOK_SMART_BLOG_POST_FOOTER', Hook::exec('displaySmartAfterPost'));
        }
        
        $this->context->smarty->assign('meta_title',$meta_title);
        $this->context->smarty->assign('meta_description',$meta_description);
        $this->context->smarty->assign('meta_keywords',$meta_keyword);

        $this->setTemplate('module:smartblog/views/templates/front/posts.tpl');
    }

      /**
     * Sets default medias for this controller
     */
    public function setMedia()
    {
        parent::setMedia();

        if (!$this->useMobileTheme()) {
            //TODO : check why cluetip css is include without js file
            $this->addCSS(array(
            
                _THEME_CSS_DIR_.'product_list.css' => 'all'
            ));
        }
            
            
    }

    public function _posts()
    {

        $SmartBlogPost = new SmartBlogPost();

        if (Tools::isSubmit('addComment')) {
            $id_lang = $this->context->language->id;
            $id_post = pSQL(Tools::getValue('id_post'));
            $post = $SmartBlogPost->getPost($id_post, $id_lang);
            if ($post['comment_status'] == 1) {
 
                $name = pSQL(Tools::getValue('name'));
                $comment = pSQL(Tools::getValue('comment'));
                $mail = pSQL(Tools::getValue('mail'));
                if (Tools::getValue('mail') == '') {
                    $website = '#';
                } else {
                    $website = pSQL(Tools::getValue('website'));
                }

                $id_parent_post = (int) Tools::getValue('id_parent_post');

                $comments = array();
                
                if (empty($name)) {
                    $this->_report .= '<p class="error">' . $this->module->l('Name is required') . '</p>';
                } elseif (empty($comment)) {
                    $this->_report .= '<p class="error">' . $this->module->l('Comment is required') . '</p>';
                } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    $this->_report .= '<p class="error">' . $this->module->l('E-mail is not valid') . '</p>';
                } else {
                    $comments['name'] = $name;
                    $comments['mail'] = $mail;
                    $comments['comment'] = $comment;
                    $comments['website'] = $website;
                    if (!$id_parent_post = pSQL(Tools::getvalue('comment_parent'))) {
                        $id_parent_post = 0;
                    }
                    $value = Configuration::get('smartacceptcomment');
                    if (Configuration::get('smartacceptcomment') != '' && Configuration::get('smartacceptcomment') != null) {
                        $value = Configuration::get('smartacceptcomment');
                    } else {
                        $value = 0;
                    }
                    $bc = new Blogcomment();
                    $bc->id_post = (int) $id_post;
                    $bc->name = $name;
                    $bc->email = $mail;
                    $bc->content = $comment;
                    $bc->website = $website;
                    $bc->id_parent = (int) $id_parent_post;
                    $bc->active = (int) $value;
                    if ($bc->add()) {
                        $this->_report.='<p class="success">' . $this->module->l('Comment added !') . '</p>';
                        Hook::exec('actionsbpostcomment', array('bc' => $bc));
                        $this->smartsendMail($name, $mail, $comment);
                    }
                }
            }
        }
    }

    private function smartsendMail($sname, $semailAddr, $scomment, $slink = null)
    {
        $name = Tools::stripslashes($sname);
        $e_body = 'You have Received a New Comment In Your Blog Post From ' . $name . '. Comment: ' . $scomment . ' .Your Can reply Here : ' . $slink . '';
        $emailAddr = Tools::stripslashes($semailAddr);
        $comment = Tools::stripslashes($scomment);
        $subject = 'New Comment Posted';
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $to = Configuration::get('PS_SHOP_EMAIL');
        $contactMessage = "
				$comment 
				Name: $name
				IP: " . ((version_compare(_PS_VERSION_, '1.3.0.0', '<')) ? $_SERVER['REMOTE_ADDR'] : Tools::getRemoteAddr());
        if (Mail::Send($id_lang, 'contact', $subject, array(
                    '{message}' => nl2br($e_body),
                    '{email}' => $emailAddr,
                        ), $to, null, $emailAddr, $name
                ))
            return true;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getCover()
    {

        if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $this->post->id . '.jpg')) {
            $post_img = $this->post->id.'.jpg';
        } else {
            $post_img = 'no';
        }
        return $post_img;
    }
    
    public function getPostCategories($id_post){
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_post_category` WHERE id_smart_blog_post =  ' . (int)$id_post;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    public static function getCategoryDetail($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p 
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . pSQL($id) . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return Db::getInstance()->executeS($sql);
    }
   
}
