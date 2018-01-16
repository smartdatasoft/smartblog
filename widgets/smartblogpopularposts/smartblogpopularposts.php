<?php
if (!defined('_PS_VERSION_'))
    exit;

require_once (_PS_MODULE_DIR_.'smartblog/classes/SmartBlogPost.php');
require_once (_PS_MODULE_DIR_.'smartblog/smartblog.php');
class SmartBlogPopularPosts extends Module {


	public function __construct() {
	$this->name = 'smartblogpopularposts';
	$this->tab = 'front_office_features';
  $this->version = '2.0.1';
  $this->bootstrap = true;
	$this->author = 'SmartDataSoft';
	$this->secure_key = Tools::encrypt($this->name);

	parent::__construct();

	$this->displayName = $this->l('Smart Blog Popular Posts');
	$this->description = $this->l('The Most Powerfull Presta shop Blog  Module\'s Popular Posts - by smartdatasoft');
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
	 || !$this->registerHook('actionsbsingle')
	 )
	return false;
            Configuration::updateGlobalValue('smartshowpopularpost',2);
	 return true;
    }
    
       public function uninstall() {
	    $this->DeleteCache();
            if (!parent::uninstall())
                 return false;
            Configuration::deleteByName('smartshowpopularpost');
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
               if (!$this->isCached('smartblogpopularposts.tpl', $this->getCacheId()))
                       {
                           global $smarty;

                           $id_lang = $this->context->language->id;

                       $posts =  SmartBlogPost::getPopularPosts($id_lang);
                       $i = 0;
                           foreach ($posts as $post) {
                           	$employee = new  Employee( $post['id_author']);
                           	
                           	$posts[$i]['lastname'] = $employee->lastname;
                           	$posts[$i]['firstname'] = $employee->firstname;
                               if (file_exists(_PS_MODULE_DIR_.'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg') )
                               {
                                  $image =   $post['id_smart_blog_post'];
                                  $posts[$i]['post_img'] = $image;
                               }
                               else
                               {
                                  $posts[$i]['post_img'] ='no';
                               }
                               $i++;
                           }

            $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
   
            $smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);


            $this->smarty->assign(array(
                 'smartbloglink' => $smartbloglink,
                  'posts' => $posts
                               ));
                       }
                   return $this->display(__FILE__, 'views/templates/front/smartblogpopularposts.tpl',$this->getCacheId());
                }
            }
		public function DeleteCache()
            {
				return $this->_clearCache('smartblogpopularposts.tpl', $this->getCacheId());
			}
          public function hookRightColumn($params) {
                 return $this->hookLeftColumn($params);
            }
            
         public function hookdisplaySmartBlogLeft($params) {
                 return $this->hookLeftColumn($params);
            }
            
         public function hookdisplaySmartBlogRight($params){
                 return $this->hookLeftColumn($params);
            }    
            
         public function getContent(){
         
                $html = '';
                if(Tools::isSubmit('save'.$this->name))
                {
                    Configuration::updateValue('smartshowpopularpost', Tools::getvalue('smartshowpopularpost'));
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
                    'label' => $this->l('Number of popular Posts Show'),
                    'name' => 'smartshowpopularpost',
                    'size' => 15,
                    'required' => true
                )                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
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
        
        $helper->fields_value['smartshowpopularpost'] = Configuration::get('smartshowpopularpost');
        return $helper;
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
		public function hookactionsbsingle($params)
            {
                 return $this->DeleteCache();
            }
}