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

class AdminImageTypeController extends ModuleAdminController
{

    public function __construct()
    {
        $this->table = 'smart_blog_imagetype';
        $this->className = 'BlogImageType';
        $this->module = 'smartblog';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->fields_list = array(
            'id_smart_blog_imagetype' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
            ),
            'type_name' => array(
                'title' => $this->l('Type Name'),
                'width' => 350,
                'type' => 'text',
            ),
            'width' => array(
                'title' => $this->l('Width'),
                'width' => 60,
                'type' => 'text',
            ),
            'height' => array(
                'title' => $this->l('Height'),
                'width' => 60,
                'type' => 'text',
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'width' => 220,
                'type' => 'text',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => 60,
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false
            )
        );
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
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Blog Category'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Type Name'),
                    'name' => 'type_name',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('Enter Your Image Type Name Here'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('width'),
                    'name' => 'width',
                    'size' => 15,
                    'required' => true,
                    'desc' => $this->l('Image height in px')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'height',
                    'size' => 15,
                    'required' => true,
                    'desc' => $this->l('Image height in px')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'post',
                                'name' => 'Post'
                            ),
                            array(
                                'id_option' => 'Category',
                                'name' => 'category'
                            ),
                            array(
                                'id_option' => 'Author',
                                'name' => 'author'
                            )
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
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
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        if (!($BlogImageType = $this->loadObject(true)))
            return;

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save   '),
        );
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return $this->setPromotion() . parent::renderList();
    }

    public function setPromotion(){
        $this->context->smarty->assign(array(
            'smartpromotion' => smartblog::getSmartPromotion('image_type_list')
        ));
        $promotion = $this->context->smarty->fetch(_PS_MODULE_DIR_.'smartblog/views/templates/admin/promotion.tpl');
        return $promotion;
    }

    public function initToolbar()
    {
        parent::initToolbar();
    }

}
