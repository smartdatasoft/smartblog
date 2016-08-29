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

class BlogPostMeta extends ObjectModel
{

    public $id_smart_blog_post_meta;
    public $id_smart_blog_post;    
    public $meta_key;
    public $meta_value;
   
    public static $definition = array(
        'table' => 'smart_blog_post_meta',
        'primary' => 'id_smart_blog_post_meta',
        'multilang' => false,
        'fields' => array(
            'id_smart_blog_post' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),            
            'meta_key' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true),            
            'meta_value' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),            
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
//        Shop::addTableAssociation('smart_blog_category', array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }
    public static function get($id_post = null, $name = null, $default = false, $id_lang = null)
    {
        if(empty($name)) return false;
//        if(empty($id_post)) $id_post = Tools::getValue('id_smart_blog_post');
        if(empty($id_post)) return false;
        
        if(!empty($id_lang) && Validate::isInt($id_lang)){
            $name .= "_{$id_lang}";
        }
        
        $result = Db::getInstance()->getValue('SELECT `meta_value` FROM `'._DB_PREFIX_."smart_blog_post_meta` WHERE `meta_key`='{$name}' AND `id_smart_blog_post`={$id_post}");
        return !empty($result) ? $result : $default;
    }
    public static function updateValue($id_post, $name, $value = '')
    {
        if(empty($name) || empty($id_post)) return false;
        $db = Db::getInstance();
        $id = $db->getValue('SELECT id_smart_blog_post_meta FROM `'._DB_PREFIX_."smart_blog_post_meta` WHERE `id_smart_blog_post`={$id_post} AND `meta_key`='{$name}'");
        $value = Tools::purifyHTML($value);
        if(!empty($id)){            
            $db->update('smart_blog_post_meta',array('meta_value'=>$value),"`id_smart_blog_post`={$id_post} AND `meta_key`='{$name}'");
        }else{
            $db->insert('smart_blog_post_meta',array(array('id_smart_blog_post'=>$id_post,'meta_key'=>$name,'meta_value'=>$value)), true, false, Db::INSERT_IGNORE);
        }        
        
    }
    
}