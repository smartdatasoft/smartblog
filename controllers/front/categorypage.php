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

require_once dirname(__FILE__) . '/../../classes/controllers/FrontController.php';

class smartblogCategoryPageModuleFrontController extends smartblogModuleFrontController
{


	public $ssl = false;
	public $smartblogCategory;

	public function init()
	{

		parent::init();
	}


	public function canonicalRedirection($canonicalURL = '')
	{
		if (Tools::getValue('live_edit')) {
			return;
		}

		$protocol_link    = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

		$smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);
		if (Validate::isLoadedObject($this->smartblogCategory) && ($canonicalURL = $smartbloglink->getSmartBlogCategoryLink($this->smartblogCategory, $this->smartblogCategory->link_rewrite))) {
			parent::canonicalRedirection($canonicalURL);
		}
	}


	public function initContent()
	{

		$category_status  = '';
		$totalpages       = 0;
		$cat_image        = 'no';
		$categoryinfo     = '';
		$title_category   = '';
		$cat_link_rewrite = '';
		$blogcomment      = new Blogcomment();
		$SmartBlogPost    = new SmartBlogPost();
		$BlogCategory     = new BlogCategory();
		$BlogPostCategory = new BlogPostCategory();

		$smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');

		// now we will check whihc option we need to url rewrite
		switch ($smartblogurlpattern) {

			case 1:
				$SmartBlog   = new smartblog();
				$slug        = Tools::getValue('slug');
				$id_category = $SmartBlog->categoryslug2id($slug);
				break;
			case 2:
				$SmartBlog   = new smartblog();
				$id_category = Tools::getValue('id_category');
				// if($id_category==''){
				// $id_category = $SmartBlog->categoryslug2id($slug);
				// }

				break;

			default:
				$id_category = Tools::getValue('id_category');
		}

		// $categoryinfo = $BlogCategory->getNameCategory($id_category);
		$posts_per_page = Configuration::get('smartpostperpage');
		$limit_start    = 0;
		$limit          = $posts_per_page;

		if (!$id_category) {

			$total = (int) $SmartBlogPost->getToltal($this->context->language->id);
		} else {
			$total = (int) $SmartBlogPost->getToltalByCategory($this->context->language->id, $id_category);
			Hook::exec('actionsbcat', array('id_category' => pSQL(Tools::getvalue('id_category'))));
		}
		if ($total != '' || $total != 0) {
			$totalpages = ceil($total / $posts_per_page);
		}
		if ((bool) Tools::getValue('page')) {
			$c           = (int) Tools::getValue('page');
			$limit_start = $posts_per_page * ($c - 1);
		}
		if (!$id_category) {
			$meta_title       = Configuration::get('smartblogmetatitle');
			$meta_keyword     = Configuration::get('smartblogmetakeyword');
			$meta_description = Configuration::get('smartblogmetadescrip');

			$allNews = $SmartBlogPost->getAllPost($this->context->language->id, $limit_start, $limit);
		} else {
			if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/category/' . $id_category . '.jpg')) {
				$cat_image = $id_category;
				$ssl       = null;
				$force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
				$base_ssl  = ($force_ssl == 1) ? 'https://' : 'http://';
				$uri_path  = __PS_BASE_URI__ . 'modules/smartblog/images/category/' . $id_category . '.jpg';
				$cat_image = $base_ssl . Tools::getMediaServer($uri_path) . $uri_path;
			} else {
				$cat_image = 'no';
			}
			$categoryinfo   = $BlogCategory->getNameCategory($id_category);
			$title_category = $categoryinfo[0]['name'];

			$meta_title       = $categoryinfo[0]['meta_title'];
			$meta_keyword     = $categoryinfo[0]['meta_keyword'];
			$meta_description = $categoryinfo[0]['meta_description'];

			$category_status  = $categoryinfo[0]['active'];
			$cat_link_rewrite = $categoryinfo[0]['link_rewrite'];
			if ($category_status == 1) {
				$allNews = $BlogPostCategory->getToltalByCategory($this->context->language->id, $id_category, $limit_start, $limit);
			} elseif ($category_status == 0) {
				$allNews = '';
			}
		}
		$i  = 0;
		$to = array();

		if (!empty($allNews)) {
			foreach ($allNews as $item) {
				$to[$i] = $blogcomment->getToltalComment($item['id_post']);
				$i++;

				if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $item['id_post'] . '.jpg')) {
					$item['post_img'] = $item['id_post'] . '.jpg';
				} else {
					$item['post_img'] = 'no';
				}
			}
			$j = 0;
			foreach ($to as $item) {
				if ($item == '') {
					$allNews[$j]['totalcomment'] = 0;
				} else {
					$allNews[$j]['totalcomment'] = $item;
				}
				$j++;
			}
		}
		$protocol_link        = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

		$smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);
		$i             = 0;
		if (!empty($allNews)) {
			if (count($allNews) >= 1) {
				if (is_array($allNews)) {
					foreach ($allNews as $post) {
						$allNews[$i]['created'] = Smartblog::displayDate($post['created']);
						if (new DateTime() >= new DateTime($post['created'])) {
							$allNews[$i]['published'] = true;
						} else {
							$allNews[$i]['published'] = false;
						}
						$i++;
					}
				}
			}
		}

		$this->post_id = $id_category;
		parent::initContent();

		// $this->canonicalRedirection();

		$this->context->smarty->assign(
			array(

				'smartbloglink'        => $smartbloglink,
				'postcategory'         => $allNews,
				'category_status'      => $category_status,
				'title_category'       => $title_category,
				'cat_link_rewrite'     => $cat_link_rewrite,
				'id_category'          => $id_category,
				'cat_image'            => $cat_image,
				'categoryinfo'         => $categoryinfo,
				'smartshowauthorstyle' => Configuration::get('smartshowauthorstyle'),
				'smartshowauthor'      => Configuration::get('smartshowauthor'),
				'limit'                => isset($limit) ? $limit : 0,
				'limit_start'          => isset($limit_start) ? $limit_start : 0,
				'c'                    => isset($c) ? $c : 1,
				'total'                => $total,
				'smartblogliststyle'   => Configuration::get('smartblogliststyle'),
				'smartcustomcss'       => Configuration::get('smartcustomcss'),
				'smartshownoimg'       => Configuration::get('smartshownoimg'),
				'smartdisablecatimg'   => Configuration::get('smartdisablecatimg'),
				'smartshowviewed'      => Configuration::get('smartshowviewed'),
				'post_per_page'        => $posts_per_page,
				'pagenums'             => $totalpages - 1,
				'totalpages'           => $totalpages,
			)
		);

		$template_name = 'module:smartblog/views/templates/front/postcategory.tpl';

		$this->setTemplate($template_name);
	}
}
