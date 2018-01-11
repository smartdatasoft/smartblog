<?php

class AdminBlogCategoryController extends AdminController
{

    public $module;
 
    protected   $smart_blog_category = null;

    protected static $category = null;

    public function __construct()
    {
        $this->table = 'smart_blog_category';
        $this->className = 'BlogCategory';
        $this->module = 'smartblog';
        $this->lang = true;

        $this->image_dir = '../modules/smartblog/images/category';
        $this->bootstrap = true;
  
        $id_smart_blog_category = (int)Tools::getValue('id_smart_blog_category', Tools::getValue('id_smart_blog_category_parent', 1));
        self::$category = new BlogCategory($id_smart_blog_category);
        if (!Validate::isLoadedObject(self::$category)) {
            die('Category cannot be loaded');
        }

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->tpl_list_vars['icon'] = 'icon-folder-close';
        $this->tpl_list_vars['title'] = $this->l('Categories');

        $this->fields_list = array(
            'id_smart_blog_category' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto'
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'maxlength' => 90,
                'orderby' => false,
                'callback' => 'removeHtmlTags'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position'
            ),
            'active' => array(
                'title' => $this->l('Displayed'),
                'class' => 'fixed-width-sm',
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false
            )
        );

        $this->_select = 'position ';
        $this->_orderBy = 'position';
      
        if (Shop::isFeatureActive())
            Shop::addTableAssociation($this->table, array('type' => 'shop'));

        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'smart_blog_category_shop sbs ON a.id_smart_blog_category=sbs.id_smart_blog_category && sbs.id_shop IN(' . implode(',', Shop::getContextListShopID()) . ')';

        $this->_select = 'sbs.id_shop';
        $this->_defaultOrderBy = 'a.id_smart_blog_category';
        $this->_defaultOrderWay = 'DESC';

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->_group = 'GROUP BY a.id_smart_blog_category';
        }
      

        $this->blog_category = self::getCurrentBlogCategory();
        $this->_where = ' AND `id_parent` = '.(int)$this->blog_category->id;
        $this->_select = 'position ';

        parent::__construct();
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true){
        $translated = Context::getContext()->getTranslator()->trans($string);
        if ($translated !== $string) {
            return $translated;
        }

        if ($class === null || $class == 'AdminTab') {
            $class = substr(get_class($this), 0, -10);
        } elseif (strtolower(substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = substr($class, 0, -10);
        }
        return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
    }

    public function removeHtmlTags($val){
        return substr(strip_tags($val),0,110) . "...";
    }
    public static function getCurrentBlogCategory()
    {
        return self::$category;
    }

    public function renderList()
    {

        if (isset($this->_filter) && trim($this->_filter) == '') {
            $this->_filter = $this->original_filter;
        }

        $this->_group = 'GROUP BY a.`id_smart_blog_category`';
        if (isset($this->toolbar_btn['new'])) {
            $this->toolbar_btn['new']['href'] .= '&id_parent='.(int)pSQL(Tools::getValue('id_smart_blog_category'));
        }

        return parent::renderList();
    }

    public function setPromotion(){
        $this->context->smarty->assign(array(
            'smartpromotion' => smartblog::getSmartPromotion('category_list')
        ));
        $promotion = $this->context->smarty->fetch(_PS_MODULE_DIR_.'smartblog/views/templates/admin/promotion.tpl');
        return $promotion;
    }

    public function renderView()
    {
        $this->initToolbar();
        return $this->renderList();
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->renderPageHeaderToolbar();
        if (Tools::getValue('add'.$this->table) !== false || Tools::getValue('update'.$this->table) !== false) {
            $this->content .= $this->renderForm();
        } else {
            $id_smart_blog_category = (int)Tools::getValue('id_smart_blog_category');
            if (!$id_smart_blog_category) {
                $id_smart_blog_category = 1;
            }

            $smartcmsblog_tabs = array('blog_category', 'smartblog');
            // Cleaning links
            $cat_bar_index = self::$currentIndex;
            foreach ($smartcmsblog_tabs as $tab) {
                if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway')) {
                    $cat_bar_index = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', self::$currentIndex);
                }
            }

            $this->context->smarty->assign(array(
                'smartblog_breadcrumb' => BlogCategory::getPath($cat_bar_index, $id_smart_blog_category, '', '', 'smartblog'),
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
                'page_header_toolbar_title' => $this->toolbar_title,
            ));

            // smartblog_breadcrumb
            $this->content .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'smartblog/views/templates/admin/breadcrumb.tpl');
            $this->content .= $this->renderList();
        }
        $this->context->smarty->assign(array(
            'content' => $this->setPromotion().$this->content
        ));
    }

    public function renderPageHeaderToolbar()
    {

        $id_smart_blog_category = (int)Tools::getValue('id_smart_blog_category');
        $id_smart_cmsblog_post = Tools::getValue('id_smart_cmsblog_post');

        if (!$id_smart_blog_category) {
            $id_smart_blog_category = 1;
        }

        $this->show_page_header_toolbar = true;

        if (Tools::getValue('add'.$this->table) !== false) {
            $this->toolbar_title[] = $this->l('New Category');
        } elseif(Tools::getValue('update'.$this->table) !== false) {
            $this->toolbar_title[] = $this->l('Update Category');
        } else {
            $this->toolbar_title[] = $this->l('Category');

            $this->page_header_toolbar_btn['new_smart_cmsblog_category'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new category', null, null, false),
                'icon' => 'process-icon-new'
            );

        }

        $this->page_header_toolbar_title = implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->toolbar_title);

        if (is_array($this->page_header_toolbar_btn)
            && $this->page_header_toolbar_btn instanceof Traversable
            || trim($this->page_header_toolbar_title) != '') {
            $this->show_page_header_toolbar = true;
        }

        $this->context->smarty->assign(array(
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_title' => $this->toolbar_title,
        ));
    }

    public function renderForm()
    {

        $this->display = 'edit';
        $this->initToolbar();
        //Added From Old 
        if (!($obj = $this->loadObject(true)))
            return;

        $image = _MODULE_SMARTBLOG_DIR_ . 'category/' . $obj->id . '.jpg';

        $image_url = ImageManager::thumbnail($image, $this->table . '_' . pSQL(Tools::getvalue('id_smart_blog_category')) . '.jpg', 200, 'jpg', true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

       // $root = BlogCategory::getRootCategory()['id_smart_blog_category'];
        
        $categories = BlogCategory::getCategories($this->context->language->id, false);
        $html_categories = BlogCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_parent'), 1);


        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Category'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'class' => 'copyMeta2friendlyURL',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Parent Blog Category'),
                    'name' => 'id_parent',
                    'col' => '4',
                    'html_content' => '<select name="id_parent">'.$html_categories.'</select>'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Description')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Category Image:'),
                    'name' => 'category_image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'delete_url' => self::$currentIndex . '&' . $this->identifier . '=' . pSQL(Tools::getvalue('id_smart_blog_category')) . '&token=' . $this->token . '&deleteImage=1',
                    'hint' => $this->l('Upload a feature image from your computer.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Keyword'),
                    'name' => 'meta_keyword',
                    'lang' => true,
                    'size' => 60,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Meta Keyword. Separated by comma(,)')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta Description'),
                    'name' => 'meta_description',
                    'rows' => 10,
                    'cols' => 62,
                    'lang' => true,
                    'required' => false,
                    'desc' => $this->l('Enter Your Category Meta Description')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link Rewrite'),
                    'name' => 'link_rewrite',
                    'size' => 60,
                    'lang' => true,
                    'required' => true,
                    'desc' => $this->l('Enetr Your Category Slug. Use In SEO Friendly URL')
                ),
                
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
       
            ),
        );
 
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($BlogCategory = $this->loadObject(true)))
            return;

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        $image = ImageManager::thumbnail(_MODULE_SMARTBLOG_DIR_ . 'category/' . $BlogCategory->id_smart_blog_category . '.jpg', $this->table . '_' . (int) $BlogCategory->id_smart_blog_category . '.' . $this->imageType, 350, $this->imageType, true);

        $this->fields_value = array(
            'image' => $image ? $image : false,
            'size' => $image ? filesize(_MODULE_SMARTBLOG_DIR_ . 'category/' . $BlogCategory->id_smart_blog_category . '.jpg') / 1000 : false
        );


        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (!($object = $this->loadObject(true))) {
            return;
        }

        if (!in_array($this->display, array('edit', 'add')))
        $this->multishop_context_group = false;


        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $this->action = 'save';
            if ($id_smart_blog_category = (int)Tools::getValue('id_smart_blog_category')) {
                $this->id_object = $id_smart_blog_category;
                if (!BlogCategory::checkBeforeMove($id_smart_blog_category, (int)Tools::getValue('id_parent'))) {
                    $this->errors[] = Tools::displayError('The CMS Category cannot be moved here.');
                    Tools::redirectAdmin(self::$currentIndex.'&updatesmart_blog_category&viewsmart_blog_category&id_smart_blog_category='.(int)$object->id.'&token='.pSQL(Tools::getValue('token')));
                    return false;
                }
            }
            $object = parent::postProcess();
            $this->updateAssoShop((int)Tools::getValue('id_smart_blog_category'));
            if ($object !== false) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&id_smart_blog_category='.(int)$object->id.'&token='.Tools::getValue('token'));
            }
            return $object;
        }
        elseif (Tools::isSubmit('forcedeleteImage') || (isset($_FILES['category_image']) && $_FILES['category_image']['size'] > 0) || Tools::getValue('deleteImage')) {
            $this->processForceDeleteImage();
            if (Tools::isSubmit('forcedeleteImage'))
                Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminCategories') . '&conf=7');
        }
        $object = parent::postProcess();
    }

    public function processForceDeleteImage()
    {
        $blog_post = $this->loadObject(true);

        if (Validate::isLoadedObject($blog_post)) {

            $this->deleteImage($blog_post->id_smart_blog_category);
        }
    }

    public function deleteImage($id_smart_blog_category)
    {

        if (!$id_smart_blog_category)
            return false;

        echo _MODULE_SMARTBLOG_DIR_ . 'category/' . $id_smart_blog_category . '.jpg';

        // Delete base image
        if (file_exists(_MODULE_SMARTBLOG_DIR_ . '/category/' . $id_smart_blog_category . '.jpg'))
            unlink($this->image_dir . '/' . $id_smart_blog_category . '.jpg');
        else
            return false;

        // now we need to delete the image type of post

        $files_to_delete = array();

        // Delete auto-generated images
        $image_types = BlogImageType::GetImageAllType('category');
        foreach ($image_types as $image_type)
            $files_to_delete[] = $this->image_dir . '/' . $id_smart_blog_category . '-' . $image_type['type_name'] . '.jpg';

        // Delete tmp images
        $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_category_' . $id_smart_blog_category . '.jpg';
        $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_category_mini_' . $id_smart_blog_category . '.jpg';

        foreach ($files_to_delete as $file)
            if (file_exists($file) && !@unlink($file))
                return false;

        return true;
    }

    protected function postImage($id)
    {

        if (isset($_FILES['category_image']) && isset($_FILES['category_image']['tmp_name']) && !empty($_FILES['category_image']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($_FILES['category_image'], 4000000))
                return $this->displayError($this->l('Invalid image'));
            else {
                $ext = Tools::substr($_FILES['category_image']['name'], strrpos($_FILES['category_image']['name'], '.') + 1);
                $file_name = $id . '.' . $ext;
                $path = _PS_MODULE_DIR_ . 'smartblog/images/category/' . $file_name;
                if (!move_uploaded_file($_FILES['category_image']['tmp_name'], $path))
                    return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
                else {
                    if (Configuration::hasContext('category_image', null, Shop::getContext()) && Configuration::get('BLOCKBANNER_IMG') != $file_name)
                        @unlink(dirname(__FILE__) . '/' . Configuration::get('BLOCKBANNER_IMG'));

                    $images_types = BlogImageType::GetImageAllType('category');
                    foreach ($images_types as $image_type) {
                        $dir = _PS_MODULE_DIR_ . 'smartblog/images/category/' . $id . '-' . Tools::stripslashes($image_type['type_name']) . '.jpg';
                        if (file_exists($dir))
                            unlink($dir);
                    }
                    foreach ($images_types as $image_type) {
                        ImageManager::resize($path, _PS_MODULE_DIR_ . 'smartblog/images/category/' . $id . '-' . Tools::stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']
                        );
                    }
                }
            }
        }
    }
}
