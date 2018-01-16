<?php
if (!defined('_PS_VERSION_'))
    exit;
require_once (_PS_MODULE_DIR_.'smartblog/classes/Blogcomment.php');
require_once (_PS_MODULE_DIR_.'smartblog/smartblog.php');
class SmartblogLatestComments extends Module{

        public function __construct() {
        $this->name = 'smartbloglatestcomments';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->bootstrap = true;
        $this->author = 'SmartDataSoft';
        $this->secure_key = Tools::encrypt($this->name);
        
        parent::__construct();
        
        $this->displayName = $this->l('Smart Blog Latest Comments');
        $this->description = $this->l('The Most Powerfull Presta shop Blog  Module\'s Latest Comments - by smartdatasoft');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
    }
    
        public function install(){
        $langs = Language::getLanguages();
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
         if (!parent::install() || !$this->registerHook('leftColumn') 
     || !$this->registerHook('displaySmartBlogLeft') 
		 || !$this->registerHook('displaySmartBlogRight') 
		 || !$this->registerHook('actionsbpostcomment') 
         )
    return false;
         
         Configuration::updateGlobalValue('smartshowhomecomments',2);
        
         return true;
    }
    
         public function uninstall() {
		  $this->DeleteCache();
            if (!parent::uninstall())
                 return false;
            Configuration::deleteByName('smartshowhomecomments');
            return true;
                }
                
         public function hookLeftColumn($params){
             if(Module::isInstalled('smartblog') != 1){
                 $this->smarty->assign( array(
                              'smartmodname' => $this->name
                     ));
                        return $this->display(__FILE__, 'views/templates/front/install_required.tpl');
                }
                else
                {
             if (!$this->isCached('smartbloglatestcomments.tpl', $this->getCacheId()))
                    {
                  /* Server Params */
                  $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                  $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

                  $smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);


                        $id_lang = $this->context->language->id;
                        $latesComments =  Blogcomment::getLatestComments($id_lang);
                        $this->smarty->assign( array(
                            'smartbloglink' => $smartbloglink,
                            'latesComments' => $latesComments,
                            'modules_dir' => _PS_BASE_URL_.__PS_BASE_URI__.'modules/'
                            ));
                    }
                return $this->display(__FILE__, 'views/templates/front/smartbloglatestcomments.tpl',$this->getCacheId());
                }
            }
		public function DeleteCache()
            {
				return $this->_clearCache('smartbloglatestcomments.tpl', $this->getCacheId());
			}
		public function hookactionsbpostcomment($params)
            {
                 return $this->DeleteCache();
            }
          public function hookRightColumn($params) {
                 return $this->hookLeftColumn($params);
            }
            
         public function hookdisplaySmartBlogLeft($params){
                 return $this->hookLeftColumn($params);
            }
            
         public function hookdisplaySmartBlogRight($params) {
                 return $this->hookLeftColumn($params);
            }    
        public function getContent(){
         
                $html = '';
                if(Tools::isSubmit('save'.$this->name))
                {
                    Configuration::updateValue('smartshowhomecomments', Tools::getvalue('smartshowhomecomments'));
                    $html = $this->displayConfirmation($this->l('The settings have been updated successfully.'));
                    $helper = $this->SettingForm();
                    $html .= $helper->generateForm($this->fields_form); 
                    return $html;
                }
                else
                {
                   $helper = $this->SettingForm();
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
                    'type' => 'text',
                    'label' => $this->l('Number of Comments Show'),
                    'name' => 'smartshowhomecomments',
                    'size' => 15,
                    'required' => true
                )                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
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
        
        $helper->fields_value['smartshowhomecomments'] = Configuration::get('smartshowhomecomments');
        return $helper;
      }
}