<?php
if (!defined('_PS_VERSION_'))
    exit;
 
require_once (_PS_MODULE_DIR_.'smartblog/classes/SmartBlogPost.php');
require_once (_PS_MODULE_DIR_.'smartblog/smartblog.php');
class smartblogsearch extends Module {
    
        public function __construct() {
        $this->name = 'smartblogsearch';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->bootstrap = true;
        $this->author = 'SmartDataSoft';
        $this->secure_key = Tools::encrypt($this->name);
        
        parent::__construct();
        
        $this->displayName = $this->l('Smart Blog Search');
        $this->description = $this->l('The Most Powerfull Presta shop Blog Search Module\'s - by smartdatasoft');
      //  $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        }
        
        public function install(){
                $langs = Language::getLanguages();
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                 if (!parent::install() 
                    || !$this->registerHook('leftColumn') 
                    || !$this->registerHook('displaySmartBlogRight') 
                    || !$this->registerHook('displaySmartBlogLeft') 
                 )
            return false;
                 return true;
            }
            
                public function uninstall() {
            if (!parent::uninstall())
                 return false;
            return true;
                }
                
          public function hookLeftColumn($params)
            {
              if(Module::isInstalled('smartblog') != 1){
                 $this->smarty->assign( array(
                              'smartmodname' => $this->name
                     ));
                        return $this->display(__FILE__, 'views/templates/front/install_required.tpl');
                }
                else
                {
                 $this->smarty->assign( array(
                              'smartsearch' => pSQL(Tools::getValue('smartsearch'))
                     ));
					return $this->display(__FILE__, 'views/templates/front/smartblogsearch.tpl');
                }
            }
            
         public function hookRightColumn($params)
            {
                 return $this->hookLeftColumn($params);
            }
         public function hookdisplaySmartBlogLeft($params)
            {
                 return $this->hookLeftColumn($params);
            }
         public function hookdisplaySmartBlogRight($params)
            {
                 return $this->hookLeftColumn($params);
            }   
}