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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SmartBlogPost extends ObjectModel {


	public $id_smart_blog_post;
	public $id_author;
	public $id_category;
	public $position = 0;
	public $active   = 1;
	public $available;
	public $created;
	public $modified;
	public $short_description;
	public $viewed;
	public $comment_status = 1;
	public $associations;
	public $meta_title;
	public $meta_keyword;
	public $meta_description;
	public $image;
	public $content;
	public $link_rewrite;
	public $is_featured;
	public $id_smart_blog_category;
	public static $definition = array(
		'table'     => 'smart_blog_post',
		'primary'   => 'id_smart_blog_post',
		'multilang' => true,
		'fields'    => array(
			'active'            => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
			),
			'position'          => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			),
			// 'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'id_author'         => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			),
			'available'         => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
			),
			'created'           => array(
				'type'     => self::TYPE_DATE,
				'validate' => 'isString',
			),
			'modified'          => array(
				'type'     => self::TYPE_DATE,
				'validate' => 'isString',
			),
			'viewed'            => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			),
			'is_featured'       => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			),
			'comment_status'    => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			),
			'associations'      => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'image'             => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'meta_title'        => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
				'lang'     => true,
				'required' => true,
			),
			'meta_keyword'      => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isString',
			),
			'meta_description'  => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isString',
			),
			'short_description' => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isString',
				'required' => true,
			),
			'content'           => array(
				'type'     => self::TYPE_HTML,
				'lang'     => true,
				'validate' => 'isString',
				'required' => true,
			),
			'link_rewrite'      => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isLinkRewrite',
				'required' => false,
			),
		),
	);

	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {
		Shop::addTableAssociation( 'smart_blog_post', array( 'type' => 'shop' ) );
		parent::__construct( $id, $id_lang, $id_shop );
	}

	// Related Product....
	public static function getRelatedProduct( $id_lang = null, $id_post = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		if ( Configuration::get( 'smartshowrelatedproduct' ) != '' && Configuration::get( 'smartshowrelatedproduct' ) != null ) {
			$limit = Configuration::get( 'smartshowrelatedproduct' );
		} else {
			$limit = 5;
		}

		if ( $id_post == null ) {
			$id_post = 1;
		}

		$sql             = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post_related WHERE id_smart_blog_post = ' . $id_post;
		$postDetails     = Db::getInstance()->executeS( $sql );
		if(empty($postDetails)){
			return array();
		}
		$currentPost     = $postDetails[0];
		$product_ids     = explode( '-', $currentPost['related_poroduct_id'] );
		$productIdString = '';
		$limitcount      = 0;
		foreach ( $product_ids as $product_id ) {
			if ( empty( $product_id ) ) {
				continue;
			}
			if ( $limitcount >= $limit ) {
				continue;
			}
			$limitcount++;
			$productIdString .= $product_id . ',';
		}
		$productIdString = substr_replace( $productIdString, '', -1 );

		$products = self::getProductsByProductIDS( (string) $productIdString );

		 return $products;
	}

	public static function getRelatedPostsById_post( $id_post = null ) {
		if ( Configuration::get( 'smartshowrelatedpost' ) != '' && Configuration::get( 'smartshowrelatedpost' ) != null ) {
			$limit = Configuration::get( 'smartshowrelatedpost' );
		} else {
			$limit = 5;
		}

		$sql = 'SELECT itl.*,it.* FROM `' . _DB_PREFIX_ . 'smart_blog_post` it,`' . _DB_PREFIX_ . 'smart_blog_post_category` itc1, `' . _DB_PREFIX_ . 'smart_blog_post_category` itc2 ,`' . _DB_PREFIX_ . 'smart_blog_post_lang` itl, `' . _DB_PREFIX_ . 'smart_blog_post_shop` its'

				. ' WHERE it.id_smart_blog_post = itc2.id_smart_blog_post AND itl.id_smart_blog_post = itc2.id_smart_blog_post AND  itc1.id_smart_blog_category =itc2.id_smart_blog_category  AND itc1.id_smart_blog_post =' . (int) $id_post . ' AND itc2.id_smart_blog_post <>' . (int) $id_post . ' AND it.active =1 AND itl.id_lang = ' . (int) Context::getContext()->language->id . ' AND its.id_smart_blog_post = it.id_smart_blog_post AND its.id_shop = ' . (int) Context::getContext()->shop->id . ' ORDER BY it.id_smart_blog_post DESC LIMIT 0,' . $limit;

			$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS( $sql );

			$id_posts = array();

		foreach ( $result as $id_item ) {

			if ( ! in_array( $id_item, $id_posts ) ) {
				$id_posts[] = $id_item;
			}
		}
			return $id_posts;
	}

	public static function getPost( $id_post, $id_lang = null ) {
		$result = array();
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 AND p.id_smart_blog_post = ' . $id_post;

		if ( ! $post = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$selected_cat = BlogCategory::getPostCategoriesFull( (int) $post[0]['id_smart_blog_post'], Context::getContext()->language->id );

		$result['id_category']      = 1;
		$result['cat_link_rewrite'] = '';
		$result['cat_name']         = '';

		foreach ( $selected_cat as $key => $value ) {
			$result['id_category']      = $selected_cat[ $key ]['id_category'];
			$result['cat_link_rewrite'] = $selected_cat[ $key ]['link_rewrite'];
			$result['cat_name']         = $selected_cat[ $key ]['name'];
		}

		$result['id_post']           = $post[0]['id_smart_blog_post'];
		$result['meta_title']        = $post[0]['meta_title'];
		$result['meta_description']  = $post[0]['meta_description'];
		$result['short_description'] = $post[0]['short_description'];
		$result['meta_keyword']      = $post[0]['meta_keyword'];
		$result['link_rewrite']      = $post[0]['link_rewrite'];
		if ( ( Module::isEnabled( 'smartshortcode' ) == 1 ) && ( Module::isInstalled( 'smartshortcode' ) == 1 ) ) {
			require_once _PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php';
			$smartshortcode    = new SmartShortCode();
			$result['content'] = $smartshortcode->parse( $post[0]['content'] );
		} else {

			$result['content'] = $post[0]['content'];
		}
		if ( Module::isInstalled( 'jscomposer' ) && Module::isEnabled( 'jscomposer' ) ) {
			require_once _PS_MODULE_DIR_ . 'jscomposer/jscomposer.php';
			$result['content'] = JsComposer::do_shortcode( $result['content'] );

		}

		$result['active']         = $post[0]['active'];
		$result['created']        = $post[0]['created'];
		$result['comment_status'] = $post[0]['comment_status'];
		$result['viewed']         = $post[0]['viewed'];
		$result['is_featured']    = $post[0]['is_featured'];
		$employee                 = new Employee( $post[0]['id_author'] );
		$result['lastname']       = $employee->lastname;
		$result['firstname']      = $employee->firstname;
		if ( file_exists( _PS_MODULE_DIR_ . 'smartblog/images/' . $post[0]['id_smart_blog_post'] . '.jpg' ) ) {
			$image              = $post[0]['id_smart_blog_post'] . '.jpg';
			$result['post_img'] = $image;
		} else {
			$result['post_img'] = null;
		}

		return $result;
	}

	public static function getAllPost( $id_lang = null, $limit_start, $limit ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		if ( $limit_start == '' || $limit_start < 0 ) {
			$limit_start = 0;
		}
		if ( $limit == '' ) {
			$limit = 5;
		}
		$result       = array();
		$BlogCategory = '';

		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 ORDER BY p.id_smart_blog_post DESC LIMIT ' . $limit_start . ',' . $limit;

		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$BlogCategory = new BlogCategory();
		$i            = 0;
		foreach ( $posts as $post ) {

			// if ( new DateTime() >= new DateTime( $post['created'] ) ) {

			// } else {
			// die( __DIR__ . ' ' . __FILE__ . ' ' . __LINE__ );

			// continue;
			// }

			$selected_cat = BlogCategory::getPostCategoriesFull( (int) $post['id_smart_blog_post'], Context::getContext()->language->id );

			$result[ $i ]['id_category']      = 1;
			$result[ $i ]['cat_link_rewrite'] = '';
			$result[ $i ]['cat_name']         = '';

			foreach ( $selected_cat as $key => $value ) {
				$result[ $i ]['id_category']      = $selected_cat[ $key ]['id_category'];
				$result[ $i ]['cat_link_rewrite'] = $selected_cat[ $key ]['link_rewrite'];
				$result[ $i ]['cat_name']         = $selected_cat[ $key ]['name'];
			}

			$result[ $i ]['id_post']           = $post['id_smart_blog_post'];
			$result[ $i ]['is_featured']       = $post['is_featured'];
			$result[ $i ]['viewed']            = $post['viewed'];
			$result[ $i ]['meta_title']        = $post['meta_title'];
			$result[ $i ]['meta_description']  = $post['meta_description'];
			$result[ $i ]['short_description'] = $post['short_description'];
			$result[ $i ]['content']           = $post['content'];
			$result[ $i ]['meta_keyword']      = $post['meta_keyword'];
			$result[ $i ]['link_rewrite']      = $post['link_rewrite'];
			$employee                          = new Employee( $post['id_author'] );

			$result[ $i ]['lastname']  = $employee->lastname;
			$result[ $i ]['firstname'] = $employee->firstname;
			if ( file_exists( _PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg' ) ) {
				$image                    = $post['id_smart_blog_post'];
				$result[ $i ]['post_img'] = $image;
			} else {
				$result[ $i ]['post_img'] = 'no';
			}
			$result[ $i ]['created'] = $post['created'];

			$i++;
		}

		return $result;
	}

	public static function getToltal( $id_lang = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1';
		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$return_count = 0;

		foreach ( $posts as $post ) {
			if ( new DateTime() >= new DateTime( $post['created'] ) ) {
				$return_count++;
			} else {
				continue;
			}
		}

		return $return_count;
	}

	public static function getToltalByCategory( $id_lang = null, $id_category = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		if ( $id_category == null ) {
			$id_category = 1;
		}
		$sql = 'SELECT COUNT(*) AS num FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN
                ' . _DB_PREFIX_ . 'smart_blog_post_category pc ON p.id_smart_blog_post=pc.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON pc.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '
                AND p.active= 1 AND pc.id_smart_blog_category = ' . pSQL( $id_category );
		return Db::getInstance()->getValue( $sql );
		// if (!$posts = Db::getInstance()->executeS($sql))
		// return false;
		// return count($posts);
	}

	public static function addTags( $id_lang = null, $id_post, $tag_list, $separator = ',' ) {

		
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		if ( ! Validate::isUnsignedId( $id_lang ) ) {
			return false;
		}

		if ( ! is_array( $tag_list ) ) {
			$tag_list = array_filter( array_unique( array_map( 'trim', preg_split( '#\\' . $separator . '#', $tag_list, null, PREG_SPLIT_NO_EMPTY ) ) ) );
		}

		$list = array();
		if ( is_array( $tag_list ) ) {
			foreach ( $tag_list as $tag ) {
				$id_tag = BlogTag::TagExists( $tag, (int) $id_lang );
				if ( ! $id_tag ) {
					$slug = BlogTag::TagUrlBuild( $tag );
					$tag_obj = new BlogTag( null, $tag, (int) $id_lang );
					if ( ! Validate::isLoadedObject( $tag_obj ) ) {
						$tag_obj->name    = $tag;
						$tag_obj->slug    = $slug;
						$tag_obj->id_lang = (int) $id_lang;
						$tag_obj->add();
					}
					if ( ! in_array( $tag_obj->id, $list ) ) {
						$list[] = $tag_obj->id;
					}
				} else {
					if ( ! in_array( $id_tag, $list ) ) {
						$list[] = $id_tag;
					}
				}
			}
		}
		$data = '';
		foreach ( $list as $tag ) {
			$data .= '(' . (int) $tag . ',' . (int) $id_post . '),';
		}
		$data = rtrim( $data, ',' );
		

		return Db::getInstance()->execute(
			'
		INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post_tag` (`id_tag`, `id_post`)
		VALUES ' . $data
		);
	}
	public static function subStr( $string, $length ) {
		return strlen( $string ) > $length ? substr( $string, 0, $length ) . '...' : $string;
	}
	public function add( $autodate = true, $null_values = false ) {
		if ( ! parent::add( $autodate, $null_values ) ) {
			return false;
		} elseif ( Tools::getIsset( 'products' ) ) {
			return $this->setProducts( Tools::getValue( 'products' ) );
		}
		return true;
	}

	public static function postViewed( $id_post ) {

		$sql = 'UPDATE ' . _DB_PREFIX_ . 'smart_blog_post as p SET p.viewed = (p.viewed+1) where p.id_smart_blog_post = ' . $id_post;

		return Db::getInstance()->execute( $sql );

	}

	public function setProducts( $array ) {
		$result = Db::getInstance()->execute( 'DELETE FROM ' . _DB_PREFIX_ . 'smart_blog_post_tag WHERE id_tag = ' . (int) $this->id );
		if ( is_array( $array ) ) {
			$array   = array_map( 'intval', $array );
			$result &= ObjectModel::updateMultishopTable( 'smart_blog_post_tag', array( 'indexed' => 0 ), 'a.id_post IN (' . implode( ',', $array ) . ')' );
			$ids     = array();
			foreach ( $array as $id_post ) {
				$ids[] = '(' . (int) $id_post . ',' . (int) $this->id . ')';
			}

			if ( $result ) {
				$result &= Db::getInstance()->execute( 'INSERT INTO ' . _DB_PREFIX_ . 'smart_blog_post_tag (id_post, id_tag) VALUES ' . implode( ',', $ids ) );
				if ( Configuration::get( 'PS_SEARCH_INDEXATION' ) ) {
					$result &= Search::indexation( false );
				}
			}
		}
		return $result;
	}

	public static function deleteTagsForProduct( $id_post ) {
		return Db::getInstance()->execute( 'DELETE FROM `' . _DB_PREFIX_ . 'smart_blog_post_tag` WHERE `id_post` = ' . (int) $id_post );
	}

	public static function getProductTags( $id_post ) {
		$id_lang = (int) Context::getContext()->language->id;
		if ( ! $tmp = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'
		SELECT  t.`name`,t.`slug`
		FROM ' . _DB_PREFIX_ . 'smart_blog_tag t
		LEFT JOIN ' . _DB_PREFIX_ . 'smart_blog_post_tag pt ON (pt.id_tag = t.id_tag AND t.id_lang = ' . $id_lang . ')
		WHERE pt.`id_post`=' . (int) $id_post
		) ) {
			return false;
		}
		return $tmp;
	}

	public static function getProductTagsBylang( $id_post, $id_lang = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$tags = '';
		if ( ! $tmp = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT  t.`name`
            FROM ' . _DB_PREFIX_ . 'smart_blog_tag t
            LEFT JOIN ' . _DB_PREFIX_ . 'smart_blog_post_tag pt ON (pt.id_tag = t.id_tag AND t.id_lang = ' . $id_lang . ')
            WHERE pt.`id_post`=' . (int) $id_post
		) ) {
			return false;
		}
		$i = 1;
		foreach ( $tmp as $val ) {
			if ( $i >= count( $tmp ) ) {
				$tags .= $val['name'];
			} else {
				$tags .= $val['name'] . ',';
			}
			$i++;
		}
		return $tags;
	}

	public static function getPopularPosts( $id_lang = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		if ( Configuration::get( 'smartshowpopularpost' ) != '' && Configuration::get( 'smartshowpopularpost' ) != null ) {
			$limit = Configuration::get( 'smartshowpopularpost' );
		} else {
			$limit = 5;
		}
		$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT p.id_author ,p.viewed ,p.created , p.id_smart_blog_post,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                    ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                    ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                    WHERE pl.id_lang=' . $id_lang . ' AND p.active = 1 ORDER BY p.viewed DESC LIMIT 0,' . $limit
		);
		foreach ( $result as $key => $value ) {
			$result[ $key ]['created'] = smartblog::displayDate( $value['created'] );
		}
		return $result;
	}

	public static function getRecentPosts( $id_lang = null ) {

		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}

		if ( Configuration::get( 'smartshowrecentpost' ) != '' && Configuration::get( 'smartshowrecentpost' ) != null ) {
			$limit = Configuration::get( 'smartshowrecentpost' );
		} else {
			$limit = 5;
		}

		$result = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS(
			'SELECT  p.id_author,p.id_smart_blog_post,p.created,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '  AND p.active = 1 ORDER BY p.id_smart_blog_post DESC LIMIT 0,' . $limit
		);

		foreach ( $result as $key => $value ) {
			$result[ $key ]['created'] = smartblog::displayDate( $value['created'] );
		}

		return $result;
	}

	public static function tagsPost( $tags, $id_lang = null ) {
		$result = array();
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}

		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post=ps.id_smart_blog_post  AND  ps.id_shop = ' . (int) Context::getContext()->shop->id . ' INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_tag pt ON pl.id_smart_blog_post = pt.id_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_tag t ON pt.id_tag=t.id_tag 
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '  AND p.active = 1 	 		
                AND t.slug="' . pSQL( $tags ) . '"';

		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$BlogCategory = new BlogCategory();
		$i            = 0;
		foreach ( $posts as $post ) {
			$result[ $i ]['id_post']           = $post['id_smart_blog_post'];
			$result[ $i ]['viewed']            = $post['viewed'];
			$result[ $i ]['is_featured']       = $post['is_featured'];
			$result[ $i ]['meta_title']        = $post['meta_title'];
			$result[ $i ]['short_description'] = $post['short_description'];
			$result[ $i ]['meta_description']  = $post['meta_description'];
			$result[ $i ]['content']           = $post['content'];
			$result[ $i ]['meta_keyword']      = $post['meta_keyword'];
			// $result[$i]['id_category'] = $post['id_category'];
			$result[ $i ]['id_category']  = 1;
			$result[ $i ]['link_rewrite'] = $post['link_rewrite'];

			// $result[$i]['cat_name'] = $BlogCategory->getCatName($post['id_category']);
			// $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
			$result[ $i ]['cat_name']         = 'uncategories';
			$result[ $i ]['cat_link_rewrite'] = 'uncategories';

			$employee = new Employee( $post['id_author'] );

			$result[ $i ]['lastname']  = $employee->lastname;
			$result[ $i ]['firstname'] = $employee->firstname;
			if ( file_exists( _PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg' ) ) {
				$image                    = $post['id_smart_blog_post'];
				$result[ $i ]['post_img'] = $image;
			} else {
				$result[ $i ]['post_img'] = 'no';
			}
			$result[ $i ]['created'] = $post['created'];

			$i++;
		}
		return $result;
	}
	// ($month = null, $year = null, $limit_start = 0, $limit = 5)
	public static function getArchiveResult( $month = null, $year = null, $day = null, $limit_start = 0, $limit = 5 ) {
		$BlogCategory = '';
		$day          = pSQL( $day );
		$month        = pSQL( $month );
		$year         = pSQL( $year );
		$result       = array();
		$id_lang      = (int) Context::getContext()->language->id;
		if ( $month != '' and $month != null and $year != '' and $year != null and $day != '' and $day != null ) {
			$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
            where s.active = 1 and DAY(s.created) = ' . $day . ' and MONTH(s.created) = ' . $month . ' AND YEAR(s.created) = ' . $year . ' ORDER BY s.id_smart_blog_post DESC';
		} elseif ( $month != '' and $month != null and $year != '' and $year != null ) {
			$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
            where s.active = 1 and MONTH(s.created) = ' . $month . ' AND YEAR(s.created) = ' . $year . ' ORDER BY s.id_smart_blog_post DESC';
		} elseif ( $month == '' and $month == null and $year != '' and $year != null ) {
			$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
           where s.active = 1 AND YEAR(s.created) = ' . $year . ' ORDER BY s.id_smart_blog_post DESC';
		} elseif ( $month != '' and $month != null and $year == '' and $year == null ) {
			$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
           where s.active = 1 AND   MONTH(s.created) = ' . $month . '  ORDER BY s.id_smart_blog_post DESC';
		} else {
			$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
            where s.active = 1 ORDER BY s.id_smart_blog_post DESC';
		}
		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$BlogCategory = new BlogCategory();
		$i            = 0;
		foreach ( $posts as $post ) {

			$result[ $i ]['id_post']           = $post['id_smart_blog_post'];
			$result[ $i ]['viewed']            = $post['viewed'];
			$result[ $i ]['is_featured']       = $post['is_featured'];
			$result[ $i ]['meta_title']        = $post['meta_title'];
			$result[ $i ]['short_description'] = $post['short_description'];
			$result[ $i ]['meta_description']  = $post['meta_description'];
			$result[ $i ]['content']           = $post['content'];
			$result[ $i ]['meta_keyword']      = $post['meta_keyword'];
			$result[ $i ]['id_category']       = $post['id_category'];
			$result[ $i ]['link_rewrite']      = $post['link_rewrite'];
			//$result[ $i ]['cat_name']          = $BlogCategory->getCatName( $post['id_smart_blog_post'] );
			$result[ $i ]['cat_link_rewrite']  = $BlogCategory->getCatLinkRewrite( $post['id_smart_blog_post'] );
			$employee                          = new Employee( $post['id_author'] );

			$result[ $i ]['lastname']  = $employee->lastname;
			$result[ $i ]['firstname'] = $employee->firstname;
			if ( file_exists( _PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg' ) ) {
				$image                    = $post['id_smart_blog_post'];
				$result[ $i ]['post_img'] = $image;
			} else {
				$result[ $i ]['post_img'] = 'no';
			}
			$result[ $i ]['created'] = $post['created'];

			$i++;
		}
		return $result;
	}

	public static function getArchiveD( $month, $year ) {

		$sql = 'SELECT DAY(p.created) as day FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                 where MONTH(p.created) = ' . pSQL( $month ) . ' AND YEAR(p.created) = ' . pSQL( $year ) . ' GROUP BY DAY(p.created)';

		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		return $posts;
	}

	public static function getArchiveM( $year ) {

		$sql = 'SELECT MONTH(p.created) as month FROM ' . _DB_PREFIX_ . 'smart_blog_post p  INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                 where YEAR(p.created) = ' . pSQL( $year ) . ' GROUP BY MONTH(p.created)';

		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $posts;
	}

	public static function getArchive() {
		$result = array();
		$sql    = 'SELECT YEAR(p.created) as year FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                GROUP BY YEAR(p.created)';

		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$result = array(
			'id'          => 0,
			'name'        => '',
			'link'        => '',
			'level_depth' => '',
			'desc'        => '',
			'children'    => array(),
		);
		$i      = 0;
		$count  = 0;
		foreach ( $posts as $key => $value ) {
			$result['children'][ $i ] = array(
				'id'          => $count,
				'name'        => $value['year'],
				'link'        => smartblog::GetSmartBlogLink( 'smartblog_year', array( 'year' => $value['year'] ) ),
				'level_depth' => '',
				'desc'        => '',
				'children'    => array(),
			);
			$count++;

			$months = self::getArchiveM( $value['year'] );
			$j      = 0;
			foreach ( $months as $month ) {

				$monthNum  = $month['month'];
				$dateObj   = DateTime::createFromFormat( '!m', $monthNum );
				$monthName = $dateObj->format( 'F' );

				$result['children'][ $i ]['children'][ $j ] = array(
					'id'          => $count,
					'name'        => $monthName,
					'link'        => smartblog::GetSmartBlogLink(
						'smartblog_month',
						array(
							'year'  => $value['year'],
							'month' => $month['month'],
						)
					),
					'level_depth' => '',
					'desc'        => '',
					'children'    => array(),
				);
				$count++;
				$days = self::getArchiveD( $month['month'], $value['year'] );
				$k    = 0;
				// foreach ($days as $day) {
				// $time = strtotime($value['year'].'/'.$month['month'].'/'.$day['day']);

				// $newformat = date('jS \of F Y',$time);
				// $result['children'][$i]['children'][$j]['children'][$k] = array(
				// 'id' => $count,
				// 'name' => $newformat,
				// 'link' => smartblog::GetSmartBlogLink('smartblog_day',array('year'=>$value['year'],'month'=>$month['month'],'day'=>$day['day'])),
				// 'level_depth' => '',
				// 'desc' => '',
				// 'children' => array()
				// );
				// $count++;
				// $k++;
				// }
				$j++;
			}
			$i++;
		}

		return $result;
	}

	public static function getArchiveOld() {
		$result = array();
		$sql    = 'SELECT YEAR(p.created) as year FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                GROUP BY YEAR(p.created)';

		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		$i = 0;
		foreach ( $posts as $value ) {
			$result[ $i ]['year']  = $value['year'];
			$result[ $i ]['month'] = self::getArchiveM( $value['year'] );
			$months                = self::getArchiveM( $value['year'] );
			$j                     = 0;
			foreach ( $months as $month ) {
				$result[ $i ]['month'][ $j ]['day'] = self::getArchiveD( $month['month'], $value['year'] );
				$j++;
			}
			$i++;
		}
		return $result;
	}

	// need optimization ($keyword = NULL, $id_lang = NULL, $limit_start = 0, $limit = 5)
	public static function SmartBlogSearchPost( $keyword = null, $id_lang = NULL5 ) {
		if ( $keyword == null ) {
			return false;
		}
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$keyword = pSQL( $keyword );
		$sql     = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post_lang pl, ' . _DB_PREFIX_ . 'smart_blog_post p 
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '  AND p.active = 1 
                AND pl.id_smart_blog_post=p.id_smart_blog_post AND
                (pl.meta_title LIKE \'%' . $keyword . '%\' OR
                 pl.meta_keyword LIKE \'%' . $keyword . '%\' OR
                 pl.meta_description LIKE \'%' . $keyword . '%\' OR
                 pl.content LIKE \'%' . $keyword . '%\') ORDER BY p.id_smart_blog_post DESC';
		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$BlogCategory = new BlogCategory();
		$i            = 0;

		$result = array();

		foreach ( $posts as $post ) {

			$result[ $i ]['id_post']           = $post['id_smart_blog_post'];
			$result[ $i ]['viewed']            = $post['viewed'];
			$result[ $i ]['is_featured']       = $post['is_featured'];
			$result[ $i ]['meta_title']        = $post['meta_title'];
			$result[ $i ]['short_description'] = $post['short_description'];
			$result[ $i ]['meta_description']  = $post['meta_description'];
			$result[ $i ]['content']           = $post['content'];
			$result[ $i ]['meta_keyword']      = $post['meta_keyword'];
			$result[ $i ]['id_category']       = $post['id_category'];
			$result[ $i ]['link_rewrite']      = $post['link_rewrite'];
			//$result[ $i ]['cat_name']          = $BlogCategory->getCatName(  $post['id_smart_blog_post'] );
			$result[ $i ]['cat_link_rewrite']  = $BlogCategory->getCatLinkRewrite( $post['id_smart_blog_post'] );
			$employee                          = new Employee( $post['id_author'] );

			$result[ $i ]['lastname']  = $employee->lastname;
			$result[ $i ]['firstname'] = $employee->firstname;
			if ( file_exists( _PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg' ) ) {
				$image                    = $post['id_smart_blog_post'];
				$result[ $i ]['post_img'] = $image;
			} else {
				$result[ $i ]['post_img'] = 'no';
			}
			$result[ $i ]['created'] = Smartblog::displayDate( $post['created'] );
			$i++;
		}
		return $result;
	}

	public static function SmartBlogSearchPostCount( $keyword = null, $id_lang = null ) {
		if ( $keyword == null ) {
			return false;
		}
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		$keyword = pSQL( $keyword );
		$sql     = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post_lang pl, ' . _DB_PREFIX_ . 'smart_blog_post p 
                WHERE pl.id_lang=' . $id_lang . '
                AND pl.id_smart_blog_post=p.id_smart_blog_post AND p.active = 1 AND 
                (pl.meta_title LIKE \'%' . $keyword . '%\' OR
                 pl.meta_keyword LIKE \'%' . $keyword . '%\' OR
                 pl.meta_description LIKE \'%' . $keyword . '%\' OR
                 pl.content LIKE \'%' . $keyword . '%\') ORDER BY p.id_smart_blog_post DESC';
		if ( ! $posts = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return count( $posts );
	}

	public static function getBlogImage() {

		$sql = 'SELECT id_smart_blog_post FROM ' . _DB_PREFIX_ . 'smart_blog_post';

		if ( ! $result = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $result;
	}

	public static function GetPostSlugById( $id_post, $id_lang = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}

		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '
                AND p.active= 1 AND p.id_smart_blog_post = ' . pSQL( $id_post );

		if ( ! $post = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		return $post[0]['link_rewrite'];
	}

	public static function GetPostMetaByPost( $id_post, $id_lang = null ) {
		$meta = array();
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}

		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '
                AND p.active= 1 AND p.id_smart_blog_post = ' . pSQL( $id_post );

		if ( ! $post = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}

		$the_post = $post[0];

		if ( isset( $the_post['meta_title'] ) && $the_post['meta_title'] != '' ) {
			$meta['title'] = $the_post['meta_title'];
		} else {
			$meta['title'] = Configuration::get( 'smartblogmetatitle' );
		}

		if ( isset( $the_post['meta_description'] ) && $the_post['meta_description'] != '' ) {
			$meta['description'] = $the_post['meta_description'];
		} else {
			$meta['description'] = Configuration::get( 'smartblogmetadescrip' );
		}

		if ( isset( $the_post['meta_keyword'] ) && $the_post['meta_keyword'] != '' ) {
			$meta['keywords'] = $the_post['meta_keyword'];
		} else {
			$meta['keywords'] = Configuration::get( 'smartblogmetakeyword' );
		}

		return $meta;
	}

	public static function GetPostLatestHome( $limit ) {
		$sorting = Configuration::get( 'latestnews_sort_by' );

		if ( $sorting == 'name_ASC' ) {
			$orderby  = 'pl.meta_title';
			$orderway = 'ASC';
		} elseif ( $sorting == 'name_DESC' ) {
			$orderby  = 'pl.meta_title';
			$orderway = 'DESC';
		} elseif ( $sorting == 'id_ASC' ) {
			$orderby  = 'p.id_smart_blog_post';
			$orderway = 'ASC';
		} else {
			$orderby  = 'p.id_smart_blog_post';
			$orderway = 'DESC';
		}

		if ( $limit == '' && $limit == null ) {
			$limit = 3;
		}
		$id_lang        = (int) Context::getContext()->language->id;
		$id_lang_defaut = Configuration::get( 'PS_LANG_DEFAULT' );
		$result         = array();
		$sql            = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . ' 		
                AND p.active= 1 ORDER BY  ' . $orderby . ' ' . $orderway . '
                LIMIT ' . $limit;

		$posts = Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS( $sql );
		/*
		if (empty($posts)) {
			$sql2 = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN
				' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN
				' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post  AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
				WHERE pl.id_lang=' . $id_lang_defaut . '
				AND p.active= 1 ORDER BY '.$orderby.' '.$orderway.'
				LIMIT ' . pSQL($limit);
			$posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql2);
		}*/
		$i = 0;
		foreach ( $posts as $post ) {
			$result[ $i ]['id']                = $post['id_smart_blog_post'];
			$result[ $i ]['title']             = $post['meta_title'];
			$result[ $i ]['meta_description']  = strip_tags( $post['meta_description'] );
			$result[ $i ]['short_description'] = strip_tags( $post['short_description'] );
			$result[ $i ]['content']           = strip_tags( $post['content'] );
			$result[ $i ]['category']          = $post['id_category'];
			$result[ $i ]['date_added']        = Smartblog::displayDate( $post['created'] );
			$result[ $i ]['viewed']            = $post['viewed'];
			$result[ $i ]['is_featured']       = $post['is_featured'];
			$result[ $i ]['link_rewrite']      = $post['link_rewrite'];
			if ( file_exists( _PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg' ) ) {
				$image                    = $post['id_smart_blog_post'];
				$result[ $i ]['post_img'] = $image;
			} else {
				$result[ $i ]['post_img'] = 'no';
			}
			$i++;
		}
		return $result;
	}

	/*
	public function getImages($id_lang, $id_product)
	{
		$attribute_filter = ($id_product_attribute ? ' AND ai.`id_product_attribute` = ' . (int) $id_product_attribute : '');
		$sql = 'SELECT *
					FROM `' . _DB_PREFIX_ . 'image` i
					LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`)';

		if ($id_product_attribute)
			$sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';

		$sql .= ' WHERE i.`id_product` = ' . (int) $id_product . ' AND il.`id_lang` = ' . (int) $id_lang . $attribute_filter . '
					ORDER BY i.`position` ASC';
		$images = Db::getInstance()->executeS($sql);


		foreach ($images as $k => $image)
			$images[$k] = new Image($image['id_image']);
	}
	*/
	public static function getAccessoriesLight( $id_lang, $id_smart_blog_post ) {

		if(!Module::isInstalled( 'smartblogrelatedproducts' )){
			return array();
		}

		if ( empty( $id_smart_blog_post ) ) {
			return array();
		}
		$associates = Db::getInstance()->getValue( 'SELECT `related_poroduct_id` FROM `' . _DB_PREFIX_ . "smart_blog_post_related` WHERE `id_smart_blog_post`={$id_smart_blog_post}" );

		if ( empty( $associates ) ) {
			return array();
		}

		$associates = str_replace( '-', ',', Tools::substr( $associates, 0, -1 ) );

		return Db::getInstance()->executeS(
			'
	      SELECT p.`id_product`, p.`reference`, pl.`name`
	      FROM `' . _DB_PREFIX_ . 'product` p      
	      LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
	        p.`id_product` = pl.`id_product`
	        AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang( 'pl' ) . '
	      )
	      WHERE p.id_product IN(' . $associates . ')'
		);
		// return Db::getInstance()->executeS('
		// SELECT p.`id_product`, p.`reference`, pl.`name`
		// FROM `'._DB_PREFIX_.'smart_blog_product_related` as b_pr
		// LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product`= b_pr.`id_product`)
		//
		// LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
		// p.`id_product` = pl.`id_product`
		// AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
		// )
		// WHERE `id_smart_blog_post` = '.(int)$id_smart_blog_post
		// );
	}

	/**
	 * Delete product accessories
	 *
	 * @return mixed Deletion result
	 */
	public function deleteAccessories() {
		$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'smart_blog_product_related` WHERE `id_smart_blog_post` = ' . (int) $this->id;
		return Db::getInstance()->execute( $sql );
	}

	/**
	 * Link accessories with product
	 *
	 * @param array $accessories_id Accessories ids
	 */
	public function changeAccessories( $accessories_id ) {
		foreach ( $accessories_id as $id_product ) {
			Db::getInstance()->insert(
				'smart_blog_product_related',
				array(
					'id_smart_blog_post' => (int) $this->id,
					'id_product'         => (int) $id_product,
				)
			);
		}
	}

	public static function getNextPostsById( $id_lang = null, $id_post = null ) {

		$sql = 'SELECT  p.id_smart_blog_post,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post 
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '  AND p.active = 1 AND p.id_smart_blog_post = ' . pSQL( $id_post ) . '+1';

		if ( ! $posts_next = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $posts_next;
	}

	public static function getPreviousPostsById( $id_lang = null, $id_post = null ) {

		$sql = 'SELECT  p.id_smart_blog_post,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post 
                WHERE pl.id_lang=' . pSQL( $id_lang ) . '  AND p.active = 1 AND p.id_smart_blog_post = ' . pSQL( $id_post ) . '-1';

		if ( ! $posts_previous = Db::getInstance()->executeS( $sql ) ) {
			return false;
		}
		return $posts_previous;
	}


	public static function getProductsByProductIDS( $ext = '' ) {
		$ext        = explode( ',', $ext );
		$productIds = $ext;// self::getViewedProductIds($ext);

		if ( count( $productIds ) ) {
			$assembler = new ProductAssembler( Context::getcontext() );

				$presenterFactory     = new ProductPresenterFactory( Context::getcontext() );
				$presentationSettings = $presenterFactory->getPresentationSettings();
				$presenter            = new ProductListingPresenter(
					new ImageRetriever(
						Context::getcontext()->link
					),
					Context::getcontext()->link,
					new PriceFormatter(),
					new ProductColorsRetriever(),
					Context::getcontext()->getTranslator()
				);

				$productsViewed = array();
			if ( is_array( $productIds ) && count( $productIds ) ) {
				foreach ( $productIds as $productId ) {
					if ( ! $productId ) {
						continue;
					}

					$productsViewed[] = $presenter->present(
						$presentationSettings,
						$assembler->assembleProduct(
							array(
								'id_product'           => $productId,
								'id_product_attribute' => Product::getDefaultAttribute( $productId ),
							)
						),
						Context::getcontext()->language
					);
				}
			}
				return $productsViewed;
		} else {
			return false;
		}
	}
	public static function getRelatedPostsByProduct( $id_lang = null, $id_product = null ) {
		if ( $id_lang == null ) {
			$id_lang = (int) Context::getContext()->language->id;
		}
		if ( Configuration::get( 'smartshowrelatedproductpost' ) != '' && Configuration::get( 'smartshowrelatedproductpost' ) != null ) {
			$limit = Configuration::get( 'smartshowrelatedproductpost' );
		} else {
			$limit = 5;
		}

		if ( $id_product == null ) {
			$id_product = 1;
		}

		$sql      = 'SELECT * FROM `' . _DB_PREFIX_ . "smart_blog_post_related`" ;
		$allPosts = Db::getInstance()->executeS( $sql );
		if ( empty( $allPosts ) ) {
			return array();
		}

		$relatedPosts = array();
		$post_ids     = array();
		$j            = 0;
		foreach ( $allPosts as $post ) {
			$associations = $post['related_poroduct_id'];
			$associations = explode( '-', $associations );
			foreach ( $associations as $productId ) {
				if ( $productId == $id_product ) {

					$sql1 = 'SELECT  p.id_smart_blog_post,p.created,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                  ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                  ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                  WHERE pl.id_lang=' . pSQL( $id_lang ) . '  AND p.active = 1  AND p.`id_smart_blog_post` IN(' . $post['id_smart_blog_post'] . ') ORDER BY p.id_smart_blog_post DESC LIMIT 0,' . $limit;

					$post           = Db::getInstance()->executeS( $sql1 );
					$relatedPosts[] = $post[0];
				}
			}
		}

		// print_r($relatedPosts[0]);die();
		return $relatedPosts;
	}
}
