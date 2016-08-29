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

class BlogCategory extends ObjectModel
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
 
     /** @var string Name */
    public $name;

    /** @var int Parents number */
    public $level_depth;


    public static $definition = array(
        'table' => 'smart_blog_category',
        'primary' => 'id_smart_blog_category',
        'multilang' => true,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'level_depth' =>        array('type' => self::TYPE_INT),
            'desc_limit' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'modified' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),

            /* Lang fields */

            'name' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'meta_keyword' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),

        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('smart_blog_category', array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }


 
    /**
   * Return available categories
   *
   * @param int $id_lang Language ID
   * @param bool $active return only active categories
   * @return array Categories
   */
    
  public static function getCategories($id_lang, $active = true, $order = true)
  {
    if (!Validate::isBool($active))
      die(Tools::displayError());

    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT *
    FROM `'._DB_PREFIX_.'smart_blog_category` c
    LEFT JOIN `'._DB_PREFIX_.'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`
    WHERE `id_lang` = '.(int)$id_lang.'
    '.($active ? 'AND `active` = 1' : '').'
    ORDER BY `name` ASC');

    if (!$order)
      return $result;

    $categories = array();
    foreach ($result as $row)
      $categories[$row['id_parent']][$row['id_smart_blog_category']]['infos'] = $row;
    return $categories;
  }


  public static function recurseCMSCategory($categories, $current, $id_cms_category = 1, $id_selected = 1, $is_html = 0)
  {
    $html = '<option value="'.$id_cms_category.'"'.(($id_selected == $id_cms_category) ? ' selected="selected"' : '').'>'
      .str_repeat('&nbsp;', $current['infos']['level_depth'] * 5)
      .BlogCategory::hideBlogCategoryPosition(stripslashes($current['infos']['name'])).'</option>';
    if ($is_html == 0)
      echo $html;
    if (isset($categories[$id_cms_category]))
      foreach (array_keys($categories[$id_cms_category]) as $key)
        $html .= BlogCategory::recurseCMSCategory($categories, $categories[$id_cms_category][$key], $key, $id_selected, $is_html);
    return $html;
  }

  /**
   * Hide CMSCategory prefix used for position
   *
   * @param string $name CMSCategory name
   * @return string Name without position
   */
  public static function hideBlogCategoryPosition($name)
  {
    return preg_replace('/^[0-9]+\./', '', $name);
  }
    public static function getNameCategory($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p 
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result;
    }

    public static function getCatName($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = 'SELECT pl.name FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p 
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result[0]['name'];
    }

    public static function getCatLinkRewrite($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = 'SELECT pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p 
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result[0]['link_rewrite'];
    }

    public static function getCatImage()
    {
 
        $sql = 'SELECT id_smart_blog_category FROM ' . _DB_PREFIX_ . 'smart_blog_category';

        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result;
    }

    public static function getCategory($active = 1, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        
        $sorting = Configuration::get('sort_category_by');
        if($sorting == 'name_ASC'){
            $orderby = 'sbcl.name';
            $orderway = 'ASC';
        }elseif($sorting == 'name_DESC'){
            $orderby = 'sbcl.name';
            $orderway = 'DESC';
        }elseif($sorting == 'id_ASC'){
            $orderby = 'sbc.id_smart_blog_category';
            $orderway = 'ASC';
        }else{
            $orderby = 'sbc.id_smart_blog_category';
            $orderway = 'DESC';
        }
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ($id_lang) . ')
		INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and sbs.id_shop = ' . (int) Context::getContext()->shop->id . ' WHERE sbc.`active`= '.$active.' ORDER BY '.$orderby.' '.$orderway);

        return $result;
    }

    public static function getCategoryNameByPost($id_post)
    {

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.id_category FROM `' . _DB_PREFIX_ . 'smart_blog_post` p where p.id_smart_blog_post =  ' . $id_post);

        return $result[0]['id_category'];
    }

    public static function getPostByCategory($id_smart_blog_category)
    {
        $sql = 'select count(id_smart_blog_post) as count from `' . _DB_PREFIX_ . 'smart_blog_post` where id_category = ' . $id_smart_blog_category;

        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result[0]['count'];
    }

    public static function GetMetaByCategory($id_category, $id_lang = null)
    {
        $meta = array(); 
        
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` '
                . 'sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ($id_lang) . ')
                    INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and'
                . ' sbs.id_shop = ' . (int) Context::getContext()->shop->id . ' WHERE sbc.`active`= 1 and sbc.id_smart_blog_category = ' . $id_category);

        if ($result[0]['meta_title'] == '' && $result[0]['meta_title'] == NULL) {
            $meta['meta_title'] = Configuration::get('smartblogmetatitle');
        } else {
            $meta['meta_title'] = $result[0]['meta_title'];
        }

        if ($result[0]['meta_description'] == '' && $result[0]['meta_description'] == NULL) {
            $meta['meta_description'] = Configuration::get('smartblogmetadescrip');
        } else {
            $meta['meta_description'] = $result[0]['meta_description'];
        }

        if ($result[0]['meta_keyword'] == '' && $result[0]['meta_keyword'] == NULL) {
            $meta['meta_keywords'] = Configuration::get('smartblogmetakeyword');
        } else {
            $meta['meta_keywords'] = $result[0]['meta_keyword'];
        }

        return $meta;
    }

    /* @static
     * @param null $id_lang
     * @return Category
     */

    public static function getTopCategory($id_lang = null)
    {
        if (is_null($id_lang))
            $id_lang = (int) Context::getContext()->language->id;
        $cache_id = 'BlogCategory::getTopCategory_' . (int) $id_lang;
       if (!Cache::isStored($cache_id))
       {
        $id_category = (int) Db::getInstance()->getValue('
      SELECT `id_smart_blog_category`
      FROM `' . _DB_PREFIX_ . 'smart_blog_category`
      WHERE `id_parent` = 0');

       
        Cache::store($cache_id, new BlogCategory($id_category, $id_lang));
        }
       // return new BlogCategory($id_category, $id_lang);
        return Cache::retrieve($cache_id);
    }
    
    /**
     * New methods for category mega checkbox
     */
    public static function getRootCategory($id_lang = null) {
        if ($id_lang == NULL)
            $id_lang = (int) Context::getContext()->language->id;
        $root_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                            SELECT *, sbc.`id_smart_blog_category` AS `id_category`, sbcl.`name` 
                            FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl 
                                ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ($id_lang) . ')
            INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs 
                ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and sbs.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                    WHERE sbc.`active`= 1 AND sbc.`id_parent` = 0');
        
        return isset($root_category[0]) ? $root_category[0] : array();
    }

    public static function getChildren($id_cat, $id_lang = null, $active = true) {
        if ($id_lang == NULL)
            $id_lang = (int) Context::getContext()->language->id;
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *, sbc.`id_smart_blog_category` AS `id_category`, sbcl.`name`  FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc 
                INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl 
                    ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ($id_lang) . ')
                ' . Shop::addSqlAssociation('smart_blog_category', 'sbc') . '                 
                    WHERE 1 
                    ' . ($active ? 'AND sbc.`active`= 1 ' : '') . ' 
                    AND sbc.id_parent = ' . $id_cat);
        return $results;
    }

    /**
     *
     * @param int  $id_parent
     * @param int  $id_lang
     * @param bool $active
     * @param bool $id_shop
     * @return array
     */
    public static function hasChildren($id_parent, $id_lang, $active = true, $id_shop = false) {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cache_id = 'BlogCategory::hasChildren_' . (int) $id_parent . '-' . (int) $id_lang . '-' . (bool) $active . '-' . (int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $query = 'SELECT c.`id_smart_blog_category` AS id_category, "" as name
			FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON (c.`id_smart_blog_category` = cl.`id_smart_blog_category`)
			' . Shop::addSqlAssociation('smart_blog_category', 'c') . '
			WHERE `id_lang` = ' . (int) $id_lang . '
			AND c.`id_parent` = ' . (int) $id_parent . '
			' . ($active ? 'AND `active` = 1' : '') . ' LIMIT 1';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query, true, false);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     *
     * @param Array $ids_category
     * @param int $id_lang
     * @return Array
     */
    public static function getCategoryInformations($ids_category, $id_lang = null) {
        if ($id_lang === null) {
            $id_lang = Context::getContext()->language->id;
        }

        if (!is_array($ids_category) || !count($ids_category)) {
            return;
        }

        $categories = array();
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_smart_blog_category` AS `id_category`, cl.`name` , cl.`link_rewrite`, cl.`id_lang`
		FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON (c.`id_smart_blog_category` = cl.`id_smart_blog_category`)
		' . Shop::addSqlAssociation('smart_blog_category', 'c') . '
		WHERE cl.`id_lang` = ' . (int) $id_lang . '
		AND c.`id_smart_blog_category` IN (' . implode(',', array_map('intval', $ids_category)) . ')');

        foreach ($results as $category) {
            $categories[$category['id_category']] = $category;
        }

        return $categories;
    }

    public static function getAllCategoriesName($root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '') {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

//        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
//            $groups = (array)$groups;
//        }

        $cache_id = 'BlogCategory::getAllCategoriesName_' . md5((int) $root_category . (int) $id_lang . (int) $active . (int) $use_shop_restriction
                        . (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
				SELECT c.`id_smart_blog_category` AS `id_category`, cl.`name` 
				FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
				' . ($use_shop_restriction ? Shop::addSqlAssociation('smart_blog_category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`
				
				' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'smart_blog_category` c2 ON c2.`id_smart_blog_category` = ' . (int) $root_category . ' ' : '') . '
				WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int) $id_lang : '') . '
				' . ($active ? ' AND c.`active` = 1' : '') . '				
				' . ($sql_limit != '' ? $sql_limit : '')
            );

            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    /**
     * Get Each parent category of this category until the root category
     *
     * @param int $id_lang Language ID
     * @return array Corresponding categories
     */
    public function getParentsCategories($id_lang = null) {
        $context = Context::getContext()->cloneContext();
        $context->shop = clone($context->shop);

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        $categories = null;
        $id_current = $this->id;
//        if (count(Category::getCategoriesWithoutParent()) > 1 && Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) != 1) {
//            $context->shop->id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
//        } elseif (!$context->shop->id) {
//            $context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
//        }
        $id_shop = $context->shop->id;
        while (true) {
            $sql = '
			SELECT c.*,c.`id_smart_blog_category` AS `id_category`, cl.*, cl.`name`  
			FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl
				ON (c.`id_smart_blog_category` = cl.`id_smart_blog_category`
				AND `id_lang` = ' . (int) $id_lang . ')';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` cs ON (c.`id_smart_blog_category` = cs.`id_smart_blog_category` AND cs.`id_shop` = ' . (int) $id_shop . ')';
            }
            $sql .= ' WHERE c.`id_smart_blog_category` = ' . (int) $id_current;
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND cs.`id_shop` = ' . (int) $context->shop->id;
            }
            $root_category = self::getRootCategory();
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP && (!Tools::isSubmit('id_smart_blog_category') || (int) Tools::getValue('id_smart_blog_category') == (int) $root_category['id_smart_blog_category'])) {
                $sql .= ' AND c.`id_parent` != 0';
            }

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

            if ($result) {
                $categories[] = $result;
            } elseif (!$categories) {
                $categories = array();
            }
            if (!$result || ($result['id_category'] == $context->shop->id_category)) {
                return $categories;
            }
            $id_current = $result['id_parent'];
        }
    }
    public static function getNestedCategories($root_category = null, $id_lang = false, $active = true, $groups = null,
        $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

//        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
//            $groups = (array)$groups;
//        }

        $cache_id = 'BlogCategory::getNestedCategories_'.md5((int)$root_category.(int)$id_lang.(int)$active.(int)$use_shop_restriction);

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
				SELECT c.*, cl.*, c.`id_smart_blog_category` AS `id_category`, cl.`name` AS `name`
				FROM `'._DB_PREFIX_.'smart_blog_category` c
				'.($use_shop_restriction ? Shop::addSqlAssociation('smart_blog_category', 'c') : '').'
				LEFT JOIN `'._DB_PREFIX_.'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`				
				WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND cl.`id_lang` = '.(int)$id_lang : '').'
				'.($active ? ' AND c.`active` = 1' : '').'				
				'.($sql_limit != '' ? $sql_limit : '')
            );

            $categories = array();
            $buff = array();

            if (!isset($root_category)) {
                $root_category = self::getRootCategory();
                $root_category = $root_category['id_category'];
            }

            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_category'] == $root_category) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cache_id, $categories);
        } else {
            $categories = Cache::retrieve($cache_id);
        }

        return $categories;
    }
    public static function getPostCategoriesFull($id_post = '', $id_lang = null) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $ret = array();
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_smart_blog_category` AS `id_category`, cl.`name` , cl.`link_rewrite` 
                        FROM `' . _DB_PREFIX_ . 'smart_blog_post_category` cp
			LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category` c ON (c.id_smart_blog_category = cp.id_smart_blog_category)
			LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON (cp.`id_smart_blog_category` = cl.`id_smart_blog_category`)
			' . Shop::addSqlAssociation('smart_blog_category', 'c') . '
			WHERE cp.`id_smart_blog_post` = ' . (int) $id_post . '
				AND cl.`id_lang` = ' . (int) $id_lang
        );

        foreach ($row as $val) {
            $ret[$val['id_category']] = $val;
        }

        return $ret;
    }

    public static function updateAssocCat($id_post) {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete('smart_blog_post_category', "id_smart_blog_post={$id_post}");


        $cats = $insert = array();
//        $cats = array(1);
        if (Tools::isSubmit('categoryBox')) {
//            $cats = array_merge($cats, Tools::getValue('categoryBox'));
            $cats = Tools::getValue('categoryBox');
            if(is_array($cats)){
                foreach ($cats as $cat) {
                    $insert[] = array(
                        'id_smart_blog_category' => $cat,
                        'id_smart_blog_post' => $id_post,
                    );
                }


                Db::getInstance(_PS_USE_SQL_SLAVE_)->insert('smart_blog_post_category', $insert, false, false, Db::INSERT_IGNORE);
            }
        }

        return true;
    }

    /**
     * Check if CMSCategory can be moved in another one
     *
     * @param int $id_parent Parent candidate
     * @return bool Parent validity
     */
    public static function checkBeforeMove($id_cms_category, $id_parent)
    {
        if ($id_cms_category == $id_parent) {
            return false;
        }
        if ($id_parent == 1) {
            return true;
        }
        $i = (int)$id_parent;

        while (42) {
            $result = Db::getInstance()->getRow('SELECT `id_parent` FROM `'._DB_PREFIX_.'smart_blog_category` WHERE `id_smart_blog_category` = '.(int)$i);
            if (!isset($result['id_parent'])) {
                return false;
            }
            if ($result['id_parent'] == $id_cms_category) {
                return false;
            }
            if ($result['id_parent'] == 1) {
                return true;
            }
            $i = $result['id_parent'];
        }
    }
}
