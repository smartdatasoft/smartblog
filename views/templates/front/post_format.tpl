{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $post.post_format == 'video'}
    <div class="custom-markup">
        {$post.post_format_data['video-markup']}
    </div>
     <div class="sdsarticle-des">
                    <span itemprop="description" class="clearfix"><div id="lipsum">
                            {$post.short_description|escape:'htmlall':'UTF-8'}</div></span>
        </div>
{elseif $post.post_format == 'audio'}
    <div class="custom-markup">
        {$post.post_format_data['audio-markup']}
    </div>
     <div class="sdsarticle-des">
                    <span itemprop="description" class="clearfix"><div id="lipsum">
                            {$post.short_description|escape:'htmlall':'UTF-8'}</div></span>
        </div>

{elseif $post.post_format == 'gallery'}
    {$images = SmartBlogGallaryImage::getImages(Context::getContext()->language->id, $post.id_post)}
    {if !empty($images)}
        {$gallery_path = $modules_dir|cat:'smartblog/gallary/'}
        <div id="smartblogpostgallery-{$post.id_post|escape:'htmlall':'UTF-8'}" class="smartblogpostgallery">
            <ul class="sliders">
                {foreach $images as $image}                    
                    <li>
                        {$img_path = $gallery_path|cat:smartblog::gallerypathbyid($image.id_smart_blog_gallary_images)|cat:'.jpg'}
                        {if isset($image.legend) && !empty($image.legend)}
                            {$img_alt = $image.legend}
                        {else}
                            {$img_alt = {l s='Post Gallery' mod='smartblog'}}
                        {/if}
                        <img src="{$img_path|escape:'htmlall':'UTF-8'}" title="{$img_alt|escape:'htmlall':'UTF-8'}" alt="{$img_alt|escape:'htmlall':'UTF-8'}" />
                    </li>
                {/foreach}
            </ul>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {

                var elem = $('#smartblogpostgallery-{$post.id_post|escape:'htmlall':'UTF-8'} > .sliders');
                var slider_width = elem.closest('.articleContent').width();
                if (typeof $.prototype.bxSlider != 'undefined') {
                    elem.bxSlider({
                        useCSS: true,
                        maxSlides: 1,
                        slideWidth: slider_width,
                        infiniteLoop: true,
                        hideControlOnEnd: true,
                        pager: false,
                        autoHover: true,
                        auto: true,
                        speed: 500,
                        pause: 3000,
                        controls: true
                    });
                }

            });
        </script>    

        <div class="sdsarticle-des">
                    <span itemprop="description" class="clearfix"><div id="lipsum">
                            {$post.short_description|escape:'htmlall':'UTF-8'}</div></span>
        </div>

    {/if}
{elseif $post.post_format == 'link'}
   <h2> <a href="{$post.post_format_data['link-url']|escape:'htmlall':'UTF-8'}" title="{$post.post_format_data['link-title']|escape:'htmlall':'UTF-8'}">{$post.post_format_data['link-title']|escape:'htmlall':'UTF-8'}</a>   
    </h2>
    <span class="link"><a href="{$post.post_format_data['link-url']|escape:'htmlall':'UTF-8'}" title="{$post.post_format_data['link-title']|escape:'htmlall':'UTF-8'}">{$post.post_format_data['link-url']|escape:'htmlall':'UTF-8'}</a>   </span> 
{elseif $post.post_format == 'quote'}
 

    <blockquote class="bs-callout bs-callout-info">{$post.post_format_data['quote-text']|escape:'htmlall':'UTF-8'}
 <footer><cite>{$post.post_format_data['quote-author']|escape:'htmlall':'UTF-8'}</cite></footer>
    </blockquote>
{else}
    
    {if isset($ispost) && !empty($ispost)}
    <a itemprop="url" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">    
    
    {/if}    
        {assign var="activeimgincat" value='0'}
        {$activeimgincat = $smartshownoimg}
        
         
        
           {if isset($cameFromLoop)} 
               {if $post.post_img == "no"} 
                {else} 
                    {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1} 
                    <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}" src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}" class="imageFeatured">
 
                  {/if}
                {/if}
           {else}
               {if $post_img == "no"} 
                {else} 
                    {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1} 
                    <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}" src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}" class="imageFeatured">
 
                  {/if}
                {/if}
           {/if} 
             
           
        
        
    {if isset($ispost) && !empty($ispost)}
    </a>
    {/if}
   <div class="sdsarticle-des" style="text-align: left;">
	{if isset($cameFromLoop)}
            
	{$post.short_description}
		{/if}
   </div>

{/if}
