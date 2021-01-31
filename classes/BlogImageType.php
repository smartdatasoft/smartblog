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

class BlogImageType extends ObjectModel
{

    public $id_smart_blog_imagetype;
    public $type_name;
    public $width;
    public $height;
    public $type;
    public $active = 1;
    public static $definition = array(
        'table' => 'smart_blog_imagetype',
        'primary' => 'id_smart_blog_imagetype',
        'multilang' => false,
        'fields' => array(
            'width' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'height' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'type_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
        ),
    );

    public static function GetImageAllType($type)
    {
        $img_type = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                                    SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_imagetype` where `active` = 1 and `type` = "' . $type . '"');
        return $img_type;
    }

    public static function GetImageAll()
    {
        $img_type = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                                    SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_imagetype` where active = 1');
        return $img_type;
    }

    public static function GetImageByType($type_name)
    {
        $img_type = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                                    SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_imagetype` where active = 1 and type_name = "' . $type_name . '"');
        return $img_type;
    }

    public static function ImageGenerate()
    {

        $get_blog_image = SmartBlogPost::getBlogImage();
        $get_cate_image = BlogCategory::getCatImage();
        $get_author_image = _PS_MODULE_DIR_ . 'smartblog/images/avatar/avatar.jpg';
        $category_types = BlogImageType::GetImageAllType('Category');
        $posts_types = BlogImageType::GetImageAllType('post');
        $author_types = BlogImageType::GetImageAllType('Author');

        foreach ($category_types as $image_type) {
            foreach ($get_cate_image as $cat_img) {
                $path = _PS_MODULE_DIR_ . 'smartblog/images/category/' . $cat_img['id_smart_blog_category'] . '.jpg';
                ImageManager::resize($path, _PS_MODULE_DIR_ . 'smartblog/images/category/' . $cat_img['id_smart_blog_category'] . '-' . Tools::stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']
                );
            }
        }
        foreach ($posts_types as $image_type) {
            foreach ($get_blog_image as $blog_img) {

                $path = _PS_MODULE_DIR_ . 'smartblog/images/' . $blog_img['id_smart_blog_post'] . '.jpg';
                ImageManager::resize($path, _PS_MODULE_DIR_ . 'smartblog/images/' . $blog_img['id_smart_blog_post'] . '-' . Tools::stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']
                );
            }
        }
        foreach ($author_types as $author_type) {
            ImageManager::resize($get_author_image, _PS_MODULE_DIR_ . 'smartblog/images/avatar/avatar-' . Tools::stripslashes($author_type['type_name']) . '.jpg', (int) $author_type['width'], (int) $author_type['height']
            );
        }
    }

    public static function ImageDelete()
    {

        $get_blog_image = SmartBlogPost::getBlogImage();
        $get_cate_image = BlogCategory::getCatImage();
        $category_types = BlogImageType::GetImageAllType('category');
        $posts_types = BlogImageType::GetImageAllType('post');
        $author_types = BlogImageType::GetImageAllType('author');
        foreach ($category_types as $image_type) {
            foreach ($get_cate_image as $cat_img) {

                $dir = _PS_MODULE_DIR_ . 'smartblog/images/category/' . $cat_img['id_smart_blog_category'] . '-' . Tools::stripslashes($image_type['type_name']) . '.jpg';
                if (file_exists($dir))
                    unlink($dir);
            }
        }
        foreach ($posts_types as $image_type) {
            foreach ($get_blog_image as $blog_img) {

                $dir = _PS_MODULE_DIR_ . 'smartblog/images/' . $blog_img['id_smart_blog_post'] . '-' . Tools::stripslashes($image_type['type_name']) . '.jpg';
                if (file_exists($dir))
                    unlink($dir);
            }
        }
        foreach ($author_types as $image_type) {
            $dir = _PS_MODULE_DIR_ . 'smartblog/images/avatar/avatar-' . Tools::stripslashes($image_type['type_name']) . '.jpg';
            if (file_exists($dir))
                unlink($dir);
        }
    }

}
