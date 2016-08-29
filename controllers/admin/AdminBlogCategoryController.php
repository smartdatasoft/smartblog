<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminBlogCategoryController extends AdminController
{

    public $module;
 
  /** @var object smart_blog_category() instance for navigation*/
    protected   $smart_blog_category = null;

    public function __construct()
    {
        $this->table = 'smart_blog_category';
        $this->className = 'BlogCategory';
        $this->module = 'smartblog';
        $this->lang = true;
 
       

        $this->image_dir = '../modules/smartblog/images/category';
        $this->bootstrap = true;
  

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
        'id_smart_blog_category' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
         'name' => array('title' => $this->l('Name'), 'width' => 'auto',
          //'callback' => 'hideSMARTBLOGCategoryPosition', 'callback_object' => 'SMARTBLOGCMSCategory'
          ),
        'description' => array('title' => $this->l('Description'), 'maxlength' => 90, 'orderby' => false),
        'position' => array('title' => $this->l('Position'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
        'active' => array(
            'title' => $this->l('Displayed'), 'class' => 'fixed-width-sm', 'active' => 'status',
            'align' => 'center','type' => 'bool', 'orderby' => false
        ));

       /* $id_smart_blog_category = (int)Tools::getValue('id_smart_blog_category', Tools::getValue('id_smart_blog_category', 1));

        $this->smart_blog_category = new BlogCategory($id_smart_blog_category);

 

        $this->context = Context::getContext();
   
  
        $this->_where = ' AND `id_parent` = '.(int)$this->smart_blog_category->id;
        */
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
      
          parent::__construct();

   
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
        $this->addRowAction('view');
        $this->addRowAction('add');
        $this->addRowAction('edit');
        $this->addRowAction('delete');


  
        return parent::renderList();
    }

    public function renderView()
    {
        $this->initToolbar();
        return $this->renderList();
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


      $html_categories = '<div class="col-lg-9">
                        <div class="row">
                          <select name="id_parent">
                            '.$html_categories.'
                          </select>
                        </div>
                      </div>';
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

                // custom template
                array(
                    'type' => 'html',
                    'label' => $this->l('Parent Blog Category'),
                    'name' => 'id_parent',
                    'html_content' => $html_categories,
 
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
    
    public function processDelete()
    {
        $id_parent = BlogCategory::getRootCategory();
        $id_parent = $id_parent['id_smart_blog_category'];
        
        if((int)Tools::getValue('id_smart_blog_category') == (int) $id_parent)
            $this->errors[] = $this->l('You cannot delete this category because it is the root category');
        else
            parent::processDelete();

    }

 
    public function postProcess()
    {
        if (!($object = $this->loadObject(true))) {
            return;
        }

 
        if (!in_array($this->display, array('edit', 'add')))
        $this->multishop_context_group = false;

        if (Tools::isSubmit('forcedeleteImage') || (isset($_FILES['category_image']) && $_FILES['category_image']['size'] > 0) || Tools::getValue('deleteImage')) {
            $this->processForceDeleteImage();
            if (Tools::isSubmit('forcedeleteImage'))
                Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminCategories') . '&conf=7');
        }
         else if (Tools::isSubmit('submitAdd'.$this->table)) {
            $this->action = 'save';
            
            if ($id_smart_blog_category = (int)pSQL(Tools::getValue('id_smart_blog_category'))) {
                $this->id_object = $id_smart_blog_category;
                if (!BlogCategory::checkBeforeMove($id_smart_blog_category, (int)pSQL(Tools::getValue('id_parent')))) {
                    $this->errors[] = Tools::displayError('The Blog Category cannot be moved here.');
                   
                   Tools::redirectAdmin(self::$currentIndex.'&updatesmart_blog_category&viewsmart_blog_category&id_smart_blog_category='.(int)$object->id.'&token='.pSQL(Tools::getValue('token')));

                     return false;
                }
            }
            $object = parent::postProcess();
            $this->updateAssoShop((int)Tools::getValue('id_smart_blog_category'));
            if ($object !== false) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&viewsmart_blog_category&id_smart_blog_category='.(int)$object->id.'&token='.pSQL(Tools::getValue('token')));
            }
            return $object;
        }

 
        return parent::postProcess();
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

  
    public function initToolbar()
    {
        $this->context->smarty->assign(array(
            'showad' => '0', 
        ));
         parent::initToolbar();
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
