<?php

class SmartBlogTag extends ObjectModel
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
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        ),
    );

    public static function TagExists($tag, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = 'SELECT id_tag FROM '._DB_PREFIX_.'smart_blog_tag WHERE id_lang='.(int) $id_lang.' AND name="'.pSQL($tag).'"';

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $posts[0]['id_tag'];
    }
}