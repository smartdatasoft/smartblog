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

function upgrade_module_2_1_2($object)
{

 
    
    //----------------------------------------------------------------------------------
    Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . 'smart_blog_category`  ADD `level_depth` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `id_parent`');
 
     //-------------------------------------------------------------------------------------
    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT  *  FROM ' . _DB_PREFIX_ . 'smart_blog_category ');

 

//set all root parent category level depth 1
  $result_update = Db::getInstance()->execute('  UPDATE   ' . _DB_PREFIX_ . 'smart_blog_category SET `level_depth` = 1 WHERE   `id_parent` = 0 ') ;

  $level_depth = 1;

    foreach ($results as $result) {

         $id_parent = $result['id_parent'];
        $id_smart_blog_category = $result['id_smart_blog_category'];

          $level_depth = $result['level_depth'];
   

          if($id_parent >0)  {

         

            //now find level depth of parent

          
            $results_depth = Db::getInstance()->executeS('
            SELECT  *  FROM ' . _DB_PREFIX_ . 'smart_blog_category where id_smart_blog_category =  ' .$id_parent );

        
            $rs =$results_depth[0];
    
 

            if((int) $rs['level_depth']<0)
                 $rs['level_depth'] = 1;

             $level_depth = ((int) $rs['level_depth']) +1;
           

            $result_update = Db::getInstance()->execute('
            UPDATE   ' . _DB_PREFIX_ . 'smart_blog_category SET `level_depth` = '.$level_depth.' WHERE   `id_smart_blog_category` =  ' . $id_smart_blog_category);
 
         }


        }
    return true;
}