<?php

class AdminSmartblogAddonsController extends ModuleAdminController
{

    public $asso_type = 'shop';

    public function __construct()
    {
        $this->module = 'smartblog';
        $this->toolbar_title = 'SmartBlog Addons';

        $this->lang = true;
        $this->context = Context::getContext();
        $this->bootstrap = true;

        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {

        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_SMARTBLOG_JS_DIR_ . 'addons.js');
        Media::addJsDef(array('sblogaddons_ajaxurl' => $this->context->link->getAdminLink('AdminSmartblogAddons'), 'controller_name' => "AdminSmartblogAddons"));
        $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/theme.css', 'all', 99999);
    }

    public function initContent()
    {
        parent::initContent();

        $addons_arr = array(
            'smartblogsearch' => array(
                'title' => "Smart Blog Search",
                'version' => '3.0.0',
                'description' => "Search your Blogs with this free module.",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogtag' => array(
                'title' => "Smart Blog Tag",
                'version' => '3.0.0',
                'description' => "Add Tags to Your Awesome blog posts With This Module.",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogarchive' => array(
                'title' => "Smart Blog Archive",
                'version' => '3.0.0',
                'description' => "The Archive Module for the Most Powerful PrestaShop Blog Module.",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogcategories' => array(
                'title' => "Smart Blog Categories",
                'version' => '3.0.0',
                'description' => "Show Categories of Your Blogs with This Addon of the Most Powerful PrestaShop Blog Module.",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogaddthisbutton' => array(
                'title' => "Smart Blog Moduel Add This Button",
                'version' => '3.0.0',
                'description' => "Share you blog in various social media network with this module.",
                'price' => 2,
                'd_link' => '',
            ),
            'smartblogdisqus' => array(
                'title' => "Smart Blog Disqus Comments",
                'version' => '2.0.0',
                'description' => "This module integrate Disqus in the most powerful prestaShop Blog Module.",
                'price' => 3,
                'd_link' => '',
            ),
            'smartblogfbcoments' => array(
                'title' => "Smart Blog Facebook Comments",
                'version' => '2.0.0',
                'description' => "Integrate facebook comments in the most powerful PrestaShop Blog module.",
                'price' => 3,
                'd_link' => '',
            ),
            'smartblogfeed' => array(
                'title' => "RSS SmartBlog Feed",
                'version' => '3.0.0',
                'description' => "The Most Powerful PrestaShop Blog  Module's RSS feed Extension - by smartdatasoft",
                'price' => 2,
                'd_link' => '',
            ),
            'smartbloghomelatestnews' => array(
                'title' => "SmartBlog Home Latest News",
                'version' => '3.0.0',
                'description' => "Show Latest Posts of Your Awesome Blogs with This Module - by smartdatasoft",
                'price' => 5,
                'd_link' => '',
            ),
            'smartbloglatestcomments' => array(
                'title' => "Smart Blog Latest Comments",
                'version' => '3.0.0',
                'description' => "Show Latest Comments of Your Awesome Blogs with This Module - by smartdatasoft",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogpopularposts' => array(
                'title' => "Smart Blog Popular Posts",
                'version' => '3.0.0',
                'description' => "Show Popular Posts of Your Awesome Blogs with This Module - by smartdatasoft",
                'price' => 3,
                'd_link' => '',
            ),
            'smartblogrecentposts' => array(
                'title' => "Smart Blog Recent Posts",
                'version' => '3.0.0',
                'description' => "Show Recent Posts of Your Awesome Blogs with This Module - by smartdatasoft",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogrelatedposts' => array(
                'title' => "Smart Blog Categories",
                'version' => '2.0.0',
                'description' => "Show Related Posts of Your Awesome Blogs with This Module - by smartdatasoft",
                'price' => 0,
                'd_link' => '',
            ),
            'smartblogrelatedproducts' => array(
                'title' => "Smart Blog Related Product",
                'version' => '2.0.0',
                'description' => "Show Related Products of Your Awesome Blogs with This Module - by smartdatasoft",
                'price' => 6,
                'd_link' => '',
            ),
        );

        foreach ($addons_arr as $name => $addon) {

            if (file_exists(_PS_MODULE_DIR_ . $name)) {
                if (Module::isInstalled($name) && Module::isEnabled($name)) {
                    $addons_arr[$name]['installed'] = 1;
                } else {
                    $addons_arr[$name]['installed'] = 0;
                }
            } else {
                $addons_arr[$name]['installed'] = -1;
            }
        }

        $template_file = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/form.tpl';

        $validity = Configuration::get('SMARTBLOG_LICENSE_VALIDITY');

        $this->context->smarty->assign(array(
            'addons' =>  $addons_arr,
            'smartblog_validity' =>  $validity,
            'image_url' => _MODULE_SMARTBLOG_IMAGE_URL_
        ));

        $content = $this->context->smarty->fetch($template_file);
        $this->context->smarty->assign(array(
            'content' =>  $content,
        ));
    }

    public function ajaxProcessActionAddon()
    {

        $module = Tools::getValue("addon");
        $act = Tools::getValue("installed");
        if ($act) {
            if (Module::isInstalled($module) && Module::isEnabled($module)) {
                $mod_ins = Module::getInstanceByName(trim($module));
                $mod_ins->uninstall();
                echo '0';
            }
        } else {
            if (!Module::isInstalled($module)) {
                $mod_ins = Module::getInstanceByName(trim($module));
                $mod_ins->install();
                echo '1';
            }
        }
        die();
    }
    /**
     * AjaxProcessDownNow processes downloading the update.
     *
     * @return void
     */
    public function ajaxProcessDownNow()
    {
        $down_url = Tools::getValue('down_url');

        if ($down_url == '') {
            include_once _MODULE_SMARTBLOG_CLASS_DIR_ . 'SmartBlogLicense.php';
            $licence_obj = new SmartBlogLicense(0);
            $purchase_code = Configuration::get('SMARTBLOG_LICENSE');
            $d_link = $licence_obj->smartblog_get_update($purchase_code, 0);

            $return_arr = array();
            $return_arr['status'] = 2;
            $return_arr['msg'] = $d_link;
            echo json_encode($return_arr);
            die();
        } else {
            $down_path = _PS_MODULE_DIR_;
            $newfile   = $down_path . '/classy_productextratab.zip';

            $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $down_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
            $result = curl_exec($ch);
            file_put_contents($newfile, $result);
            $last = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            if (curl_errno($ch)) {
                $return_arr = array();
                $return_arr['status'] = 0;
                $return_arr['msg'] = 'Could not update the module. Please try again.';
                echo json_encode($return_arr);
            } else {
                $zip = new \ZipArchive();
                if ($zip->open($newfile) === true) {
                    $zip->extractTo(_PS_MODULE_DIR_);
                    $zip->close();
                }
                Configuration::updateValue('SMARTBLOG_STABLE', '');
                Configuration::updateValue('SMARTBLOG_DLINK', '');
                $return_arr = array();
                $return_arr['status'] = 1;
                $return_arr['msg'] = 'Updated';
                echo json_encode($return_arr);
            }
            curl_close($ch);
            die();
        }
    }
}
