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

if (!defined('_PS_VERSION_'))
    exit;


include_once(_PS_MODULE_DIR_ . 'smartblog/classes/SmartBlogGallaryImage.php');

class AdminSmartBlogAjaxController extends ModuleAdminController
{

    public function __construct()
    {
        $this->display_header = false;
        $this->display_footer = false;
        $this->content_only = true;
        //$this->bindToAjaxRequest();        
        parent::__construct();
        $this->_ajax_results['error_on'] = 1;
        // Let's include Lushslider Model
        $this->imageType = 'jpg';
        $this->max_file_size = (int) (Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function ajaxProcessaddGallaryImage()
    {


        self::$currentIndex = 'index.php?tab=AdminSmartBlog&token=' . Tools::getAdminTokenLite('AdminSmartBlog');
        $smart_blog_post = new SmartBlogPost((int) Tools::getValue('id_smart_blog_post'));
        $legends = Tools::getValue('legend');

        if (!is_array($legends))
            $legends = (array) $legends;

        if (!Validate::isLoadedObject($smart_blog_post)) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
        }

        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize($this->max_image_size);
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            $image = new SmartBlogGallaryImage();
            $image->id_smart_blog_post = (int) ($smart_blog_post->id);
            $image->position = SmartBlogGallaryImage::getHighestPosition($smart_blog_post->id) + 1;

            foreach ($legends as $key => $legend)
                if (!empty($legend))
                    $image->legend[(int) $key] = $legend;


            if (($validate = $image->validateFieldsLang(false, true)) !== true)
                $file['error'] = Tools::displayError($validate);

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0))
                continue;

            if (!$image->add())
                $file['error'] = Tools::displayError('Error while creating additional image');
            else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = Tools::displayError('An error occurred during new folder creation');
                    continue;
                }

                $error = 0;

                if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST :
                            $file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
                            break;

                        case ImageManager::ERROR_FILE_WIDTH :
                            $file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
                            break;

                        case ImageManager::ERROR_MEMORY_LIMIT :
                            $file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');
                            break;

                        default:
                            $file['error'] = Tools::displayError('An error occurred while copying image.');
                            break;
                    }
                    continue;
                } else {
                    $imagesTypes = BlogImageType::GetImageAllType('post');


                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['type_name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                            $file['error'] = Tools::displayError('An error occurred while copying image:') . ' ' . Tools::stripslashes($imageType['name']);
                            continue;
                        }
                    }
                }

                unlink($file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', array('id_smart_blog_post_mage' => $image->id, 'id_smart_blog_post' => $smart_blog_post->id));

                if (!$image->update()) {
                    $file['error'] = Tools::displayError('Error while updating status');
                    continue;
                }



                $file['status'] = 'ok';
                $file['id'] = $image->id;
                $file['position'] = $image->position;
                $file['cover']    = $image->cover;
                $file['legend']   = $image->legend;
                $file['path']     = $image->getExistingImgPath();
                $file['shops']     = array("{$this->context->shop->id}"=>true);


                @unlink(_PS_TMP_IMG_DIR_ . 'smart_blog_post_' . (int) $smart_blog_post->id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'smart_blog_post__mini_' . (int) $smart_blog_post->id . '_' . $this->context->shop->id . '.jpg');
            }
        }

        die(Tools::jsonEncode(array($image_uploader->getName() => $files)));
    }

}
