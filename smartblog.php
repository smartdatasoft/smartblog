<?php
if (!defined('_PS_VERSION_'))
    exit;

define('_MODULE_SMARTBLOG_DIR_', _PS_MODULE_DIR_ . 'smartblog/images/');
define('_MODULE_SMARTBLOG_GALLARY_DIR_', _PS_MODULE_DIR_ . 'smartblog/gallary/');

require_once __DIR__.'/widgets/autoload.php';

require_once (dirname(__FILE__) . '/classes/smartpromotion.php');
require_once (dirname(__FILE__) . '/classes/BlogCategory.php');
require_once (dirname(__FILE__) . '/classes/BlogImageType.php');
require_once (dirname(__FILE__) . '/classes/BlogTag.php');
require_once (dirname(__FILE__) . '/classes/SmartBlogPost.php');
require_once (dirname(__FILE__) . '/classes/SmartBlogHelperTreeCategories.php');
require_once (dirname(__FILE__) . '/classes/Blogcomment.php');
require_once (dirname(__FILE__) . '/classes/BlogPostCategory.php');
require_once (dirname(__FILE__) . '/classes/SmartBlogLink.php');
require_once (dirname(__FILE__) . '/controllers/admin/AdminAboutUsController.php');


class smartblog extends Module
{

    public $nrl;
    public $crl;
    public $erl;
    public $capl;
    public $warl;
    public $sucl;

    public function __construct(){
        $this->name = 'smartblog';
        $this->tab = 'front_office_features';
        $this->version = '3.0.0';
        $this->author = 'SmartDataSoft';
        $this->need_upgrade = true;
        $this->controllers = array('category', 'details', 'search', 'tagpost');
        $this->secure_key = Tools::encrypt($this->name);
        $this->smart_shop_id = Context::getContext()->shop->id;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Smart Blog');

        $this->nrl = $this->l('Name is required');
        $this->crl = $this->l('Comment must be between 25 and 1500 characters!');
        $this->erl = $this->l('E-mail address not valid !');
        $this->capl = $this->l('Captcha is not valid');
        $this->warl = $this->l('Warning: Please check required form bellow!');
        $this->sucl = $this->l('Your comment successfully submitted.');

        $this->description = $this->l('The Most Powerfull Prestashop Blog  Module - by smartdatasoft');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        $this->module_key = '5679adf718951d4bc63422b616a9d75d';

    }

    public function install()
    {

        Configuration::updateGlobalValue('smartblogrootcat', '1');

        Configuration::updateGlobalValue('smartpostperpage', '5');
        Configuration::updateGlobalValue('smartshowauthorstyle', '1');
        Configuration::updateGlobalValue('smartshowauthor', '1');
        Configuration::updateGlobalValue('smartmainblogurl', 'smartblog');
        Configuration::updateGlobalValue('smartusehtml', '1');
        Configuration::updateGlobalValue('smartshowauthorstyle', '1');
        Configuration::updateGlobalValue('smartenablecomment', '1');
        Configuration::updateGlobalValue('smartenableguestcomment', '1');
        Configuration::updateGlobalValue('smartcaptchaoption', '1');
        Configuration::updateGlobalValue('smartshowviewed', '1');
        Configuration::updateGlobalValue('smartshownoimg', '1');
        Configuration::updateGlobalValue('smartshowcolumn', '3');
        Configuration::updateGlobalValue('smartacceptcomment', '1');
        Configuration::updateGlobalValue('smartcustomcss', '');
        Configuration::updateGlobalValue('smartdisablecatimg', '1');
        Configuration::updateGlobalValue('smartdataformat', 'm/d/Y H:i:s');
        Configuration::updateGlobalValue('smartblogurlpattern', 2);

        Configuration::updateGlobalValue('smartblogmetatitle', 'Smart Bolg Title');
        Configuration::updateGlobalValue('smartblogmetakeyword', 'smart,blog,smartblog,prestashop blog,prestashop,blog');
        Configuration::updateGlobalValue('smartblogmetadescrip', 'Prestashop powerfull blog site developing module. It has hundrade of extra plugins. This module developed by SmartDataSoft.com');
        Configuration::updateGlobalValue('smartshowhomepost', 4);

        

        
        
        $this->addquickaccess(); 
        $this->htaccessCreate();
        if (!parent::install() || !$this->registerHook('displayHeader') || 
            !$this->registerHook('header') ||
			!$this->registerHook('moduleRoutes') ||
            !$this->registerHook('displayBackOfficeHeader') ||

			!$this->registerHook('actionsbnewpost') ||
			!$this->registerHook('actionsbupdatepost') ||
			!$this->registerHook('actionsbdeletepost') ||
			!$this->registerHook('actionsbtogglepost') ||
			!$this->registerHook('actionsbnewcat') ||
			!$this->registerHook('actionsbupdatecat') ||
			!$this->registerHook('actionsbdeletecat') ||
			!$this->registerHook('actionsbtogglecat') ||
			!$this->registerHook('actionsbpostcomment') ||
			!$this->registerHook('actionsbappcomment') ||
			!$this->registerHook('actionsbsingle') ||
			!$this->registerHook('actionsbcat') ||
			!$this->registerHook('actionsbsearch') ||
			!$this->registerHook('actionsbheader') ||
            !$this->registerHook('actionHtaccessCreate') 
        )
            return false;
        $sql = array();
        require_once(dirname(__FILE__) . '/sql/install.php');
        foreach ($sql as $sq) :
            if (!Db::getInstance()->Execute($sq))
                return false;
        endforeach;

        $this->CreateSmartBlogTabs();
        $this->SampleDataInstall();
        $this->installDummyData();
        $this->SmartHookInsert();
        $this->SmartHookRegister();

        return true;
    }

    public function installDummyData(){
        $image_types = BlogImageType::GetImageAllType('post');
        $id_smart_blog_posts = $this->getAllPost();
        $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
        $langs = Language::getLanguages();

        $arrayImg = array();
        foreach (scandir(__DIR__.'/dummy_data') as $images) {
            if (in_array($images, ['.', '..'])) {
                continue;
            }
            $arrayImg[] = $images;
        }
        $img_count = 0;
        foreach ($id_smart_blog_posts as $id_smart_blog_post) {
            $files_to_delete = array();

            $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_post_' . $id_smart_blog_post['id_smart_blog_post'] . '.jpg';
            $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_post_mini_' . $id_smart_blog_post['id_smart_blog_post'] . '.jpg';

            foreach ($langs as $l)
            {
                $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_post_' . $id_smart_blog_post['id_smart_blog_post'] . '_' . $l['id_lang'] . '.jpg';
                $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_post_mini_' . $id_smart_blog_post['id_smart_blog_post'] . '_' . $l['id_lang'] . '.jpg';
            }

            foreach ($files_to_delete as $file)
                if (file_exists($file)) @unlink($file);

            Tools::Copy(__DIR__."/dummy_data/".$arrayImg[$img_count], _PS_MODULE_DIR_."/smartblog/images/".$id_smart_blog_post['id_smart_blog_post'].'.jpg');
            foreach ($image_types as $image_type) {
                ImageManager::resize(__DIR__."/dummy_data/".$arrayImg[$img_count], _PS_MODULE_DIR_ . 'smartblog/images/' . $id_smart_blog_post['id_smart_blog_post'] . '-' . stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']
                );
            }
            $img_count = (count($arrayImg) > $img_count)? $img_count+1 : 0;
        }
        
    }

    public static function getAllPost()
    {
        $sql = 'SELECT p.id_smart_blog_post 
                FROM `' . _DB_PREFIX_ . 'smart_blog_post_lang` p';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
            return false;
        return $result;
    }

    public function hookactionHtaccessCreate()
    {

        $content = file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        if (!preg_match('/\# Images Blog\n/', $content)) {
            $content = preg_replace_callback('/\# Images\n/', array($this, 'updateSiteHtaccess'), $content);
            @file_put_contents(_PS_ROOT_DIR_ . '/.htaccess', $content);
        }
    }

    public function hookdisplayBackOfficeHeader($params)
    {

        $this->smarty->assign(array(
            'smartmodules_dir' => __PS_BASE_URI__
        ));

        return $this->display(__FILE__, 'views/templates/admin/addjs.tpl');
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/fw.css');
        $this->context->controller->addCSS($this->_path . 'views/css/smartblogstyle.css', 'all');

        $smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');
        $id_post = null;

        switch ($smartblogurlpattern) {
            case 1:
                $slug = Tools::getValue('slug');
                $id_post = self::slug2id($slug);
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
        if($id_post){
            $obj_post = new SmartBlogPost($id_post, true, $this->context->language->id, $this->context->shop->id);
            $meta_title = $obj_post->meta_title;
            $meta_keyword = $obj_post->meta_keyword;
            $meta_description = $obj_post->meta_description;
        } else {
            $meta_title = Configuration::get('smartblogmetatitle');
            $meta_keyword = Configuration::get('smartblogmetakeyword');
            $meta_description = Configuration::get('smartblogmetadescrip');
        }


        $this->smarty->assign(array(
            'feedUrl' => Tools::getShopDomain(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/rss.php',
            'meta_title' => $meta_title,
            'meta_keyword' => $meta_keyword,
            'meta_description' => $meta_description
        ));
        return $this->display(__FILE__, 'views/templates/front/plugins/blogfeedheader.tpl');
    }

    public function htaccessCreate()
    {
        $content = file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        if (!preg_match('/\# Images Blog\n/', $content)) {
            $content = preg_replace_callback('/\# Images\n/', array($this, 'updateSiteHtaccess'), $content);
            @file_put_contents(_PS_ROOT_DIR_ . '/.htaccess', $content);
        }
    }

    public function updateSiteHtaccess($match)
    {
        $htupdate = '';
        require_once dirname(__FILE__) . '/htupdate.php';
        $str = '';
        if (isset($match[0])) {
            $str .= "\n{$htupdate}\n\n{$match[0]}\n";
        }
        return $str;
    }

    public function addquickaccess()
    {
        $link = new Link();
        $qa = new QuickAccess();
        $qa->link = $link->getAdminLink('AdminModules') . '&configure=smartblog';
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $qa->name[$language['id_lang']] = 'Smart Blog Setting';
        }
        $qa->new_window = '0';
        if ($qa->save()) {
            Configuration::updateValue('smartblog_quick_access', $qa->id);
        }
    }

    private function CreateSmartBlogTabs()
    {

        $langs = Language::getLanguages();
        $smarttab = new Tab();
        $smarttab->class_name = "AdminSmartBlog";
        $smarttab->module = "";
        $smarttab->id_parent = 0;
        foreach ($langs as $l) {
            $smarttab->name[$l['id_lang']] = $this->l('Blog');
        }
        $smarttab->save();
        $tab_id = $smarttab->id;
        @copy(dirname(__FILE__) . "/views/img/AdminSmartBlog.gif", _PS_ROOT_DIR_ . "/img/t/AdminSmartBlog.gif");

        $tabvalue = array();
        // assign tab value from include file
        require_once(dirname(__FILE__) . '/sql/install_tab.php');
        foreach ($tabvalue as $tab) {
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            if($tab['id_parent']==-1)
                    $newtab->id_parent = $tab['id_parent'];
                else
            $newtab->id_parent = $tab_id;

            $newtab->module = $tab['module'];
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->save();
        }
        return true;
    }

    public function SampleDataInstall()
    {
        $damisql = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_category (id_parent,level_depth,active) VALUES (0,0,1);";
        Db::getInstance()->execute($damisql);

        $damisql_1 = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_category (id_parent,level_depth,active) VALUES (1,1,1);";
        Db::getInstance()->execute($damisql_1);

        $damisq1l = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_category_shop (id_smart_blog_category,id_shop) VALUES (1,'" . (int) $this->smart_shop_id . "');";
        Db::getInstance()->execute($damisq1l);

        $damisq1l_1 = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_category_shop (id_smart_blog_category,id_shop) VALUES (2,'" . (int) $this->smart_shop_id . "');";
        Db::getInstance()->execute($damisq1l_1);

        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $damisql2 = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_category_lang (id_smart_blog_category,name,meta_title,id_lang,link_rewrite) VALUES (1,'Home','Home','" . (int) $language['id_lang'] . "','home');";
            Db::getInstance()->execute($damisql2);

            $damisql2_1 = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_category_lang (id_smart_blog_category,name,meta_title,id_lang,link_rewrite) VALUES (2,'Politics','Politics','" . (int) $language['id_lang'] . "','politics');";
            Db::getInstance()->execute($damisql2_1);
        }
        for ($i = 1; $i <= 4; $i++) {
            Db::getInstance()->Execute('
                                                INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post`(`id_author`, `id_category`, `position`, `active`, `available`, `created`, `viewed`, `comment_status`) 
                                                VALUES(1,1,0,1,1,"' . Date('y-m-d H:i:s') . '",0,1)');
        }

        $languages = Language::getLanguages(false);
        for ($i = 1; $i <= 4; $i++) {
            if ($i == 1):
                $title = 'From Now we are certified web agency';
                $slug = 'from-now-we-are-certified-web-agency';
                $des = 'Smartdatasoft is an offshore web development company located in Bangladesh. We are serving this sector since 2010. Our team is committed to develop high quality web based application and theme for our clients and also for the global marketplace. As your web development partner we will assist you in planning, development, implementation and upgrade! Why Smartdatasoft? Smartdatasoft released their first prestashop theme in November 2012. Till now we have 6+ prestashop theme which are getting sold on global renowned marketplace. Those themes are getting used in more than 400 customers eCommerce websites. Those themes are very user friendly and highly customize able from admin dashboard. For these reason these theme are very popular among the end users and developers';
            elseif ($i == 2):
                $title = 'What is Bootstrap? – The History and the Hype';
                $slug = 'what-is-bootstrap';
                $des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
            elseif ($i == 3):
                $title = 'Answers to your Questions about PrestaShop 1.6';
                $slug = 'question-about-prestashop';
                $des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
            elseif ($i == 4):
                $title = 'Share the Love for PrestaShop 1.6';
                $slug = 'share-love-for-prestashop';
                $des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
            elseif ($i == 5):
                $title = 'Christmas Sale is here 5';
                $slug = 'christmas-sale-is-here';
                $des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
            elseif ($i == 6):
                $title = 'Christmas Sale is here 6';
                $slug = 'christmas-sale-is-here';
                $des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
            elseif ($i == 7):
                $title = 'Christmas Sale is here 7';
                $slug = 'christmas-sale-is-here';
                $des = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.';
            endif;
            foreach ($languages as $language) {
                if (!Db::getInstance()->Execute('
                       INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post_lang`(`id_smart_blog_post`,`id_lang`,`meta_title`,`meta_description`,`short_description`,`content`,`link_rewrite`)
                        VALUES(' . $i . ',' . (int) $language['id_lang'] . ', 
							"' . htmlspecialchars($title) . '", 
							"' . htmlspecialchars($des) . '","' . Tools::substr($des, 0, 200) . '","' . htmlspecialchars($des) . '","' . $slug . '"
						)'))
                    return false;
            }
        }
        for ($i = 1; $i <= 4; $i++) {
            Db::getInstance()->Execute('
                                                INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post_shop`(`id_smart_blog_post`, `id_shop`) 
                                                VALUES(' . $i . ',' . (int) $this->smart_shop_id . ')');
        }
        for ($i = 1; $i <= 7; $i++) {
            if ($i == 1):
                $type_name = 'home-default';
                $width = '240';
                $height = '160';
                $type = 'post';
            elseif ($i == 2):
                $type_name = 'home-small';
                $width = '65';
                $height = '45';
                $type = 'post';
            elseif ($i == 3):
                $type_name = 'single-default';
                $width = '770';
                $height = '385';
                $type = 'post';
            elseif ($i == 4):
                $type_name = 'home-small';
                $width = '65';
                $height = '45';
                $type = 'Category';
            elseif ($i == 5):
                $type_name = 'home-default';
                $width = '240';
                $height = '160';
                $type = 'Category';
            elseif ($i == 6):
                $type_name = 'single-default';
                $width = '770';
                $height = '385';
                $type = 'Category';
            elseif ($i == 7):
                $type_name = 'author-default';
                $width = '54';
                $height = '54';
                $type = 'Author';
            endif;
            $damiimgtype = "INSERT INTO " . _DB_PREFIX_ . "smart_blog_imagetype (type_name,width,height,type,active) VALUES ('" . $type_name . "','" . $width . "','" . $height . "','" . $type . "',1);";
            Db::getInstance()->execute($damiimgtype);
        }
        return true;
    }

    public function hookHeader($params){
        $this->smarty->assign('meta_title', 'This is Title'.' - '.'MName');
    }
    public function SmartHookInsert()
    {
        $hookvalue = array();
        require_once(dirname(__FILE__) . '/sql/addhook.php');

        foreach ($hookvalue as $hkv) {

            $hookid = Hook::getIdByName($hkv['name']);
            if (!$hookid) {
                $add_hook = new Hook();
                $add_hook->name = pSQL($hkv['name']);
                $add_hook->title = pSQL($hkv['title']);
                $add_hook->description = pSQL($hkv['description']);
                $add_hook->position = pSQL($hkv['position']);
                $add_hook->live_edit = $hkv['live_edit'];
                $add_hook->add();
                $hookid = $add_hook->id;
                if (!$hookid)
                    return false;
            }else {
                $up_hook = new Hook($hookid);
                $up_hook->update();
            }
        }
        return true;
    }

    public function SmartHookRegister()
    {
        $hookvalue = array();
        require_once(dirname(__FILE__) . '/sql/addhook.php');

        foreach ($hookvalue as $hkv) {

            $this->registerHook($hkv['name']);
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('smartblogmetatitle') ||
            !Configuration::deleteByName('smartblogmetakeyword') ||
            !Configuration::deleteByName('smartblogmetadescrip') ||
            !Configuration::deleteByName('smartpostperpage') ||
            !Configuration::deleteByName('smartblogrootcat') ||
            !Configuration::deleteByName('smartacceptcomment') ||
            !Configuration::deleteByName('smartusehtml') ||
            !Configuration::deleteByName('smartcaptchaoption') ||
            !Configuration::deleteByName('smartshowviewed') ||
            !Configuration::deleteByName('smartdisablecatimg') ||
            !Configuration::deleteByName('smartenablecomment') ||
            !Configuration::deleteByName('smartenableguestcomment') ||
            !Configuration::deleteByName('smartmainblogurl') ||
            !Configuration::deleteByName('smartshowcolumn') ||
            !Configuration::deleteByName('smartshowauthorstyle') ||
            !Configuration::deleteByName('smartcustomcss') ||
            !Configuration::deleteByName('smartshownoimg') ||
            !Configuration::deleteByName('smartshowauthor') ||
            !Configuration::deleteByName('smartblogurlpattern') ||
            !Configuration::deleteByName('smartdataformat') ||
            !Configuration::deleteByName('smartshowhomepost')
        )
            return false;

        $idtabs = array();

        require_once(dirname(__FILE__) . '/sql/uninstall_tab.php');
        foreach ($idtabs as $tabid):
            if ($tabid) {
                $tab = new Tab($tabid);
                $tab->delete();
            }
        endforeach;
        $sql = array();
        require_once(dirname(__FILE__) . '/sql/uninstall.php');
        foreach ($sql as $s) :
            if (!Db::getInstance()->Execute($s))
                return false;
        endforeach;

        // $this->SmartHookDelete();
        $this->deletequickaccess();
        $this->DeleteCache();
        return true;
    }

    public static function getSmartPromotion($page){
        $module = 'smartblog';// you need to set module name
        $module = trim(preg_replace('/[\s\t\n\r\s]+/', '_', $module));
        $page = trim(preg_replace('/[\s\t\n\r\s]+/', '_', $page));
        $Smartpromotion = new Smartpromotion();
        $timeout = Configuration::get('smartpromotion_update_timeout_'.$module.'_'.$page, false);
        $now = time();
        if($now > (int)$timeout) {
            Configuration::updateValue('smartpromotion_'.$module.'_'.$page, $Smartpromotion->checkPromotion($module,$page));
            $timelimit = 24*60*60;
            Configuration::updateValue('smartpromotion_update_timeout_'.$module.'_'.$page, $now + $timelimit);
        }
        $promotion = unserialize(Configuration::get('smartpromotion_'.$module.'_'.$page));
        return html_entity_decode($promotion['content']);
    }

    public function deletequickaccess()
    {
        $qa = new QuickAccess(Configuration::get('smartblog_quick_access'));
        $qa->delete();
    }


    public function getContent()
    {
        $feed_url = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/smartblog/rss.php';
        $feed_url_html = '<div class="row">
        <div class="alert alert-info"><strong>Feed URL: </strong>' . $feed_url . '</div>
    </div>';


        $html = '';
        $this->autoregisterhook('moduleRoutes', 'smartblog');
        $this->autoregisterhook('vcBeforeInit', 'smartlegendaaddons');
        if (Tools::isSubmit('savesmartblog')) {
            Configuration::updateValue('smartblogmetatitle', Tools::getvalue('smartblogmetatitle'));
            Configuration::updateValue('smartenablecomment', Tools::getvalue('smartenablecomment'));
            Configuration::updateValue('smartenableguestcomment', Tools::getvalue('smartenableguestcomment'));
            Configuration::updateValue('smartblogmetakeyword', Tools::getvalue('smartblogmetakeyword'));
            Configuration::updateValue('smartblogmetadescrip', Tools::getvalue('smartblogmetadescrip'));
            Configuration::updateValue('smartpostperpage', Tools::getvalue('smartpostperpage'));
            Configuration::updateValue('smartblogrootcat', Tools::getvalue('smartblogrootcat'));
            Configuration::updateValue('smartblogurlpattern', Tools::getvalue('smartblogurlpattern'));
            Configuration::updateValue('smartacceptcomment', Tools::getvalue('smartacceptcomment'));
            Configuration::updateValue('smartcaptchaoption', Tools::getvalue('smartcaptchaoption'));
            Configuration::updateValue('smartshowviewed', Tools::getvalue('smartshowviewed'));
            Configuration::updateValue('smartdisablecatimg', Tools::getvalue('smartdisablecatimg'));
            Configuration::updateValue('smartshowauthorstyle', Tools::getvalue('smartshowauthorstyle'));
            Configuration::updateValue('smartshowauthor', Tools::getvalue('smartshowauthor'));
            Configuration::updateValue('smartshowcolumn', Tools::getvalue('smartshowcolumn'));
            Configuration::updateValue('smartmainblogurl', Tools::getvalue('smartmainblogurl'));
            Configuration::updateValue('smartusehtml', Tools::getvalue('smartusehtml'));
            Configuration::updateValue('smartshownoimg', Tools::getvalue('smartshownoimg'));
            Configuration::updateValue('smartdataformat', Tools::getvalue('smartdataformat'));
            Configuration::updateValue('smartcustomcss', Tools::getvalue('smartcustomcss'), true);
            Configuration::updateValue('smartshowhomepost', Tools::getvalue('smartshowhomepost'));

            

            

            $this->processImageUpload($_FILES);
            $html = $this->displayConfirmation($this->l('The settings have been updated successfully.'));
            $helper = $this->SettingForm();
            $html .= $feed_url_html;
            $html .= $helper->generateForm($this->fields_form);
            $helper = $this->regenerateform();
            $html .= $helper->generateForm($this->fields_form);
            $auc = new AdminAboutUsController();
            
            return $html;
        } elseif (Tools::isSubmit('generateimage')) {
            if (Tools::getvalue('isdeleteoldthumblr') != 1) {
                BlogImageType::ImageGenerate();
                $html = $this->displayConfirmation($this->l('Generate New Thumblr Succesfully.'));
                $helper = $this->SettingForm();
                $html .= $helper->generateForm($this->fields_form);
                $helper = $this->regenerateform();
                $html .= $helper->generateForm($this->fields_form);
                $auc = new AdminAboutUsController();
                
                return $html;
            } else {
                BlogImageType::ImageDelete();
                BlogImageType::ImageGenerate();
                $html = $this->displayConfirmation($this->l('Delete Old Image and Generate New Thumblr Succesfully.'));
                $helper = $this->SettingForm();
                $html .= $helper->generateForm($this->fields_form);
                $helper = $this->regenerateform();
                $html .= $helper->generateForm($this->fields_form);
                $auc = new AdminAboutUsController();
                
                return $html;
            }
        } else {
            $helper = $this->SettingForm();
            $html .= $helper->generateForm($this->fields_form);
            $helper = $this->regenerateform();
            $html .= $helper->generateForm($this->fields_form);
            $auc = new AdminAboutUsController();
            
            return $html;
        }
    }

    public function autoregisterhook($hook_name = 'moduleRoutes', $module_name = 'smartblog', $shop_list = null)
    {
        if ((Module::isEnabled($module_name) == 1) && (Module::isInstalled($module_name) == 1)) {
            $return = true;
            $id_sql = 'SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module` WHERE `name` = "' . $module_name . '"';
            $id_module = Db::getInstance()->getValue($id_sql);
            if (is_array($hook_name))
                $hook_names = $hook_name;
            else
                $hook_names = array($hook_name);
            foreach ($hook_names as $hook_name) {
                if (!Validate::isHookName($hook_name))
                    throw new PrestaShopException('Invalid hook name');
                if (!isset($id_module) || !is_numeric($id_module))
                    return false;
                //$hook_name_bak = $hook_name;
                if ($alias = Hook::getRetroHookName($hook_name))
                    $hook_name = $alias;
                $id_hook = Hook::getIdByName($hook_name);
                //$live_edit = Hook::getLiveEditById((int) Hook::getIdByName($hook_name_bak));
                if (!$id_hook) {
                    $new_hook = new Hook();
                    $new_hook->name = pSQL($hook_name);
                    $new_hook->title = pSQL($hook_name);
                    $new_hook->live_edit = (bool) preg_match('/^display/i', $new_hook->name);
                    $new_hook->position = (bool) $new_hook->live_edit;
                    $new_hook->add();
                    $id_hook = $new_hook->id;
                    if (!$id_hook)
                        return false;
                }
                if (is_null($shop_list))
                    $shop_list = Shop::getShops(true, null, true);
                foreach ($shop_list as $shop_id) {
                    $sql = 'SELECT hm.`id_module`
                        FROM `' . _DB_PREFIX_ . 'hook_module` hm, `' . _DB_PREFIX_ . 'hook` h
                        WHERE hm.`id_module` = ' . (int) ($id_module) . ' AND h.`id_hook` = ' . $id_hook . '
                        AND h.`id_hook` = hm.`id_hook` AND `id_shop` = ' . (int) $shop_id;
                    if (Db::getInstance()->getRow($sql))
                        continue;

                    $sql = 'SELECT MAX(`position`) AS position
                        FROM `' . _DB_PREFIX_ . 'hook_module`
                        WHERE `id_hook` = ' . (int) $id_hook . ' AND `id_shop` = ' . (int) $shop_id;
                    if (!$position = Db::getInstance()->getValue($sql))
                        $position = 0;

                    $return &= Db::getInstance()->insert('hook_module', array(
                        'id_module' => (int) $id_module,
                        'id_hook' => (int) $id_hook,
                        'id_shop' => (int) $shop_id,
                        'position' => (int) ($position + 1),
                    ));
                }
            }
            return $return;
        }else {
            return false;
        }
    }

    protected function regenerateform()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Blog Thumblr Configuration'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Delete Old Thumblr'),
                    'name' => 'isdeleteoldthumblr',
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
                )
            ),
            'submit' => array(
                'title' => $this->l('Re Generate Thumblr'),
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang)
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->show_toolbar = false;
        $helper->submit_action = 'generateimage';
        $helper->fields_value['isdeleteoldthumblr'] = Configuration::get('isdeleteoldthumblr');
        return $helper;
    }

    public function processImageUpload($FILES)
    {
        if (isset($FILES['avatar']) && isset($FILES['avatar']['tmp_name']) && !empty($FILES['avatar']['tmp_name'])) {
            if (ImageManager::validateUpload($FILES['avatar'], 4000000))
                return $this->displayError($this->l('Invalid image'));
            else {
                $ext = Tools::substr($FILES['avatar']['name'], strrpos($FILES['avatar']['name'], '.') + 1);
                $file_name = 'avatar.' . $ext;
                $path = _PS_MODULE_DIR_ . 'smartblog/images/avatar/' . $file_name;
                if (!move_uploaded_file($FILES['avatar']['tmp_name'], $path))
                    return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
                else {
                    $author_types = BlogImageType::GetImageAllType('author');
                    foreach ($author_types as $image_type) {
                        $dir = _PS_MODULE_DIR_ . 'smartblog/images/avatar/avatar-' . Tools::stripslashes($image_type['type_name']) . '.jpg';
                        if (file_exists($dir))
                            unlink($dir);
                    }
                    $images_types = BlogImageType::GetImageAllType('author');
                    foreach ($images_types as $image_type) {
                        ImageManager::resize($path, _PS_MODULE_DIR_ . 'smartblog/images/avatar/avatar-' . Tools::stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']
                        );
                    }
                }
            }
        }
    }

    public function SettingForm()
    {
        $blog_url = smartblog::GetSmartBlogLink('smartblog');
        $img_desc = '';
        $img_desc .= '' . $this->l('Upload a Avatar from your computer.<br/>N.B : Only jpg image is allowed');
        $img_desc .= '<br/><img style="clear:both;border:1px solid black;" alt="" src="' . __PS_BASE_URI__ . 'modules/smartblog/images/avatar/avatar.jpg" height="100" width="100"/><br />';
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Setting'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Title'),
                    'name' => 'smartblogmetatitle',
                    'size' => 70,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Keyword'),
                    'name' => 'smartblogmetakeyword',
                    'size' => 70,
                    'required' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta Description'),
                    'name' => 'smartblogmetadescrip',
                    'rows' => 7,
                    'cols' => 66,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Main Blog Url'),
                    'name' => 'smartmainblogurl',
                    'size' => 15,
                    'required' => true,
                    'desc' => '<p class="alert alert-info"><a href="' . $blog_url . '">' . $blog_url . '</a></p>'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use .html with Friendly Url'),
                    'name' => 'smartusehtml',
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
                    'type' => 'radio',
                    'label' => $this->l('Blog Page Url Pattern'),
                    'name' => 'smartblogurlpattern',
                    'required' => false,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'smartblogurlpattern_a',
                            'value' => 1,
                            'label' => $this->l('alias/{slug}html ( ex: alias/share-the-love-for-prestashop-1-6.html)')
                        ),
                        array(
                            'id' => 'smartblogurlpattern_b',
                            'value' => 2,
                            'label' => $this->l('alias/{id_post}_{slug}html ( ex: alias/1_share-the-love-for-prestashop-1-6.html)')
                        ), 
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of posts per page'),
                    'name' => 'smartpostperpage',
                    'size' => 15,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Date format'),
                    'name' => 'smartdataformat',
                    'size' => 15,
                    'required' => true,
                    'desc' => '<p class="alert alert-info"><a target="_blank" href="https://smartdatasoft.zendesk.com/hc/en-us/articles/205493262">Documentation on date and time formatting</a></p>'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Auto accepted comment'),
                    'name' => 'smartacceptcomment',
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
                ), array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Captcha'),
                    'name' => 'smartcaptchaoption',
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
                    'label' => $this->l('Enable Comment'),
                    'name' => 'smartenablecomment',
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
                    'label' => $this->l('Allow Guest Comment'),
                    'name' => 'smartenableguestcomment',
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
                    'label' => $this->l('Show Author Name'),
                    'name' => 'smartshowauthor',
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
                ), array(
                    'type' => 'switch',
                    'label' => $this->l('Show Post Viewed'),
                    'name' => 'smartshowviewed',
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
                    'label' => $this->l('Show Author Name Style'),
                    'desc' => 'YES : \'First Name Last Name\'<br> NO : \'Last Name First Name\'',
                    'name' => 'smartshowauthorstyle',
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('First Name, Last Name')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Last Name, First Name')
                        )
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('AVATAR Image:'),
                    'name' => 'avatar',
                    'display_image' => false,
                    'desc' => $img_desc
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show \'No Image\''),
                    'name' => 'smartshownoimg',
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
                    'label' => $this->l('Show Category'),
                    'name' => 'smartdisablecatimg',
                    'required' => false,
                    'desc' => 'Show category image and description on category page',
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
                    'type' => 'textarea',
                    'label' => $this->l('Custom CSS'),
                    'name' => 'smartcustomcss',
                    'rows' => 7,
                    'cols' => 66,
                    'required' => false
                ),
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
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . 'token=' . Tools::getAdminTokenLite('AdminModules'),
            )
        );
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'save' . $this->name;


        
        $helper->fields_value['smartpostperpage'] = Configuration::get('smartpostperpage');
        $helper->fields_value['smartdataformat'] = Configuration::get('smartdataformat');
        $helper->fields_value['smartacceptcomment'] = Configuration::get('smartacceptcomment');
        $helper->fields_value['smartshowauthorstyle'] = Configuration::get('smartshowauthorstyle');
        $helper->fields_value['smartshowauthor'] = Configuration::get('smartshowauthor');
        $helper->fields_value['smartmainblogurl'] = Configuration::get('smartmainblogurl');
        $helper->fields_value['smartusehtml'] = Configuration::get('smartusehtml');
        $helper->fields_value['smartshowcolumn'] = Configuration::get('smartshowcolumn');
        $helper->fields_value['smartblogmetakeyword'] = Configuration::get('smartblogmetakeyword');
        $helper->fields_value['smartblogmetatitle'] = Configuration::get('smartblogmetatitle');
        $helper->fields_value['smartblogmetadescrip'] = Configuration::get('smartblogmetadescrip');
        $helper->fields_value['smartshowviewed'] = Configuration::get('smartshowviewed');
        $helper->fields_value['smartdisablecatimg'] = Configuration::get('smartdisablecatimg');
        $helper->fields_value['smartenablecomment'] = Configuration::get('smartenablecomment');
        $helper->fields_value['smartenableguestcomment'] = Configuration::get('smartenableguestcomment');
        $helper->fields_value['smartcustomcss'] = Configuration::get('smartcustomcss');
        $helper->fields_value['smartshownoimg'] = Configuration::get('smartshownoimg');
        $helper->fields_value['smartcaptchaoption'] = Configuration::get('smartcaptchaoption');
        $helper->fields_value['smartblogurlpattern'] = Configuration::get('smartblogurlpattern');
        $helper->fields_value['smartshowhomepost'] = Configuration::get('smartshowhomepost');
        
        

        return $helper;
    }

    public static function GetSmartBlogUrl()
    {
        $ssl_enable = Configuration::get('PS_SSL_ENABLED');
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $rewrite_set = (int) Configuration::get('PS_REWRITING_SETTINGS');
        $ssl = null;
        static $force_ssl = null;
        if ($ssl === null) {
            if ($force_ssl === null)
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null)
            $shop = new Shop($id_shop);
        else
            $shop = Context::getContext()->shop;
        $base = ($ssl == 1 && $ssl_enable == 1) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain;
        $langUrl = Language::getIsoById($id_lang) . '/';
        if ((!$rewrite_set && in_array($id_shop, array((int) Context::getContext()->shop->id, null))) || !Language::isMultiLanguageActivated($id_shop) || !(int) Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop))
            $langUrl = '';

        return $base . $shop->getBaseURI() . $langUrl;
    }

    public static function GetSmartBlogLink($rewrite = 'smartblog', $params = null, $id_shop = null, $id_lang = null)
    {
        $url = smartblog::GetSmartBlogUrl();
        $dispatcher = Dispatcher::getInstance();
        $id_lang = (int) Context::getContext()->language->id;
        $force_routes = (bool) Configuration::get('PS_REWRITING_SETTINGS');
        if ($params != null) {
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params, $force_routes);
        } else {
            $params = array();
            return $url . $dispatcher->createUrl($rewrite, $id_lang, $params, $force_routes);
        }
    }

    public function hookModuleRoutes($params)
    {
        $alias = Configuration::get('smartmainblogurl');
        $usehtml = (int) Configuration::get('smartusehtml');
        if ($usehtml != 0) {
            $html = '.html';
        } else {
            $html = '';
        }

        $smartblogurlpattern = (int) Configuration::get('smartblogurlpattern');

        $my_link = array();

        switch ($smartblogurlpattern) {

            case 1:
                $my_link = $this->urlPatterWithoutId($alias, $html);
                break;
            case 2:
                $my_link = $this->urlPatterWithIdOne($alias, $html);
                break; 

            default:
                $my_link = $this->urlPatterWithIdOne($alias, $html);
        }

        return $my_link;
    }

    public function urlPatterWithoutId($alias, $html)
    {
        $my_link = array(
            'smartblog' => array(
                'controller' => 'category',
                'rule' => $alias . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list' => array(
                'controller' => 'category',
                'rule' => $alias . '/category' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list_module' => array(
                'controller' => 'category',
                'rule' => 'module/' . $alias . '/category' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list_pagination' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_pagination' => array(
                'controller' => 'category',
                'rule' => $alias . '/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category_rule' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/{slug}' . $html,
                'keywords' => array(
                    'id_category' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'slug'),
                    'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/{slug}' . $html,
                'keywords' => array(
                    'id_category' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'slug'),
                    'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category_pagination' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/{slug}/page/{page}' . $html,
                'keywords' => array(
                    'id_category' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'slug'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_cat_page_mod' => array(
                'controller' => 'category',
                'rule' => 'module/' . $alias . '/category/{slug}/page/{page}' . $html,
                'keywords' => array(
                    'id_category' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'slug'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_search' => array(
                'controller' => 'search',
                'rule' => $alias .  '/search',
               'keywords' => array(
                    'search_query' => array('regexp' => '[_a-zA-Z0-9-\pL\+]*', 'param' => 'search_query'),

                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_tag' => array(
                'controller' => 'tagpost',
                'rule' => $alias . '/tag/{tag}' . $html,
                'keywords' => array(
                    'tag' => array('regexp' => '[_a-zA-Z0-9-\pL\+]*', 'param' => 'tag'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_search_pagination' => array(
                'controller' => 'search',
                'rule' => $alias . '/search/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_post_rule' => array(
                'controller' => 'details',
                'rule' => $alias . '/{slug}' . $html,
                'keywords' => array(
                    'id_post' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_post'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'slug'),
                    'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_post' => array(
                'controller' => 'details',
                'rule' => $alias . '/{slug}' . $html,
                'keywords' => array(
                    'id_post' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_post'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'slug'),
                    'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_archive' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_archive_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_month' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/{month}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_month_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/{month}/page/{page}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_day' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/{month}/{day}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'day' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'day'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_day_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/{month}/{day}/page/{page}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'day' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'day'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_year' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_year_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/page/{page}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
        );
        return $my_link;
    }

    public function urlPatterWithIdOne($alias, $html) {
        $my_link = array(
            'smartblog' => array(
                'controller' => 'category',
                'rule' => $alias.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list' => array(
                'controller' => 'category',
                'rule' => $alias.'/category'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list_module' => array(
                'controller' => 'category',
                'rule' => 'module/'.$alias.'/category'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list_pagination' => array(
                'controller' => 'category',
                'rule' =>       $alias.'/category/page/{page}'.$html,
                'keywords' => array(
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_pagination' => array(
                'controller' => 'category',
                'rule' =>       $alias.'/page/{page}'.$html,
                'keywords' => array(
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category' => array(
                'controller' => 'category',
                'rule' =>        $alias.'/category/{id_category}_{slug}'.$html,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category_rule' => array(
                'controller' => 'category',
                'rule' =>        $alias.'/category/{id_category}_{slug}'.$html,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category_pagination' => array(
                'controller' => 'category',
                'rule' =>       $alias.'/category/{id_category}_{slug}/page/{page}'.$html,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'page' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_cat_page_mod' => array(
                'controller' => 'category',
                'rule' =>       'module/'.$alias.'/category/{id_category}_{slug}/page/{page}'.$html,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'page' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_search' => array(
                'controller' => 'search',
                'rule' => $alias.'/search'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_tag' => array(
                'controller' => 'tagpost',
                'rule' => $alias.'/tag/{tag}'.$html,
                'keywords' => array(
                    'tag' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'tag'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_search_pagination' => array(
                'controller' => 'search',
                'rule' =>       $alias.'/search/page/{page}'.$html,
                'keywords' => array(
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_post' => array(
                'controller' => 'details',
                'rule' =>       $alias.'/{id_post}_{slug}'.$html,
                'keywords' => array(
                    'id_post' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_post'),
                    'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_post_rule' => array(
                'controller' => 'details',
                'rule' =>       $alias.'/{id_post}_{slug}'.$html,
                'keywords' => array(
                    'id_post' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_post'),
                    'slug'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_archive' => array(
                'controller' => 'archive',
                'rule' => $alias.'/archive'.$html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_archive_pagination' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/page/{page}'.$html,
                'keywords' => array(
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_month' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/{year}/{month}'.$html,
                'keywords' => array(
                    'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_month_pagination' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/{year}/{month}/page/{page}'.$html,
                'keywords' => array(
                    'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_day' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/{year}/{month}/{day}'.$html,
                'keywords' => array(
                    'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'day' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'day'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_day_pagination' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/{year}/{month}/{day}/page/{page}'.$html,
                'keywords' => array(
                    'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'day' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'day'),
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_year' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/{year}'.$html,
                'keywords' => array(
                    'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_year_pagination' => array(
                'controller' => 'archive',
                'rule' =>       $alias.'/archive/{year}/page/{page}'.$html,
                'keywords' => array(
                    'year' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'page' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
        );
        return $my_link;
    }

    public static function displayDate($date, $id_lang = null, $full = false, $separator = null)
    {
        if ($id_lang !== null) {
            Tools::displayParameterAsDeprecated('id_lang');
        }
        if ($separator !== null) {
            Tools::displayParameterAsDeprecated('separator');
        }

        if (!$date || !($time = strtotime($date))) {
            return $date;
        }

        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }

        if (!Validate::isDate($date) || !Validate::isBool($full)) {
            throw new PrestaShopException('Invalid date');
        }

        $date_format = ($full ? Configuration::get('smartdataformat') : Configuration::get('smartdataformat'));
        return date($date_format, $time);
    }

    public static function categoryslug2id($slug)
    {
        $sql = 'SELECT p.id_smart_blog_category 
                FROM `' . _DB_PREFIX_ . 'smart_blog_category_lang` p 
                WHERE p.link_rewrite =  "' . pSQL($slug) . '"';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
            return false;
        return $result[0]['id_smart_blog_category'];
    }

    public static function slug2id($slug)
    {
        $sql = 'SELECT p.id_smart_blog_post 
                FROM `' . _DB_PREFIX_ . 'smart_blog_post_lang` p 
                WHERE p.link_rewrite =  "' . pSQL($slug) . '"';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
            return false;
        return $result[0]['id_smart_blog_post'];
    }



    public function smartblogcategoriesHookLeftColumn($params)
    {


        if (!$this->isCached('plugins/smartblogcategories.tpl')) {
            $view_data = array();
            $id_lang = $this->context->language->id;
            $BlogCategory = new BlogCategory();
            $categories = $BlogCategory->getCategory(1, $id_lang);
            $i = 0;
            foreach ($categories as $category) {
                $categories[$i]['count'] = $BlogCategory->getPostByCategory($category['id_smart_blog_category']);
                $i++;
            }
            $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $protocol_content = (isset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
   
            $smartbloglink = new SmartBlogLink($protocol_link, $protocol_content);


            $this->smarty->assign(array(
                 'smartbloglink' => $smartbloglink,
                'categories' => $categories
            ));
        }
        return $this->display(__FILE__, 'views/templates/front/plugins/smartblogcategories.tpl');
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

    public function DeleteCache()
    {
        $this->_clearCache('plugins/smartblogcategories.tpl');
        $this->_clearCache('plugins/smartblog_latest_news.tpl');
        $this->_clearCache('plugins/smartblogrelatedproduct.tpl');
    }





    public function smartblogrelatedproductHookdisplayProductTab($params)
    {
        return $this->display(__FILE__, 'views/templates/front/plugins/smartproduct_tab.tpl');
    }

    public function hookdisplayProductTab($params)
    {
        return $this->smartblogrelatedproductHookdisplayProductTab($params);
    }

    public function smartblogrelatedproductHookdisplayProductTabContent($params)
    {

        $id_lang = $this->context->language->id;
        $posts = SmartBlogPost::getRelatedPostsByProduct($id_lang, Tools::getvalue('id_product'));
        $this->smarty->assign(array(
            'posts' => $posts
        ));

        return $this->display(__FILE__, 'views/templates/front/plugins/smart_product_tab_creator.tpl');
    }

    public function hookdisplayProductTabContent($params)
    {
        return $this->smartblogrelatedproductHookdisplayProductTabContent($params);
    }
}