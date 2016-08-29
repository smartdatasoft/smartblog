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

/**
 * File: CaptchaSecurityImages.php
 * Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Date: 03/08/06
 * Updated: 07/02/07
 * Requirements: PHP 4/5 with GD and FreeType libraries
 * Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
 * 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 *
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');

class CaptchaSecurityImages
{

    private $font = '../views/fonts/monofont.ttf';
    
    public function __construct($width = '120', $height = '40', $characters = '6')
    {
        $fontPath = getcwd().'/'.$this->font;
        $code = $this->generateCode($characters);
        /* font size will be 75% of the image height */
        $font_size = $height * 0.75;
        $image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
        /* set the colours */
        imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 100, 120, 180);
        /* generate random dots in background */
        for ($i = 0; $i < ($width * $height) / 3; $i++) {
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
        }
        /* generate random lines in background */
        for ($i = 0; $i < ($width * $height) / 150; $i++) {
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
        }
        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $fontPath, $code) or die('Error in imagettfbbox function');
        $x = ($width - $textbox[4]) / 2;
        $y = ($height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $fontPath, $code) or die('Error in imagettftext function');
        /* output captcha image to browser */
        $context = Context::getContext();
//        $context->cookie->ssmartblogcaptcha = $code;
        $context->cookie->__set('ssmartblogcaptcha',$code);
        $context->cookie->write();
        
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
       
        //$_SESSION['ssmartblogcaptcha'] = $code;
    }
    public function generateCode($characters)
    {
        /* list all possible characters, similar looking characters and vowels have been removed */
        $possible = '23456789bcdfghjkmnpqrstvwxyz';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= Tools::substr($possible, mt_rand(0, Tools::strlen($possible) - 1), 1);
            $i++;
        }
        return $code;
    }

    

}

$width = Tools::getIsset('width') ? Tools::getValue('width') : '120';
$height = Tools::getIsset('height') ? Tools::getValue('height') : '40';
$characters = Tools::getIsset('characters') && Tools::getValue('characters') > 1 ? Tools::getValue('characters') : '6';

$captcha = new CaptchaSecurityImages($width, $height, $characters);

die();