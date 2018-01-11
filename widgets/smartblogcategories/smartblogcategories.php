<?php
if (!defined('_PS_VERSION_'))
    exit;
 
require_once (_PS_MODULE_DIR_.'smartblog/classes/BlogCategory.php');
require_once (_PS_MODULE_DIR_.'smartblog/smartblog.php');
class SmartBlogCategories extends Module {
    
        public function __construct() {
        $this->name = 'smartblogcategories';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->bootstrap = true;
        $this->author = 'SmartDataSoft';
        $this->secure_key = Tools::encrypt($this->name);
        
        parent::__construct();
        
        $this->displayName = $this->l('Smart Blog Categories');
        $this->description = $this->l('The Most Powerfull Presta shop Blog  Module\'s tag - by smartdatasoft');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        }
        public function install(){
                $langs = Language::getLanguages();
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                 if (!parent::install() || !$this->registerHook('leftColumn')  
				 || !$this->registerHook('actionsbdeletecat') 
				 || !$this->registerHook('actionsbnewcat') 
				 || !$this->registerHook('actionsbupdatecat') 
                 || !$this->registerHook('actionsbtogglecat') 
				 || !$this->registerHook('displaySmartBlogLeft') 
                 || ! $this->registerHook('displayHeader') 
                 || !Configuration::updateValue('SMART_BLOG_CATEGORIES_DHTML', 0)
                 || !Configuration::updateValue('SMART_BLOG_CATEGORIES_POST_COUNT', 1)
                 || !Configuration::updateValue('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY', 0)
                 || !Configuration::updateValue('sort_category_by', 'id_desc')
                 || !Configuration::updateValue('SMART_BLOG_CATEGORIES_DROPDOWN', 'collapse')
                 )
            return false;
                 return true;
            }

        public function uninstall() {
             $this->DeleteCache();
            if (!parent::uninstall()  || 
                !Configuration::deleteByName('SMART_BLOG_CATEGORIES_DHTML') || 
                !Configuration::deleteByName('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY') || 
                !Configuration::deleteByName('SMART_BLOG_CATEGORIES_DROPDOWN') || 
                !Configuration::deleteByName('SMART_BLOG_CATEGORIES_POST_COUNT'))
                 return false;
            return true;
                }

            public function getContent(){
                $html = '';
                // If we try to update the settings
                if (Tools::isSubmit('submitModule'))
                {

                    $sort_category_by = Tools::getValue('sort_category_by');
                    Configuration::updateValue('sort_category_by', $sort_category_by);
                    
                    $smartblogrootcat = Tools::getValue('smartblogrootcat');
                    Configuration::updateValue('smartblogrootcat', (int)$smartblogrootcat);

                    $dhtml = Tools::getValue('SMART_BLOG_CATEGORIES_DHTML');
                    Configuration::updateValue('SMART_BLOG_CATEGORIES_DHTML', (int)$dhtml);

                    Configuration::updateValue('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY', (int)Tools::getValue('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY'));

                    $dhtml = Tools::getValue('SMART_BLOG_CATEGORIES_POST_COUNT');
                    Configuration::updateValue('SMART_BLOG_CATEGORIES_POST_COUNT', (int)$dhtml);

                    Configuration::updateValue('SMART_BLOG_CATEGORIES_DROPDOWN', (int)Tools::getValue('SMART_BLOG_CATEGORIES_DROPDOWN'));

                    $html .= $this->displayConfirmation($this->l('Configuration updated'));
                    $this->_clearCache('smartblogcategories.tpl');
                    Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
                }

                $html .= $this->renderForm();
     
                return $html;
            }

        
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display as dropdown'),
                        'name' => 'SMART_BLOG_CATEGORIES_DROPDOWN',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show only assigned categories of post'),
                        'name' => 'SMART_BLOG_ASSIGNED_CATEGORIES_ONLY',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Sort Sidebar Category List By'),
                        'name' => 'sort_category_by',
                        'desc' => 'Blog category list that is shown in the blog page sidebars',
                        'required' => false,
                        'options' => array(
                            'query' => array( 
                                array(
                                    'id_option' => 'name_ASC',
                                    'name' => 'Name ASC (A-Z)'
                                ),
                                array(
                                    'id_option' => 'name_DSC',
                                    'name' => 'Name DESC (Z-A)'
                                ),
                                array(
                                    'id_option' => 'id_ASC',
                                    'name' => 'Id ASC'
                                ),
                                array(
                                    'id_option' => 'id_ASC',
                                    'name' => 'Id DESC'
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Root Category (HOME)'),
                        'name' => 'smartblogrootcat',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show post counts'),
                        'name' => 'SMART_BLOG_CATEGORIES_POST_COUNT',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Dynamic'),
                        'name' => 'SMART_BLOG_CATEGORIES_DHTML',
                        'desc' => $this->l('Activate dynamic (animated) mode for category sublevels.'),
                        'values' => array(
                                    array(
                                        'id' => 'active_on',
                                        'value' => 1,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'active_off',
                                        'value' => 0,
                                        'label' => $this->l('Disabled')
                                    )
                                ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }
     public function getConfigFieldsValues()
    {
        return array(
            'sort_category_by' => Tools::getValue('sort_category_by', Configuration::get('sort_category_by')),
            'smartblogrootcat' => Tools::getValue('smartblogrootcat', Configuration::get('smartblogrootcat')),
            'SMART_BLOG_CATEGORIES_DHTML' => Tools::getValue('SMART_BLOG_CATEGORIES_DHTML', Configuration::get('SMART_BLOG_CATEGORIES_DHTML')),
            'SMART_BLOG_CATEGORIES_POST_COUNT' => Tools::getValue('SMART_BLOG_CATEGORIES_POST_COUNT', Configuration::get('SMART_BLOG_CATEGORIES_POST_COUNT')),
            'SMART_BLOG_ASSIGNED_CATEGORIES_ONLY' => Tools::getValue('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY', Configuration::get('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY')),
            'SMART_BLOG_CATEGORIES_DROPDOWN' => Tools::getValue('SMART_BLOG_CATEGORIES_DROPDOWN', Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN')),            

        );
    }

     public function hookHeader()
    {
     
            $this->_assignMedia();
    }

    protected function _assignMedia()
    {
        $this->context->controller->addCss(($this->_path).'css/smartblogcategories.css');
        // if(Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN'))
        //     $this->context->controller->addCss(($this->_path).'css/smartblogcategories-dropdown.css');
    }
         public function hookLeftColumn($params)
            {
   

        if (!$this->isCached('smartblogcategories.tpl')) {
            $view_data = array();
            $id_lang = $this->context->language->id;

      //       $BlogCategory = new BlogCategory();

      //       $categories = $BlogCategory->getCategories($this->context->language->id, false);

      //       if(Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN')){
      //           $smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');
      //           $id_cat = null;
      //           $sb = new smartblog();
      //           switch ($smartblogurlpattern) {
      //               case 1:
      //                   $slug = Tools::getValue('slug');
      //                   $id_cat = $sb->categoryslug2id($slug);
      //                   break;
      //               case 2:
      //                   $id_cat = pSQL(Tools::getvalue('id_cat'));
      //                   break;
      //               case 3:
      //                   $id_cat = pSQL(Tools::getvalue('id_cat'));
      //                   break;
      //               default:
      //                   $id_cat = pSQL(Tools::getvalue('id_cat'));
      //           }

      //           $html_categories = $BlogCategory->recurseCMSCategoryClickToGo($categories,1,1,$id_cat,1);
      //       } else {
      //           $root_id = (Configuration::get('smartblogrootcat'))? 0 : 1;
      //           $html_categories = BlogCategory::getCategoryQuickAccess($root_id,'');
      //       }

      //       $isDhtml = Configuration::get('SMART_BLOG_CATEGORIES_DHTML');
 

      //       $this->smarty->assign(array(
      //            'isDhtml'=> $isDhtml,
      //           'categories' => $html_categories,
      //           'display_dropdown' => Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN')
      //       ));


      // return $this->display(__FILE__, 'views/templates/front/smartblogcategories.tpl');

            /*arif call*/

            $maxdepth = 4;
            // Get all groups for this customer and concatenate them as a string: "1,2,3..."
            $groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
      
            $active = 1;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'smart_blog_category` c
            LEFT JOIN `'._DB_PREFIX_.'smart_blog_category_lang` cl ON c.`id_smart_blog_category` = cl.`id_smart_blog_category`
            WHERE   `id_lang` = '.(int)$id_lang.'
            '.($active ? 'AND `active` = 1' : '').'
            ORDER BY `meta_title` ASC');

            $resultParents = array();
            $resultIds = array();

            foreach ($result as &$row)
            {
                $resultParents[$row['id_parent']][] = &$row;
                $resultIds[$row['id_smart_blog_category']] = &$row;
            }
        $root_id = (Configuration::get('smartblogrootcat') || Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN'))? 0 : 1;
        $blockCategTree = $this->getTree($resultParents, $resultIds, 10, $root_id);


        //  echo "<pre>";
        // print_r($blockCategTree);
        // echo "</pre>";
        $isDhtml = Configuration::get('SMART_BLOG_CATEGORIES_DHTML');
        $this->smarty->assign('blockCategTree', $blockCategTree);
        $this->smarty->assign('isDhtml', $isDhtml);
        $this->smarty->assign('isDropdown', Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN'));
        $this->smarty->assign('select', true);

            if (file_exists(_PS_THEME_DIR_.'modules/smartblogcategories/new-smartblogcategories.tpl'))
                $this->smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategories/category-tree-branch.tpl');
            else
                $this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'smartblogcategories/category-tree-branch.tpl');

        }
       return $this->display(__FILE__, 'new-smartblogcategories.tpl');
            }


    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
    {
        if (is_null($id_category))
            $id_category = $this->context->shop->getCategory();
        $children = array();

        $smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');
        $sb = new smartblog();
        switch ($smartblogurlpattern) {
            case 1:
                $slug = Tools::getValue('slug');
                $id_cat = $sb->categoryslug2id($slug);
                break;
            case 2:
                $id_cat = pSQL(Tools::getvalue('id_cat'));
                break;
            case 3:
                $id_cat = pSQL(Tools::getvalue('id_cat'));
                break;
            default:
                $id_cat = pSQL(Tools::getvalue('id_cat'));
        }

        $BlogCategory = new BlogCategory();
        $tmp_all_child = $id_category.$BlogCategory->getAllChildCategory($id_category, '');
        $tmp_all_child = array_values(array_unique(explode(",",$tmp_all_child)));
        $tmp_post_of_child = $BlogCategory->getTotalPostOfChildParent($tmp_all_child);
        $total_post = (Configuration::get('SMART_BLOG_CATEGORIES_POST_COUNT'))? ' ('.$tmp_post_of_child.')' : '';
        $level_depth = '';
        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
            foreach ($resultParents[$id_category] as $subcat)
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_smart_blog_category'], $currentDepth + 1);
        if (isset($resultIds[$id_category]))
        {
            $name = $resultIds[$id_category]['name'];
            $desc = $resultIds[$id_category]['description'];
            $link_rewrite = $resultIds[$id_category]['link_rewrite'];

            $smartbloglink = new SmartBlogLink();
            $link = $smartbloglink->getSmartBlogCategoryLink($id_category,$link_rewrite);

            $level_depth = str_repeat('&nbsp;', $resultIds[$id_category]['level_depth'] * 2);
        }
        else
            $link = $name = $desc = '';

if(Configuration::get('smartblogrootcat') && Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN')){
    $return = array(
        'id' => $id_category,
        'link' => $link,
        'total_post' => $tmp_post_of_child,
        'name' => $name . $total_post,
        'desc'=> $desc,
        'level_depth'=> $level_depth,
        'id_cat'=> $id_cat,
        'children' => $children
    );
} elseif(!Configuration::get('smartblogrootcat') && Configuration::get('SMART_BLOG_CATEGORIES_DROPDOWN')) {
    if($name == 'Home'){ $name=''; $total_post=''; }
    $return = array(
        'id' => $id_category,
        'link' => $link,
        'total_post' => $tmp_post_of_child,
        'name' => $name . $total_post,
        'desc'=> $desc,
        'level_depth'=> $level_depth,
        'id_cat'=> $id_cat,
        'children' => $children
    );
}else {
    if(Configuration::get('SMART_BLOG_ASSIGNED_CATEGORIES_ONLY'))
        if($tmp_post_of_child == 0){ $name=''; $total_post=''; }
    $return = array(
        'id' => $id_category,
        'link' => $link,
        'total_post' => $tmp_post_of_child,
        'name' => $name . $total_post,
        'desc'=> $desc,
        'level_depth'=> $level_depth,
        'id_cat'=> $id_cat,
        'children' => $children
    );
}



        return $return;
    }  
         public function hookRightColumn($params)
            {
                 // return $this->hookLeftColumn($params);
            }
         public function hookdisplaySmartBlogLeft($params)
            {
                 return $this->hookLeftColumn($params);
            }
         public function hookdisplaySmartBlogRight($params)
            {
                 // return $this->hookLeftColumn($params);
            } 
		public function DeleteCache()
            {
				return $this->_clearCache('smartblogcategories.tpl', $this->getCacheId());
			}
		public function hookactionsbdeletecat($params)
            {
                 return $this->DeleteCache();
            }
		public function hookactionsbnewcat($params)
            {
                 return $this->DeleteCache();
            }
		public function hookactionsbupdatecat($params)
            {
                return $this->DeleteCache();
            }
		public function hookactionsbtogglecat($params)
            {
                return $this->DeleteCache();
            }
	
}