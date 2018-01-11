<?php
if (!defined('_PS_VERSION_'))
    exit;
 
require_once (_PS_MODULE_DIR_.'smartblog/classes/SmartBlogPost.php');
require_once (_PS_MODULE_DIR_.'smartblog/smartblog.php');
class smartblogarchive extends Module {
    
        public function __construct() {
        $this->name = 'smartblogarchive';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->bootstrap = true;
        $this->author = 'SmartDataSoft';
        $this->secure_key = Tools::encrypt($this->name);
        
        parent::__construct();
        
        $this->displayName = $this->l('Smart Blog Archive');
        $this->description = $this->l('The Most Powerfull Presta shop Blog Archive Module\'s - by smartdatasoft');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        }
        
        public function install(){
                $langs = Language::getLanguages();
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                 if (!parent::install() || !$this->registerHook('leftColumn') 
                 	|| !$this->registerHook('displaySmartBlogLeft')
                 || !$this->registerHook('displaySmartBlogRight')
				 || !$this->registerHook('actionsbdeletepost')
				 || !$this->registerHook('actionsbnewpost')
				 || !$this->registerHook('actionsbupdatepost')
				 || !$this->registerHook('actionsbtogglepost')
				 || ! $this->registerHook('displayHeader') 
				 || !Configuration::updateValue('smartblogarchive_type', 2) 
				 || !Configuration::updateValue('SMART_BLOG_ARCHIVE_DHTML', 0) 
				 )
            return false;
                 return true;
            }
        
        public function uninstall() {
			 $this->DeleteCache();
            if (!parent::uninstall()  || 
            	!Configuration::deleteByName('smartblogarchive_type') ||
            	!Configuration::deleteByName('SMART_BLOG_ARCHIVE_DHTML'))
                 return false;
            return true;
                }

		    public function getContent()
			{
				$html = '';
				// If we try to update the settings
				if (Tools::isSubmit('submitModule'))
				{
					Configuration::updateValue('smartblogarchive_type', Tools::getValue('smartblogarchive_type'));
					$dhtml = Tools::getValue('SMART_BLOG_ARCHIVE_DHTML');
					Configuration::updateValue('SMART_BLOG_ARCHIVE_DHTML', (int)$dhtml);

					$html .= $this->displayConfirmation($this->l('Configuration updated'));
					$this->_clearCache('smartblogarchive.tpl');
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
						'type' => 'radio',
						'label' => $this->l('How you like to display Archive Type'),
						'name' => 'smartblogarchive_type',
						//'hint' => $this->l('Select which  way.'),
						'values' => array(

							array(
								'id' => 'year',
								'value' => 1,
								'label' => $this->l('Year Wise')
							),
							array(
								'id' => 'month_year',
								'value' => 2,
								'label' => $this->l('Month & Year Wise')
							),

						)
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Dynamic'),
						'name' => 'SMART_BLOG_ARCHIVE_DHTML',
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


         public function hookLeftColumn($params) {
             if(Module::isInstalled('smartblog') != 1){
                 $this->smarty->assign( array(
                              'smartmodname' => $this->name
                     ));
                        return $this->display(__FILE__, 'views/templates/front/install_required.tpl');
                }
                else
                {
				if (!$this->isCached('smartblogarchive.tpl', $this->getCacheId()))
                    {
						$view_data = array();
						$id_lang = $this->context->language->id;
						$SmartBlogPost = new SmartBlogPost();
						if(Configuration::get('smartblogarchive_type') == 1){
							$archives = $SmartBlogPost->getArchiveOld();
						} else {
							$archives = $SmartBlogPost->getArchive();
						}

						$this->smarty->assign( array(
								'archive_type' => Configuration::get('smartblogarchive_type'),
								'isDhtml'=> Configuration::get('SMART_BLOG_ARCHIVE_DHTML'),
								'archives' => $archives
						 ));
					}
                    return $this->display(__FILE__, 'views/templates/front/smartblogarchive.tpl',$this->getCacheId());
                }
            }
            
         public function hookRightColumn($params){
               return $this->hookLeftColumn($params);
            }
            
         public function hookdisplaySmartBlogLeft($params){
               return $this->hookLeftColumn($params);
            }

     public function hookHeader()
	{
	 
			$this->_assignMedia();
	}

	protected function _assignMedia()
	{
		$this->context->controller->addCss(($this->_path).'css/smartblogarchive.css');
	}
  public function DeleteCache()
            {
				return $this->_clearCache('smartblogarchive.tpl', $this->getCacheId());
			}
	public function hookactionsbdeletepost($params)
            {
                 return $this->DeleteCache();
            }
	public function hookactionsbnewpost($params)
            {
                 return $this->DeleteCache();
            }
	public function hookactionsbupdatepost($params)
            {
                return $this->DeleteCache();
            }
	public function hookactionsbtogglepost($params)
            {
                return $this->DeleteCache();
            }
         public function hookdisplaySmartBlogRight($params) {
                return $this->hookLeftColumn($params);
            }
     public function getConfigFieldsValues()
	{
		return array(
			'smartblogarchive_type' => Tools::getValue('smartblogarchive_type', Configuration::get('smartblogarchive_type')),
			'SMART_BLOG_ARCHIVE_DHTML' => Tools::getValue('SMART_BLOG_ARCHIVE_DHTML', Configuration::get('SMART_BLOG_ARCHIVE_DHTML')),
		);
	}
}