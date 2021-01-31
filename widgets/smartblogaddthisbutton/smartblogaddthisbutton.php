<?php

if (!defined('_PS_VERSION_'))
    exit;
require_once (_PS_MODULE_DIR_ . 'smartblog/smartblog.php');

class SmartBlogAddThisButton extends Module
{

    public function __construct()
    {
        $this->name = 'smartblogaddthisbutton';
        $this->tab = 'front_office_features';
        $this->version = '2.0.3';
        $this->bootstrap = true;
        $this->author = 'SmartDataSoft';
        $this->secure_key = Tools::encrypt($this->name);

        parent::__construct();

        $this->displayName = $this->l('Smart Blog Moduel Add This Button');
        $this->description = $this->l('The Most Powerfull Presta shop Blog  Module\'s Add This Button - by smartdatasoft');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
    }

    public function install()
    {

        $langs = Language::getLanguages();
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        /* Adds Module */
        if (parent::install() &&
                $this->registerHook('displaySmartAfterPost') && $this->registerHook('displayHeader')
        ) {
            /* Sets up configuration */
            $res = Configuration::updateValue('SMARTBBLOG_ADD_THIS_API_KEY', '');
        }
        return (bool) $res;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;
        return true;
    }

    public function getContent()
    {
        $output = '';
        $errors = array();
        if (Tools::isSubmit('submitSmartBlogAddThis')) {
            $api_kay = Tools::getValue('SMARTBBLOG_ADD_THIS_API_KEY');

            Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('smartblogaddthisbutton.tpl'));
            Configuration::updateValue('SMARTBBLOG_ADD_THIS_API_KEY', $api_kay);


            $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
        }

        return $output . $this->renderForm();
    }

    public function hookdisplaySmartAfterPost($params)
    {

        $this->smarty->assign(
                array(
                    'addthis_api_key' => Configuration::get('SMARTBBLOG_ADD_THIS_API_KEY')
                )
        );

        return $this->display(__FILE__, 'views/templates/front/smartblogaddthisbutton.tpl');
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('Add Your own addship publisher id to track your blog post status.'),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Publisher Id'),
                        'name' => 'SMARTBBLOG_ADD_THIS_API_KEY',
                        'class' => 'fixed-width-ls',
                        'desc' => $this->l('Set the Publisher Id from  https://www.addthis.com '),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSmartBlogAddThis';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
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
            'SMARTBBLOG_ADD_THIS_API_KEY' => Tools::getValue('SMARTBBLOG_ADD_THIS_API_KEY', Configuration::get('SMARTBBLOG_ADD_THIS_API_KEY')),
        );
    }

    public function hookDisplayHeader($params)
    {

        /* Server Params */
        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

        $smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);


        $smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');
        $id_post = null;
        switch ($smartblogurlpattern) {
            case 1:
                $SmartBlog = new smartblog();
                $slug = Tools::getValue('slug');
                $id_post = $SmartBlog->slug2id($slug);
                break;
            case 2:
                $id_post = pSQL(Tools::getvalue('id_post'));
                break;
            case 3:
                $id_post = pSQL(Tools::getvalue('id_post'));
                break;
            default:
                $id_post = pSQL(Tools::getvalue('id_post'));
        }
        
        $obj_post = new SmartBlogPost($id_post, true, $this->context->language->id, $this->context->shop->id);
        if (!Validate::isLoadedObject($obj_post))
            return;


        if (!$this->isCached('smartblog_socialsharing_header.tpl', $this->getCacheId('smartblog_socialsharing_header|' . (isset($obj_post->id) && $obj_post->id ? (int) $obj_post->id : '')))) {

            $this->context->smarty->assign(array(
                'post' =>  (array)$obj_post,
                'post_img' => isset($obj_post->id) ? $obj_post->id : '',
                'smartbloglink' => $smartbloglink,
                'link_rewrite' => isset($obj_post->link_rewrite) && $obj_post->link_rewrite ? $obj_post->link_rewrite : '',
            ));
        }

        return $this->display(__FILE__, 'smartblog_socialsharing_header.tpl', $this->getCacheId('smartblog_socialsharing_header|' . (isset($obj_post->id) && $obj_post->id ? (int) $obj_post->id : '')));
    }

}