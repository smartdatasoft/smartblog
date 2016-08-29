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

class SmartBlogGallaryImage extends ObjectModel
{

    public $id;

    /** @var integer Book Image  ID */
    public $id_smart_blog_gallary_images;

    /** @var integer Product ID */
    public $id_smart_blog_post;

    /** @var integer Position used to order images of the same product */
    public $position;

    /** @var boolean Image is cover */
    public $cover;

    /** @var string Legend */
    public $legend;

    /** @var string image extension */
    public $image_format = 'jpg';

    /** @var string path to index.php file to be copied to new image folders */
    public $source_index;

    /** @var string image folder */
    protected $folder;

    /** @var string image path without extension */
    protected $existing_path;

    /** @var int access rights of created folders (octal) */
    protected static $access_rights = 0775;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'smart_blog_gallary_images',
        'primary' => 'id_smart_blog_gallary_images',
        'multilang' => true,
        'fields' => array(
            'id_smart_blog_post' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            //'cover' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'legend' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
        ),
    );
    protected static $_cacheGetSize = array();

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
        $this->image_dir = _MODULE_SMARTBLOG_GALLARY_DIR_;
        $this->source_index = _MODULE_SMARTBLOG_GALLARY_DIR_ . 'index.php';
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0)
            $this->position = SmartBlogGallaryImage::getHighestPosition($this->id_smart_blog_post) + 1;

        return parent::add($autodate, $null_values);
    }

    public function delete()
    {
        if (!parent::delete())
            return false;



        // update positions
        $result = Db::getInstance()->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
			WHERE `id_smart_blog_post` = ' . (int) $this->id_smart_blog_post . '
			ORDER BY `position`
		');
        $i = 1;
        if ($result)
            foreach ($result as $row) {
                $row['position'] = $i++;
                Db::getInstance()->update($this->def['table'], $row, '`id_smart_blog_gallary_images` = ' . (int) $row['id_smart_blog_gallary_images'], 1);
            }

        return true;
    }

    /**
     * Return available images for a product
     *
     * @param integer $id_lang Language ID
     * @param integer $id_smart_blog_post Product ID
     * @param integer $id_smart_blog_post_attribute Product Attribute ID
     * @return array Images
     */
    public static function getImages($id_lang, $id_smart_blog_post)
    {
        $sql = 'SELECT *
			FROM `' . _DB_PREFIX_ . 'smart_blog_gallary_images` i
			LEFT JOIN `' . _DB_PREFIX_ . 'smart_blog_gallary_images_lang` il ON (i.`id_smart_blog_gallary_images` = il.`id_smart_blog_gallary_images`)';


        $sql .= ' WHERE i.`id_smart_blog_post` = ' . (int) $id_smart_blog_post . ' AND il.`id_lang` = ' . (int) $id_lang . '
			ORDER BY i.`position` ASC';
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Return Images
     *
     * @return array Images
     */
    public static function getAllImages()
    {
        return Db::getInstance()->executeS('
		SELECT `id_image`, `id_smart_blog_post`
		FROM `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		ORDER BY `id_image` ASC');
    }

    /**
     * Return number of images for a product
     *
     * @param integer $id_smart_blog_post Product ID
     * @return integer number of images
     */
    public static function getImagesTotal($id_smart_blog_post)
    {
        $result = Db::getInstance()->getRow('
		SELECT COUNT(`id_image`) AS total
		FROM `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		WHERE `id_smart_blog_post` = ' . (int) $id_smart_blog_post);
        return $result['total'];
    }

    /**
     * Return highest position of images for a product
     *
     * @param integer $id_smart_blog_post Product ID
     * @return integer highest position of images
     */
    public static function getHighestPosition($id_smart_blog_post)
    {
        $result = Db::getInstance()->getRow('
		SELECT MAX(`position`) AS max
		FROM `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		WHERE `id_smart_blog_post` = ' . (int) $id_smart_blog_post);
        return $result['max'];
    }

    /**
     * Delete product cover
     *
     * @param integer $id_smart_blog_post Product ID
     * @return boolean result
     */
    public static function deleteCover($id_smart_blog_post)
    {
        if (!Validate::isUnsignedId($id_smart_blog_post))
            die(Tools::displayError());

        if (file_exists(_PS_TMP_IMG_DIR_ . 'book_' . $id_smart_blog_post . '.jpg'))
            unlink(_PS_TMP_IMG_DIR_ . 'book_' . $id_smart_blog_post . '.jpg');

        return (Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
			SET `cover` = 0
			WHERE `id_smart_blog_post` = ' . (int) $id_smart_blog_post
                ) &&
                Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images` i 
			SET i.`cover` = 0
			WHERE  i.id_smart_blog_gallary_images = i.id_smart_blog_gallary_images AND i.`id_smart_blog_post` = ' . (int) $id_smart_blog_post
        ));
    }

    /**
     * Copy images from a product to another
     *
     * @param integer $id_smart_blog_post_old Source product ID
     * @param boolean $id_smart_blog_post_new Destination product ID
     */
    public static function duplicateProductImages($id_smart_blog_post_old, $id_smart_blog_post_new, $combination_images)
    {
        $images_types = BlogImageType::getImagesTypes('post');
        $result = Db::getInstance()->executeS('
		SELECT `id_image`
		FROM `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		WHERE `id_smart_blog_post` = ' . (int) $id_smart_blog_post_old);
        foreach ($result as $row) {
            $image_old = new Image($row['id_image']);
            $image_new = clone $image_old;
            unset($image_new->id);
            $image_new->id_smart_blog_post = (int) $id_smart_blog_post_new;

            // A new id is generated for the cloned image when calling add()
            if ($image_new->add()) {
                $new_path = $image_new->getPathForCreation();
                foreach ($images_types as $image_type) {
                    if (file_exists(_MODULE_SMARTBLOG_GALLARY_DIR_ . $image_old->getExistingImgPath() . '-' . $image_type['name'] . '.jpg')) {
                        if (!Configuration::get('PS_LEGACY_IMAGES'))
                            $image_new->createImgFolder();
                        copy(_MODULE_SMARTBLOG_GALLARY_DIR_ . $image_old->getExistingImgPath() . '-' . $image_type['name'] . '.jpg', $new_path . '-' . $image_type['name'] . '.jpg');
                    }
                }

                if (file_exists(_MODULE_SMARTBLOG_GALLARY_DIR_ . $image_old->getExistingImgPath() . '.jpg'))
                    copy(_MODULE_SMARTBLOG_GALLARY_DIR_ . $image_old->getExistingImgPath() . '.jpg', $new_path . '.jpg');

                SmartBlogGallaryImage::replaceAttributeImageAssociationId($combination_images, (int) $image_old->id, (int) $image_new->id);

                // Duplicate shop associations for images
                $image_new->duplicateShops($id_smart_blog_post_old);
            } else
                return false;
        }
        return SmartBlogGallaryImage::duplicateAttributeImageAssociations($combination_images);
    }

    protected static function replaceAttributeImageAssociationId(&$combination_images, $saved_id, $id_image)
    {
        if (!isset($combination_images['new']) || !is_array($combination_images['new']))
            return;
        foreach ($combination_images['new'] as $id_smart_blog_post_attribute => $image_ids)
            foreach ($image_ids as $key => $image_id)
                if ((int) $image_id == (int) $saved_id)
                    $combination_images['new'][$id_smart_blog_post_attribute][$key] = (int) $id_image;
    }

    /**
     * Reposition image
     *
     * @param integer $position Position
     * @param boolean $direction Direction
     * @deprecated since version 1.5.0.1 use Image::updatePosition() instead
     */
    public function positionImage($position, $direction)
    {
        Tools::displayAsDeprecated();

        $position = (int) $position;
        $direction = (int) $direction;

        // temporary position
        $high_position = Image::getHighestPosition($this->id_smart_blog_post) + 1;

        Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		SET `position` = ' . (int) $high_position . '
		WHERE `id_smart_blog_gallary_images` = ' . (int) $this->id_smart_blog_post . '
		AND `position` = ' . ($direction ? $position - 1 : $position + 1));

        Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		SET `position` = `position`' . ($direction ? '-1' : '+1') . '
		WHERE `id_smart_blog_gallary_images` = ' . (int) $this->id);

        Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
		SET `position` = ' . $this->position . '
		WHERE `id_smart_blog_post` = ' . (int) $this->id_smart_blog_post . '
		AND `position` = ' . (int) $high_position);
    }

    /**
     * Change an image position and update relative positions
     *
     * @param int $way position is moved up if 0, moved down if 1
     * @param int $position new position of the moved image
     * @return int success
     */
    public function updatePosition($way, $position)
    {
        if (!isset($this->id) || !$position)
            return false;

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = (Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way ? '> ' . (int) $this->position . ' AND `position` <= ' . (int) $position : '< ' . (int) $this->position . ' AND `position` >= ' . (int) $position) . '
			AND `id_smart_blog_post`=' . (int) $this->id_smart_blog_post) && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'smart_blog_gallary_images`
			SET `position` = ' . (int) $position . '
			WHERE `id_smart_blog_gallary_images` = ' . (int) $this->id_image));

        return $result;
    }

    public static function getSize($type)
    {
        if (!isset(self::$_cacheGetSize[$type]) || self::$_cacheGetSize[$type] === null)
            self::$_cacheGetSize[$type] = Db::getInstance()->getRow('
				SELECT `width`, `height`
				FROM ' . _DB_PREFIX_ . 'image_type
				WHERE `name` = \'' . pSQL($type) . '\'
			');
        return self::$_cacheGetSize[$type];
    }

    public static function getWidth($params)
    {
        $result = self::getSize($params['type']);
        return $result['width'];
    }

    public static function getHeight($params)
    {
        $result = self::getSize($params['type']);
        return $result['height'];
    }

    /**
     * Clear all images in tmp dir
     */
    public static function clearTmpDir()
    {
        foreach (scandir(_PS_TMP_IMG_DIR_) as $d)
            if (preg_match('/(.*)\.jpg$/', $d))
                unlink(_PS_TMP_IMG_DIR_ . $d);
    }

    /**
     * Delete Image - Product attribute associations for this image
     */
    public function deleteProductAttributeImage()
    {
        return Db::getInstance()->execute('
			DELETE
			FROM `' . _DB_PREFIX_ . 'product_attribute_image`
			WHERE `id_smart_blog_gallary_images` = ' . (int) $this->id
        );
    }

    /**
     * Delete the product image from disk and remove the containing folder if empty
     * Handles both legacy and new image filesystems
     */
    public function deleteImage($force_delete = false)
    {
        if (!$this->id)
            return false;

        // Delete base image
        if (file_exists($this->image_dir . $this->getExistingImgPath() . '.' . $this->image_format))
            unlink($this->image_dir . $this->getExistingImgPath() . '.' . $this->image_format);
        else
            return false;

        $files_to_delete = array();

        // Delete auto-generated images
        $image_types = BlogImageType::getImagesTypes();
        foreach ($image_types as $image_type)
            $files_to_delete[] = $this->image_dir . $this->getExistingImgPath() . '-' . $image_type['name'] . '.' . $this->image_format;

        // Delete watermark image
        $files_to_delete[] = $this->image_dir . $this->getExistingImgPath() . '-watermark.' . $this->image_format;
        // delete index.php
        $files_to_delete[] = $this->image_dir . $this->getImgFolder() . 'index.php';
        // Delete tmp images
        $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'product_' . $this->id_smart_blog_post . '.' . $this->image_format;
        $files_to_delete[] = _PS_TMP_IMG_DIR_ . 'product_mini_' . $this->id_smart_blog_post . '.' . $this->image_format;

        foreach ($files_to_delete as $file)
            if (file_exists($file) && !@unlink($file))
                return false;

        // Can we delete the image folder?
        if (is_dir($this->image_dir . $this->getImgFolder())) {
            $delete_folder = true;
            foreach (scandir($this->image_dir . $this->getImgFolder()) as $file)
                if (($file != '.' && $file != '..')) {
                    $delete_folder = false;
                    break;
                }
        }
        if (isset($delete_folder) && $delete_folder)
            @rmdir($this->image_dir . $this->getImgFolder());

        return true;
    }

    /**
     * Recursively deletes all product images in the given folder tree and removes empty folders.
     *
     * @param string $path folder containing the product images to delete
     * @param string $format image format
     * @return bool success
     */
    public static function deleteAllImages($path, $format = 'jpg')
    {
        if (!$path || !$format || !is_dir($path))
            return false;
        foreach (scandir($path) as $file) {
            if (preg_match('/^[0-9]+(\-(.*))?\.' . $format . '$/', $file))
                unlink($path . $file);
            else if (is_dir($path . $file) && (preg_match('/^[0-9]$/', $file)))
                Image::deleteAllImages($path . $file . '/', $format);
        }

        // Can we remove the image folder?
        if (is_numeric(basename($path))) {
            $remove_folder = true;
            foreach (scandir($path) as $file)
                if (($file != '.' && $file != '..' && $file != 'index.php')) {
                    $remove_folder = false;
                    break;
                }

            if ($remove_folder) {
                // we're only removing index.php if it's a folder we want to delete
                if (file_exists($path . 'index.php'))
                    @unlink($path . 'index.php');
                @rmdir($path);
            }
        }

        return true;
    }

    /**
     * Returns image path in the old or in the new filesystem
     *
     * @ returns string image path
     */
    public function getExistingImgPath()
    {

        if (!$this->id)
            return false;

        if (!$this->existing_path) {
            if (Configuration::get('PS_LEGACY_IMAGES') && file_exists(_MODULE_SMARTBLOG_GALLARY_DIR_ . $this->id_smart_blog_post . '-' . $this->id . '.' . $this->image_format))
                $this->existing_path = $this->id_smart_blog_post . '-' . $this->id;
            else
                $this->existing_path = $this->getImgPath();
        }

        return $this->existing_path;
    }

    /**
     * Returns the path to the folder containing the image in the new filesystem
     *
     * @return string path to folder
     */
    public function getImgFolder()
    {
        if (!$this->id)
            return false;

        if (!$this->folder)
            $this->folder = Image::getImgFolderStatic($this->id);

        return $this->folder;
    }

    /**
     * Create parent folders for the image in the new filesystem
     *
     * @return bool success
     */
    public function createImgFolder()
    {
        if (!$this->id)
            return false;

        if (!file_exists(_MODULE_SMARTBLOG_GALLARY_DIR_ . $this->getImgFolder())) {
            // Apparently sometimes mkdir cannot set the rights, and sometimes chmod can't. Trying both.
            $success = @mkdir(_MODULE_SMARTBLOG_GALLARY_DIR_ . $this->getImgFolder(), self::$access_rights, true);
            $chmod = @chmod(_MODULE_SMARTBLOG_GALLARY_DIR_ . $this->getImgFolder(), self::$access_rights);

            // Create an index.php file in the new folder
            if (($success || $chmod) && !file_exists(_MODULE_SMARTBLOG_GALLARY_DIR_ . $this->getImgFolder() . 'index.php') && file_exists($this->source_index))
                return @copy($this->source_index, _MODULE_SMARTBLOG_GALLARY_DIR_ . $this->getImgFolder() . 'index.php');
        }
        return true;
    }

    /**
     * Returns the path to the image without file extension
     *
     * @return string path
     */
    public function getImgPath()
    {
        if (!$this->id)
            return false;

        $path = $this->getImgFolder() . $this->id;
        return $path;
    }

    /**
     * Returns the path to the folder containing the image in the new filesystem
     *
     * @param mixed $id_image
     * @return string path to folder
     */
    public static function getImgFolderStatic($id_image)
    {
        if (!is_numeric($id_image))
            return false;
        $folders = str_split((string) $id_image);
        return implode('/', $folders) . '/';
    }

    /**
     * Move all legacy product image files from the image folder root to their subfolder in the new filesystem.
     * If max_execution_time is provided, stops before timeout and returns string "timeout".
     * If any image cannot be moved, stops and returns "false"
     *
     * @param int max_execution_time
     * @return mixed success or timeout
     */
    public static function moveToNewFileSystem($max_execution_time = 0)
    {
        $start_time = time();
        $image = null;
        $tmp_folder = 'duplicates/';
        foreach (scandir(_MODULE_SMARTBLOG_GALLARY_DIR_) as $file) {
            // matches the base product image or the thumbnails
            if (preg_match('/^([0-9]+\-)([0-9]+)(\-(.*))?\.jpg$/', $file, $matches)) {
                // don't recreate an image object for each image type
                if (!$image || $image->id !== (int) $matches[2])
                    $image = new Image((int) $matches[2]);
                // image exists in DB and with the correct product?
                if (Validate::isLoadedObject($image) && $image->id_smart_blog_post == (int) rtrim($matches[1], '-')) {
                    // create the new folder if it does not exist
                    if (!$image->createImgFolder())
                        return false;

                    // if there's already a file at the new image path, move it to a dump folder
                    // most likely the preexisting image is a demo image not linked to a product and it's ok to replace it
                    $new_path = _MODULE_SMARTBLOG_GALLARY_DIR_ . $image->getImgPath() . (isset($matches[3]) ? $matches[3] : '') . '.jpg';
                    if (file_exists($new_path)) {
                        if (!file_exists(_MODULE_SMARTBLOG_GALLARY_DIR_ . $tmp_folder)) {
                            @mkdir(_MODULE_SMARTBLOG_GALLARY_DIR_ . $tmp_folder, self::$access_rights);
                            @chmod(_MODULE_SMARTBLOG_GALLARY_DIR_ . $tmp_folder, self::$access_rights);
                        }
                        $tmp_path = _MODULE_SMARTBLOG_GALLARY_DIR_ . $tmp_folder . basename($file);
                        if (!@rename($new_path, $tmp_path) || !file_exists($tmp_path))
                            return false;
                    }
                    // move the image
                    if (!@rename(_MODULE_SMARTBLOG_GALLARY_DIR_ . $file, $new_path) || !file_exists($new_path))
                        return false;
                }
            }
            if ((int) $max_execution_time != 0 && (time() - $start_time > (int) $max_execution_time - 4))
                return 'timeout';
        }
        return true;
    }

    /**
     * Try to create and delete some folders to check if moving images to new file system will be possible
     *
     * @return boolean success
     */
    public static function testFileSystem()
    {
        $safe_mode = Tools::getSafeModeStatus();
        if ($safe_mode)
            return false;
        $folder1 = _MODULE_SMARTBLOG_GALLARY_DIR_ . 'testfilesystem/';
        $test_folder = $folder1 . 'testsubfolder/';
        // check if folders are already existing from previous failed test
        if (file_exists($test_folder)) {
            @rmdir($test_folder);
            @rmdir($folder1);
        }
        if (file_exists($test_folder))
            return false;

        @mkdir($test_folder, self::$access_rights, true);
        @chmod($test_folder, self::$access_rights);
        if (!is_writeable($test_folder))
            return false;
        @rmdir($test_folder);
        @rmdir($folder1);
        if (file_exists($folder1))
            return false;
        return true;
    }

    /**
     * Returns the path where a product image should be created (without file format)
     *
     * @return string path
     */
    public function getPathForCreation()
    {
        if (!$this->id)
            return false;
        if (Configuration::get('PS_LEGACY_IMAGES')) {
            if (!$this->id_smart_blog_post)
                return false;
            $path = $this->id_smart_blog_post . '-' . $this->id;
        }
        else {
            $path = $this->getImgPath();
            $this->createImgFolder();
        }
        return _MODULE_SMARTBLOG_GALLARY_DIR_ . $path;
    }

}