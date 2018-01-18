<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
require_once (dirname(__FILE__) . '/../smartblog/smartblog.php');
class smartblogfeed extends Module
{
	private $_postErrors = array();
	
	public function __construct()
	{
		$this->name = 'smartblogfeed';
		$this->tab = 'front_office_features';
		$this->version = 2.0;
		$this->author = 'smartdatasoft';
		$this->need_instance = 0;
		$this->bootstrap = true;
		
		$this->_directory = dirname(__FILE__).'/../../';
		parent::__construct();
		
		$this->displayName = $this->l('RSS SmartBlog Feed.');
		$this->description = $this->l('Generate an RSS SmartBlog feed.');
	}

	public function install(){
		if (!parent::install() && !$this->registerHook('header'))
            return false;
        Configuration::updateValue('smart_update_period', 'hourly');
		Configuration::updateValue('smart_update_frequency', '1');
            return true;
    }
	function hookHeader($params)
	{
		if(!($id_category = (int)Tools::getValue('id_category')))
		{
			if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], Tools::getHttpHost()) && preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs))
			{
				if (isset($regs[2]) && is_numeric($regs[2]))
					$id_category = (int)($regs[2]);
				elseif (isset($regs[5]) && is_numeric($regs[5]))
					$id_category = (int)$regs[5];
			}
			elseif ($id_post = (int)Tools::getValue('id_post'))
			{
				$id_category = $this->getPostcat($id_post);
			}
		}
		$this->smarty->assign(array(
			'feedUrl' => Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/rss.php?id_category='.$id_category,
		));
		return $this->display(__FILE__, 'views/templates/front/blogfeedheader.tpl');
	}
	
	public function getPostcat($id_post,$id_lang = null)
	{
        $result = array();  
        if($id_lang == null){
                    $id_lang = (int)Context::getContext()->language->id;
                }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'smart_blog_post p INNER JOIN 
                '._DB_PREFIX_.'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                '._DB_PREFIX_.'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang='.$id_lang.'
                AND p.active= 1 AND p.id_smart_blog_post = '.$id_post;
        if (!$post = Db::getInstance()->executeS($sql))
			return false;
                $result['id_category'] = $post[0]['id_category'];
        return $result['id_category'];
    }
	
	public static function getToltalByCategory($id_lang = null, $id_category = null){
		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p';
		if($id_category > 0){
        	$sql .= ' INNER JOIN
                ' . _DB_PREFIX_ . 'smart_blog_post_category pc ON p.id_smart_blog_post=pc.id_smart_blog_post';
        }
    	$sql .= ' INNER JOIN
        ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
        ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id;
        
        $sql .= ' WHERE pl.id_lang=' . pSQL($id_lang) . ' AND p.active= 1';
        if($id_category > 0)
        	$sql .= ' AND pc.id_smart_blog_category = ' . pSQL($id_category);

        if (!$posts = Db::getInstance()->executeS($sql))
			return false;

        $i = 0;
                $BlogCategory = new BlogCategory();
            foreach($posts as $post){
                $result[$i]['id_post'] = $post['id_smart_blog_post'];
                $result[$i]['viewed'] = $post['viewed'];
                $result[$i]['meta_title'] = $post['meta_title'];
                $result[$i]['meta_description'] = $post['meta_description'];
                $result[$i]['short_description'] = $post['short_description'];
                $result[$i]['content'] = $post['content'];
                $result[$i]['meta_keyword'] = $post['meta_keyword'];
                $result[$i]['id_category'] = $post['id_category'];
                $result[$i]['link_rewrite'] = $post['link_rewrite'];
                $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
                $employee = new  Employee( $post['id_author']);
             
                $result[$i]['lastname'] = $employee->lastname;
                $result[$i]['firstname'] = $employee->firstname;
                 if (file_exists(_PS_MODULE_DIR_.'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg') )
                {
                   $image =   $post['id_smart_blog_post'];
                   $result[$i]['post_img'] = $image;
		}
                else
                {
                   $result[$i]['post_img'] ='no';
                }
				$options = array();
				$options['id_post'] = $post['id_smart_blog_post'];
				$options['slug'] = $post['link_rewrite'];
                $result[$i]['created'] = $post['created'];
                $result[$i]['blink'] = smartblog::GetSmartBlogLink('smartblog_post',$options);
                $i++;
            }
			
        return $result;
    }
     public function getContent(){
     	$feed_url = '<a href="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/smartblogfeed/rss.php" target="_blank">'._PS_BASE_URL_.__PS_BASE_URI__.'modules/smartblogfeed/rss.php</a><br>';
     	$feed_url_withid = '<a href="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/smartblogfeed/rss.php?id_category=1" target="_blank">'._PS_BASE_URL_.__PS_BASE_URI__.'modules/smartblogfeed/rss.php?id_category=[id]</a>';
                $feed_url_html = '<div class="row">
		<div class="alert alert-info"><strong>Feed URL: </strong>'.$feed_url.'</div>
		<div class="alert alert-info"><strong>Feed URL With id: </strong>'.$feed_url_withid.'</div>
	</div>';
				$html = '';
                if(Tools::isSubmit('save'.$this->name))
                {
					if(Tools::getvalue('smart_update_period') != null && Tools::getvalue('smart_update_period') != '' && 
						Tools::getvalue('smart_update_frequency') != null && Tools::getvalue('smart_update_frequency') != ''){
						Configuration::updateValue('smart_update_period', Tools::getvalue('smart_update_period'));
						Configuration::updateValue('smart_update_frequency', Tools::getvalue('smart_update_frequency'));
						$html = $this->displayConfirmation($this->l('The settings have been updated successfully.'));
						 $html .= $feed_url_html;
						$helper = $this->SettingForm();
						$html .= $helper->generateForm($this->fields_form); 
						return $html;
					}else{
						$html = $this->displayError($this->l('Required All Field'));
						$html .= $feed_url_html;
						$helper = $this->SettingForm();
						$html .= $helper->generateForm($this->fields_form);
						return $html;
					}
                }
                else
                {
                   $helper = $this->SettingForm();
                   $html .= $feed_url_html;
                   $html .= $helper->generateForm($this->fields_form);
                   return $html;
                }
            }
            
     public function SettingForm() {
     	
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fields_form[0]['form'] = array(
          'legend' => array(
          'title' => $this->l('General Setting'),
            ),
            'input' => array(
                
						array(
							'type' => 'select',
							'label' => $this->l('Update Period'),
							'name' => 'smart_update_period',
							'required' => false,
							'options' => array(
								'query' => array(
									array(
									'id_option' => 'hourly',
									'name' => 'Hourly'
									),
									array(
									'id_option' => 'daily',
									'name' => 'Daily'
									),
									array(
									'id_option' => 'weekly',
									'name' => 'Weekly'
									),
									array(
									'id_option' => 'monthly',
									'name' => 'Monthly'
									),
									array(
									'id_option' => 'yearly',
									'name' => 'Yearly'
									)
								),
								'id' => 'id_option',
								'name' => 'name'
							)
						),
						array(
						'type' => 'text',
						'label' => $this->l('Update Frequency'),
						'name' => 'smart_update_frequency',
						'size' => 60,
						'required' => false,
						'desc' => $this->l('Update Duration')
						)
            ),
            'submit' => array(
                'title' => $this->l('Save') 
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        foreach (Language::getLanguages(false) as $lang)
                            $helper->languages[] = array(
                                    'id_lang' => $lang['id_lang'],
                                    'iso_code' => $lang['iso_code'],
                                    'name' => $lang['name'],
                                    'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
                            );
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save'.$this->name.'token=' . Tools::getAdminTokenLite('AdminModules'),
            )
        );
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;       
        $helper->toolbar_scroll = true;    
        $helper->submit_action = 'save'.$this->name;
        
        $helper->fields_value['smart_update_period'] = Configuration::get('smart_update_period');
        $helper->fields_value['smart_update_frequency'] = Configuration::get('smart_update_frequency');
        return $helper;
      }
}