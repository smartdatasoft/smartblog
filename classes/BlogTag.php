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

class BlogTag extends ObjectModel
{

    public $id_tag;
    public $id_lang;
    public $name;
    public static $definition = array(
        'table' => 'smart_blog_tag',
        'primary' => 'id_tag',
        'multilang' => false,
        'fields' => array(
            'id_tag' => array('type' => self::TYPE_BOOL, 'validate' => 'isunsignedInt'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString')
        ),
    );

    public static function TagExists($tag, $id_lang = null)
    {
        if ($id_lang == null)
            $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT id_tag FROM ' . _DB_PREFIX_ . 'smart_blog_tag WHERE id_lang=' . $id_lang . ' AND name="' . $tag . '"';

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return $posts[0]['id_tag'];
    }

}
