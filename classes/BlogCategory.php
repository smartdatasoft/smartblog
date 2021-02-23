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

class BlogCategory extends ObjectModel {

	public $id;
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
		'table'     => 'smart_blog_category',
		'primary'   => 'id_smart_blog_category',
		'multilang' => true,
		'fields'    => array(
			'id_parent'        => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			),
			'position'         => array( 'type' => self::TYPE_INT ),
			'level_depth'      => array( 'type' => self::TYPE_INT ),
			'desc_limit'       => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
			),
			'active'           => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
			),
			'created'          => array(
				'type'     => self::TYPE_DATE,
				'validate' => 'isString',
			),
			'modified'         => array(
				'type'     => self::TYPE_DATE,
				'validate' => 'isString',
			),

			/* Lang fields */

			'name'             => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isCatalogName',
				'required' => true,
				'size'     => 64,
			),
			'link_rewrite'     => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isLinkRewrite',
				'required' => true,
			),
			'meta_title'       => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isGenericName',
			),
			'meta_keyword'     => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isGenericName',
				'lang'     => true,
			),
			'meta_description' => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isGenericName',
			),
			'description'      => array(
				'type'     => self::TYPE_HTML,
				'lang'     => true,
				'validate' => 'isCleanHtml',
			),

		),
	);

	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {
		Shop::addTableAssociation( 'smart_blog_category', array( 'type' => 'shop' ) );
		parent::__construct( $id, $id_lang, $id_shop );
	}

	public function add( $autodate = true, $null_values = false ) {
		$this->position    = self::getLastPosition( (int) $this->id_parent );
		$this->level_depth = $this->calcLevelDepth();
		foreach ( $this->name as $k => $value ) {
			if ( preg_match( '/^[1-9]\./', $value ) ) {
				$this->name[ $k ] = '0' . $value;
			}
		}
		$ret = parent::add( $autodate, $null_values );
		$this->cleanPositions( $this->id_parent );
		return $ret;
	}

	public function update( $null_values = false ) {

		$this->level_depth = $this->calcLevelDepth();
		foreach ( $this->name as $k => $value ) {
			if ( preg_match( '/^[1-9]\./', $value ) ) {
				$this->name[ $k ] = '0' . $value;
			}
		}
		return parent::update( $null_values );
	}

	public function delete() {
		if ( $this->id == 1 ) {
			return false;
		}

		$this->clearCache();

		// Get children categories
		$to_delete = array( (int) $this->id );
		$this->recursiveDelete( $to_delete, (int) $this->id );
		$to_delete = array_unique( $to_delete );

		// Delete CMS Category and its child from database
		$list         = count( $to_delete ) > 1 ? implode( ',', $to_delete ) : (int) $this->id;
		$id_shop_list = Shop::getContextListShopID();
		if ( count( $this->id_shop_list ) ) {
			$id_shop_list = $this->id_shop_list;
		}

		Db::getInstance()->delete( $this->def['table'] . '_shop', '`' . $this->def['primary'] . '` IN (' . $list . ') AND id_shop IN (' . implode( ', ', $id_shop_list ) . ')' );

		$has_multishop_entries = $this->hasMultishopEntries();
		if ( ! $has_multishop_entries ) {
			Db::getInstance()->execute( 'DELETE FROM `' . _DB_PREFIX_ . 'smart_blog_category` WHERE `id_smart_blog_category` IN (' . $list . ')' );
			Db::getInstance()->execute( 'DELETE FROM `' . _DB_PREFIX_ . 'smart_blog_category_lang` WHERE `id_smart_blog_category` IN (' . $list . ')' );
		}

		$this->cleanPositions( $this->id_parent );

		return true;
	}

	protected function recursiveDelete( &$to_delete, $id_smart_blog_category ) {
		if ( ! is_array( $to_delete ) || ! $id_smart_blog_category ) {
			die( Tools::displayError() );
		}

		$result = Db::getInstance()->executeS(
			'
        SELECT `id_smart_blog_category`
        FROM `' . _DB_PREFIX_ . 'smart_blog_category`
        WHERE `id_parent` = ' . (int) $id_smart_blog_category
		);
		foreach ( $result as $row ) {
			$to_delete[] = (int) $row['id_smart_blog_category'];
			$this->recursiveDelete( $to_delete, (int) $row['id_smart_blog_category'] );
		}
	}

	public function calcLevelDepth() {
		$parentCategory = new BlogCategory( $this->id_parent );
		if ( ! $parentCategory ) {
			die( 'parent CMS Category does not exist' );
		}
		return $parentCategory->level_depth + 1;
	}

	public static function getLastPosition( $id_category_parent ) {
		return ( Db::getInstance()->getValue( 'SELECT MAX(position)+1 FROM `' . _DB_PREFIX_ . 'smart_blog_category` WHERE `id_parent` = ' . (int) $id_category_parent ) );
	}

	public static function cleanPositions( $id_category_parent ) {
		$result = Db::getInstance()->executeS(
			'
    SELECT `id_smart_blog_category`
    FROM `' . _DB_PREFIX_ . 'smart_blog_category`
    WHERE `id_parent` = ' . (int) $id_category_parent . '
    ORDER BY `position`'
		);
		$sizeof = count( $result );
		for ( $i = 0; $i < $sizeof; ++$i ) {
			$sql = '
      UPDATE `' . _DB_PREFIX_ . 'smart_blog_category`
      SET `position` = ' . (int) $i . '
      WHERE `id_parent` = ' . (int) $id_category_parent . '
      AND `id_smart_blog_category` = ' . (int) $result[ $i ]['id_smart_blog_category'];
			Db::getInstance()->execute( $sql );
		}
		return true;
	}

	public static function getCategories( $id_lang, $active = true, $order = true ) {
		if ( ! Validate::isBool( $active ) ) {
			die( Tools::displayError() );
		}

		$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
            SELECT *
            FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`
            WHERE `id_lang` = ' . (int) $id_lang . '
            ' . ( $active ? 'AND `active` = 1' : '' ) . '
            ORDER BY `meta_title` ASC'
		);

		if ( ! $order ) {
			return $result;
		}

		$categories = array();
		foreach ( $result as $row ) {
			$categories[ $row['id_parent'] ][ $row['id_smart_blog_category'] ]['infos'] = $row;
		}

		return $categories;
	}

	public static function getCatImage() {

		$sql = 'SELECT id_smart_blog_category FROM ' . _DB_PREFIX_ . 'smart_blog_category';

		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $result;
	}

	public static function hideCMSBlogCategoryPosition( $name ) {
		return preg_replace( '/^[0-9]+\./', '', $name );
	}

	public static function hideQuickBlogCategoryPosition( $name ) {
		return preg_replace( '/^[0-9]+\./', '', $name );
	}

	public static function getChildCategoriesByParentId( $id_parent, $id_lang, $active = true, $order = true ) {
		if ( ! Validate::isBool( $active ) ) {
			die( Tools::displayError() );
		}

		$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
            SELECT *
            FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`
            WHERE `id_parent` = ' . (int) $id_parent . '
            AND `id_lang` = ' . (int) $id_lang . '
            ' . ( $active ? 'AND `active` = 1' : '' ) . '
            ORDER BY `meta_title` ASC'
		);

		if ( ! $order ) {
			return $result;
		}

		$categories = array();
		foreach ( $result as $row ) {
			$categories[ $row['id_parent'] ][ $row['id_smart_blog_category'] ]['infos'] = $row;
		}

		return $categories;
	}

	public static function getCategoryQuickAccess( $id_parent, $current ) {
		$context         = Context::getContext();
		$categories      = self::getChildCategoriesByParentId( $id_parent, $context->language->id, 1 );
		$html_categories = '';
		foreach ( $categories as $key_one => $value_one ) {
			foreach ( $value_one as $key_two => $value_two ) {
				$child_cat = self::getChildCategoriesByParentId( $value_two['infos']['id_smart_blog_category'], $context->language->id, 1 );

				$smartbloglink = new SmartBlogLink();

				$tmp_rewrite = $smartbloglink->getSmartBlogCategoryLink( $value_two['infos']['id_smart_blog_category'], $value_two['infos']['link_rewrite'] );

				$tmp_count = self::getPostByCategory( $value_two['infos']['id_smart_blog_category'] );

				$tmp_all_child = $value_two['infos']['id_smart_blog_category'] . self::getAllChildCategory( $value_two['infos']['id_smart_blog_category'], '' );

				$tmp_all_child = array_values( array_unique( explode( ',', $tmp_all_child ) ) );

				$tmp_post_of_child = self::getTotalPostOfChildParent( $tmp_all_child );

				if ( Configuration::get( 'SMART_BLOG_ASSIGNED_CATEGORIES_ONLY' ) ) {
					if ( $tmp_post_of_child == 0 ) {
						continue 1;
					}
				}

				$html_categories .= '<li>';

				$grower       = '';
				$grower_style = '';
				if ( Configuration::get( 'SMART_BLOG_CATEGORIES_DHTML' ) ) {
					$grower       = '<span class="grower CLOSE"> </span>';
					$grower_style = 'style="display: block;"';
				}

				$html_categories .= ( count( $child_cat ) > 0 ) ? $grower : '';

				$html_categories .= '<a href="' . $tmp_rewrite . '">';
				if ( $value_two['infos']['level_depth'] > 1 ) {
					$html_categories .= str_repeat( '&nbsp;', $value_two['infos']['level_depth'] * 4 );
				}
				$html_categories .= $value_two['infos']['name'];
				$html_categories .= ( Configuration::get( 'SMART_BLOG_CATEGORIES_POST_COUNT' ) ) ? ' (' . $tmp_post_of_child . ')' : '';
				$html_categories .= '</a>';

				if ( count( $child_cat ) > 0 ) {
					$html_categories .= '<ul ' . $grower_style . '>';
					$html_categories .= self::getCategoryQuickAccess( $value_two['infos']['id_smart_blog_category'], $html_categories );
					$html_categories .= '</li>';
					$html_categories .= '</ul>';
				} else {
					// $html_categories .= '</ul>';
				}
				$html_categories .= '</li>';
			}
		}

		return $html_categories;
	}

	public static function getTotalPostOfChildParent( $all_child = array() ) {
		$total_post = 0;
		if ( is_array( $all_child ) ) {
			foreach ( $all_child as $key => $child_id ) {
				$total_post = (int) self::getPostByCategory( $child_id );
			}
		}
		return $total_post;
	}

	public static function getAllChildCategory( $id_smart_blog_category, $current ) {
		$sql = 'select id_smart_blog_category from `' . _DB_PREFIX_ . 'smart_blog_category` where id_smart_blog_category = ' . $id_smart_blog_category;

		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		// echo '<br>for '.$id_smart_blog_category;
		foreach ( $result as $key => $value ) {
			$current .= ',' . $value['id_smart_blog_category'];
			if ( self::haveChildCategory( $value['id_smart_blog_category'] ) > 1 ) {
				$current .= self::getAllChildCategory( $value['id_smart_blog_category'], $current );
			}
		}

		// if(BlogCategory::haveChildCategory($id_smart_blog_category) > 1)
		// $current[] = BlogCategory::getAllChildCategory($id_smart_blog_category, $current);

		return $current;
	}

	public static function haveChildCategory( $id_smart_blog_category ) {
		$sql = 'select count(id_smart_blog_category) as count from `' . _DB_PREFIX_ . 'smart_blog_category` where id_parent = ' . $id_smart_blog_category;

		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		return $result[0]['count'];
	}

	public static function recurseCMSCategoryClickToGo( $categories, $current, $id_smart_blog_category = 1, $id_selected = 1, $is_html = 0 ) {

		$smartbloglink = new SmartBlogLink();

		$tmp_rewrite = ( isset( $current['infos']['id_smart_blog_category'] ) and isset( $current['infos']['link_rewrite'] ) ) ? $smartbloglink->getSmartBlogCategoryLink( $current['infos']['id_smart_blog_category'], $current['infos']['link_rewrite'] ) : '';

		$html = '<option value="' . $tmp_rewrite . '"' . ( ( $id_selected == $id_smart_blog_category ) ? ' selected="selected"' : '' ) . '>'
			. str_repeat( '&nbsp;', $current['infos']['level_depth'] * 5 )
			. self::hideCMSBlogCategoryPosition( stripslashes( $current['infos']['name'] ) ) . '</option>';
		if ( $is_html == 0 ) {
			echo $html;
		}
		if ( isset( $categories[ $id_smart_blog_category ] ) ) {
			foreach ( array_keys( $categories[ $id_smart_blog_category ] ) as $key ) {
				$html .= self::recurseCMSCategoryClickToGo( $categories, $categories[ $id_smart_blog_category ][ $key ], $key, $id_selected, $is_html );
			}
		}
		return $html;
	}

	public static function recurseCMSCategory( $categories, $current, $id_smart_blog_category = 1, $id_selected = 1, $is_html = 0 ) {
		$html = '<option value="' . $id_smart_blog_category . '"' . ( ( $id_selected == $id_smart_blog_category ) ? ' selected="selected"' : '' ) . '>'
			. str_repeat( '&nbsp;', $current['infos']['level_depth'] * 5 )
			. self::hideCMSBlogCategoryPosition( stripslashes( $current['infos']['name'] ) ) . '</option>';
		if ( $is_html == 0 ) {
			echo $html;
		}
		if ( isset( $categories[ $id_smart_blog_category ] ) ) {
			foreach ( array_keys( $categories[ $id_smart_blog_category ] ) as $key ) {
				$html .= self::recurseCMSCategory( $categories, $categories[ $id_smart_blog_category ][ $key ], $key, $id_selected, $is_html );
			}
		}
		return $html;
	}

	public static function checkBeforeMove( $id_smart_blog_category, $id_parent ) {
		if ( $id_smart_blog_category == $id_parent ) {
			return false;
		}
		if ( $id_parent == 1 ) {
			return true;
		}
		$i = (int) $id_parent;

		while ( 42 ) {
			$result = Db::getInstance()->getRow( 'SELECT `id_parent` FROM `' . _DB_PREFIX_ . 'smart_blog_category` WHERE `id_smart_blog_category` = ' . (int) $i );
			if ( ! isset( $result['id_parent'] ) ) {
				return false;
			}
			if ( $result['id_parent'] == $id_smart_blog_category ) {
				return false;
			}
			if ( $result['id_parent'] == 1 ) {
				return true;
			}
			$i = $result['id_parent'];
		}
	}
	public static function getPath( $url_base, $id_category, $path = '', $highlight = '', $category_type = 'smartblog', $home = false ) {
		$context = Context::getContext();
		if ( $category_type == 'smartblog' ) {
			$category = new BlogCategory( $id_category, $context->language->id );

			if ( ! $category->id ) {
				return $path;
			}

			$name = ( $highlight != null ) ? str_ireplace( $highlight, '<span class="highlight">' . $highlight . '</span>', self::hideCMSBlogCategoryPosition( $category->name ) ) : self::hideCMSBlogCategoryPosition( $category->name );
			$edit = '<a href="' . Tools::safeOutput( $url_base . '&id_smart_blog_category=' . $category->id . '&updatesmart_blog_category&token=' . Tools::getAdminToken( 'AdminBlogCategory' . (int) Tab::getIdFromClassName( 'AdminBlogCategory' ) . (int) $context->employee->id ) ) . '">
                    <i class="icon-pencil"></i></a> ';
			if ( $category->id == 1 ) {
				$edit = '<li><a href="' . Tools::safeOutput( $url_base . '&id_smart_blog_category=' . $category->id . '&viewsmart_blog_category&token=' . Tools::getAdminToken( 'AdminBlogCategory' . (int) Tab::getIdFromClassName( 'AdminBlogCategory' ) . (int) $context->employee->id ) ) . '">
                        <i class="icon-home"></i></a></li> ';
			}
			$path = $edit . '<li><a href="' . Tools::safeOutput( $url_base . '&id_smart_blog_category=' . $category->id . '&viewsmart_blog_category&token=' . Tools::getAdminToken( 'AdminBlogCategory' . (int) Tab::getIdFromClassName( 'AdminBlogCategory' ) . (int) $context->employee->id ) ) . '">
            ' . $name . '</a></li> > ' . $path;
			if ( $category->id == 1 ) {
				return substr( $path, 0, strlen( $path ) - 3 );
			}
			return self::getPath( $url_base, $category->id_parent, $path, '', 'smartblog' );
		}
	}

	public static function getRootCategory( $id_lang = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$root_category = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
                            SELECT *, sbc.`id_smart_blog_category` AS `id_category`, sbcl.`name` 
                            FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl 
                                ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ( $id_lang ) . ')
            INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs 
                ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and sbs.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                    WHERE sbc.`active`= 1 AND sbc.`id_parent` = 0'
		);

		return isset( $root_category[0] ) ? $root_category[0] : array();
	}

	public static function getNameCategory( $id ) {
		$id_lang = (int) Context::getContext()->language->id;
		$sql     = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p 
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $result;
	}

	public static function getPostCategoriesFull( $id_post = '', $id_lang = null ) {
		$root_cat_status = Configuration::get( 'smartblogrootcat' );
		if ( ! $id_lang ) {
			$id_lang = Context::getContext()->language->id;
		}

		$ret = array();
		$row = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
            SELECT cp.`id_smart_blog_category` AS `id_category`, cl.`name` , cl.`link_rewrite` 
                        FROM `' . _DB_PREFIX_ . 'smart_blog_post_category` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category` c ON (c.id_smart_blog_category = cp.id_smart_blog_category)
            LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON (cp.`id_smart_blog_category` = cl.`id_smart_blog_category`)
            ' . Shop::addSqlAssociation( 'smart_blog_category', 'c' ) . '
            WHERE cp.`id_smart_blog_post` = ' . (int) $id_post . '
                AND cl.`id_lang` = ' . (int) $id_lang
		);

		foreach ( $row as $val ) {
			if ( $root_cat_status == 0 and $val['id_category'] == 1 ) {
				continue;
			}

			$ret[ $val['id_category'] ] = $val;
		}

		return $ret;
	}

	public static function getChildren( $id_cat, $id_lang = null, $active = true ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$results = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
            SELECT *, sbc.`id_smart_blog_category` AS `id_category`, sbcl.`name`  FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc 
                INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl 
                    ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ( $id_lang ) . ')
                ' . Shop::addSqlAssociation( 'smart_blog_category', 'sbc' ) . '                 
                    WHERE 1 
                    ' . ( $active ? 'AND sbc.`active`= 1 ' : '' ) . ' 
                    AND sbc.id_parent = ' . $id_cat
		);
		return $results;
	}

	public function getParentsCategories( $id_lang = null ) {
		$context       = Context::getContext()->cloneContext();
		$context->shop = clone($context->shop);

		if ( is_null( $id_lang ) ) {
			$id_lang = $context->language->id;
		}

		$categories = null;
		$id_current = $this->id;
		$id_shop    = $context->shop->id;
		while ( true ) {
			$sql = '
            SELECT c.*,c.`id_smart_blog_category` AS `id_category`, cl.*, cl.`name`  
            FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl
                ON (c.`id_smart_blog_category` = cl.`id_smart_blog_category`
                AND `id_lang` = ' . (int) $id_lang . ')';
			if ( Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ) {
				$sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` cs ON (c.`id_smart_blog_category` = cs.`id_smart_blog_category` AND cs.`id_shop` = ' . (int) $id_shop . ')';
			}
			$sql .= ' WHERE c.`id_smart_blog_category` = ' . (int) $id_current;
			if ( Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ) {
				$sql .= ' AND cs.`id_shop` = ' . (int) $context->shop->id;
			}
			$root_category = self::getRootCategory();
			if ( Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP && ( ! Tools::isSubmit( 'id_smart_blog_category' ) || (int) Tools::getValue( 'id_smart_blog_category' ) == (int) $root_category['id_smart_blog_category'] ) ) {
				$sql .= ' AND c.`id_parent` != 0';
			}

			$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->getRow( $sql );

			if ( $result ) {
				$categories[] = $result;
			} elseif ( ! $categories ) {
				$categories = array();
			}
			if ( ! $result || ( $result['id_category'] == $context->shop->id_category ) ) {
				return $categories;
			}
			$id_current = $result['id_parent'];
		}
	}

	public static function getCategory( $active = 1, $id_lang = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}

		$sorting  = Configuration::get( 'sort_category_by' );
		$orderby  = 'sbcl.name';
		$orderway = 'ASC';
		if ( $sorting == 'name_ASC' ) {
			$orderby  = 'sbcl.name';
			$orderway = 'ASC';
		} elseif ( $sorting == 'name_DESC' ) {
			$orderby  = 'sbcl.name';
			$orderway = 'DESC';
		} elseif ( $sorting == 'id_ASC' ) {
			$orderby  = 'sbc.id_smart_blog_category';
			$orderway = 'ASC';
		} else {
			$orderby  = 'sbc.id_smart_blog_category';
			$orderway = 'DESC';
		}

		$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
                SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ( $id_lang ) . ')
        INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and sbs.id_shop = ' . (int) Context::getContext()->shop->id . ' WHERE sbc.`active`= ' . $active . ' ORDER BY ' . $orderby . ' ' . $orderway
		);

		return $result;
	}

	public static function getPostByCategory( $id_smart_blog_category ) {
		$sql = 'select count(id_smart_blog_post) as count from `' . _DB_PREFIX_ . 'smart_blog_post_category` where id_smart_blog_category = ' . $id_smart_blog_category;

		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		return $result[0]['count'];
	}

	public static function getCategoryInformations( $ids_category, $id_lang = null ) {
		if ( $id_lang === null ) {
			$id_lang = Context::getContext()->language->id;
		}

		if ( ! is_array( $ids_category ) || ! count( $ids_category ) ) {
			return;
		}

		$categories = array();
		$results    = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
        SELECT c.`id_smart_blog_category` AS `id_category`, cl.`name` , cl.`link_rewrite`, cl.`id_lang`
        FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
        LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON (c.`id_smart_blog_category` = cl.`id_smart_blog_category`)
        ' . Shop::addSqlAssociation( 'smart_blog_category', 'c' ) . '
        WHERE cl.`id_lang` = ' . (int) $id_lang . '
        AND c.`id_smart_blog_category` IN (' . implode( ',', array_map( 'intval', $ids_category ) ) . ')'
		);

		foreach ( $results as $category ) {
			$categories[ $category['id_category'] ] = $category;
		}

		return $categories;
	}

	public static function hasChildren( $id_parent, $id_lang, $active = true, $id_shop = false ) {
		if ( ! Validate::isBool( $active ) ) {
			die( Tools::displayError() );
		}

		$cache_id = 'BlogCategory::hasChildren_' . (int) $id_parent . '-' . (int) $id_lang . '-' . (bool) $active . '-' . (int) $id_shop;
		if ( ! Cache::isStored( $cache_id ) ) {
			$query  = 'SELECT c.`id_smart_blog_category` AS id_category, cl.`name` as name
            FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON (c.`id_smart_blog_category` = cl.`id_smart_blog_category`)
            ' . Shop::addSqlAssociation( 'smart_blog_category', 'c' ) . '
            WHERE `id_lang` = ' . (int) $id_lang . '
            AND c.`id_parent` = ' . (int) $id_parent . '
            ' . ( $active ? 'AND `active` = 1' : '' ) . ' LIMIT 1';
			$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS( $query, true, false );
			Cache::store( $cache_id, $result );
			return $result;
		}
		return Cache::retrieve( $cache_id );
	}

	public static function getAllCategoriesName( $root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '' ) {
		if ( isset( $root_category ) && ! Validate::isInt( $root_category ) ) {
			die( Tools::displayError() );
		}

		if ( ! Validate::isBool( $active ) ) {
			die( Tools::displayError() );
		}

		// if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
		// $groups = (array)$groups;
		// }

		$cache_id = 'BlogCategory::getAllCategoriesName_' . md5(
			(int) $root_category . (int) $id_lang . (int) $active . (int) $use_shop_restriction
						. ( isset( $groups ) && Group::isFeatureActive() ? implode( '', $groups ) : '' )
		);

		if ( ! Cache::isStored( $cache_id ) ) {
			$result = Db::getInstance()->executeS(
				'
                SELECT c.`id_smart_blog_category` AS `id_category`, cl.`name` 
                FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
                ' . ( $use_shop_restriction ? Shop::addSqlAssociation( 'smart_blog_category', 'c' ) : '' ) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`
                
                ' . ( isset( $root_category ) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'smart_blog_category` c2 ON c2.`id_smart_blog_category` = ' . (int) $root_category . ' ' : '' ) . '
                WHERE 1 ' . $sql_filter . ' ' . ( $id_lang ? 'AND `id_lang` = ' . (int) $id_lang : '' ) . '
                ' . ( $active ? ' AND c.`active` = 1' : '' ) . '              
                ' . ( $sql_limit != '' ? $sql_limit : '' )
			);

			Cache::store( $cache_id, $result );
		} else {
			$result = Cache::retrieve( $cache_id );
		}

		return $result;
	}

	public static function getCatName( $id ) {
		$id_lang = (int) Context::getContext()->language->id;
		$sql     = 'SELECT pl.name FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl join ' . _DB_PREFIX_ . 'smart_blog_post_category as p3 on pl.id_smart_blog_category=p3.id_smart_blog_category  WHERE pl.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;

		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $result[0]['name'];
	}

	public static function getCatLinkRewrite( $id ) {
		$id_lang = (int) Context::getContext()->language->id;
		$sql     = 'SELECT pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl join ' . _DB_PREFIX_ . 'smart_blog_post_category as p3 on pl.id_smart_blog_category=p3.id_smart_blog_category  WHERE pl.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $result[0]['link_rewrite'];
	}

	public static function getNestedCategories( $root_category = null, $id_lang = false, $active = true, $groups = null,
		$use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '' ) {
		if ( isset( $root_category ) && ! Validate::isInt( $root_category ) ) {
			die( Tools::displayError() );
		}

		if ( ! Validate::isBool( $active ) ) {
			die( Tools::displayError() );
		}

		// if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
		// $groups = (array)$groups;
		// }

		$cache_id = 'BlogCategory::getNestedCategories_' . md5( (int) $root_category . (int) $id_lang . (int) $active . (int) $use_shop_restriction );

		if ( ! Cache::isStored( $cache_id ) ) {
			$result = Db::getInstance()->executeS(
				'
                SELECT c.*, cl.*, c.`id_smart_blog_category` AS `id_category`, cl.`name` AS `name`
                FROM `' . _DB_PREFIX_ . 'smart_blog_category` c
                ' . ( $use_shop_restriction ? Shop::addSqlAssociation( 'smart_blog_category', 'c' ) : '' ) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`              
                WHERE 1 ' . $sql_filter . ' ' . ( $id_lang ? 'AND cl.`id_lang` = ' . (int) $id_lang : '' ) . '
                ' . ( $active ? ' AND c.`active` = 1' : '' ) . '              
                ' . ( $sql_limit != '' ? $sql_limit : '' )
			);

			$categories = array();
			$buff       = array();

			if ( ! isset( $root_category ) ) {
				$root_category = self::getRootCategory();
				$root_category = $root_category['id_category'];
			}

			foreach ( $result as $row ) {
				$current = &$buff[ $row['id_category'] ];
				$current = $row;

				if ( $row['id_category'] == $root_category ) {
					$categories[ $row['id_category'] ] = &$current;
				} else {
					$buff[ $row['id_parent'] ]['children'][ $row['id_category'] ] = &$current;
				}
			}

			Cache::store( $cache_id, $categories );
		} else {
			$categories = Cache::retrieve( $cache_id );
		}

		return $categories;
	}

	public static function updateAssocCat( $id_post ) {
		Db::getInstance( _PS_USE_SQL_SLAVE_ )->delete( 'smart_blog_post_category', "id_smart_blog_post={$id_post}" );

		$cats = $insert = array();
		// $cats = array(1);
		if ( Tools::isSubmit( 'categoryBox' ) ) {
			// $cats = array_merge($cats, Tools::getValue('categoryBox'));
			$cats = Tools::getValue( 'categoryBox' );
			if ( is_array( $cats ) ) {
				foreach ( $cats as $cat ) {
					$insert[] = array(
						'id_smart_blog_category' => $cat,
						'id_smart_blog_post'     => $id_post,
					);
				}

				Db::getInstance( _PS_USE_SQL_SLAVE_ )->insert( 'smart_blog_post_category', $insert, false, false, Db::INSERT_IGNORE );
			}
		}

		return true;
	}


	public static function GetMetaByCategory( $id_category, $id_lang = null ) {
		$meta = array();

		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$where_string = '';

		if ( $id_category ) {
			$where_string = ' WHERE sbc.`active`= 1 and sbc.id_smart_blog_category = ' . $id_category;
			$result       = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
				'SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` '
					. 'sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ( $id_lang ) . ')
						INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and'
					. ' sbs.id_shop = ' . (int) Context::getContext()->shop->id . $where_string
			);
			$the_category = $result[0];

			// echo '<pre>';
			// print_r( $the_category );
			// echo '</pre>';

			// die( __DIR__ . ' ' . __FILE__ . ' ' . __LINE__ );
			if ( isset( $the_category['meta_title'] ) && $the_category['meta_title'] != '' ) {
				$meta['title'] = $the_category['meta_title'];
			} else {
				$meta['title'] = Configuration::get( 'smartblogmetatitle' );
			}

			if ( isset( $the_category['meta_description'] ) && $the_category['meta_description'] != '' ) {
				$meta['description'] = $the_category['meta_description'];
			} else {
				$meta['description'] = Configuration::get( 'smartblogmetadescrip' );
			}

			if ( isset( $the_category['meta_keyword'] ) && $the_category['meta_keyword'] != '' ) {
				$meta['keywords'] = $the_category['meta_keyword'];
			} else {
				$meta['keywords'] = Configuration::get( 'smartblogmetakeyword' );
			}
		} else {
			$meta['title']       = Configuration::get( 'smartblogmetatitle' );
			$meta['description'] = Configuration::get( 'smartblogmetadescrip' );
			$meta['keywords']    = Configuration::get( 'smartblogmetakeyword' );
		}
		return $meta;
	}

	public static function GetMetaForSearch() {
		$meta = array();

		$meta['title'] = 'Search :' . Configuration::get( 'smartblogmetatitle' );

		return $meta;
	}
}
