<?php
/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class SmartBlogLink
{

    /** @var bool Rewriting activation */
    protected $allow;
    protected $url;
    public static $cache = array('page' => array());
    public $protocol_link;
    public $protocol_content;
    protected $ssl_enable;
    protected static $category_disable_rewrite = null;

    /**
     * Constructor (initialization only)
     */
    public function __construct($protocol_link = null, $protocol_content = null)
    {
        $this->allow = (int) Configuration::get('PS_REWRITING_SETTINGS');
        $this->url = $_SERVER['SCRIPT_NAME'];
        $this->protocol_link = $protocol_link;
        $this->protocol_content = $protocol_content;

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }

        /* if (Link::$category_disable_rewrite === null) {
          Link::$category_disable_rewrite = array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'));
          } */

        $this->ssl_enable = Configuration::get('PS_SSL_ENABLED');
    }

    /**
     * Returns a link to a product image for display
     * Note: the new image filesystem stores product images in subdirectories of img/p/
     *
     * @param string $name rewrite link of the image
     * @param string $ids id part of the image filename - can be "id_product-id_image" (legacy support, recommended) or "id_image" (new)
     * @param string $type
     */
    public function getImageLink($name, $ids, $type = null)
    {
        

       if(Configuration::get('smartshownoimg')){

       }
        $not_default = false;
        if(Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')){
            $this->protocol_content = 'https://';
        }


        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_MODULE_SMARTBLOG_DIR_ . $ids . ($type ? '-' . $type : '') . '-' . (int) Context::getContext()->shop->theme_name . '.jpg')) ? '-' . Context::getContext()->shop->theme_name : '');
        if ((Configuration::get('PS_LEGACY_IMAGES') && (file_exists(_MODULE_SMARTBLOG_DIR_ . $ids . ($type ? '-' . $type : '') . $theme . '.jpg'))) || ($not_default = strpos($ids, 'default') !== false)) {
            if ($this->allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__ . 'blog/' . $ids . ($type ? '-' . $type : '') . $theme . '/' . $name . '.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_ . $ids . ($type ? '-' . $type : '') . $theme . '.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $theme = ((Shop::isFeatureActive() && file_exists(_MODULE_SMARTBLOG_DIR_ . Image::getImgFolderStatic($id_image) . $id_image . ($type ? '-' . $type : '') . '-' . (int) Context::getContext()->shop->theme_name . '.jpg')) ? '-' . Context::getContext()->shop->theme_name : '');
            if ($this->allow == 1) {
                $uri_path = __PS_BASE_URI__ . 'blog/' . $id_image . ($type ? '-' . $type : '') . $theme . '/' . $name . '.jpg';
            } else {
                 
                $uri_path = __PS_BASE_URI__ . 'modules/smartblog/images/' . $id_image . ($type ? '-' . $type : '') . $theme . '.jpg';

            }
        }
// echo $this->protocol_content . Tools::getMediaServer($uri_path) . $uri_path.'<br>';
        if (@getimagesize($this->protocol_content . Tools::getMediaServer($uri_path) . $uri_path)) {
            $protocol_content = ($this->ssl_enable) ? 'https://' : 'http://'; 
            $return_val = $protocol_content . Tools::getMediaServer($uri_path) . $uri_path;
        } else {
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            
            $tmp_main_img = __PS_BASE_URI__ . 'modules/smartblog/images/'.$id_image.'.jpg';
            if(@getimagesize($this->protocol_content . Tools::getMediaServer($tmp_main_img) . $tmp_main_img)){
                $posts_types = BlogImageType::GetImageAllType('post');
                foreach ($posts_types as $image_type) {
                    if(stripslashes($image_type['type_name']) == $type){
                        ImageManager::resize(__DIR__."/../images/".$id_image.".jpg", _PS_MODULE_DIR_ . 'smartblog/images/'.$id_image.'-' . stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']);
                    }
                }
            } else {
                if(Configuration::get('smartshownoimg')){
                    $no_img = __PS_BASE_URI__ . 'modules/smartblog/images/no.jpg';
                    if ($this->allow == 1) {
                        $return_val = __PS_BASE_URI__ . 'blog/' . 'no' . ($type ? '-' . $type : '') . '/' . $name . '.jpg';;
                    } else {
                        $return_val = __PS_BASE_URI__ . 'modules/smartblog/images/no' . ($type ? '-' . $type : '') . '.jpg';
                    }
                    $return_val = $this->protocol_content . Tools::getMediaServer($return_val) . $return_val;
                    if (!@getimagesize($return_val) AND @getimagesize($this->protocol_content . Tools::getMediaServer($no_img) . $no_img)) {
                        $posts_types = BlogImageType::GetImageAllType('post');

                        foreach ($posts_types as $image_type) {
                            if(stripslashes($image_type['type_name']) == $type){
                                ImageManager::resize(__DIR__."/../images/no.jpg", _PS_MODULE_DIR_ . 'smartblog/images/no-' . stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']);

                            }
                        }
                    }
                } else {
                    $return_val = "false";
                }
            }
        }

        return (isset($return_val))? $return_val : '';
    }

    public  function getSmartBlogPostLink($blogpost, $alias = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol= false)
    {
        
 
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }


        //$url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);
        $url = smartblog::GetSmartBlogUrl();

        

        $dispatcher = Dispatcher::getInstance();

        if (!is_object($blogpost)) {
            if ($alias !== null && !$dispatcher->hasKeyword('smartblog_post_rule', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('smartblog_post_rule', $id_lang, 'meta_title', $id_shop)) {
                return $url.$dispatcher->createUrl('smartblog_post_rule', $id_lang, array('id_post' => (int)$blogpost,'slug'=>$alias), $this->allow, '', $id_shop);
            }
            $blogpost = new SmartBlogPost($blogpost, $id_lang);
        }

 
        $params = array();
        $params['slug'] = $blogpost->link_rewrite;
        $params['id_post'] = $blogpost->id_smart_blog_post;


 
 
        if ($params != null) {
            return $url . $dispatcher->createUrl('smartblog_post_rule', $id_lang, $params, $this->allow);
        } else {
            $params = array();
            return $url . $dispatcher->createUrl('smartblog_post_rule', $id_lang, $params,  $this->allow);
        }

    }

    public  function getSmartBlogCategoryLink($blogcategory, $alias = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol= false)
    {
       
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        //$url = smartblog::GetSmartBlogUrl();
        $dispatcher = Dispatcher::getInstance();

        if (!is_object($blogcategory)) {
            if ($alias !== null && !$dispatcher->hasKeyword('smartblog_category_rule', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('smartblog_category_rule', $id_lang, 'meta_title', $id_shop)) {

    

                return $url.$dispatcher->createUrl('smartblog_category_rule', $id_lang, array('id_category' => (int)$blogcategory,'slug'=>$alias), $this->allow, '', $id_shop);
            }
            $blogcategory = new BlogCategory($blogcategory, $id_lang);
        }
 
        $params = array();
        $params['slug'] = $blogcategory->link_rewrite;
        $params['id_category'] = $id_category;
        
 

 

        if ($params != null) {
            return $url . $dispatcher->createUrl('smartblog_category_rule', $id_lang, $params, $this->allow);
        } else {
            $params = array();
            return $url . $dispatcher->createUrl('smartblog_category_rule', $id_lang, $params,  $this->allow);
        }
        
    }
    protected function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if ((!$this->allow && in_array($id_shop, array($context->shop->id,  null))) || !Language::isMultiLanguageActivated($id_shop) || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            return '';
        }

        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        return Language::getIsoById($id_lang).'/';
    }
 
     protected function getBaseLink($id_shop = null, $ssl = null, $relative_protocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relative_protocol) {
            $base = '//'.($ssl && $this->ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }

    public  function getSmartBlogCategoryPagination($id_category, $post_link_rewrite, $pageNum)
    {
        $rewrite = 'smartblog_category_pagination';
        $params = array();
        $params['slug'] = $post_link_rewrite;
        $params['id_category'] = $id_category;
        $params['page'] = $pageNum;
        $url = smartblog::GetSmartBlogUrl();
        $dispatcher = Dispatcher::getInstance();
        $id_lang = (int) Context::getContext()->language->id;
 
        if ($params != null) {
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params,  $this->allow);
        } else {
            $params = array();
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params,  $this->allow);
        }
    }
    public  function getSmartBlogListPagination($pageNum)
    {
        $rewrite = 'smartblog_pagination';
        $params = array();
        $params['page'] = $pageNum;
        $url = smartblog::GetSmartBlogUrl();
        $dispatcher = Dispatcher::getInstance();
        $id_lang = (int) Context::getContext()->language->id;
       
        if ($params != null) {
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params,  $this->allow);
        } else {
            $params = array();
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params,  $this->allow);
        }
    }
    
    public function getSmartBlogTag($tagName)
    {
        $rewrite = 'smartblog_tag';
        $params = array();
        $params['tag'] =  str_replace(" ","-",$tagName) ; 
        $url = smartblog::GetSmartBlogUrl();
        $dispatcher = Dispatcher::getInstance();
        $id_lang = (int) Context::getContext()->language->id;
 
        if ($params != null) {
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params, $this->allow);
        } else {
            $params = array();
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params,  $this->allow);
        }
    }
    
    
}
