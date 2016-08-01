<?php

class AdminBlogPostController extends AdminController {

    public $asso_type = 'shop';
    protected $_blog_post = null;

    public function __construct() {
        $this->table = 'smart_blog_post';
        $this->className = 'SmartBlogPost';
        $this->lang = true;
        $this->image_dir = '../modules/smartblog/images';
        $this->context = Context::getContext();
        $this->_defaultOrderBy = 'created';
        $this->_defaultorderWay = 'DESC';
        $this->bootstrap = true;
        if (Shop::isFeatureActive())
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();
        $this->fields_list = array(
            'id_smart_blog_post' => array(
                'title' => $this->l('Id'),
                'width' => 50,
                'type' => 'text',
                'orderby' => true,
                'filter' => true,
                'search' => true
            ),
            'viewed' => array(
                'title' => $this->l('View'),
                'width' => 50,
                'type' => 'text',
                'lang' => true,
                'orderby' => true,
                'filter' => false,
                'search' => false
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'image' => $this->image_dir,
                'orderby' => false,
                'search' => false,
                'width' => 200,
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'meta_title' => array(
                'title' => $this->l('Title'),
                'width' => 440,
                'type' => 'text',
                'lang' => true,
                'orderby' => true,
                'filter' => true,
                'search' => true
            ),
            'created' => array(
                'title' => $this->l('Posted Date'),
                'width' => 100,
                'type' => 'date',
                'lang' => true,
                'orderby' => true,
                'filter' => true,
                'search' => true
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => '70',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => true,
                'filter' => true,
                'search' => true
            )
        );
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop sbs ON a.id_smart_blog_post=sbs.id_smart_blog_post && sbs.id_shop IN(' . implode(',', Shop::getContextListShopID()) . ')';
        $this->_select = 'sbs.id_shop';
        $this->_defaultOrderBy = 'a.id_smart_blog_post';
        $this->_defaultOrderWay = 'DESC';
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->_group = 'GROUP BY a.smart_blog_post';
        }
        parent::__construct();
    }

    public function renderList() {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function postProcess() {
        $SmartBlogPost = new SmartBlogPost();
        $BlogPostCategory = new SmartBlogPostCategory();

        if (Tools::isSubmit('deletesmart_blog_post') && Tools::getValue('id_smart_blog_post') != '') {
            $SmartBlogPost = new SmartBlogPost((int) Tools::getValue('id_smart_blog_post'));

            if (!$SmartBlogPost->delete()) {
                $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                        . ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            } else {
                Hook::exec('actionsbdeletepost', array('SmartBlogPost' => $SmartBlogPost));
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
            }
        } elseif (Tools::getValue('deleteImage')) {

            $this->processForceDeleteImage();
            if (Tools::isSubmit('forcedeleteImage'))
                Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminBlogPost') . '&conf=7');
        }
        elseif (Tools::isSubmit('submitAddsmart_blog_post')) {
            parent::validateRules();
            if (count($this->errors))
                return false;
            if (!$id_smart_blog_post = (int) Tools::getValue('id_smart_blog_post')) {
                $SmartBlogPost = new $SmartBlogPost();
                $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $title = Tools::getValue('meta_title_'.$language['id_lang']);
                    $SmartBlogPost->meta_title[$language['id_lang']] = $title;
                    $SmartBlogPost->meta_keyword[$language['id_lang']] = (string) Tools::getValue('meta_keyword_' . $language['id_lang']);
                    $SmartBlogPost->meta_description[$language['id_lang']] = Tools::getValue('meta_description_' . $language['id_lang']);
                    $SmartBlogPost->short_description[$language['id_lang']] = (string) Tools::getValue('short_description_' . $language['id_lang']);
                    $SmartBlogPost->content[$language['id_lang']] = Tools::getValue('content_' . $language['id_lang']);
                    if (Tools::getValue('link_rewrite_' . $language['id_lang']) == '' && Tools::getValue('link_rewrite_' . $language['id_lang']) == null) {
                        $SmartBlogPost->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('meta_title_' . $id_lang_default));
                    } else {
                        $SmartBlogPost->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('link_rewrite_' . $language['id_lang']));
                    }
                }
                $SmartBlogPost->id_parent = Tools::getValue('id_parent');
                $SmartBlogPost->position = 0;
                $SmartBlogPost->active = Tools::getValue('active');

                $SmartBlogPost->id_category = Tools::getValue('id_category');
                $SmartBlogPost->comment_status = Tools::getValue('comment_status');
                $SmartBlogPost->id_author = $this->context->employee->id;
                $SmartBlogPost->created = Date('y-m-d H:i:s');
                $SmartBlogPost->modified = Date('y-m-d H:i:s');
                $SmartBlogPost->available = 1;
                $SmartBlogPost->is_featured = Tools::getValue('is_featured');
                $SmartBlogPost->viewed = 1;


                $SmartBlogPost->post_type = Tools::getValue('post_type');

                if (!$SmartBlogPost->save())
                    $this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
                else {
                    Hook::exec('actionsbnewpost', array('SmartBlogPost' => $SmartBlogPost));
                    $this->updateTags($languages, $SmartBlogPost);
                    $this->processImage($_FILES, $SmartBlogPost->id);
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
                }
            } elseif ($id_smart_blog_post = Tools::getValue('id_smart_blog_post')) {

                $SmartBlogPost = new $SmartBlogPost($id_smart_blog_post);
                $languages = Language::getLanguages(false);
			foreach ($languages as $language){
                $title = Tools::getValue('meta_title_'.$language['id_lang']);
				$SmartBlogPost->meta_title[$language['id_lang']] = $title;
				$SmartBlogPost->meta_keyword[$language['id_lang']] = Tools::getValue('meta_keyword_'.$language['id_lang']);
				$SmartBlogPost->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
				$SmartBlogPost->short_description[$language['id_lang']] = Tools::getValue('short_description_'.$language['id_lang']);
				$SmartBlogPost->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
				$SmartBlogPost->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('link_rewrite_'.$language['id_lang']));
                        }
                        $SmartBlogPost->is_featured = Tools::getValue('is_featured');
                        $SmartBlogPost->id_parent = Tools::getValue('id_parent');
                        $SmartBlogPost->active = Tools::getValue('active');
                        $SmartBlogPost->id_category = Tools::getValue('id_category');
                        $SmartBlogPost->comment_status = Tools::getValue('comment_status');
                        $SmartBlogPost->id_author = $this->context->employee->id;
                        $SmartBlogPost->modified = Date('y-m-d H:i:s');
                if (!$SmartBlogPost->update())
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.')
                            . ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
                else
                    Hook::exec('actionsbupdatepost', array('SmartBlogPost' => $SmartBlogPost));
                $this->updateTags($languages, $SmartBlogPost);
                $this->processImage($_FILES, $SmartBlogPost->id_smart_blog_post);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
            }
        }elseif (Tools::isSubmit('statussmart_blog_post') && Tools::getValue($this->identifier)) {

            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    if ($object->toggleStatus()) {
                        Hook::exec('actionsbtogglepost', array('SmartBlogPost' => $this->object));
                        $identifier = ((int) $object->id_parent ? '&id_smart_blog_post=' . (int) $object->id_parent : '');
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogPost'));
                    } else
                        $this->errors[] = Tools::displayError('An error occurred while updating the status.');
                } else
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                            . ' <b>' . $this->table . '</b> ' . Tools::displayError('(cannot load object)');
            } else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }elseif (Tools::isSubmit('smart_blog_postOrderby') && Tools::isSubmit('smart_blog_postOrderway')) {
            $this->_defaultOrderBy = Tools::getValue('smart_blog_postOrderby');
            $this->_defaultOrderWay = Tools::getValue('smart_blog_postOrderway');
        }
    }

    public function processForceDeleteImage() {
        $blog_post = $this->loadObject(true);

        if (Validate::isLoadedObject($blog_post)) {

            $this->deleteImage($blog_post->id_smart_blog_post);
        }
    }

    public function deleteImage($id_smart_blog_post = 1) {

        if (!$id_smart_blog_post)
            return false;


        // Delete base image
        if (file_exists(_MODULE_SMARTBLOG_DIR_ . '/' . $id_smart_blog_post . '.jpg'))
            unlink($this->image_dir . '/' . $id_smart_blog_post . '.jpg');
        else
            return false;

        // now we need to delete the image type of post

        $files_to_delete = array();

        // Delete auto-generated images
        $image_types = SmartBlogImageType::GetImageAllType('post');
        foreach ($image_types as $image_type)
            $files_to_delete[] = $this->image_dir . '/' . $id_smart_blog_post . '-' . $image_type['type_name'] . '.jpg';

        // Delete tmp images
        $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_post_' . $id_smart_blog_post . '.jpg';
        $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'smart_blog_post_mini_' . $id_smart_blog_post . '.jpg';

        foreach ($files_to_delete as $file)
            if (file_exists($file) && !@unlink($file))
                return false;

        return true;
    }

    public function processImage($FILES, $id) {


        if (isset($FILES['image']) && isset($FILES['image']['tmp_name']) && !empty($FILES['image']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($FILES['image'], 4000000))
                return $this->displayError($this->l('Invalid image'));
            else {

                $path = _PS_MODULE_DIR_ . 'smartblog/images/' . $id . '.' . $this->imageType;

                $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if (!$tmp_name)
                    return false;



                if (!move_uploaded_file($FILES['image']['tmp_name'], $tmp_name))
                    return false;


                // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
                if (!ImageManager::checkImageMemoryLimit($tmp_name))
                    $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');

                // Copy new image
                if (empty($this->errors) && !ImageManager::resize($tmp_name, $path, (int) $width, (int) $height, ($ext ? $ext : $this->imageType)))
                    $this->errors[] = Tools::displayError('An error occurred while uploading the image.');

                if (count($this->errors))
                    return false;
                if ($this->afterImageUpload()) {
                    unlink($tmp_name);
                    //  return true;
                }

                $posts_types = SmartBlogImageType::GetImageAllType('post');
                foreach ($posts_types as $image_type) {
                    $dir = _PS_MODULE_DIR_ . 'smartblog/images/' . $id . '-' . stripslashes($image_type['type_name']) . '.jpg';
                    if (file_exists($dir))
                        unlink($dir);
                }
                foreach ($posts_types as $image_type) {
                    ImageManager::resize($path, _PS_MODULE_DIR_ . 'smartblog/images/' . $id . '-' . stripslashes($image_type['type_name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height']
                    );
                }
            }
        }
    }

    public function renderForm() {
        $img_desc = '';
        $img_desc .= $this->l('Upload a Feature Image from your computer.<br/>N.B : Only jpg image is allowed');
        if (Tools::getvalue('id_smart_blog_post') != '' && Tools::getvalue('id_smart_blog_post') != NULL) {
            $img_desc .= '<br/><img style="height:auto;width:300px;clear:both;border:1px solid black;" alt="" src="' . __PS_BASE_URI__ . 'modules/smartblog/images/' . Tools::getvalue('id_smart_blog_post') . '.jpg" /><br />';
        }

        if (!($obj = $this->loadObject(true)))
            return;

        $image = _MODULE_SMARTBLOG_DIR_ . $obj->id . '.jpg';

        $image_url = ImageManager::thumbnail($image, $this->table . '_' . Tools::getvalue('id_smart_blog_post') . '.jpg', 200, 'jpg', true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;



        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Post'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'post_type',
                    'default_value' => 0,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Blog Title'),
                    'name' => 'meta_title',
                    'id' => 'name',
                    'class' => 'copyMeta2friendlyURL',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('Enter Your Blog Post Title'),
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'content',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'required' => true,
                    'hint' => array($this->l('Enter Your Post Description'),
                        $this->l('Invalid characters:') . ' <>;=#{}'
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Feature Image:'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'delete_url' => self::$currentIndex . '&' . $this->identifier . '=' . Tools::getvalue('id_smart_blog_post') . '&token=' . $this->token . '&deleteImage=1',
                    'hint' => $this->l('Upload a feature image from your computer.'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Blog Category'),
                    'name' => 'id_category',
                    'options' => array(
                        'query' => SmartBlogCategory::getCategory(),
                        'id' => 'id_smart_blog_category',
                        'name' => 'meta_title'
                    ),
                    'desc' => $this->l('Select Your Parent Category')
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => array(
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                        $this->l('Invalid characters:') . ' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Short Description'),
                    'name' => 'short_description',
                    'rows' => 10,
                    'cols' => 62,
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->l('Enter Your Post Short Description'),
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta Description'),
                    'name' => 'meta_description',
                    'rows' => 10,
                    'cols' => 62,
                    'lang' => true,
                    'required' => false,
                    'desc' => $this->l('Enter Your Post Meta Description')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link Rewrite'),
                    'name' => 'link_rewrite',
                    'size' => 60,
                    'lang' => true,
                    'required' => false,
                    'hint' => $this->l('Only letters and the hyphen (-) character are allowed.')
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Tag'),
                    'name' => 'tags',
                    'size' => 60,
                    'lang' => true,
                    'required' => false,
                    'hint' => array(
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                        $this->l('Invalid characters:') . ' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Comment Status'),
                    'name' => 'comment_status',
                    'required' => false,
                    'class' => 't',
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
                    ),
                    'desc' => $this->l('You Can Enable or Disable Your Comments')
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
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
                ), array(
                    'type' => 'radio',
                    'label' => $this->l('Is Featured?'),
                    'name' => 'is_featured',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'is_featured',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'is_featured',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($SmartBlogPost = $this->loadObject(true)))
            return;

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save   '),
            'class' => 'button'
        );

        $image = ImageManager::thumbnail(_MODULE_SMARTBLOG_DIR_ . $SmartBlogPost->id_smart_blog_post . '.jpg', $this->table . '_' . (int) $SmartBlogPost->id_smart_blog_post . '.' . $this->imageType, 350, $this->imageType, true);

        $this->fields_value = array(
            'image' => $image ? $image : false,
            'size' => $image ? filesize(_MODULE_SMARTBLOG_DIR_ . $SmartBlogPost->id_smart_blog_post . '.jpg') / 1000 : false
        );

        if (Tools::getvalue('id_smart_blog_post') != '' && Tools::getvalue('id_smart_blog_post') != NULL) {
            foreach (Language::getLanguages(false) as $lang) {
                $this->fields_value['tags'][(int) $lang['id_lang']] = SmartBlogPost::getProductTagsBylang((int) Tools::getvalue('id_smart_blog_post'), (int) $lang['id_lang']);
            }
        }

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');

        return parent::renderForm();
    }

    public function initToolbar() {

        parent::initToolbar();
    }

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    }

    public function updateTags($languages, $post) {
        $tag_success = true;
        if (!SmartBlogPost::deleteTagsForProduct((int) $post->id))
            $this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
        foreach ($languages as $language)
            if ($value = Tools::getValue('tags_' . $language['id_lang']))
                $tag_success &= SmartBlogPost::addTags($language['id_lang'], (int) $post->id, $value);

        if (!$tag_success)
            $this->errors[] = Tools::displayError('An error occurred while adding tags.');
        return $tag_success;
    }

}
