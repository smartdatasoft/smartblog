<?php

class SmartBlogCategory extends ObjectModel
{
    public $id_smart_blog_category;
    public $id_parent;
    public $position;
    public $desc_limit;
    public $active = 1;
    public $created;
    public $modified;
    public $meta_title;
    public $meta_keyword;
    public $meta_description;
    public $description;
    public $link_rewrite;

    public static $definition = array(
        'table' => 'smart_blog_category',
        'primary' => 'id_smart_blog_category',
        'multilang' => true,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'desc_limit' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'modified' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),

            'meta_title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true,
            ),
            'meta_keyword' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'link_rewrite' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true,
            ),
        ),
    );

    /**
     * SmartBlogCategory constructor.
     * @param int|null $id     SmartBlogCategory ID
     * @param int|null $idLang Language ID
     * @param int|null $idShop Shop ID
     */
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        Shop::addTableAssociation('smart_blog_category', array('type' => 'shop'));

        return parent::__construct($id, $idLang, $idShop);
    }

    /**
     * Get root SmartBlogCategory
     *
     * @param int|null $idLang Language ID
     * @return array|false|mysqli_result|null|PDOStatement|resource Database result
     * @throws PrestaShopDatabaseException
     */
    public static function getRootCategory($idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_category', 'sbc');
        $sql->innerJoin('smart_blog_category_lang', 'sbcl', 'sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category`');
        $sql->innerJoin('smart_blog_category_shop', 'sbcs', 'sbc.`id_smart_blog_category` = sbcs.`id_smart_blog_category`');
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        $sql->where('sbcs.`id_shop` = '.(int) Context::getContext()->shop->id);
        $sql->where('sbc.`active` = 1');
        $sql->where('sbc.`id_parent` = 0');
        $rootCategory = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $rootCategory;
    }

    /**
     * Get category name
     *
     * @param int $id SmartBlogCategory ID
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getCategoryName($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_category_lang', 'sbcl');
        $sql->innerJoin('smart_blog_category', 'sbc', 'sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category`');
        $sql->where('sbc.`id_smart_blog_category` = '.(int) $id);
        $sql->where('sbcl.`id_lang` = '.(int) $id_lang);
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * Get category meta name
     *
     * @param int $idSmartBlogCategory SmartBlogCategory ID
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function getCategoryMetaName($idSmartBlogCategory)
    {
        $idLang = (int) Context::getContext()->language->id;
        $sql = new DbQuery();
        $sql->select('sbc.`meta_title`');
        $sql->from('smart_blog_category_lang', 'sbcl');
        $sql->innerJoin('smart_blog_category', 'sbc', 'sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category`');
        $sql->where('sbc.`id_smart_blog_category` = '.(int) $idSmartBlogCategory);
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * Get category link rewrite
     *
     * @param int $idSmartBlogCategory SmartBlogCategory ID
     * @return bool|string Rewrite part, false if not found
     * @throws PrestaShopDatabaseException
     */
    public static function getCategoryLinkRewrite($idSmartBlogCategory)
    {
        $idLang = (int) Context::getContext()->language->id;
        $sql = new DbQuery();
        $sql->select('sbcl.`link_rewrite`');
        $sql->from('smart_blog_category_lang', 'sbcl');
        $sql->innerJoin('smart_blog_category', 'sbc', 'sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category`');
        $sql->where('sbc.`id_smart_blog_category` = '.(int) $idSmartBlogCategory);
        $sql->where('sbcl.`id_lang` = '.(int) $idLang);
        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result[0]['link_rewrite'];
    }

    /**
     * Get category image
     *
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getCategoryImage()
    {
        $sql = new DbQuery();
        $sql->select('sbc.`id_smart_blog_category`');
        $sql->from('smart_blog_category', 'sbc');

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * Get category
     *
     * @param int  $active
     * @param null $id_lang
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getCategory($active = 1, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_category', 'sbc');
        $sql->innerJoin('smart_blog_category_lang', 'sbcl', 'sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category_lang`');
        $sql->innerJoin('smart_blog_category_shop', 'sbcs', 'sbc.`id_smart_blog_category` = sbcs.`id_smart_blog_category_lang`');
        $sql->where('sbcl.`id_lang` = '.(int) $id_lang);
        $sql->where('sbcs.`id_shop` = '.(int) Context::getContext()->shop->id);
        $sql->where('sbc.`active` = '.(int) $active);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    /**
     * Get SmartBlogCategory name by SmartBlogPost ID
     *
     * @param int $idSmartBlogPost Post ID
     * @return mixed
     * @throws PrestaShopDatabaseException
     */
    public static function getCategoryNameByPost($idSmartBlogPost)
    {
        $sql = new DbQuery();
        $sql->select('sbp.`id_category`');
        $sql->from('smart_blog_post', 'sbp');
        $sql->where('sbp.`id_smart_blog_post` = '.(int) $idSmartBlogPost);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Get post count in category
     *
     * @param $idSmartBlogCategory
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function getPostCountInCategory($idSmartBlogCategory)
    {
        $sql = new DbQuery();
        $sql->select('COUNT(sbp.`id_smart_blog_post`)');
        $sql->from('smart_blog_post', 'sbp');
        $sql->where('sbp.`id_category` = '.(int) $idSmartBlogCategory);

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * Get meta by SmartBlogCategory ID
     *
     * @param int  $idSmartBlogCategory SmartBlogCategory ID
     * @param null $idLang              Language ID
     * @return mixed
     * @throws PrestaShopDatabaseException
     */
    public static function GetMetaByCategory($idSmartBlogCategory, $idLang = null)
    {
        if ($idLang == null) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_category', 'sbc');
        $sql->innerJoin('smart_blog_category_lang', ' sbcl', 'sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category');
        $sql->innerJoin('smart_blog_category_shop', 'sbcs', 'sbc.`id_smart_blog_category` = sbcs.`id_smart_blog_category`');
        $sql->where('sbc.`active` = 1');
        $sql->where('sbcs.`id_shop` = '.(int) Context::getContext()->shop->id);
        $sql->where('sbc.`id_smart_blog_category` = '.(int) $idSmartBlogCategory);

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($result[0]['meta_title'] == '' && $result[0]['meta_title'] == null) {
            $meta['meta_title'] = Configuration::get('smartblogmetatitle');
        } else {
            $meta['meta_title'] = $result[0]['meta_title'];
        }

        if ($result[0]['meta_description'] == '' && $result[0]['meta_description'] == null) {
            $meta['meta_description'] = Configuration::get('smartblogmetadescrip');
        } else {
            $meta['meta_description'] = $result[0]['meta_description'];
        }

        if ($result[0]['meta_keyword'] == '' && $result[0]['meta_keyword'] == null) {
            $meta['meta_keywords'] = Configuration::get('smartblogmetakeyword');
        } else {
            $meta['meta_keywords'] = $result[0]['meta_keyword'];
        }

        return $meta;
    }
}